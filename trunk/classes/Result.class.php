<?php
/** 
 * Result Class
 * Represents the result of a query against the database.
 */
class Result 
{
  /**
   * The number of rows affected by the associated Insert, Delete
   * or Update query.
   *
   * @var integer
   */
  public $affected = 0;
  
  /** 
   * The number of rows returned by the associated query.
   *
   * @var integer
   */
  public $count = 0;
  
  /** 
   * The columns returned by the associated query.
   *
   * @var array
   */
  public $columns = array();
  
  /**
   * The data returned by the associated query.
   *
   * @var array
   */
  public $rows = array();
  
  /**
   * The index of the current row.
   * 
   * @var integer
   */
  private $index = 0;
  
  /**
   * A flag that indicates that no further changes may be made to the result
   * resource by outside code.
   *
   * @var boolean
   */
  private $finalised = false;
  
  /** 
   * Profiling information collected during the query.
   *
   * @var array
   */
  public $profiling = array();

  //
  // Schema-related
  //
  
  /**
   * Add a column to the result resource.
   * Only valid if the result resource has not been finalised.
   *
   * @param string $name The name of the column.
   * @return boolean
   */
  public function addColumn($name)
  {
    if($this->finalised)
    {
      // Cannot add more rows once finalised
      return false;
    }
    
    $this->columns[] = strval($name);
    
    return true;
  }
  
  /**
   * Add a row to the result resource.
   * Only valid if the result resource has not been finalised.
   *
   * @param array $row An associative array of column names => values.
   * @return boolean
   */
  public function addRow($row)
  {
    if($this->finalised)
    {
      // Cannot add more rows once finalised
      return false;
    }
  
    if(!is_array($row))
    {
      // Invalid argument
      return false;
    }
    
    if(count($row) != count($this->columns))
    {
      // Column-value count mismatch
      return false;
    }
    
    $this->rows[] = $row;
    $this->count ++;
    
    return true;
  }
  
  /**
   * Finalise the result resource, preventing any further row additions.
   * 
   * @return boolean
   */
  public function finalise()
  {
    $this->finalised = true;
    
    return true;
  }
  
  //
  // Iteration-related
  //
  
  /**
   * Get the current row from the result resource and advance the current row index.
   *
   * @param boolean $asArray Optionally return an array instead of an object.
   * @return mixed
   */
  public function next($asArray = false)
  {
    // Reached end?
    if($this->end())
    {
      return false;
    }
    
    $thisRow = array();
    
    foreach($this->columns as $column)
    {
      $thisRow[$column] = $this->rows[$this->index][$column];
    }
    
    // Advance the index
    $this->index ++;
    
    return ($asArray ? $thisRow : (object)$thisRow);
  }
  
  /**
   * Checks if the current row index is at the end of the result resource.
   *
   * @return boolean
   */
  public function end()
  {
    return ($this->index == $this->count);
  }
  
  //
  // Profiling-related
  //
  
  /**
   * Add a profiling time slot.
   *
   * @param string $name The name of the time slot.
   * @param float $time The time consumed in microseconds.
   * @return boolean
   */
  public function addProfileTime($name, $time)
  {
    if(!array_key_exists($name, $this->profiling))
    {
      $this->profiling[$name] = 0.0;
    }
    
    $this->profiling[$name] += round($time * 1000, 4);
    
    return true;
  } 
  
  //
  // Getters/Setters
  //
  
  /**
   * Set the number of affected rows.
   * Only valid if the result resource has not been finalised.
   * 
   * @param integer $affectedRows The number of affected rows.
   * @return boolean
   */
  public function setAffectedRows($affectedRows)
  {
    if($this->finalised)
    {
      return false;
    }
  
    $this->affectedRows = $affectedRows;
    
    return true;
  }
}