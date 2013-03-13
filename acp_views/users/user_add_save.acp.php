<?php
/**
 * user_add_save.acp.php
 * ACP View: Users: Add Save
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

// Check if the user already exists
$user = $IF->DB->select('if_users',
  Predicate::_equal(new Value('user_name'), $_POST['name']));

if($user->count != 0)
{
  // Can't add
  header('Location: ./?act=users');
  exit();
}

// Save all changes
$IF->DB->insert('if_users', array(
  'user_name' => $_POST['name'],
  'user_full_name' => $_POST['full_name'],
  'user_email' => $_POST['email'],
  'user_password' => md5($_POST['password'] . IF_PW_SALT),
  'user_group_id' => $_POST['group']
));

?>


    <h1>Users &amp; Permissions &raquo; Add User</h1>
    <p>
      The user account has been created.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=users'; },
        <?php print $IF::$CONFIG['acp_save_delay']; ?>);
    </script>