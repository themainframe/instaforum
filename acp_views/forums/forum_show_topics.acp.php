<?php
/**
 * forum_show_topics.acp.php
 * ACP View: Forums: Show Topics
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


// Save all changes
$forum = $IF->DB->select('if_forums',
  Predicate::_equal(new Value('forum_id'), $_GET['id']));

if($forum->count != 1)
{
  // Not found
  header('Location: ./?act=forums');
}

// Reassign to the actual forum object
$forum = $forum->next();

?>

    <h1>Board &raquo; Topics in <?php print $forum->forum_title; ?></h1>

    <p>You can manipulate the topics in this forum here.<br />
      <a href="?act=forums" title="Back">Click here</a> to go back to the forum list.

    </p>

    <?php

      $data = new IF_Dataview();

      $data->addColumns(array(
        'name' => array(
          'name' => 'Topic Name',
          'cell_css' => array(
            'font-weight' => 'bold'
          )
        ),
        'creator' => array(
          'name' => 'Creator',
          'css' => array(
            'width' => '150px'
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
      $result = $IF->DB->select('if_topics');
      foreach($result->rows as $row)
      {
        // Count posts
        $posts = $IF->DB->select('if_posts',
          Predicate::_equal(new Value('post_topic_id'), $row['topic_id']));

        // Get the creator
        $creator =$IF->DB->select('if_users',
          Predicate::_equal(new Value('user_id'), $row['topic_owner_id'])); 

        // Creator exists?
        if($creator->count == 0)
        {
          $creatorName = 'Guest User';
        }
        else
        {          
          $creatorRow = $creator->next();
          $creatorName = '<a href="?act=user_edit&id=' . $creatorRow->user_id . '">' . 
            $creatorRow->user_name . '</a>';
        }

        $data->addRow(array(
          $row['topic_name'],
          $creatorName,
          $posts->count,
            '<a class="button red" href="?act=forum_topic_delete&forum_id=' . 
            $_GET['id'] . '&id=' . 
            $row['topic_id'] . '">Delete</a>'
        ));
      }

      print $data->render();

    ?>