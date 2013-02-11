<?php
/**
 * login.acp.php
 * ACP View: Login Form
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

// Login action?
if(isset($_GET['mode']) && $_GET['mode'] == 'do')
{
  // Validate
  $result = $IF->DB->select('if_admins', 
    Predicate::_and(
      Predicate::_equal(new Value('admin_name'), $_POST['username']),
      Predicate::_equal(new Value('admin_password'), md5($_POST['password']))
    )
  );

  $message = '';
  
  if($result->count == 1)
  {
    $_SESSION['admin_name'] = $_POST['username'];

    // Go to dashboard
    header('Location: ?act=home');
  }
  else
  {
    $message = 'Invalid credentials.';
  }

}

?><!DOCTYPE html>
<html>
<head>
  <title><?php print IF_APP_NAME; ?> - <?php print isset($ACP_TITLE) ? $ACP_TITLE : 'Admin'; ?></title>
  <link rel="stylesheet" type="text/css" href="acp_static/style/default.css" />
  <link rel="stylesheet" type="text/css" href="js/external/jquery.min.js" />
  <link rel="stylesheet" type="text/css" href="acp_static/js/acp.js" />
</head>
<body>
  <form action="?act=login&amp;mode=do" method="post">
  <div class="login">
    <div class="banner">
      <span class="logo"><?php print IF_APP_NAME; ?></span>
    </div>

    <div class="field">
      <div class="info">
        <span class="title">Username</span>
        <p class="description">Enter your username.</p>
      </div>
      <div class="value">
        <input type="text" name="username" />
      </div>
    </div>

    <div class="field">
      <div class="info">
        <span class="title">Password</span>
        <p class="description">Enter your password.</p>
      </div>
      <div class="value">
        <input type="password" name="password" />
      </div>
    </div>

    <div class="field">
      <div class="info">
        <strong style="color: #f00"><?php print $message; ?></strong>
      </div>
      <div class="value">
        <input type="submit" value="Log In" />
      </div>
    </div>

  </div>
  </form>
</body>
</html>
