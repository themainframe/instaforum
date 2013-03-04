<?php
/**
 * group_edit.acp.php
 * ACP View: Groups: Edit
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
$group = $IF->DB->select('if_groups',
  Predicate::_equal(new Value('group_id'), $_GET['id']));

if($group->count != 1)
{
  // Not found
  header('Location: ./?act=groups');
}

// Reassign to the actual forum object
$group = $group->next();

?>

  <h1>Users &amp; Permissions &raquo; Editing Group: 
    <?php print $group->group_name; ?></h1>

  <h2>Group Properties</h2>

  <form action="?act=group_edit_save&amp;id=<?php print $group->group_id; ?>" method="post">

    <div class="field">
      <div class="info">
        <span class="title">Group name</span>
        <p class="description">
          The name of the group.
        </p>
      </div>
      <div class="value">
        <input name="name" type="text" value="<?php print $group->group_name; ?>" />
      </div>
    </div>

    <br style="clear:both;" />
    <br style="clear:both;" />

    <table>
      <thead>
        <tr>
          <td>Group Permission Masks</td>
<?php
  foreach($IF::$permissions as $permission)
  {
    ?>
          <td style="width: 100px; text-align: center"><?php print $permission; ?></td>
    <?php
  }
?>
        </tr>
      </thead>
      <tbody>

<?php

  // Cache all permission mask values for the group first
  $permissions = $IF->DB->select('if_permissions',
    Predicate::_equal(new Value('permission_group_id'), $_GET['id']));

  // Build the mask
  $mask = array();
  while($permission = $permissions->next())
  {
    $mask[$permission->permission_forum_id] = (array)$permission;
  }


  // First, grab all the forums
  $forums = $IF->DB->select('if_forums');

  while($forum = $forums->next())
  {
    ?>
      <tr>
        <td><strong><?php print $forum->forum_title; ?></strong></td>
  <?php
    foreach($IF::$permissions as $key => $name)
    {
      ?>
        <td style="text-align: center">
          <input type="checkbox" name="<?php print $forum->forum_id . '_' . $key; ?>"
            <?php print $mask[$forum->forum_id]['permission_' . $key] ? 'checked' : ''; ?> />
        </td>
      <?php
    }
  ?>
      </tr>
    <?php
  }

?>

      </tbody>
    </table>

    <br />

    <div class="field">
      <div class="info">
      </div>
      <div class="value">
        <input type="submit" value="Save Changes" />
      </div>
    </div>

  </form>