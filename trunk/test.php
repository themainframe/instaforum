<?php

header('Content-type: text/plain');

// Include database
include 'classes/Files.class.php';
include 'classes/DB.class.php';
include 'classes/Predicate.class.php';
include 'classes/Result.class.php';

$result = DB::select('bikes');

while($row = $result->next())
{
  print $row->name . "\n";
} 