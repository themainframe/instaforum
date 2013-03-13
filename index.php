<?php
/**
 * index.php
 * ACP index file.
 *
 * @test
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
// Check we are installed
// ------------------------------------------------------
if(!file_exists('./db/if_forums.table'))
{
  // Redirect to installer
  header('Location: ./install');
  exit();
}

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
  'home' => array('acp/home.acp.php', 'Home'),
  'login' => array('acp/login.acp.php', 'Log In'),
  'logout' => array('acp/logout.acp.php', 'Log Out'),
  
  'forums' => array('forums/forums.acp.php', 'Forums'),
  'forum_delete' => array('forums/forum_delete.acp.php', 'Forums - Deleted'),
  'forum_new' => array('forums/forum_new.acp.php', 'Forums - Create'),
  'forum_edit' => array('forums/forum_edit.acp.php', 'Forums - Edit'),
  'forum_edit_save' => array('forums/forum_edit_save.acp.php', 'Forums - Edit'),
  'forum_show_topics' => array('forums/forum_show_topics.acp.php', 'Forums - Show Topics'),
  'forum_topic_delete' => array('forums/forum_topic_delete.acp.php', 'Forums - Delete Topic'),
  'forum_show_posts' => array('forums/forum_show_posts.acp.php', 'Forums - Show Posts'),

  'users' => array('users/users.acp.php', 'Users'),
  'user_edit' => array('users/user_edit.acp.php', 'Users - Edit'),
  'user_edit_save' => array('users/user_edit_save.acp.php', 'Users - Edit'),
  'user_delete' => array('users/user_delete.acp.php', 'Users - Deleted'),
  'user_add' => array('users/user_add.acp.php', 'Users - Add'),
  'user_add_save' => array('users/user_add_save.acp.php', 'Users - Added'),

  'groups' => array('groups/groups.acp.php', 'Groups'),
  'group_edit' => array('groups/group_edit.acp.php', 'Groups - Edit'),
  'group_edit_save' => array('groups/group_edit_save.acp.php', 'Groups - Edit'),

  'hooks' => array('hooks/hooks.acp.php', 'Hooks'),
  'style' => array('style/style.acp.php', 'Style Editor'),
  'style_save' => array('style/style_save.acp.php', 'Style Editor'),

  'configuration' => array('configuration/configuration.acp.php', 'Configuration'),
  'configuration_save' => array('configuration/configuration_save.acp.php', 'Configuration - Saved'),

  'about' => array('acp/about.acp.php', 'About')
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
