<?php

header('Content-type: text/plain');

// Include database
include 'classes/Files.class.php';
include 'classes/DB.class.php';
include 'classes/Predicate.class.php';
include 'classes/Result.class.php';

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