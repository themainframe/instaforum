<?php
/**
 * user_add.acp.php
 * ACP View: Users: Add
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

?>

  <h1>Users &amp; Permissions &raquo; Add User</h1>
  <h2>User Properties</h2>

  <form action="?act=user_add_save" method="post">

    <div class="field">
      <div class="info">
        <span class="title">User name</span>
        <p class="description">
          The name of the user.
        </p>
      </div>
      <div class="value">
        <input type="text" name="name" />
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
        <input type="text" name="full_name" />
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
        <input type="text" name="email" value="" />
      </div>
    </div>

    <div class="field">
      <div class="info">
        <span class="title">Password</span>
        <p class="description">
          An password for the user account.
        </p>
      </div>
      <div class="value">
        <input type="password" name="password" value="" />
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
    ?><option value="<?php print $group->group_id;?>">
      <?php print $group->group_name; ?></option>
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
        <input type="submit" value="Save" />
      </div>
    </div>

  </form>

