<?php
/**
 * forum_delete.acp.php
 * ACP View: Forums: Delete
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
$IF->DB->delete('if_forums',
  Predicate::_equal(new Value('forum_id'), $_GET['id']));

// Delete topics
$IF->DB->delete('if_topics',
  Predicate::_equal(new Value('topic_forum_id'), $_GET['id']));

// Delete posts
$IF->DB->delete('if_posts',
  Predicate::_equal(new Value('post_forum_id'), $_GET['id']));

?>

    <h1>Board &raquo; Forums</h1>
    <p>
      The forum has been deleted.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=forums'; }, 3000);
    </script>