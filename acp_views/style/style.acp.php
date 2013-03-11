<?php
/**
 * style.acp.php
 * ACP View: Style Editor
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

// Generate the style contexts
$styles = array(
  'IF-body' => array(
    'name' => 'Forum Body',
    'code' => $IF->modules['Config']->get('board_style_IF-body')
  ),
  'IF-board' => array(
    'name' => 'Board Index',
    'code' => $IF->modules['Config']->get('board_style_IF-board')
  ),
  'IF-topic' => array(
    'name' => 'Post List (Topic)',
    'code' => $IF->modules['Config']->get('board_style_IF-topic')
  )
);

?>
    <h1>Integration &raquo; Style Editor</h1>
    <p>
      The Style Editor allows you to tweak the appearance of the forum.<br /><br />
      Choose an aspect below and alter the 
      <a href="http://en.wikipedia.org/wiki/Cascading_Style_Sheets" title="CSS" target="_blank">
        CSS</a> as required.
    </p>

    <table>
      <tr>
        <td style="width: 200px; vertical-align: top; padding: 0px;">
          <table>
            <thead>
              <tr>
                <td>Aspect</td>
              </tr>
            </thead>
            <tbody>
<?php
  foreach($styles as $styleKey => $style)
  {
?>
              <tr><td><a href="#" code="<?php print $style['code']; ?>" class="style" 
                id="<?php print $styleKey; ?>">
                <?php print $style['name']; ?>
              </a></td></tr>
<?php
  }
?>
            </tbody>
          </table>
        </td>
        <td style="vertical-align: top;">
          <table>
            <thead>
              <tr>
                <td>Edit code</td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><textarea id="code" class="code" 
                  style="width: 100%; height: 300px; position: relative; left: -14px; margin-top: 10px"></textarea></td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </table>

<form action="?act=style_save" method="post" id="styleform">

    <br class="clear" />
    <input class="button" id="stylesubmit" value="Save changes" style="float: right" type="button" />

</form>