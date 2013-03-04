<?php
/**
 * configuration_save.acp.php
 * ACP View: Configuration Edit: Save
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
$IF->DB->delete('if_config');
  
foreach($_POST as $key => $value)
{
  $IF->DB->insert('if_config', array(
    'config_key' => $key,
    'config_value' => $value
  ));
}

?>

    <h1>Board &raquo; Configuration</h1>
    <p>
      The configuration has been saved.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=configuration'; }, 
        <?php print $IF::$CONFIG['acp_save_delay']; ?>);
    </script>