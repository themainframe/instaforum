<?php
/**
 * home.acp.php
 * ACP View: Home
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
    <h1>Welcome to <?php print IF_APP_NAME; ?></h1>
    <p>

      <strong><?php print IF_APP_NAME; ?></strong> is an instant-deployment fully-integrated web forum solution written in PHP &amp; JS.<br />
      <br />

      It comprises a storage engine, forum engine &amp; administration zone in an easy-to-install package.
      <br />
      <?php print IF_APP_NAME; ?> is a dissertation project by Damien Walsh, a third year undergraduate studying 
      Internet Computing at the University of Manchester School of Computer Science.

      <br /><br />
      <a href="http://github.com/themainframe/instaforum">Click here</a> to visit the GitHub page for the project.

    </p>