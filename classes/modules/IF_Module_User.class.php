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
class IF_Module_user extends IF_Module
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
   * Check if the current user can perform the specified action in the
   * specified forum.
   * 
   * @param string $permissionString The permission string to check.
   * @param integer $forumID The forum ID.
   * @return boolean
   */
  public function can($permissionString, $forumID)
  {
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