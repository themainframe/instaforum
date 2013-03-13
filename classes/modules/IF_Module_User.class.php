<?php
/**
 * IF_Module_User.class.php
 * Defines the User module class.
 *   
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
/**
 * The User module class.
 *
 * The User module provides access to permissions and user account
 * alterations.
 *
 * @package IF
 */
class IF_Module_User extends IF_Module
{
  /**
   * Perform the login.
   * 
   * @param string $username The username.
   * @param string $password The password.
   * @return array
   */
  public function login($username, $password)
  {
    // Perform the login and return the result.
    $results = $this->parent->DB->select('if_users',
      Predicate::_and(
        Predicate::_equal(new Value('user_name'), $username),
        Predicate::_equal(new Value('user_password'),
          md5($password . IF_PW_SALT))
      )
    );

    // Count them
    if($results->count == 0)
    {
      // Failed login
      return false;
    }

    // Otherwise, provide details
    $user = (array)$results->next();

    // Strip out the password to avoid revealling salt crypto
    unset($user['user_password']);

    // Store the session
    $_SESSION['user_id'] = $user['user_id']; 

    return $user;
  }

  /**
   * Destroy the session (logout).
   * 
   * @return boolean
   */
  public function logout()
  {
    $_SESSION['user_id'] = -1;

    return true;
  }

  /**
   * Register a user on the system.
   * 
   * @param string $userName The name of the user to create.
   * @param string $password The password to use.
   * @param string $email The email address.
   * @param string $fullName The full name to use.
   * @return array
   */
  public function register($userName, $password, $email, $fullName)
  {
    // Check if the username is taken already?
    $userCheck = $this->parent->DB->select('if_users',
      Predicate::_equal(new Value('user_name'), $userName));

    if($userCheck->count == 1)
    {
      return array(
        'status' => false,
        'message' => 'A user with that name already exists.'
      );  
    }

    // Get the default group
    $defaultGroup = 
      intval($this->parent->modules['Config']->get('users_default_group'));

    // Create the user account
    $this->parent->DB->insert('if_users', array(
      'user_id' => NULL,
      'user_name' => $userName,
      'user_password' => md5($password . IF_PW_SALT),
      'user_full_name' => $fullName,
      'user_email' => $email,
      'user_group_id' => $defaultGroup
    ));

    // Everything OK
    return array(
      'status' => true,
      'message' => ''
    );
  } 

  /**
   * Check if the current user can perform the specified action in the
   * specified forum.
   * 
   * @param string $permissionString The permission string to check.
   * @param integer $forumID The forum ID.
   * @return boolean
   */
  public function can($permissionString, $forumID)
  {
    // Am I logged in?
    if(!isset($_SESSION) || !isset($_SESSION['user_id'])  || 
      $_SESSION['user_id'] == -1)
    {
      // Use the default group
      $defaultGroup = 
        intval($this->parent->modules['Config']->get('users_public_group'));

      // Get permissions for this forum
      $results = $this->parent->DB->select('if_permissions',
        Predicate::_and(
          Predicate::_equal(new Value('permission_group_id'), $defaultGroup),
          Predicate::_equal(new Value('permission_forum_id'), $forumID)
        )
      );

      // Get the relevant field
      $fields = (array)$results->next();
      return $fields['permission_' . $permissionString];
    }

    // Get the group I am in.
    $userQuery = $this->parent->DB->select('if_users',
      Predicate::_equal(new Value('user_id'), $_SESSION['user_id']));

    if($userQuery->count == 0)
    {
      // I don't exist, therefore can't do anything
      return false;
    }

    // Get the user (me)
    $user = $userQuery->next();

    // Get permissions for my group and the specified forum
    $results = $this->parent->DB->select('if_permissions',
      Predicate::_and(
        Predicate::_equal(new Value('permission_group_id'), $user->user_group_id),
        Predicate::_equal(new Value('permission_forum_id'), $forumID)
      )
    );

    // Find the relevant permissions mask field
    $fields = (array)$results->next();

    return $fields['permission_' . $permissionString];
  }
}