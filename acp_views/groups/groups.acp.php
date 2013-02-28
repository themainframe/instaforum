    <h1>Users &amp; Permissions &raquo; Groups</h1>

    <p>This section of the Admin Panel allows you to manipulate
      groups and their associated permissions.

    </p>

    <?php

      $data = new IF_Dataview();

      $data->addColumns(array(
        'name' => array(
          'name' => 'Group Name',
          'cell_css' => array(
            'font-weight' => 'bold'
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
          '<a class="button" href="./?act=group_edit&id=' . 
            $row['user_id'] . '">Edit</a>' . 
            '<a class="button red" href="?act=group_delete&id=' . 
            $row['user_id'] . '">Delete</a>'
        ));
      }

      print $data->render();

    ?>