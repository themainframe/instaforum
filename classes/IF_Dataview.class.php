<?php
/**
 * IF_Dataview.class.php
 * Data view renderer class for ACP.
 * 
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

/**
 * Data view renderer class.
 */
class IF_Dataview
{
  /** 
   * The columns present in the Data View
   *
   * @var array
   */
  public $columns = array();

  /** 
   * The rows present in the Data View
   *
   * @var array
   */
  public $rows = array();

  /**
   * The number of rows to render per page.
   *
   * @var integer
   */
  public $rowsPerPage = 20;

  /** 
   * The current page.
   *
   * @var integer
   */
  public $page = 1;

}
