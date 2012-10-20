<?php

header('Content-type: text/plain');

// Interfaces
include 'interfaces/IDataSource.interface.php';

// Include database
include 'classes/datasources/FileDB/Files.class.php';
include 'classes/datasources/FileDB/FileDB.class.php';
include 'classes/datasources/FileDB/Predicate.class.php';
include 'classes/datasources/FileDB/Result.class.php';

// Open
FileDB::open('./db/');

// Insert
FileDB::insert('people', array(
  'name' => 'John',
  'fav_colour' => 'Red'
));

// Select
$res = FileDB::select('people');
print $res->count . ' rows in the table.' . "\n";


// Delete everything!!
$res = FileDB::delete('people');
print $res->affected . ' rows were affected by the query.';


$res = FileDB::select('people');
print "\n" . $res->count . ' rows are in the table now.';