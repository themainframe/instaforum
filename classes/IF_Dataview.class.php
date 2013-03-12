<?php
/**
 * IF_Dataview.class.php
 * Data view renderer class for ACP.
 * 
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
/**
 * Data view renderer class.
 * 
 * @package IF
 */
class IF_Dataview
{
  /** 
   * The name of the data view used when accessing query string values that
   * relate uniquely to it.
   * 
   * @var string
   */
  public $name = 'dv';

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

  /**
   * Initialise a new Dataview.
   * 
   * @param array $name Optionally the name of this Dataview.  Default 'dv'.
   * @return IF_Dataview
   */
  public function __construct($name = 'dv')
  {
    $this->name = $name;
  }

  /**
   * Add a column to the dataview.
   * 
   * @param string $ID The unique ID of the column.
   * @param array $column The column details.
   */
  public function addColumn($ID, $column)
  {
    $this->columns[$ID] = $column;
  }

  /**
   * Add a multitude of columns to the dataview.
   * 
   * @param array $columns An associative array of columns and their IDs.
   */
  public function addColumns($columns)
  {
    foreach($columns as $key => $column)
    {
      $this->addColumn($key, $column);
    }
  }

  /**
   * Add a row to the dataview.
   *
   * @param array $row An array of values.
   */
  public function addRow($row)
  {
    $this->rows[] = $row;
  }

  /**
   * Add a multitude of rows to the dataview.
   * 
   * @param array $rows An array of rows to add.
   */
  public function addRows($rows)
  {
    foreach($rows as $key => $row)
    {
      $this->addRow($row);
    }
  }

  /**
   * Generate a CSS string from an array.
   * 
   * @param array $css The CSS specificied as an array.
   * @return string
   */
  private function getCSSString($css)
  {
    if(!is_array($css))
    {
      return '';
    }

    $cssString = '';

    foreach($css as $key => $value)
    {
      $cssString .= $key . ': ' . $value . ';';
    }

    return $cssString;
  }

  /**
   * Get the sorting arrow for a column.
   * 
   * @param string $key The column key name.
   * @return string
   */
  private function getSortArrow($key)
  {
    if($_GET[$this->name . '_sort_col_dir'] && 
      $_GET[$this->name . '_sort_col'] == $key)
    {
      return $_GET[$this->name . '_sort_col_dir'] == 'd' ? 
        '&#x25B2; &nbsp;' : // Down arrow
        '&#x25BC; &nbsp;'; // Up arrow;
    }

    return '';
  }

  /**
   * Perform sorting on the data set.
   * 
   * @param string $cardinality The cardinality of the column to sort on.
   * @param boolean $ascending Optionally order in ascending order.  Default false.
   */
  private function sort($cardinality, $ascending = false)
  {
    // Obtain the column we want
    $column = array();
    foreach($this->rows as $ID => $row)
    {
      $column[] = $row[$cardinality];
    }

    array_multisort($column, $ascending ? SORT_ASC : SORT_DESC, $this->rows);
  }

  /**
   * Get a sorting URL for the column with the specified cardinality.
   * 
   * @param integer $cardinality The cardinality of the column.
   * @return string
   */
  private function getSortURL($cardinality)
  {
    // Get the base path
    $urlParts = explode('?', $_SERVER['REQUEST_URI']);
    $url = $urlParts[0] . '?';
    
    // Form an array to make changes to
    $currentQueryString = array();
    foreach($_GET as $key => $value)
    {
      if($value === '' || $key === '')
      {
        continue;
      }
      
      $currentQueryString[$key] = $value;
    }
    
    // Decide on the default order
    $order = 'a';
    if($_GET[$this->name . '_sort_col_dir'] && 
      $_GET[$this->name . '_sort_col_dir'] == 'a')
    {
      $order = 'd';
    }

    // Make changes
    $mergedGETvalues = array_merge($currentQueryString, array(
      $this->name . '_sort_col_dir' => $order,
      $this->name . '_sort_col' => $cardinality
    ));
    
    // Create a query string
    foreach($mergedGETvalues as $key => $value)
    {
      $queryString .= urlencode($key) . '=' . urlencode($value) . '&';
    }
    
    // Remove trailing &
    $queryString = substr($queryString, 0, strlen($querystring) - 1);

    return $url . $queryString;
  }

  /**
   * Render the view.
   * 
   * @return string
   */
  public function render()
  {
    // --------------------------------------------------
    // Prepare data - sorting
    // --------------------------------------------------
    if(!empty($_GET[$this->name . '_sort_col']))
    {
      $this->sort($_GET[$this->name . '_sort_col'], 
        $_GET[$this->name . '_sort_col_dir'] == 'a');
    }

    $html =  '<table>' . PHP_EOL;
    $html .= '  <thead>' . PHP_EOL;
    $html .= '    <tr>' . PHP_EOL;

    // --------------------------------------------------
    // Render the columns
    // --------------------------------------------------
    $cID = 0;
    foreach($this->columns as $key => $column)
    {
      $html .= '      <td style="' . 
        $this->getCSSString($column['css']) . '">' . PHP_EOL;
      $html .= '        <a href="' . 
        ($column['checkbox'] ? '#' : $this->getSortURL($cID + 1)) . '">' . PHP_EOL;
      $html .= '          ' . ($column['checkbox'] ? '' : $this->getSortArrow($cID + 1)) . 
        $column['name'] . PHP_EOL;
      $html .= '        </a>' . PHP_EOL;
      $html .= '      </td>' . PHP_EOL;

      $cID ++;
    }

    $html .= '    </tr>' . PHP_EOL;
    $html .= '  </thead>' . PHP_EOL;
    $html .= '  <tbody>' . PHP_EOL;

    // --------------------------------------------------
    // Render the values
    // --------------------------------------------------
    foreach($this->rows as $row)
    {
      $html .= '    <tr>' . PHP_EOL;

      $cID = 0;
      foreach($this->columns as $key => $column)
      {
        $html .= '      <td style="' . $this->getCSSString($column['cell_css']) . 
          '">' . PHP_EOL;

        if($column['checkbox'])
        { 
          $html .= '        <input type="checkbox" value="' . 
            $row[$cID] . '" />' . PHP_EOL;
        }
        else
        {
          $html .= '        ' . $row[$cID] . PHP_EOL;
        }

        $html .= '      </td>' . PHP_EOL;

        $cID ++;
      }

      $html .= '    </tr>' . PHP_EOL;
    }

    // --------------------------------------------------
    // Close up
    // --------------------------------------------------
    $html .= '  </tbody>' . PHP_EOL;
    $html .=  '</table>' . PHP_EOL;

    return $html;
  }
}