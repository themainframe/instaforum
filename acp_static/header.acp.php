<?php
/**
 * header.acp.php
 * ACP static component: Header 
 */

// ------------------------------------------------------
// Security check
// ------------------------------------------------------
if(!defined('IF_IN_ACP'))
{
  exit();
}

?><!DOCTYPE html>
<html>
<head>
  <title><?php print IF_APP_NAME; ?> - <?php print isset($ACP_TITLE) ? $ACP_TITLE : 'Admin'; ?></title>
  <link rel="stylesheet" type="text/css" href="acp_static/style/default.css" />
  <link rel="stylesheet" type="text/css" href="js/external/jquery.min.js" />
  <link rel="stylesheet" type="text/css" href="acp_static/js/acp.js" />
</head>
<body>

  <!-- BEGIN: Sidebar -->
  <div class="sidebar">
    <div class="logo">
      <span class="logo"><?php print IF_APP_NAME; ?></span>
    </div>
    <div class="section">
      <span class="title">Board</span>
      <a href="?act=forums">Forums &amp; Subforums</a>
      <a href="?act=configuration">Configuration</a>
    </div>
    <div class="section">
      <span class="title">Users &amp; Permissions</span>
      <a href="#">Users</a>
      <a href="#">Groups</a>
    </div>
    <div class="section">
      <span class="title">Integration</span>
      <a href="#">Hooks</a>
      <a href="#">Style Editor</a>
    </div>
    <div class="section">
      <span class="title">System</span>
      <a href="#">Storage</a>
      <a href="#">Plugins</a>
      <a href="#">About</a>
      <a href="?act=logout">Log Out</a>
    </div>
  </div>
  <!-- END: Sidebar -->


  <!-- BEGIN: Body -->
  <div class="body">
