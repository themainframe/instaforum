<?php
/**
 * user_delete.acp.php
 * ACP View: Users: Delete
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
$IF->DB->delete('if_users',
  Predicate::_equal(new Value('user_id'), $_GET['id']));

/**
 * @todo Remove the topics & posts owned by them
 */

?>

    <h1>Users &amp; Permissions &raquo; Forums</h1>
    <p>
      The user account has been deleted.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=users'; }, 
        <?php print $IF::$CONFIG['acp_save_delay']; ?>);
    </script>