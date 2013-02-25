<?php
/**
 * IF_Module_Board.class.php
 * Defines the Board module class
 *   
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
/**
 * The Board module class.
 *
 * The Board module provides access to the main board parameters and
 * operations.
 *
 * @package IF
 */
class IF_Module_Board extends IF_Module
{
  /**
   * Get the title of the board.
   * 
   * @return array
   */
  public function getTitle()
  {
    // Retrieve the title
    return array(
      'attribute' => 'title', 
      'value' => $this->parent->modules['Config']->get('board_title')
      );
  }

  /**
   * Get the forums that are available on this board.
   * 
   * @return array
   */
  public function getForums()
  {
    $result = $this->parent->DB->select('if_forums');
    $rows = array();

    // Count topics and posts
    foreach($result->rows as $row)
    {
      // Count topics
      $topics = $this->parent->DB->select('if_topics',
        Predicate::_equal(new Value('topic_forum_id'), $row['forum_id']));

      $rows[] = array(
        'forum_id' => $row['forum_id'],
        'forum_title' => $row['forum_title'],
        'forum_topics' => $topics->count,
        'forum_posts' => 0
      );
    }
    return $rows;
  }

  /** 
   * Get the topics for a speciifed forum.
   *
   * @param integer $ID The ID of the forum to get the topics for.
   * @return array
   */
  public function getTopics($ID)
  {
    $topics = $this->parent->DB->select('if_topics',
      Predicate::_equal(new Value('topic_forum_id'), $ID));
    $rows = array();

    foreach($topics->rows as $row)
    {
      // Count posts
      $posts = $this->parent->DB->select('if_posts',
        Predicate::_equal(new Value('post_topic_id'), $row['topic_id']));

      $rows[] = array(
        'topic_id' => $row['topic_id'],
        'topic_title' => $row['topic_name'],
        'topic_posts' => $posts->count
      );
    }

    return $rows;
  }

  /** 
   * Get the posts for a specified topic.
   *
   * @param integer $ID The ID of the topic to get the posts for.
   * @return array
   */
  public function getPosts($ID)
  {
    $posts = $this->parent->DB->select('if_posts',
      Predicate::_equal(new Value('post_topic_id'), $ID));

    $rows = array();

    foreach($posts->rows as $row)
    {
      // Count topics and posts
      $rows[] = array(
        'post_id' => $row['post_id'],
        'post_text' => $row['post_text']
      );
    }

    // Get the topic information too
    $topic = $this->parent->DB->select('if_topics',
      Predicate::_equal(new Value('topic_id'), $ID));
    
    return array(
      'posts' => $rows,
      'topic_id' => $ID,
      'topic_name' => $topic->rows[0]['topic_name']
    );
  }
}