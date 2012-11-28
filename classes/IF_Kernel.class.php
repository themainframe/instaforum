<?php
/**
 * IF_Kernel.class.php
 * Defines the kernel class
 *   
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
/**
 * The kernel class
 *
 * The kernel class is the main class for the IF application.
 *
 * @package IF
 */
class IF_Kernel 
{
  /**
   * The database conncetion instance.
   *
   * @var DB
   */
  public static $DB = null;

  /**
   * Inputs.
   *
   * @var array
   */
  public static $in = array();
  
  /**
   * Output.
   * Represents the `unwrapped` output, without the state information.
   *
   * @var array
   */
  public static $out = array();

  /** 
   * Initialise the application kernel, bootstrapping the data store load
   * process.
   *
   * @return boolean
   */
  public static function init()
  {
    // --------------------------------------------------
    // Include database classes
    // --------------------------------------------------
    require_once IF_ROOT_PATH . '/classes/datasource/DB.class.php';
    require_once IF_ROOT_PATH . '/classes/datasource/Files.class.php';
    require_once IF_ROOT_PATH . '/classes/datasource/Predicate.class.php';
    require_once IF_ROOT_PATH . '/classes/datasource/Result.class.php';
    
    // --------------------------------------------------
    // Connect database
    // --------------------------------------------------
    try
    {
      self::$DB = new DB(IF_ROOT_PATH . '/db/');
    }
    catch(Exception $exception)
    {
      self::error('blam');
      return false;
    }
    
    // --------------------------------------------------
    // Handle app input
    // --------------------------------------------------
    self::$in = self::getInput();
    
    // All OK
    return true;
  }
  
  /**
   * Handle application input.
   *
   * @return boolean
   */
  private static function getInput()
  {
    $inputs = array();
  
    foreach($_GET as $getKey => $getValue)
    {
      // Remove null bytes and path traversals
      $getValue = self::removeNullBytes($getValue);
      $inputs[$getKey] = str_replace('../', '&#46;&#46;/', $getValue);
    }
    
    return $inputs;
  }
  
  /**
   * Remove NUL (0x00) bytes from a string.
   *
   * @return string
   */
  private static function removeNullBytes($string)
  {
    return preg_replace('/x00/', '', $string);  
  }
}