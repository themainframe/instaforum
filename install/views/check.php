<?php
/**
 * check.php
 * Instaforum install pre-checks.
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

// ------------------------------------------------------
// Define some tests
// ------------------------------------------------------
$tests = array(

  array(
    'title' => 'Data directory exists',
    'test' => function() {
      return file_exists('../db/') && is_dir('../db/');
    },
    'mesg_bad' => 'You need to create a directory in ' . realpath('../') . '/db.',
    'mesg_ok' => 'Passed.'
  ),

  array(
    'title' => 'Writable data directory',
    'test' => function() {
      return is_writable('../db/');
    },
    'mesg_bad' => 'You need to change the mode of ' . realpath('../') . '/db to 775',
    'mesg_ok' => 'Passed.'
  )

);

/**
 * @todo Add more tests
 */

?>

<h1><?php print IF_APP_NAME; ?> &raquo; Environment check</h1>
<p>
  <?php print IF_APP_NAME; ?> has checked the server for suitability. 

  <br /><br />

  If everything checks out, click continue; otherwise, address the issues
  below and click <a href="?act=check">here</a> to re-check.
</p>

<br />

<?php

$allOK = true;

foreach($tests as $test)
{
  $result = $test['test']();
  if(!$result)
  {
    $allOK = false;
  }
?>

<div class="result <?php print $result ? 'ok' : 'bad'; ?>">
  <p class="title"><?php print $test['title']; ?></p>
  <p class="details"><?php print $result ? $test['mesg_ok'] : $test['mesg_bad']; ?></p>
</div>

<?php
}

if($allOK)
{
?>

<p>
  <br class="clear" />
  <a href="?act=do_install" title="Next" class="button right">Continue...</a>
</p>

<?php
}
?>