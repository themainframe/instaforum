<?php
/**
 * Board.class.php
 * Defines the Board module class
 *   
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
/**
 * The Board module class.
 *
 * The Board module provides access to the main board parameters and
 * operations.
 *
 * @package IF
 */
class IF_Module_Board extends IF_Module
{
  /**
   * Get the title of the board.
   * 
   * @return string
   */
  public function getTitle()
  {
    // Retrieve the title
    return 'Test';
  }
}