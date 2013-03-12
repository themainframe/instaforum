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

    // Make it so I have permission to see a board
    $IF->modules['User']->login('user', 'user');

    // Give permission
    $IF->DB->insert('if_permissions', array(
      'permission_id' => null,
      'permission_read' => 1,
      'permission_forum_id' => 1,
      'permission_group_id' => 1
    )); 

    // Get the forums
    $forums = $IF->modules['Board']->getForums();

    // Check it is correct
    $this->assertEquals('Your first forum', $forums[0]['forum_title']);
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

    // Check it is correct
    $this->assertEquals('A sample topic', $topics['topics'][0]['topic_title']);
  }

  /**
   * Get the posts
   * @depends testGetForums
   */
  public function testGetPosts()
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