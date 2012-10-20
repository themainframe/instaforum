<?php

header('Content-type: text/plain');

// Interfaces
include 'interfaces/IDataSource.interface.php';

// Include database
include 'classes/datasources/DB/Files.class.php';
include 'classes/datasources/DB/DB.class.php';
include 'classes/datasources/DB/Predicate.class.php';
include 'classes/datasources/DB/Result.class.php';

// Open
DB::open('./db/');

// Insert
DB::insert('people', array(
  'name' => 'John',
  'fav_colour' => 'Red'
));

// Select
$res = DB::select('people');
print $res->count . ' rows in the table.' . "\n";


// Delete everything!!
$res = DB::delete('people');
print $res->affected . ' rows were affected by the query.';


$res = DB::select('people');
print "\n" . $res->count . ' rows are in the table now.';