<?php
/**
 * already_done.php
 * Instaforum install done.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// ------------------------------------------------------
// Security contex check
// ------------------------------------------------------
if(!IF_INSTALLER)
{
  exit();
}
?>

<h1><?php print IF_APP_NAME; ?> &raquo; Welcome</h1>
<p>
  Welcome to <strong><?php print IF_APP_NAME; ?></strong>. 

  It's an instant-deployment web forum solution written in PHP that plugs
  right in to an existing website.
</p>

<p>
  Setting up takes just a few seconds - the installer will guide you through
  the process of setting up the forum and integrating it into your website.
</p>

<p>
  <br />
  <a href="?act=check" title="Next" class="button right">Get started...</a>
</p>