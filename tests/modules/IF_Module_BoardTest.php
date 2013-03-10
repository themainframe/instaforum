<?php
/**
 * Defines the Board Module test class.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

/**
 * Board Module test class
 * Evaluates the IF_Module_Board class.
 */
class IF_Module_BoardTest extends PHPUnit_Framework_TestCase
{
  /**
   * Get the forums
   */
  public function testGetForums()
  {
    global $IF;

    // Get the forums
    $forums = $IF->modules['Board']->getForums();

    // Check the first one is valid
    $forumRow = array(
      'forum_id' => 1,
      'forum_title' => 'Your first forum',
      'forum_topics' => 1,
      'forum_posts' => 1
    );

    // Check it is correct
    $this->assertEquals($forumRow, $forums[0]);
  }

  /**
   * Get the topics
   * @depends testGetForums
   */
  public function testGetTopics()
  {
    global $IF;

    // Get the topics
    $topics = $IF->modules['Board']->getTopics(1);

    // Check the first one is valid
    $topicRow = array(
      'topic_id' => 1,
      'topic_title' => 'A sample topic',
      'topic_posts' => 1,
      'forum_id' => 1
    );

    // Check it is correct
    $this->assertEquals($topicRow, $topics['topics'][0]);
  }

  /**
   * Get the posts
   * @depends testGetForums
   */
  public function testGetposts()
  {
    global $IF;

    // Get the posts
    $posts = $IF->modules['Board']->getPosts(1);

    // Check the first one is valid
    $postRow = array(
      'post_id' => 1,
      'post_text' => 'This is a sample post.'
    );

    // Check it is correct
    $this->assertEquals($postRow, $posts['posts'][0]);
  }

}