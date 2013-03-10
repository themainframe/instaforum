<?php
/**
 * hooks.acp.php
 * ACP View: Hooks
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

    <h1>Integration &raquo; Hooks</h1>

    <p>
      Hooks are how you insert the parts of the forum into your website.<br /><br />

      To insert a hook, simply add <span class="code">class="IF-hookname"</span> to an element on your page.<br />
      <?php print IF_APP_NAME; ?> will automatically convert hooks when the page loads.
    </p>

    <table>
      <thead>
        <tr>
          <td style="width: 170px;">Hook name</td>
          <td>Hook behaviour</td> 
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><span class="code">IF-title</span></td>
          <td>
            This hook will be automatically converted to the title of the board at load time.
          </td>
        </tr>
        <tr>
          <td><span class="code">IF-description</span></td>
          <td>
            This hook will be automatically converted to the description of the board at load time.
          </td>
        </tr>
        <tr>
          <td><span class="code">IF-body</span></td>
          <td>
            This hook will be automatically converted to the body of the forum at load time.
          </td>
        </tr>
        <tr>
          <td><span class="code">IF-user</span></td>
          <td>
            This hook will be automatically converted to the user login/information area at load time.
          </td>
        </tr>
      </tbody>
    </table>