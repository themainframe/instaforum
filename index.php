<?php
/**
 * index.php
 * ACP index file.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// ------------------------------------------------------
// Include required files
// ------------------------------------------------------
require_once 'init.php';
require_once IF_ROOT_PATH . '/classes/IF_Kernel.class.php';

// ------------------------------------------------------
// Security
// ------------------------------------------------------
define('IF_IN_ACP', true);
session_start();

// ------------------------------------------------------
// Initialise
// ------------------------------------------------------

$IF = new IF_Kernel();

if(!$IF->init())
{
  print '<strong>Problems encountered during kernel init.</strong>';
}

// ------------------------------------------------------
// Affirm admin status
// ------------------------------------------------------

/** 
 * @todo verify admin security level...
 */

// ------------------------------------------------------
// Load header - Output starts here!
// ------------------------------------------------------
require IF_ROOT_PATH . '/acp_static/header.acp.php';

// ------------------------------------------------------
// Load main module
// ------------------------------------------------------

// Action key
$action = $_GET['act'];

// Define the modules
$modules = array(
  'home' => array('home.acp.php', 'Home'),
  'configuration' => array('configuration.acp.php', 'Configuration'),
);

// Search for the appropriate module
if(!array_key_exists($_GET['act'], $modules))
{
  $action = 'home';
}

// Validate the file
if(!file_exists(IF_ROOT_PATH . '/acp_views/' . $modules[$action][0]))
{
  print '<h1>Module not found</h1>' . PHP_EOL;
  print '<p>Instaforum can\'t find the specified module.</p>' . PHP_EOL;
}
else
{
  // Load the module
  require IF_ROOT_PATH . '/acp_views/' . $modules[$action][0];
}

// ------------------------------------------------------
// Load footer
// ------------------------------------------------------
require IF_ROOT_PATH . '/acp_static/footer.acp.php';
