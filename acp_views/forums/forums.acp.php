<?php
/**
 * forums.acp.php
 * ACP View: Forums
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

    <h1>Board &raquo; Forums</h1>

    <p>This section of the Admin Panel allows you to control the
    discussion forums that are available on the board.

      <br />

      <form action="?act=forum_new" method="post">
        To create a new forum, type a name <input name="name" type="text" /> and click 
        <input type="submit" value="Create" />.
      </form>

    </p>

    <?php

      $data = new IF_Dataview();

      $data->addColumns(array(
        'name' => array(
          'name' => 'Name',
          'cell_css' => array(
            'font-weight' => 'bold'
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
      $result = $IF->DB->select('if_forums');
      foreach($result->rows as $row)
      {
        // Count topics and posts
        $topics = $IF->DB->select('if_topics',
          Predicate::_equal(new Value('topic_forum_id'), $row['forum_id']));

        $data->addRow(array(
          $row['forum_title'],
          '<a href="./?act=forum_show_topics&id=' . $row['forum_id']  . 
            '">' . $topics->count . '</a>',
          0,
          '<a class="button" href="./?act=forum_edit&id=' . 
            $row['forum_id'] . '">Edit</a>' . 
            '<a class="button red" href="?act=forum_delete&id=' . 
            $row['forum_id'] . '">Delete</a>'
        ));
      }

      print $data->render();

    ?>