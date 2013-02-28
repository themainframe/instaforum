<?php

  // Save all changes
  $IF->DB->delete('if_users',
    Predicate::_equal(new Value('user_id'), $_GET['id']));

  /**
   * @todo Remove the topics & posts from the forums
   */

?>

    <h1>Users &amp; Permissions &raquo; Forums</h1>
    <p>
      The user account has been deleted.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=users'; }, 3000);
    </script>