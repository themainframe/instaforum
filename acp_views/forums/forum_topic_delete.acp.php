<?php
/**
 * forum_topic_delete.acp.php
 * ACP View: Forums: Topic Delete
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
$IF->DB->delete('if_topics',
  Predicate::_equal(new Value('topic_id'), $_GET['id']));

/**
 * @todo Remove the topics & posts from the forums
 */

?>

    <h1>Board &raquo; Forums</h1>
    <p>
      The topic has been deleted.
    </p>

    <script type="text/javascript">
      setTimeout(function() { 
        window.location = '?act=forum_show_topics&id=<?php print $_GET['forum_id']; ?>'; }, 
        <?php print $IF::$CONFIG['acp_save_delay']; ?>);
    </script>