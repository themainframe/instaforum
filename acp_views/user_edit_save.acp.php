<?php

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
      setTimeout(function() { window.location = '?act=users'; }, 3000);
    </script>