<?php
/**
 * style.php
 * Style AJAX/AJACSS responder
 *   
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
 
// Header
header('Content-type: text/css');

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

$IF = new IF_Kernel();
$IF->init();

// Start the session
session_start();

// --------------------------------------------------
// Obtain the CSS
// --------------------------------------------------
$configs = $IF->DB->select('if_config');
while($config = $configs->next())
{
  if(substr($config->config_key, 0, 12) == 'board_style_')
  {
    // Output
    print '.' . substr($config->config_key, 12) . ' {' . PHP_EOL;
    print $config->config_value . PHP_EOL;
    print '}' . PHP_EOL . PHP_EOL;
  }
}