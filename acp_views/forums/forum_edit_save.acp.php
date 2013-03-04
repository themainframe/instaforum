<?php

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