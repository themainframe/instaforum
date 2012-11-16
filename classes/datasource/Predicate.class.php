<?php
/** 
 * Defines the Predicate classes.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

/**
 * Predicate value
 * Represents a dynamic value rather than a static value.
 */
class Value
{
  /**
   * The table to which the column belongs.
   * 
   * @var string
   */
  private $table = '';
  
  /**
   * The column name.
   * 
   * @var string
   */
  private $column = '';
  
  /**
   * Create a new column placeholder value
   * 
   * @param string $column The column name.
   * @param string $table Optionally the table name.  Default none.
   * @return Column
   */
  public function __construct($column, $table = '')
  {
    $this->column = $column;
    $this->table = $table;
  }
  
  /**
   * Get the column name
   *
   * @return string
   */
  public function getColumn()
  {
    return strval($this->column);
  }
}
 
/**
 * Predicate class.
 */
class Predicate
{
  /*****************************************************
   * Types: Boolean algebra
   *****************************************************/

  const PRED_AND = 0;
  const PRED_OR  = 1;
  const PRED_XOR = 2;
  
  /*****************************************************
   * Types: Comparison
   *****************************************************/
   
  const PRED_EQUAL = 4;
  const PRED_LIKE = 5;
  
  /*****************************************************
   * Types: Collections
   *****************************************************/

  const PRED_IN = 6;

  /**
   * The type of predicate this instance represents
   *
   * @var integer
   */
  private $predicateType = -1;

  /**
   * The left-hand value
   *
   * @var mixed
   */
  private $leftValue = null;

  /**
   * The right-hand value
   *
   * @var mixed
   */
  private $rightValue = null;

  /*****************************************************
   * Instance methods
   *****************************************************/
  
  /**
   * Evaluate this predicate.
   *
   * @return boolean
   */ 
  public function val($values)
  {
    // Evaluate left and right
    if(is_object($this->leftValue) &&
      get_class($this->leftValue) == 'Predicate')
    {
      // Requires evaluation
      $left = $this->leftValue->val($values);
    }
    else
    {
      $left = $this->leftValue;
    }
    
    if(is_object($this->rightValue) && 
      get_class($this->rightValue) == 'Predicate')
    {
      // Requires evaluation
      $right = $this->rightValue->val($values);
    } 
    else
    {
      $right = $this->rightValue;
    }
    
    // Evaluate values
    if(is_object($this->leftValue) &&
      get_class($this->leftValue) == 'Value')
    {
      // Dynamic value
      $left = $values[$this->leftValue->getColumn()];
    }
    
    if(is_object($this->rightValue) && 
      get_class($this->rightValue) == 'Value')
    {
      // Dynamic value
      $right = $values[$this->rightValue->getColumn()];
    } 
    
    
    // Evaluate this predicate
    switch($this->predicateType)
    {
      case self::PRED_AND:
        return $left && $right;
        
      case self::PRED_OR:
        return $left || $right;
        
      case self::PRED_XOR:
        return $left ^= $right;
        
      case self::PRED_EQUAL:
        return $left == $right;
      
      case self::PRED_IN:
        return is_array($right) && 
          in_array($left, $right);
          
      case self::PRED_LIKE:
        return strpos($left, $right) !== false;
    }
  }

  /*****************************************************
   * Constructors & Factories
   *****************************************************/
  
  /**
   * Create a new predicate with the specified left and right values.
   * 
   * @param mixed $valueLeft The first value.
   * @param mixed $valueRight The second value.
   * @param integer $type Optionally, the type of the predicate.  Default -1.
   * @return Predicate
   */
  public function __construct($leftValue, $rightValue)
  {
    $this->leftValue = $leftValue;
    $this->rightValue = $rightValue;
  }

  /**
   * Initialise a predicate as an AND of two values.
   * 
   * @param mixed $valueLeft The first value.
   * @param mixed $valueRight The second value.
   * @return Predicate
   */
  public static function _and($leftValue, $rightValue)
  {
    $newPredicate = new Predicate($leftValue, $rightValue);
    
    // Set type
    $newPredicate->predicateType = self::PRED_AND;
    return $newPredicate;
  }
  
  /**
   * Initialise a predicate as an OR of two values.
   * 
   * @param mixed $valueLeft The first value.
   * @param mixed $valueRight The second value.
   * @return Predicate
   */
  public static function _or($leftValue, $rightValue)
  {
    $newPredicate = new Predicate($leftValue, $rightValue);
    
    // Set type
    $newPredicate->predicateType = self::PRED_OR;
    return $newPredicate;
  }
  
  /**
   * Initialise a predicate as an XOR of two values.
   * 
   * @param mixed $valueLeft The first value.
   * @param mixed $valueRight The second value.
   * @return Predicate
   */
  public static function _xor($leftValue, $rightValue)
  {
    $newPredicate = new Predicate($leftValue, $rightValue);
    
    // Set type
    $newPredicate->predicateType = self::PRED_XOR;
    return $newPredicate;
  }

  /**
   * Initialise a predicate that tests the equality of two values.
   * 
   * @param mixed $valueLeft The first value.
   * @param mixed $valueRight The second value.
   * @return Predicate
   */
  public static function _equal($leftValue, $rightValue)
  {
    $newPredicate = new Predicate($leftValue, $rightValue);
    
    // Set type
    $newPredicate->predicateType = self::PRED_EQUAL;
    return $newPredicate;
  }

  /**
   * Initialise a predicate that tests if an element is a member of an array.
   * 
   * @param mixed $valueLeft The first value.
   * @param mixed $valueRight The second value.
   * @return Predicate
   */
  public static function _in($leftValue, $rightValue)
  {
    $newPredicate = new Predicate($leftValue, $rightValue);
    
    // Set type
    $newPredicate->predicateType = self::PRED_IN;
    return $newPredicate;
  }

  /**
   * Initialise a predicate that tests if one string appears in another.
   * 
   * @param mixed $valueLeft The first value.
   * @param mixed $valueRight The second value.
   * @return Predicate
   */
  public static function _like($leftValue, $rightValue)
  {
    $newPredicate = new Predicate($leftValue, $rightValue);
    
    // Set type
    $newPredicate->predicateType = self::PRED_LIKE;
    return $newPredicate;
  }
}
