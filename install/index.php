<?php
/**
 * index.php
 * Instaforum installer index.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// ------------------------------------------------------
// Security contex
// ------------------------------------------------------
define('IF_INSTALLER', true);

// ------------------------------------------------------
// Load classes and boot up
// ------------------------------------------------------
require_once '../init.php';
require_once IF_ROOT_PATH . '/classes/IF_Kernel.class.php';
require_once 'installer_tools.php';

// ------------------------------------------------------
// Detect existing installations
// ------------------------------------------------------
if(!file_exists('../db/if_forums.table'))
{
  define('IF_DB_PRESENT', true);
}

?><!DOCTYPE html>
<html>
  <head>
    <title>Installer</title>
    <script type="text/javascript" src="../js/external/jquery.min.js"></script>
    <style type="text/css">
      body,html {
        font-family: sans-serif;
        margin: 0;
        width: 100%;
        height: 100%;
      }

      div.container {
        margin: auto;
        width: 600px;
        border-top: 30px #000 solid;
        padding: 0px 20px 40px 20px;
        border-bottom: 30px #000 solid;
      }

      a.button {
        padding: 5px 9px 5px 9px;
        border: 3px solid #000;
        color: #000;
        text-decoration: none;
      }

      .right {
        float: right;
      }

      .clear {
        clear: both;
      }

      div.result {
        border-left: 20px solid #afafaf;
        padding-left: 15px;
        margin-left: 20px;
      }

      div.result p.title {
        font-weight: bold;
        margin: 0;
      }

      div.result p.details {
        margin-top: 4px;
        color: #afafaf;
        max-width: 530px;
      }

      div.ok {
        border-left: 20px solid #4eb34b !important;
      }

      div.bad {
        border-left: 20px solid #b34b4b !important;
      }

    </style>
  </head>
  <body>
  <table style="width: 100%; height: 100%;">
    <tbody>
      <tr><td>
  <div class="container">
<?php

// ------------------------------------------------------
// Decide action
// ------------------------------------------------------
$action = str_replace('..', '', $_GET['act']);

// Exists?
if(!file_exists('views/' . $action . '.php'))
{
  $action = 'home';
}

// ------------------------------------------------------
// Override if install done
// ------------------------------------------------------
if(!defined('IF_DB_PRESENT'))
{
  $action = 'already_done';
}

// ------------------------------------------------------
// Load view
// ------------------------------------------------------
include 'views/' . $action . '.php';

?>
  </div>
      </td></tr>
    </tbody>
  </table>
  </body>
</html>