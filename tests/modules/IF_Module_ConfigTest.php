<?php
/**
 * Defines the Config Module test class.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

/**
 * Config Module test class
 * Evaluates the IF_Module_Config class.
 */
class IF_Module_ConfigTest extends PHPUnit_Framework_TestCase
{
  /**
   * Get the board title
   */
  public function testGetConfig()
  {
    global $IF;

    // Get the board title value
    $boardTitle = $IF->modules['Config']->get('board_title');

    // Check it is correct
    $this->assertEquals($boardTitle, 'A Board');
  }
}