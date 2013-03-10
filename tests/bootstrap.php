<?php
/**
 * Instaforum Test Suite
 * Boostrap file.
 * 
 * Runs prior to the test suite starting.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// Remove the contents of test_db
system('rm -rf ../test_db/*');

// Load application constants
require_once '../init.php';

// Load required classes
require_once '../classes/IF_Kernel.class.php';

// Set up a forum installation to test on
include '../install/installer_tools.php';

// Define database path
define('DB_PATH', 'test_db');

// Try to remove any old test remenants
print '[ ] Removing all data...' . PHP_EOL;
@system('rm -rf ../' . DB_PATH);

// Create a database for tests to work in
print '[ ] Creating ' . IF_APP_NAME . ' installation in ' . 
  DB_PATH . '...' . PHP_EOL;
mkdir('../' . DB_PATH);
chmod('../' . DB_PATH, 0777);

// Instanciate kernel class
print '[ ] Initialising kernel class...' . PHP_EOL;
global $IF;
$IF = new IF_Kernel();
$IF->init(DB_PATH);

// Perform the installation
print '[ ] Running installation...' . PHP_EOL;
$tempAdminPW = do_install('../install/schema.json');
print '[ ] Installation done, admin password is ' . $tempAdminPW . PHP_EOL;