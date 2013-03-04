<?php
/**
 * forum_new.acp.php
 * ACP View: Forums: New Forum
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// ------------------------------------------------------
// Security check
// ------------------------------------------------------
if(!defined('IF_IN_ACP'))
{
  exit();
}

// Save all changes
$IF->DB->insert('if_forums', array(
  'forum_title' => $_POST['name']
));

?>

    <h1>Board &raquo; Forums</h1>
    <p>
      The forum has been created.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=forums'; }, 3000);
    </script>