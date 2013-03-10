<?php
/**
 * Defines the Board Module test class.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// Instanciate kernel class
$IF = new IF_Kernel();
$IF->init();

/**
 * Board Module test class
 * Evaluates the IF_Module_Board class.
 */
class IF_Module_BoardTest extends PHPUnit_Framework_TestCase
{
  /**
   * Get the forums
   */
  public function testGetForums()
  {
    global $IF;
    $forums = $IF->modules['Board']->getForums();
  }
}
