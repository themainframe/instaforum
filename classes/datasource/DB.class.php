<?php
/**
 * Defines the FileDB classes
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
 
/**
 * Schema integrity exception
 */
final class SchemaException extends Exception
{
}
 
/**
 * DB Class
 * Provides interaction with the stored data.
 */
class DB
{
  /**
   * The location of the data files.
   * 
   * @var string
   */
  public $dataPath = '';
  
  /**
   * Data types
   *
   * @var array
   */
  public $types = array(
    
    // Integer types
    'int' => 8,
    'int16' => 16,
    
    // Boolean
    'bool' => 1,
    
    // String types
    'str32' => 32,
    'str64' => 64,
    
    // Blob (binary/text) types
    // This is a "special" type in that the engine handles it differently.
    'blob' => 32
  );
  
  /**
   * Open the database at the specified directory.
   *
   * @throws FileNotFoundException
   * @param string $directory The data store directory to use.
   * @return boolean
   */
  public function __construct($directory)
  {
    if(File::isWritable($directory))
    {
      // Directory is OK
      $this->dataPath = $directory;
    }
    else
    {
      throw new FileNotFoundException($directory);
    }
    
    return true;
  }

  /**
   * List the tables on the database.
   * Returns an array of strings, the names of the tables.
   *
   * @return array
   */
  public function listTables()
  {
    // Read the DB directory
    $tables = array();
    
    if($dataHandle = opendir($this->dataPath))
    {
      while(($file = readdir($dataHandle)) !== false)
      {        
        if($file != '.' && $file != '..' && is_dir($this->dataPath . '/' . $file) && 
          strpos($file, '.table') !== false)
        {
          $tables[] = str_replace('.table', '', $file);
        }
      }
    } 
    
    return $tables;
  }
  
  /**
   * Evaluate the NUL-to-used ratio of a table.
   *
   * Returns an associative array of associative arrays with two keys, 
   * 'used' and 'unused'; specifying the number of bytes in each column
   * that are wasted or used by the DB engine.
   * 
   * @param string $tableName The name of the table to analyse.
   * @return array
   */
  public function analyseTable($tableName)
  {
    // Get columns 
    $columns = self::getTableCols($tableName);
    
    // Open the data file
    $tableDataFile = $this->dataPath . '/' . $tableName . '.table/data';
    
    if(!is_readable($tableDataFile))
    {
      // Can't read data file
      throw new FileNotFoundException($tableDataFile);
    }
    
    // Open handle and prepare to keep track of column usage
    $tableDataHandle = fopen($tableDataFile, 'r');
    
    // Lock the table for reading
    flock($tableDataHandle, LOCK_SH);

    $columnUsage = array();
    
    // Initialise array
    foreach($columns as $columnName => $column)
    {
      $columnUsage[$columnName] = array(
        'used' => 0,
        'unused' => 0
      );
    }
    
    // Start reading
    while(!feof($tableDataHandle))
    {
      // Read a row
      $row = array();
      
      // Build up the row 
      foreach($columns as $columnName => $column)
      {
        $segment = fread($tableDataHandle, $this->types[$column['type']]);
        $unpacked = self::unpackSegment($segment);
        
        // Count bytes
        $segmentLength = $this->types[$column['type']];
        $unpackedLength = strlen($unpacked);
        
        // Tally up
        $columnUsage[$columnName]['used'] += $unpackedLength;
        $columnUsage[$columnName]['unused'] += 
          ($segmentLength - $unpackedLength);
      }
    }
    
    // Unlock and close 
    fclose($tableDataHandle);
 
    return $columnUsage;
  }
   
  /**
   * Convert an array of data into a binstring
   * 
   * @param array $data The data.
   * @param integer $pad Optionally a padding value to pad to with NUL bytes.
   * @return string
   */
  private function getChrs(array $data, $pad = -1)
  {
    $result = '';
    $bCount = 0;
    
    // Convert $data?
    if(!is_array($data))
    {
      $data = str_split(strval($data));
    }
    
    // Write in data
    foreach($data as $d)
    {
      $result .= $d;
      $bCount ++;
    }
    
    // Empty string case
    if(count($data) == 1 && $data[0] == '')
    {
      $data = array();
      $bCount = 0;
    }

    // Pad with NUL (0x00) bytes
    if($pad)
    {
      for(; $bCount < $pad; $bCount ++)
      {
        $result .= chr(0x00);
      }
    }
    
    return $result;
  }
  
  /**
   * Unpack a segment of a data file.
   *
   * @param string $segment The segment to unpack.
   * @return string
   */
  private function unpackSegment($segment)
  {
    $realData = array();
    $characters = str_split(strval($segment));
    
    foreach($characters as $c)
    {
      if($c != chr(0x00))
      {
        $realData[] = $c; 
      }
    }
    
    return implode($realData);
  }

  /** 
   * Truncate a table.
   * ! Removes all data from the table instantly ! 
   * 
   * @throws PermissionDeniedException
   * @param string $tableName The table to truncate. 
   * @return boolean.
   */
  public function truncate($tableName)
  {
    // Easy, just clear the file, no schema data is stored 
    // in datafiles.
    $tableDataFile = $this->dataPath . '/' . $tableName . '.table/data';
    
    if(!is_writeable($tableDataFile))
    {
      // Can't write to the data file
      throw new PermissionDeniedException($tableDataFile);
    }
   
    // Open for writing
    $truncateHandle = fopen($tableDataFile, 'w');
    fclose($truncateHandle);

    // Remove blob files associated with the table
    $tableBlobsPath = $this->dataPath . '/' . $tableName . '.table/blobs/';
    
    if(!is_writable($tableBlobsPath))
    {
      throw new PermissionDeniedException($tableBlobsPath); 
    }
    
    // Remove all files
    if($tableBlobsHandle = opendir($tableBlobsPath))
    {
      while(($file = readdir($tableBlobsHandle)) !== false)
      {
        if($file != '.' && $file != '..')
        {
          unlink($tableBlobsPath . $file);
        }
      }
    } 

    return true;
  } 
  
  /** 
   * Create a table.
   * 
   * $columns should be an associative array matching the prototype:
   *
   *   array(
   *     'columnName' => array(
   *        'auto' => boolean,
   *        'type' => string
   *     )
   *     ...
   *   )
   *
   * @throws PermissionDeniedException, SchemaException
   * @param string $tableName The name of the new table.
   * @param array $columns An associative array describing the columns.
   * @return boolean
   */ 
  public function createTable($tableName, array $columns)
  {
    // Check if the DB is in a writable state
    if(!is_writable($this->dataPath))
    {
      throw new PermissionDeniedException($this->dataPath);
    }
    
    $tablePath = $this->dataPath . '/' . $tableName . '.table';
    
    // Check if the table name is taken
    if(file_exists($tablePath))
    {
      throw new SchemaException('Table with name ' . $tableName . 
        ' already exists in the DB');
    }
    
    // Create the directory and the blobs/autos directories
    mkdir($tablePath);
    mkdir($tablePath . '/blobs'); 
    mkdir($tablePath . '/autos'); 
    
    // Write base files
    touch($tablePath . '/data');
    touch($tablePath . '/definition');
    
    // Open definition file for writing
    $definitionHandle = fopen($tablePath . '/definition', 'w');
    
    // Lock
    flock($definitionHandle, LOCK_EX);
    
    // Build the definition text
    $columnNamesWritten = array();
    
    foreach($columns as $columnName => $column)
    {
      // Check type is valid
      if(!array_key_exists($column['type'], $this->types))
      {
        throw new SchemaException('Type ' . $column['type'] . 
          ' is not defined in the DB');
      }
      
      // Write the column to the definition file
      fwrite($definitionHandle, $column['type'] . ' ' . $columnName . 
        (array_key_exists('auto', $column) && $column['auto'] ? ' auto' : ''));
        
      // If this is an "auto" column, create a file to store the current
      // auto value
      if(array_key_exists('auto', $column) && $column['auto'])
      {
        file_put_contents($tablePath . '/autos/' . $columnName, 
          '1');
      }
        
      // Not last column? Linebreak required
      if(count($columnNamesWritten) != count($columns) - 1)
      {
        fwrite($definitionHandle, "\n");
      }
      
      // Remember that this column has been written
      $columnNamesWritten[] = $columnName;
    }
    
    // Close file & clear the lock
    fclose($definitionHandle);
    
    return true;
  }
  
  /** 
   * Delete a table from the database instantly.
   *
   * @throws PermissionDeniedException
   * @param string $tableName The name of the table to remove.
   * @return boolean
   */ 
  public function deleteTable($tableName)
  {
    // Check if the DB is in a writable state
    if(!is_writable($this->dataPath))
    {
      throw new PermissionDeniedException($this->dataPath);
    }
    
    $tablePath = $this->dataPath . '/' . $tableName . '.table';
    
    // Get the columns
    $columns = self::getTableCols($tableName);
    
    // Remove auto values
    foreach($columns as $columnName => $column)
    {
      if($column['auto'])
      {
        unlink($tablePath . '/autos/' . $columnName);
      }
    }
    
    // Remove auto directory
    rmdir($tablePath . '/autos');
    
    // Remove all blob files by truncating the table first
    self::truncate($tableName);
    
    // Remove the blobs directory and defition/data files
    rmdir($tablePath . '/blobs');
    unlink($tablePath . '/definition');
    unlink($tablePath . '/data');
    
    // Remove the table directory
    rmdir($tablePath);
    
    return true;
  }
  
  /**
   * Write a row into a table.
   *
   * @throws FileNotFoundException, IOException
   * @param string $tableName The table to write to.
   * @param array $values An associative array containing the values.
   * @return Result
   */
  public function insert($tableName, array $values)
  {
    // Create a result
    $result = new Result();
  
    // Open the data file
    $tableDataFile = $this->dataPath . '/' . $tableName . '.table/data';
    
    if(!is_writeable($tableDataFile))
    {
      // Can't write to the data file
      throw new FileNotFoundException($tableDataFile);
    }
    
    // Read lines
    $tableDataHandle = fopen($tableDataFile, 'a');
    
    // Lock
    flock($tableDataHandle, LOCK_EX);
    
    if(!$tableDataHandle)
    {
      throw new IOException($tableDataFile);
    }
  
    // Get the columns for the specified table.
    $columns = self::getTableCols($tableName);
    
    // Write to the end of the file
    foreach($columns as $columnName => $column)
    {
      // Write the segment
      $segSize = $this->types[$column['type']];
     
      // Is the column auto-incrementing and is the increment value desired?
      if($column['auto'] && $values[$columnName] == false)
      {
        // Build the column path
        $autoColumnPath = $this->dataPath . '/' . $tableName . '.table' . 
          '/autos/' . $columnName;
        
        // Retrieve the value
        $value = intval(file_get_contents($autoColumnPath));  
          
        // Write back the value
        file_put_contents($autoColumnPath, strval($value + 1));
  
        // Overwrite column value
        $values[$columnName] = $value;
      }
      
      // Int type?
      if($column['type'] == 'int')
      {
        $values[$columnName] = intval($values[$columnName]);
      }
  
      // Blob?
      if($column['type'] == 'blob')
      { 
        $segData = self::getChrs(
            str_split(self::linkBlob($tableName, $values[$columnName])),
            $this->types['blob']
          );
      }
      else
      {
        $segData = self::getChrs(
          str_split(substr($values[$columnName], 0,
          $this->types[$column['type']])),
          $this->types[$column['type']]);
      }
      
      fwrite($tableDataHandle, $segData);
    }
    
    // Update the result
    $result->setAffectedRows(1);
    $result->finalise();
    
    // Close up and clear the lock
    fclose($tableDataHandle);
    
    return $result;
  }

  /** 
   * Link a blob to a table.
   * 
   * @throws FileNotFoundException
   * @param string $tableName The table to link to.
   * @param string $blobData The data to store.
   * @return string
   */
  private function linkBlob($tableName, $blobData)
  {
    // Path to the blobs directory for the table
    $blobsPath = $this->dataPath . '/' . $tableName . '.table/blobs/';

    // Check it is possible to create the blob.
    if(!File::isWritable($blobsPath))
    {
      throw new FileNotFoundException($blobsPath);
    }

    // Find a unique filename
    $fName = '';
    while($fName == '' || file_exists($blobsPath . $fName))
    {
       $fName = substr(md5(microtime()), 0, 16);
    } 

    // Write data into the blobfile
    file_put_contents($blobsPath . $fName, $blobData);

    return $fName;
  }
 
  /** 
   * Unlink a blob from a table.
   * 
   * @throws FileNotFoundException
   * @param string $tableName The table to link to.
   * @param string $blobID The ID of the blob to remove.
   * @return boolean 
   */
  private function unlinkBlob($tableName, $blobID)
  {
    // Path to the blob file
    $blobPath = $this->dataPath . '/' . $tableName .
      '.table/blobs/' . $blobID;

    // Check it is possible to create the blob.
    if(!File::isWritable($blobPath))
    {
      throw new FileNotFoundException($blobPath);
    }

    // Remove the blob
    unlink($blobPath);

    return true;
  } 

  /**
   * Resolve a blob for a named table.
   *
   * @throws FileNotFoundException
   * @param string $tableName The table to which the blob belongs.
   * @param string $blobID The ID of the blob to resolve.
   * @return string
   */
  private function resolveBlob($tableName, $blobID)
  {
    // Path to the blob file
    $blobPath = $this->dataPath . '/' . $tableName .
      '.table/blobs/' . $blobID;

    // Check it is possible to read the blob.
    if(!File::isReadable($blobPath))
    {
      throw new FileNotFoundException($blobPath);
    }
    
    return file_get_contents($blobPath);
  }

  /**
   * Deletes specified tuples from a table.
   * Only deletes rows satisfying $predicate, if present.
   * 
   * @throws FileNotFoundException
   * @param string $tableName The table to act on.
   * @param Predicate $predicate Optionally a predicate.
   * @return Result
   */
  public function delete($tableName, $predicate = null)
  {  
    // Create a result
    $result = new Result();
  
    // Get structure
    $structure = self::getTableCols($tableName);
  
    // Calculate widths
    $widths = array();
    foreach($structure as $columnName => $column)
    {
      $widths[$columnName] = $this->types[$column['type']];
    }

    // Open the data file
    $tableDataFile = $this->dataPath . '/' . $tableName . '.table/data';
    
    if(!is_readable($tableDataFile))
    {
      // Can't read data file
      throw new FileNotFoundException($tableDataFile);
    }
    
    $tableDataHandle = fopen($tableDataFile, 'r');
    
    // Lock
    flock($tableDataHandle, LOCK_EX);
    
    // Collect new data
    $rows = array(); 

    // Count deleted rows
    $deletedRows = 0;
 
    // Start reading
    while(!feof($tableDataHandle))
    {
      // Read a row
      $row = array();
      
      // Build up the row 
      foreach($structure as $columnName => $column)
      {
        $segment = fread($tableDataHandle, $this->types[$column['type']]);
        
        // Nothing read?
        if(strlen($segment) == 0)
        {
          break 2;
        }
        
        $row[$columnName] = self::unpackSegment($segment);
      }
      
      // Check the row against the predicate if present
      if($predicate && !$predicate->val($row))
      {
        // Keep the row
        $rows[] = $row;
      }
      else
      {
        // Drop the row
        $deletedRows ++;
      }
      
    }
    
    // Close & clear the lock
    fclose($tableDataHandle);
    
    // Truncate
    self::truncate($tableName);

    // Rewrite
    foreach($rows as $row)
    {
      self::insert($tableName, $row);
    } 

    // Update the result
    $result->setAffectedRows($deletedRows);
    $result->finalise();

    return $result;
  }
 

  /**
   * Update specified fields of a table using an associative array mask.
   * Only updates rows satisfying $predicate, if present.
   * 
   * @throws FileNotFoundException
   * @param string $tableName The table to act on.
   * @param array $changes An associative array mask of changes to make.
   * @param Predicate $predicate Optionally a predicate.
   * @return Result
   */
  public function update($tableName, array $changes, $predicate = null)
  {  
    // Create a result
    $result = new Result();
  
    // Start timer
    $structureStartTime = microtime(true);
  
    // Get structure
    $structure = self::getTableCols($tableName);
  
    // Calculate widths
    $widths = array();
    foreach($structure as $columnName => $column)
    {
      $widths[$columnName] = $this->types[$column['type']];
    }
    
    // End timer
    $structureEndTime = microtime(true);
    $result->addProfileTime('GettingStructure', 
      $structureEndTime - $structureStartTime);

    // Open the data file
    $tableDataFile = $this->dataPath . '/' . $tableName . '.table/data';
    
    if(!is_readable($tableDataFile))
    {
      // Can't read data file
      throw new FileNotFoundException($tableDataFile);
    }
    
    $tableDataHandle = fopen($tableDataFile, 'r');
    
    // Lock
    flock($tableDataHandle, LOCK_SH);
    
    // Collect new data
    $rows = array(); 

    // Count affected rows
    $affectedRows = 0;
 
    // Start reading
    while(!feof($tableDataHandle))
    {
      // Read a row
      $row = array();
      
      // Build up the row 
      foreach($structure as $columnName => $column)
      {
        $segment = fread($tableDataHandle, $this->types[$column['type']]);
        
        // Nothing read?
        if(strlen($segment) == 0)
        {
          break 2;
        }
        
        $row[$columnName] = self::unpackSegment($segment);
        
        // Blob resolution required?
        if($column['type'] == 'blob')
        {
          $row[$columnName] = self::resolveBlob($tableName, $row[$columnName]);
        }
      }
      
      // Check the row against the predicate if present
      if(!$predicate || $predicate->val($row))
      {
        // Perform the update - merge one array into the other
        foreach($structure as $cName => $cInfo)
        {
          // If the type is a blob, unlink it, it is now stale
      	  if($cInfo['type'] == 'blob')
      	  {
      	    self::unlinkBlob($tableName, $row[$cName]);
      	  }
         
          if(array_key_exists($cName, $changes))
          {
            $row[$cName] = $changes[$cName];
          }
        }
        
        $alteredRows ++;
      }
      
      $rows[] = $row;
    }
    
    // Close file & clear the lock
    fclose($tableDataHandle);
    
    // Truncate
    self::truncate($tableName);

    // Start timer
    $reinsertRowsStartTime = microtime(true);

    // Rewrite
    foreach($rows as $row)
    {
      self::insert($tableName, $row);
    } 
    
    // End timer
    $reinsertRowsEndTime = microtime(true);
    $result->addProfileTime('ReinsertingRows', 
      $reinsertRowsEndTime - $reinsertRowsStartTime);
    
    $result->setAffectedRows($affectedRows);
    $result->finalise();

    return $result;
  }
 
  /**
   * Change a DB-typed value into a PHP-typed value.
   * 
   * @param mixed $value The value to modify.
   * @param string $dbType The name of the type.
   * @return mixed
   */
  private function internalType($value, $dbType)
  {
    // Catch int
    if(strpos($dbType, 'int') === 0)
    {
      // Cast to int 
      return intval($value);
    }
    
    // Catch bool
    if(strpos($dbType, 'bool') === 0)
    {
      // Cast to boolean
      return (boolean)$value;
    }
    
    // All other types are string PHP types
    return $value;
  }

  /**
   * Select one or more rows from the database, optionally applying a predicate.
   *
   * Using the $limitCount and $limitStart arguments, it is also possible to
   * limit the number of rows returned.
   * 
   * @throws FileNotFoundException
   * @param string $tableName The name of the table to select from.
   * @param Predicate $predicate Optionally a predicate to apply to the selection.
   * @return Result
   */
  public function select($tableName, $predicate = null, $limitCount = -1,
    $limitStart = -1)
  {  
    // Start timer
    $totalStartTime = microtime(true);
  
    // Create a result
    $result = new Result();
    
    // Start timer
    $structureStartTime = microtime(true);
    
    // Get structure
    $structure = self::getTableCols($tableName);
  
    // Calculate widths
    $widths = array();
    foreach($structure as $columnName => $column)
    {
      $widths[$columnName] = $this->types[$column['type']];
      $result->addColumn($columnName);
    }
    
    $structureEndTime = microtime(true);
    $result->addProfileTime('GettingStructure', 
      $structureEndTime - $structureStartTime);
    
    // Open the data file
    $tableDataFile = $this->dataPath . '/' . $tableName . '.table/data';
    
    if(!is_readable($tableDataFile))
    {
      // Can't read data file
      throw new FileNotFoundException($tableDataFile);
    }
    
    $tableDataHandle = fopen($tableDataFile, 'r');
    
    // Lock
    flock($tableDataHandle, LOCK_SH);
    
    $initialLimit = $limitCount;
    
    // Start reading
    while(!feof($tableDataHandle))
    {      
      // End of run?
      if($initialLimit > 0 && $limitCount == 0)
      {
        // No more rows
        break;
      }
    
      // Read a row
      $row = array();
      
      foreach($structure as $columnName => $column)
      {
        $segment = fread($tableDataHandle, $this->types[$column['type']]);
        
        // Nothing read?
        if(strlen($segment) == 0)
        {
          break 2;
        }
        
        $row[$columnName] = 
          $this->internalType(self::unpackSegment($segment), 
          $column['type']);
        
        // Blob resolution required?
        if($column['type'] == 'blob')
        {
          $blobResolveStartTime = microtime(true);
          
          // Resolve the blob
          $row[$columnName] = self::resolveBlob($tableName,
            $row[$columnName]);
          
          $blobResolveEndTime = microtime(true);
          $result->addProfileTime('ResolvingBlobs', 
            $blobResolveEndTime - $blobResolveStartTime);
        }
      }
      
      $applyingPredicatesStartTime = microtime(true);
      
      // Check the row against the predicate if present
      if(!$predicate || $predicate->val($row))
      {
        // Skip this row, or add it?
        if($limitStart <= 0)
        {
          $result->addRow($row);
          $limitCount --;
        }    
      }
      
      $applyingPredicatesEndTime = microtime(true);
      $result->addProfileTime('ApplyingPredicates', 
        $applyingPredicatesEndTime - $applyingPredicatesStartTime);
      
      $limitStart --;
    }
    
    $result->finalise();
    
    // Close the file & clear the lock
    fclose($tableDataHandle);
    
    $totalEndTime = microtime(true);
    $result->addProfileTime('TotalTime', 
      $totalEndTime - $totalStartTime);
    
    return $result;
  }
  
  /**
   * Get the columns of the specified table as an associative array.
   * The array will be formatted:
   * 
   *   array(
   *     'columnName' => array('type' => type (string), 'auto' => auto (boolean)),
   *     ...
   *   )
   *
   * @throws FileNotFoundException, IOException, SchemaException
   * @param string $tableName The table to retreive columns for.
   * @return array
   */
  public function getTableCols($tableName)
  {
    // Collect table column info
    $columns = array();
  
    // Read column data file for the table
    $tableDefsFile = $this->dataPath . '/' . $tableName . '.table/definition';
    
    if(!is_readable($tableDefsFile))
    {
      // Can't read table defs file
      throw new FileNotFoundException($tableDefsFile);
    }
    
    // Read lines
    $tableDefsHandle = fopen($tableDefsFile, 'r');
    
    // Lock
    flock($tableDefsHandle, LOCK_SH);
    
    if(!$tableDefsHandle)
    {
      throw new IOException($tableDefsFile);
    }
    
    $tableDefs = fread($tableDefsHandle, filesize($tableDefsFile));
    fclose($tableDefsHandle);
    
    // Read each line
    $columnDefs = preg_split('/\n/', $tableDefs, -1, PREG_SPLIT_NO_EMPTY);
    
    foreach($columnDefs as $columnDef)
    {
      $columnDetails = preg_split('/[\t\s]/', $columnDef, -1, PREG_SPLIT_NO_EMPTY);
      
      // Check type
      if(!array_key_exists($columnDetails[0], $this->types))
      {
        throw new SchemaException('Column type "' . $columnDetails[0] . 
          '" is undefined in table ' . $tableName);
      }
      
      // Check name
      if(array_key_exists($columnDetails[1], $columns))
      {
        throw new SchemaException('Duplicate column "' . $columnDetails[1] . 
          '" in table ' . $tableName);
      }
      
      // Add to collecton
      $columns[$columnDetails[1]] = array(
        'type' => $columnDetails[0],
        'auto' => (count($columnDetails) > 2 && $columnDetails[2] == 'auto')
      );
    }
    
    return $columns;
  }
}