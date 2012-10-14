<?php

header('Content-type: text/plain');

// Include database
include 'classes/Files.class.php';
include 'classes/DB.class.php';
include 'classes/Predicate.class.php';
include 'classes/Result.class.php';

DB::createTable('people', array(
  'id' => array('type' => 'int'),
  'name' => array('type' => 'str32'),
  'fav_colour' => array('type' => 'str32')
));

sleep(5);

DB::deleteTable('people');