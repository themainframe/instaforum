<?php
/**
 * DB Manager.
 * Provides a basic interface to manage the contents of the DB.
 */

?>
<html>
<head>
  <style type="text/css">
    
    * {
      font-family: monospace;
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
      padding: 15px;
    }
    
  </style>
</head>
<body>

<?php

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
  
    break;
}


// Tables list panel
$tables = DB::listTables();


?>

<div style="float: left; width: 15%">
  
  <div style=" width: 100%" class="box">
    <div class="header">Tables</div>
    <div class="content">
  <?php
  foreach($tables as $table)
  {
    print '<a href="?view=' . $table . '">' . $table . '</a><br />' . "\n";
  }
  ?>
    </div>
  </div>
</div>


<?php
if($_GET['view'])
{
?>
  
<form action="?act=insert&view=<?php print $_GET['view']; ?>" method="post">
  
  <div style="float: right; width: 83%;" class="box">
    
    <div class="header"><?php print $_GET['view']; ?> - Insert Row</div>
    
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
  
  <div style="margin-top: 10px; float: right; width: 83%;" class="box">
    
    <div class="header"><?php print $_GET['view']; ?> - Table Content</div>
  
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
?>


</body>
</html>
