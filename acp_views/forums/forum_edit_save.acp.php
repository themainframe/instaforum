<?php
/**
 * forum_edit_save.acp.php
 * ACP View: Forums: Save changes
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
$forum = $IF->DB->update('if_forums', array(
    'forum_title' => $_POST['title']
  ),
  Predicate::_equal(new Value('forum_id'), $_GET['id']));

?>

    <h1>Board &raquo; Forums</h1>
    <p>
      The forum has been saved.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=forums'; }, 
        <?php print $IF::$CONFIG['acp_save_delay']; ?>);
    </script>