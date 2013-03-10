<?php
/**
 * do_install.php
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

// Create the kernel object
$IF = new IF_Kernel();

if(!$IF->init())
{
  print '<strong>Problems encountered during kernel init.</strong>';
}

// Do the install
if(($password = do_install('./schema.json')) == false)
{
  ?>
<h1><?php print IF_APP_NAME; ?> &raquo; Installation</h1>
<p>
  Could not read the database schema file.
</p>
  <?php
  exit();
}

?>

<h1><?php print IF_APP_NAME; ?> &raquo; Done</h1>
<p>
  <?php print IF_APP_NAME; ?> is now installed.
  Here are your administrator account details:

  <br /><br />

  <strong>Username:</strong> admin&nbsp; <br />
  <strong>Password:</strong> <?php print $password; ?>&nbsp;

  <br /><br />

  Please write these details down and keep them safe until you can
  change the adminstrator password.

  <br />
</p>


<p>
  <br class="clear" />
  <a href="../" title="Next" class="button right">Go to the Admin Panel...</a>
</p>