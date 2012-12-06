<?php
/**
 * IF_Module_Config.class.php
 * Defines the Config module class
 *   
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
/**
 * The Config module class.
 *
 * The Config module provides access to the Instaforum config hive.
 *
 * @package IF
 */
class IF_Module_Config extends IF_Module
{
  /**
   * Retrieve the value of a config hive key.
   * Returns boolean false on failure to find $key.
   * 
   * @param string $key The key to search for.
   * @return mixed
   */
  public function get($key)
  {
    $search = Predicate::_equal(new Value('config_key'), $key);
    $rows = $this->parent->DB->select('if_config', $search);

    // Found row?
    if($rows->count == 1)
    {
      $row = $rows->next();
      return $row->config_value;
    }
    else
    {
      return false;
    }
  }
}