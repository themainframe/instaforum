<?php
/**
 * users.acp.php
 * ACP View: Users
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

    <h1>Users &amp; Permissions &raquo; Users</h1>

    <p>This section of the Admin Panel allows you to manipulate
      the users that interact with the board.

      <br /><br />

      Users can join and log in to the forum using the interface on your site
      rendered by the <strong>IF-user</strong> hook.

    </p>

    <?php

      $data = new IF_Dataview();

      $data->addColumns(array(
        'name' => array(
          'name' => 'Username',
          'cell_css' => array(
            'font-weight' => 'bold'
          )
        ),
        'realname' => array(
          'name' => '"Real" Name',
          'cell_css' => array(
            'font-weight' => 'bold'
          )
        ),
        'email' => array(
          'name' => 'Email',
          'cell_css' => array(
          )
        ),
        'topics_count' => array(
          'name' => 'Topics',
          'css' => array(
            'width' => '70px'
          )
        ),
        'posts_count' => array(
          'name' => 'Posts',
          'css' => array(
            'width' => '70px'
          )
        ),
        'options' => array(
          'name' => 'Options',
          'sortable' => false,
          'css' => array(
            'width' => '100px'
          )
        ),
      ));

      // Get the rows
      $result = $IF->DB->select('if_users');
      foreach($result->rows as $row)
      {
        // Count topics and posts
        $topics = $IF->DB->select('if_topics',
          Predicate::_equal(new Value('topic_owner_id'), $row['user_id']));

        // Count posts
        $posts = $IF->DB->select('if_posts',
          Predicate::_equal(new Value('post_owner_id'), $row['user_id']));

        $data->addRow(array(
          $row['user_name'],
          $row['user_full_name'],
          $row['user_email'],
          $topics->count,
          $posts->count,
          '<a class="button" href="./?act=user_edit&id=' . 
            $row['user_id'] . '">Edit</a>' . 
            '<a class="button red" href="?act=user_delete&id=' . 
            $row['user_id'] . '">Delete</a>'
        ));
      }

      print $data->render();

    ?>