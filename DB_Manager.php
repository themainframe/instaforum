<?php
/**
 * DB Manager.
 * Provides a basic interface to manage the contents of the DB.
 */

// Include database
include 'classes/datasource/Files.class.php';
include 'classes/datasource/DB.class.php';
include 'classes/datasource/Predicate.class.php';
include 'classes/datasource/Result.class.php';

// Open
DB::open('./db/');

switch($_GET['act'])
{
  case 'truncate':
    
    // Delete the table
    DB::truncate($_GET['view']);
    
    // Redirect
    header('Location: ?port=tables&view=' . $_GET['view']);    
  
    break;
    
  case 'insert':
  
    // Get the cols first
    $table = DB::getTableCols($_GET['view']);
  
    // Build values
    $values = array();
    foreach($table as $columnName => $column)
    {
      $values[$columnName] = $_POST[$columnName];
    }
    
    // Store
    DB::insert($_GET['view'], $values);
    
    // Redirect
    header('Location: ?port=tables&view=' . $_GET['view']);
    
  
    break;
    
  case 'add_table':
  
    // Add a table
    // Collect values first
    $columnCount = intval($_POST['nr_columns']);
    
    if($columnCount < 1)
    {
      // No use continuing
      break;
    }
  
    // Collect columns
    $columns = array();
    for($cID = 1; $cID <= $columnCount; $cID ++)
    {
      $columns[$_POST['nr_' . $cID . '_name']] = array(
        'primary' => 0,
        'type' => $_POST['nr_' . $cID . '_type']
      );
    }
    
    // Check if name is taken
    $tableName = $_POST['nr_name'];
    $currentTables = DB::listTables();
    
    if(in_array($tableName, $currentTables))
    {
      // Can't continue
      break;
    }
    
    // Do the insertion
    DB::createTable($tableName, $columns);
    
    // Redirect to table view
    header('Location: ?port=tables&view=' . $tableName);
  
    break;
}


// Tables list panel
$tables = DB::listTables();

?>
<html>
<head>
  <title>DB Manager</title>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
  <style type="text/css">
    
    * {
      font-family: "Lucida Grande";
      font-size: 9pt;
    }
    
    .header {
      font-weight: bold;
      line-height: 35px;
      padding-left: 10px;
      background: #dfdfdf;
    }
    
    div.content {
      padding: 10px;
    }
    
    div.box {
      border: 2px solid #afafaf;
    }
    
    body {
      padding:  0;
      margin: 0
    }
    
    div.container {
      padding: 15px;
    }
    
    h1 {
      font-size: 12pt;
      display: inline;
    }
    
    ul.tabs {
      display: inline;
      list-style: none;
    }
    
    ul.tabs li a {
      text-decoration: none;
      color: #000;
    }
    
    ul.tabs li {
      display: inline;
      border-left: 2px solid #afafaf;
      border-top: 2px solid #afafaf;
      border-right: 2px solid #afafaf;
      padding: 6px;
      background: #dfdfdf;
      margin-right: 5px;
    }
    
    ul.tabs li.selected {
      background: #fff;
    }
    
  </style>
  <script type="text/javascript">
  
    $(function() {
      
      $('#act_col_add').click(nt_add_column);
      $('#act_col_rm').click(nt_rm_column);
      
    });
    
    // Column count
    var cols = 1;
    
    //
    // New Table
    // Add a column to the UI
    //
    function nt_add_column()
    {
      // One more column
      cols ++;
      $('#nr_columns').val(cols);
      
      // Copy the first column and clear the values
      var new_col = $('.column_row').eq(0).clone();
      
      // Set defaults
      $(new_col).find('input[type="text"]').val('');
      $(new_col).find('input[type="checkbox"]').prop('checked', false);
      
      // Set names
      $(new_col).find('.index_cb').attr('name', 'nr_' + cols + '_index');
      $(new_col).find('.auto_cb').attr('name', 'nr_' + cols + '_auto');
      $(new_col).find('.type_sel').attr('name', 'nr_' + cols + '_type');
      $(new_col).find('.name_text').attr('name', 'nr_' + cols + '_name');
      
      // Append
      $(new_col).appendTo('#new_columns');
      
      // Show remove link
      $('#act_col_rm').css('display', 'inline');
      
      return true;
    }
    
    //
    // New Table
    // Remove a column from the UI
    //
    function nt_rm_column()
    {
      // Already last?
      if(cols == 1)
      {
        return false;
      }
    
      // One less column
      cols --;
      $('#nr_columns').val(cols);
      
      // Remove last
      $('.column_row:last').remove();
      
      // Last?
      if(cols == 1)
      {
        $('#act_col_rm').css('display', 'none');
      }
      
      return true;
    }
  
  </script>
  
</head>
<body>

<div style="width:100%; background: #dfdfdf; height: 50px;">
  <h1 style="position: relative; top: 15px; left: 20px">Database Manager</h1>
  <ul class="tabs" style="position: relative; top: 26px;">
    <li class="<?php print ($_GET['port'] == 'db_info' ? 'selected' : ''); ?>">
      <a href="?port=db_info">DB Info</a>
    </li>
    <li class="<?php print ($_GET['port'] == 'tables' ? 'selected' : ''); ?>">
      <a href="?port=tables">Tables</a>
    </li>
    <li class="<?php print ($_GET['port'] == 'add_table' ? 'selected' : ''); ?>">
      <a href="?port=add_table">Add Table</a>
    </li>
  </ul>
</div>

<div class="container">

<?php

  switch($_GET['port'])
  {
  
    case 'tables':

      if($_GET['view'])
      {    
?>

  <h1><?php print $_GET['view']; ?></h1>
  
  <form action="?act=insert&view=<?php print $_GET['view']; ?>" method="post">
    
    <div style="margin-top: 10px;" class="box">
      
      <div class="header">Insert Row</div>
      
      <br />
      
      <table style="width: 100%">
        
        <tbody>
        
        <tr>
        
      <?php
      
          
        // Get table
        $table = DB::getTableCols($_GET['view']);
      
        // Show the insertion row
        foreach($table as $columnName => $column)
        {
          print '<td>' . $columnName . '(' . DB::$types[$column['type']] . '): <input type="text" name="' . $columnName . '" style="width: ' . 
            DB::$types[$column['type']] * 4 . 'px" /></td>' . "\n";
        }
      
      ?>
      
        <td>
          <input type="submit" value="Insert!" />
        </td>
        
        </tr>
        
        </tbody>
        
      </table>
      
    </div>
    
  </form>
    
    <div style="margin-top: 10px;" class="box">
      
      <div class="header">Table Content</div>
    
      <table style="width: 100%; border-spacing: 0px;">
        <thead>
          <tr class="header">
      <?php
  
        
        // Produce columns first
        foreach($table as $columnName => $column)
        {
          print '<td>' . $columnName . '</td>' . "\n";
        }
      
      ?>
      
        <td style="width: 70px">
          <a href="?act=truncate&view=<?php print $_GET['view']; ?>">Truncate</a>
        </td>
      
          </tr>
        </thead>
        <tbody>
        
      <?php
        
        // Produce rows
        $rows = DB::select($_GET['view']);
        
        while($row = $rows->next())
        {
          ?>
            <tr style="height: 30px;">
              
              <?php
              
                foreach($row as $columnName => $value)
                {
                  print '<td>' . $value. '</td>' . "\n";
                }
              
              ?>
              
              
            </tr>
          <?php
        }
      
      ?>
        
        </tbody>
      </table>
    </div>
  

<?php

    }
    else
    {
    
?>

  <div style=" width: 100%" class="box">
    <div class="header">Tables</div>
    <div class="content">
  <?php
  foreach($tables as $table)
  {
    print '<a href="?port=tables&view=' . $table . '">' . $table . '</a><br />' . "\n";
  }
  ?>
    </div>
  </div>


<?php

  }

  break;
  
  case 'add_table':
  
?>

 
  <form action="?act=add_table" method="post">
    
    <div style="width: 100%;" class="box">
      
      <div class="header">New Table</div>
      
      <div class="content">
      
        <br />
        &nbsp; <strong>Table Name: </strong> &nbsp;
        <input type="text" name="nr_name" /> &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="#" id="act_col_add">Add 1 column</a> &nbsp;  &nbsp;
        <a href="#" id="act_col_rm" style="display: none">Delete last column</a> &nbsp; &nbsp; 
        <input type="submit" value="Create Table!" style="float: right;" />
        <input type="hidden" id="nr_columns" name="nr_columns" value="1" />
        <br /><br />
        <table style="width: 100%">
          
          <thead>
            
            <tr class="header">
              <td style="padding-left: 10px; width: 50px">Auto?</td>
              <td style="padding-left: 10px; width: 50px">Index?</td>
              <td style="padding-left: 10px; width: 250px;">Type</td>
              <td style="padding-left: 10px;">Name</td>
            </tr>
            
            
          </thead>
          
          <tbody id="new_columns">
          
            <tr class="column_row">
              
              <td><input type="checkbox" class="auto_cb" name="nr_1_auto" /></td>
              <td><input type="checkbox" class="index_cb" name="nr_1_index" /></td>
              <td>
                <select style="width: 100%" class="type_sel" name="nr_1_type">
                  <option value="int">int</option>
                  <option value="str32">str32</option>
                  <option value="str64">str64</option>
                  <option value="blob">blob</option>
                </select>
              </td>
              <td><input name="nr_1_name" type="text" class="name_text" style="width: 98%; margin-left: 4px" /></td>
              
            </tr>
          
          </tbody>
          
        </table>
        
      </div>
    </div>
    
  </form>

</div>


<?php

  break;
  
}
?>

</body>
</html>
