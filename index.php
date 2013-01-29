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

// Action key
$action = isset($_GET['act']) ? $_GET['act'] : 'home';

// Draw the header & footer?
$headerFooterEnabled = true;

if(!isset($_SESSION['admin_name']))
{
  // Not logged in
  $action = 'login';
  $headerFooterEnabled = false;
}

// ------------------------------------------------------
// Load main module
// ------------------------------------------------------

// Define the modules
$modules = array(
  'home' => array('home.acp.php', 'Home'),
  'login' => array('login.acp.php', 'Log In'),

  'configuration' => array('configuration.acp.php', 'Configuration'),
);

// Set title
$ACP_TITLE = $modules[$action][1];

// Load header
if($headerFooterEnabled)
{
  require IF_ROOT_PATH . '/acp_static/header.acp.php';
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
if($headerFooterEnabled)
{ 
  require IF_ROOT_PATH . '/acp_static/footer.acp.php';
}