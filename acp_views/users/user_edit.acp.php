<?php
/**
 * user_edit.acp.php
 * ACP View: Users: Edit
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
$user = $IF->DB->select('if_users',
  Predicate::_equal(new Value('user_id'), $_GET['id']));

if($user->count != 1)
{
  // Not found
  header('Location: ./?act=users');
}

// Reassign to the actual forum object
$user = $user->next();

?>

  <h1>Users &amp; Permissions &raquo; Editing: 
    <?php print $user->user_name; ?></h1>
  <h2>User Properties</h2>

  <form action="?act=user_edit_save&amp;id=<?php print $user->user_id; ?>" method="post">

    <div class="field">
      <div class="info">
        <span class="title">User name</span>
        <p class="description">
          The user name cannot be changed.
        </p>
      </div>
      <div class="value">
        <?php print $user->user_name; ?>
      </div>
    </div>

    <div class="field">
      <div class="info">
        <span class="title">"Real" name</span>
        <p class="description">
          The Real username or nickname of the user.
        </p>
      </div>
      <div class="value">
        <input type="text" name="full_name" value="<?php print $user->user_full_name; ?>" />
      </div>
    </div>

    <div class="field">
      <div class="info">
        <span class="title">Email address</span>
        <p class="description">
          An email address to contact the user.
        </p>
      </div>
      <div class="value">
        <input type="text" name="email" value="<?php print $user->user_email; ?>" />
      </div>
    </div>

    <div class="field">
      <div class="info">
        <span class="title">Group membership</span>
        <p class="description">
          The group this user is assigned to.
        </p>
      </div>
      <div class="value">
        <select name="group">
<?php

  $groups = $IF->DB->select('if_groups');
  while($group = $groups->next())
  {
    ?><option value="<?php print $group->group_id;?>"
      <?php print $user->user_group_id == $group->group_id ? 'selected="selected"' : ''; ?>
      ><?php print $group->group_name; ?></option>
    <?php
  }

?>
        </select>
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

