<?php

  // Save all changes
  $IF->DB->delete('if_forums',
    Predicate::_equal(new Value('forum_id'), $_GET['id']));

  /**
   * @todo Remove the topics & posts from the forums
   */

?>

    <h1>Board &raquo; Forums</h1>
    <p>
      The forum has been deleted.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=forums'; }, 3000);
    </script>