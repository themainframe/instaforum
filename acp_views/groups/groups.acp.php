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
            'width' => '190px'
          )
        ),
      ));

      // Get the rows
      $result = $IF->DB->select('if_groups');
      foreach($result->rows as $row)
      {
        $data->addRow(array(
          $row['group_name'],
          '<a class="button" href="./?act=group_edit&id=' . 
            $row['group_id'] . '">Edit Permissions</a>' . 
            '<a class="button red" href="?act=group_delete&id=' . 
            $row['group_id'] . '">Delete</a>'
        ));
      }

      print $data->render();

    ?>