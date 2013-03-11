<?php
/**
 * style.acp.php
 * ACP View: Style Editor: Save
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

// For each of the defined styles...
foreach($_POST as $key => $value)
{
  // Delete it
  $IF->DB->delete('if_config',
    Predicate::_equal(new Value('config_key'), 'board_style_' . $key));

  $IF->DB->insert('if_config', array(
    'config_key' => 'board_style_' . $key,
    'config_value' => $value
  ));
}


?>
    <h1>Integration &raquo; Style Editor</h1>
    <p>
      Changes to the forum style have been changed.
    </p>

    <script type="text/javascript">
      setTimeout(function() { window.location = '?act=style'; }, 
        <?php print $IF::$CONFIG['acp_save_delay']; ?>);
    </script>