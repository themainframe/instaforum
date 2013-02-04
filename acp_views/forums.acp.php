    <h1>Board &raquo; Forums</h1>

    <p>This section of the Admin Panel allows you to control the
    discussion forums that are available on the board.</p>

    <?php

      $data = new IF_Dataview();

      $data->addColumns(array(
        'id' => array(
          'name' => 'ID',
          'checkbox' => true,
          'css' => array(
            'width' => '40px'
          )
        ),
        'name' => array(
          'name' => 'Name'
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
        $data->addRow(array(
          $row['forum_id'],
          $row['forum_title'],
          0,
          0,
          '<a class="button" href="#">Edit</a>' . 
            '<a class="button red" href="#">Delete</a>'
        ));
      }

      print $data->render();

    ?>