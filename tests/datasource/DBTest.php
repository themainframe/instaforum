<?php
/**
 * Defines the Database engine test class.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// Load required classes
include 'classes/datasource/Files.class.php';
include 'classes/datasource/DB.class.php';
include 'classes/datasource/Predicate.class.php';
include 'classes/datasource/Result.class.php';

// Define database path
define('DB_PATH', 'test_db');

// Try to remove any old test remenants
$path = getcwd();
@system('rm -rf ' . $path . '/' . DB_PATH);

// Create a database for tests to work in
mkdir(DB_PATH);
chmod(DB_PATH, 0777);

// Verify
print DB_PATH . ' is' . (file_exists(DB_PATH) ? '' : ' NOT') . ' present.' . "\n";
print DB_PATH . ' is' . (is_writable(DB_PATH) ? '' : ' NOT') . ' writable.' . "\n";

/**
 * DB Test class
 * Evaluates the DB class.
 */
class DBTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var DB The DB instance.
   */
  protected $DB = null;
  
  /**
   * @var array A test row to insert
   */
  protected $testRow = array(
    'col_a' => 12345678,
    'col_b' => 1352956159,  
    'col_c' => true,
    'col_d' => 'Hello world!',
    'col_e' => 'Hello world in 64 bytes!',
    'col_f' => 'Hello world in a blob.',
    'col_g' => 'Hello world in another blob.'
  );
  
  /**
   * @var array A test update mask
   */
  protected $testUpdateMask = array(
    'col_a' => 1234,
    'col_d' => 'Goodbye world.',
    'col_f' => 'A different blob text.'
  );
  
  /**
   * @var array The test row, with the mask applied.
   */
  protected $testRowAppliedMask = array(
    'col_a' => 1234,
    'col_b' => 1352956159,
    'col_c' => true,
    'col_d' => 'Goodbye world.',
    'col_e' => 'Hello world in 64 bytes!',
    'col_f' => 'A different blob text.',
    'col_g' => 'Hello world in another blob.'
  );
  
  /**
   * @var array Multi-row insertion test data.
   */
  protected $testRows = array(
    array( 
      'col_a' => 1,
      'col_b' => 1,
      'col_c' => true,
      'col_d' => 'A',
      'col_e' => 'B',
      'col_f' => 'C',
      'col_g' => 'D'
    ),
    array( 
      'col_a' => 2,
      'col_b' => 2,
      'col_c' => false,
      'col_d' => 'E',
      'col_e' => 'F',
      'col_f' => 'G',
      'col_g' => 'H'
    ),
    array( 
      'col_a' => 3,
      'col_b' => 3,
      'col_c' => true,
      'col_d' => 'I',
      'col_e' => 'J',
      'col_f' => 'K',
      'col_g' => 'L'
    ),  
    array( 
      'col_a' => 4,
      'col_b' => 4,
      'col_c' => false,
      'col_d' => 'M',
      'col_e' => 'N',
      'col_f' => 'O',
      'col_g' => 'P'
    )    
  );

  /**
   * Open the DB connection
   */
  public function testOpenDB()
  {
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
  }

  // --------------------------------------------------
  // Part 1: Creating tables and validating them...
  // --------------------------------------------------
  
  /**
   * Create a table.
   * @depends testOpenDB
   */
  public function testCreateTable()
  {
    // Open data store
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
  
    // Create table
    $this->DB->createTable('alltypes-test', array(
      'col_a' => array('type' => 'int', 'auto' => true),
      'col_b' => array('type' => 'int16'),  
      'col_c' => array('type' => 'bool'),
      'col_d' => array('type' => 'str32'),
      'col_e' => array('type' => 'str64'),
      'col_f' => array('type' => 'blob'),
      'col_g' => array('type' => 'blob')
    ));
    
    // Check low-level file creations happened
    $this->assertFileExists(DB_PATH . '/alltypes-test.table');
    
    // Check datafile exists & is empty
    $this->assertFileExists(DB_PATH . '/alltypes-test.table/data');
    $this->assertEmpty(file_get_contents(DB_PATH . '/alltypes-test.table/data'));
    
    // Check schema file exists & is not empty
    $this->assertFileExists(DB_PATH . '/alltypes-test.table/definition');
    $this->assertNotEmpty(file_get_contents(DB_PATH . '/alltypes-test.table/definition'));
  }
  
  /**
   * Test the created table definition file is correct.
   * @depends testCreateTable
   */
  public function testTableDefinition()
  {
    // Load prototype file
    $protoDefsFile = file_get_contents('tests/.resources/alltypes-test-defs.txt');
    
    // Load definition file
    $createdDefsFile = file_get_contents(DB_PATH . '/alltypes-test.table/definition');
    
    // Compare
    $this->assertEquals($protoDefsFile, $createdDefsFile);
  }
  
  // --------------------------------------------------
  // Part 2: Inserting rows and validating them...
  // --------------------------------------------------
  
  /**
   * Insert a row.
   * @depends testTableDefinition
   */
  public function testInsertRow()
  {
    // Open data store
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
    
    // Insert
    $this->DB->insert('alltypes-test', $this->testRow);
    
    // Verify some data was written
    $this->assertNotEmpty(file_get_contents(DB_PATH . '/alltypes-test.table/data'));
  }
  
  /**
   * Verify autos were written correctly.
   * @depends testInsertRow
   */
  public function testAutosWritten()
  {
    // Verify directory and col_a auto file exist
    $this->assertFileExists(DB_PATH . '/alltypes-test.table/autos');
    $this->assertFileExists(DB_PATH . '/alltypes-test.table/autos/col_a');
    
    // Should be "1" - no rows inserted yet.
    $this->assertEquals('1', 
      file_get_contents(DB_PATH . '/alltypes-test.table/autos/col_a'));
  }

  /**
   * Select the inserted data and verify it
   * @depends testInsertRow
   */
  public function testSelectRow()
  {
    // Open data store
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
    
    // Select the row
    $result = $this->DB->select('alltypes-test');
    
    // Was one row retrieved?
    $this->assertEquals(1, $result->count);
    
    // Get the row
    $row = $result->next();
    
    // stdClass type?
    $this->assertInstanceOf('stdClass', $row);
    
    // Get array and validate contents
    $arrayRow = (array)$row;
    
    $this->assertTrue($arrayRow === $this->testRow);
  }
  
  // --------------------------------------------------
  // Part 3: Update a row
  // --------------------------------------------------
  
  /** 
   * Update a single row
   * @depends testSelectRow
   */
  public function testUpdateRow()
  {
    // Open data store
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
    
    // Update the row with the mask
    $result = $this->DB->update('alltypes-test', $this->testUpdateMask);
    
    // Verify one affected row
    $this->assertEquals(1, $result->affected);
  }
  
  /**
   * Verify the updated row.
   * @depends testUpdateRow
   */
  public function testSelectUpdatedRow()
  {
    // Open data store
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
    
    // Select the row
    $result = $this->DB->select('alltypes-test');
    
    // Was one row retrieved?
    $this->assertEquals(1, $result->count);
    
    // Get the row
    $updatedRow = $result->next();
    
    // stdClass type?
    $this->assertInstanceOf('stdClass', $updatedRow);
    
    // Get array and validate contents
    $arrayUpdatedRow = (array)$updatedRow;
    
    $this->assertTrue($arrayUpdatedRow === $this->testRowAppliedMask);
  }
  
  // --------------------------------------------------
  // Part 4: Delete a row
  // --------------------------------------------------
  
  /**
   * Delete the row and verify that it was deleted.
   * @depends testSelectRow
   */
  public function testDeleteRow()
  {
    // Open data store
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
    
    // Delete the row
    $result = $this->DB->delete('alltypes-test');
    
    // Was one row affected?
    $this->assertEquals(1, $result->affected);
    
    // Is the data file now empty?
    $this->assertEmpty(file_get_contents(DB_PATH . '/alltypes-test.table/data'));
  }
  
  // --------------------------------------------------
  // Part 5: Insert multi rows
  // --------------------------------------------------
  
  /**
   * Add multiple rows to the DB using the insert method.
   * @depends testDeleteRow
   */
  public function testInsertMultiRows()
  {
    // Open data store
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
    
    // For each test row, add the row
    foreach($this->testRows as $row)
    {
      // Insert
      $result = $this->DB->insert('alltypes-test', $row);
      
      // Assert that one row was inserted
      $this->assertEquals(1, $result->affected);
    }
  }
  
  /**
   * Verify that the rows were indeed inserted.
   * @depends testInsertMultiRows
   */
  public function testSelectRows()
  {
    // Open data store
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
    
    // Select all rows from the table
    $result = $this->DB->select('alltypes-test');
    
    // Verify that the count is correct
    $this->assertEquals(count($this->testRows), $result->count);
    
    // Check each row, verifying the order too.
    $index = 0;
    while($row = $result->next())
    {
      // Cast to array before comparison
      $row = (array)$row;
      $this->assertTrue($row === $this->testRows[$index]);
    
      $index ++;
    }
  }
 
  // --------------------------------------------------
  // Part 6: Predicates
  // --------------------------------------------------
  
  /**
   * Update rows selectively using a predicate.
   * @depends testSelectRows
   */
  public function testPredicateUpdate()
  {
    // Open data store
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
    
    // Build a predicate
    $predicate = Predicate::_equal(new Value('col_c'), true);
    
    // Update the data
    $result = $this->DB->update('alltypes-test', $this->testUpdateMask,
      $predicate);
      
    // Were two changes made?
    $this->assertEquals(2, $result->affected);
  }
  
  /**
   * Validate the result of the predicate-controlled update.
   * @depends testPredicateUpdate
   */
  public function testPredicateUpdateResult()
  {
    // Open data store
    $this->DB = new DB(DB_PATH);
    
    // Check DB
    $this->assertInstanceOf('DB', $this->DB);
    
    // Select all rows
    $result = $this->DB->select('alltypes-test');
    
    // Verify rows
    // All the 'true' rows should now have $testUpdateMask applied. 
    $index = 1;
    while($row = $result->next())
    {
      // Cast to array before comparison
      if($row->col_c === true)
      {
        // Should match $testUpdateMask
        $this->assertTrue($row->col_a === $this->testUpdateMask['col_a']);
        $this->assertTrue($row->col_d === $this->testUpdateMask['col_d']);
        $this->assertTrue($row->col_f === $this->testUpdateMask['col_f']);
      }
      
      // Col_b should always match index or order is broken
      $this->assertEquals($row->col_b, $index);
    
      $index ++;
    }
  }
}
