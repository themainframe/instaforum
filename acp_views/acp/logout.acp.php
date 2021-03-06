<?php
/**
 * logout.acp.php
 * ACP View: Log Out
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

unset($_SESSION['admin_name']);

?>

    <h1>Logged Out</h1>

    <p>You are now logged out of the Admin panel.</p>
    <p>You will be redirected shortly.</p>

    <script type="text/javascript">
        setTimeout(function() { window.location = '?'; }, 
          <?php print $IF::$CONFIG['acp_save_delay']; ?>);
    </script>
