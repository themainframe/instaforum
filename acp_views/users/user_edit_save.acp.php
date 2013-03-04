<?php
/**
 * user_edit_save.acp.php
 * ACP View: Users: Save Changes
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
$forum = $IF->DB->update('if_users', array(
    'user_full_name' => $_POST['full_name'],
    'user_email' => $_POST['email']
  ),
  Predicate::_equal(new Value('user_id'), $_GET['id']));

?>


    <h1>Users &amp; Permissions &raquo; Forums</h1>
    <p>
      The user account has been saved.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=users'; },
        <?php print $IF::$CONFIG['acp_save_delay']; ?>);
    </script>