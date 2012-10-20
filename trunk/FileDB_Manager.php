<?php
/**
 * FileDB Manager.
 * Provides a basic interface to manage the contents of a FileDB.
 */
 
header('Content-type: text/plain');

// Interfaces
include 'interfaces/IDataSource.interface.php';

// Include database
include 'classes/datasources/FileDB/Files.class.php';
include 'classes/datasources/FileDB/FileDB.class.php';
include 'classes/datasources/FileDB/Predicate.class.php';
include 'classes/datasources/FileDB/Result.class.php';

// Open
DB::open('./db/');

switch($_GET['act'])
{
  case 'rmtable':
    

    
    break;
}

// Print tables on the LHS
print_r(DB::listTables());