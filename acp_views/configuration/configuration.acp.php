<?php
/**
 * configuration.acp.php
 * ACP View: Configuration Edit
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

// Get all config
$result = $IF->DB->select('if_config');
$config = array();

while($row = $result->next())
{
  $config[$row->config_key] = $row->config_value;
}

?>


    <h1>Board &raquo; Configuration</h1>

    <h2>Title, Keywords &amp; Description</h2>


  <form action="?act=configuration_save" method="post">

    <div class="field">
      <div class="info">
        <span class="title">Board title</span>
        <p class="description">
          The global title of the board.
        </p>
      </div>
      <div class="value">
        <input type="text" name="board.title" value="<?php print $config['board_title']; ?>" />
      </div>
    </div>

    <div class="field">
      <div class="info">
        <span class="title">Board description</span>
        <p class="description">
          A brief description of the board.
        </p>
      </div>
      <div class="value">
        <textarea name="board.description"><?php print $config['board_description']; ?></textarea>
      </div>
    </div>

    <br clear="both" />

    <h2>Users, Groups &amp; Permissions</h2>

    <div class="field">
      <div class="info">
        <span class="title">Public group</span>
        <p class="description">
          The group public users (I.e. not logged-in) users are placed in.
        </p>
      </div>
      <div class="value">

<select name="users.public_group">
<?php

  $groups = $IF->DB->select('if_groups');
  while($group = $groups->next())
  {
    ?><option value="<?php print $group->group_id;?>"
      <?php print $config['users_public_group'] == $group->group_id ? 'selected="selected"' : ''; ?>
      ><?php print $group->group_name; ?></option>
    <?php
  }

?>
</select>

      </div>
    </div>

    <div class="field">
      <div class="info">
        <span class="title">Default group</span>
        <p class="description">
          The group newly registered users are placed in.
        </p>
      </div>
      <div class="value">

<select name="users.default_group">
<?php

  $groups = $IF->DB->select('if_groups');
  while($group = $groups->next())
  {
    ?><option value="<?php print $group->group_id;?>"
      <?php print $config['users_default_group'] == $group->group_id ? 'selected="selected"' : ''; ?>
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