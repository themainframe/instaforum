<?php

  // Save all changes
  $IF->DB->insert('if_forums', array(
    'forum_title' => $_POST['name']
  ));


?>

    <h1>Board &raquo; Forums</h1>
    <p>
      The forum has been created.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=forums'; }, 3000);
    </script>