<?php

  // Save all changes
  $forum = $IF->DB->select('if_forums',
    Predicate::_equal(new Value('forum_id'), $_GET['id']));

  if($forum->count != 1)
  {
    // Not found
    header('Location: ./?act=forums');
  }

  // Reassign to the actual forum object
  $forum = $forum->next();

?>

  <h1>Board &raquo; Edit Forum</h1>
  <h2>Forum Properties</h2>

  <form action="?act=forum_edit_save&amp;id=<?php print $forum->forum_id; ?>" method="post">

    <div class="field">
      <div class="info">
        <span class="title">Forum title</span>
        <p class="description">
          Change the title of the forum
        </p>
      </div>
      <div class="value">
        <input type="text" name="title" value="<?php print $forum->forum_title; ?>" />
      </div>
    </div>

    <div class="field">
      <div class="info">
      </div>
      <div class="value">
        <input type="submit" value="Save Changes" />
      </div>
    </div>

  </form>

