<?php
/**
 * responder.php
 * Main AJAX responder
 *   
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
 
// Header
header('Content-type: text/plain');

// --------------------------------------------------
// Initialise
// --------------------------------------------------

// Load application constants
require_once '../init.php';

// --------------------------------------------------
// Include classes
// --------------------------------------------------

require_once IF_ROOT_PATH . '/classes/IF_Kernel.class.php';

// --------------------------------------------------
// Initialise application kernel & DB
// --------------------------------------------------

// Init DB
IF_Kernel::init();

print_r($_POST);

/*

// Output a preset string (dev)
print json_encode(

  0 => array(

  )

);*/