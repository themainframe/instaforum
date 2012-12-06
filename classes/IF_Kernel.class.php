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
  public $DB = null;

  /**
   * Modules.
   * An associative array of modules loaded by the kernel.
   * 
   * N.B.  'module name' => module object
   *
   * @var array
   */
  public $modules = array();

  /**
   * Inputs.
   *
   * @var array
   */
  public $in = array();

  /** 
   * Initialise the application kernel, bootstrapping the data store load
   * process.
   *
   * @return boolean
   */
  public function init()
  {
    // --------------------------------------------------
    // Include classes
    // --------------------------------------------------
    require_once IF_ROOT_PATH . '/classes/datasource/DB.class.php';
    require_once IF_ROOT_PATH . '/classes/datasource/Files.class.php';
    require_once IF_ROOT_PATH . '/classes/datasource/Predicate.class.php';
    require_once IF_ROOT_PATH . '/classes/datasource/Result.class.php';

    /**
     * @todo Implement autoloading
     */

    require_once IF_ROOT_PATH . '/classes/IF_Module.class.php';
 
    // --------------------------------------------------
    // Connect database
    // --------------------------------------------------
    try
    {
      $this->DB = new DB(IF_ROOT_PATH . '/db/');
    }
    catch(Exception $exception)
    {
      $this->error('blam');
      return false;
    }
    
    // --------------------------------------------------
    // Handle app input
    // --------------------------------------------------
    $this->in = $this->getInput();

    // --------------------------------------------------
    // Load modules
    // --------------------------------------------------
    $modulesDir = IF_ROOT_PATH . '/classes/modules/';

    // List directory
    $modulesDirHandle = opendir($modulesDir);

    // Read files
    while(false !== ($moduleFile = readdir($modulesDirHandle)))
    {
      // Skip . and ..
      if($moduleFile == '.' || $moduleFile == '..')
      {
        continue;
      }

      // Load the file
      @include $modulesDir . $moduleFile;

      // Parse filename to module name
      preg_match('/(IF_Module_([A-Za-z]+)).class.php/', $moduleFile,
        $moduleNameMatches);

      // Skip if module name looks invalid
      if(count($moduleNameMatches) != 3)
      {
        continue;
      }

      $moduleName = $moduleNameMatches[2];
      $moduleClassName = $moduleNameMatches[1];

      // Create instance
      $this->modules[$moduleName] = new $moduleClassName;

      // Add reference to kernel class object
      $this->modules[$moduleName]->parent = & $this;
    }

    // All OK
    return true;
  }
  
  /**
   * Handle application input.
   *
   * @return boolean
   */
  private function getInput()
  {
    $inputs = array();
  
    foreach($_GET as $getKey => $getValue)
    {
      // Remove null bytes and path traversals
      $getValue = $this->removeNullBytes($getValue);
      $inputs[$getKey] = str_replace('../', '&#46;&#46;/', $getValue);
    }
    
    return $inputs;
  }
  
  /**
   * Remove NUL (0x00) bytes from a string.
   *
   * @return string
   */
  private function removeNullBytes($string)
  {
    return preg_replace('/x00/', '', $string);  
  }
}