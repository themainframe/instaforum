<?php
/**
 * group_edit_Svae.acp.php
 * ACP View: Groups: Save Changes
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
$forum = $IF->DB->update('if_groups', array(
    'group_name' => $_POST['name']
  ),
  Predicate::_equal(new Value('group_id'), $_GET['id']));

// Delete permissions for the group
$IF->DB->delete('if_permissions',
  Predicate::_equal(new Value('permission_group_id'), $_GET['id']));

// Recreate permissions forum-by-forum
$forums = $IF->DB->select('if_forums');
while($forum = $forums->next())
{
  // Build the permission row
  $row = array(
    'permission_id' => NULL,
    'permission_forum_id' => $forum->forum_id,
    'permission_group_id' => $_GET['id']
  );

  // For each permission...
  foreach($IF::$permissions as $key => $name)
  {
    $row['permission_' . $key] = $_POST[$forum->forum_id . '_' . $key];
  }

  $IF->DB->insert('if_permissions', $row);
}

?>


    <h1>Users &amp; Permissions</h1>
    <p>
      The group &amp; permissions mask has been saved.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=groups'; }, 
        <?php print $IF::$CONFIG['acp_save_delay']; ?>);
    </script>