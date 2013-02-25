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
require_once IF_ROOT_PATH . '/classes/IF_Dataview.class.php';

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

// Start buffering
ob_start();

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
  'logout' => array('logout.acp.php', 'Log Out'),
  
  'forums' => array('forums.acp.php', 'Forums'),
  'forum_delete' => array('forum_delete.acp.php', 'Forums - Deleted'),
  'forum_new' => array('forum_new.acp.php', 'Forums - Create'),
  'forum_edit' => array('forum_edit.acp.php', 'Forums - Edit'),
  'forum_edit_save' => array('forum_edit_save.acp.php', 'Forums - Edit'),

  'configuration' => array('configuration.acp.php', 'Configuration'),
  'configuration_save' => array('configuration_save.acp.php', 'Configuration - Saved'),

  'about' => array('about.acp.php', 'About')
);

// Set title
$ACP_TITLE = $modules[$action][1];

// Load header
if($headerFooterEnabled)
{
  require IF_ROOT_PATH . '/acp_static/header.acp.php';
}

// Validate the file
if(!array_key_exists($action, $modules) ||
  !file_exists(IF_ROOT_PATH . '/acp_views/' . $modules[$action][0]))
{
  print '<h1>Module not found</h1>' . PHP_EOL;
  print '<p>' . IF_APP_NAME . ' can\'t find the specified module.</p>' . PHP_EOL;
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

// Finshed
ob_end_flush();
