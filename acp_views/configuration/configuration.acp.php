    <h1>Board &raquo; Configuration</h1>

    <h2>Title, Keywords &amp; Description</h2>


<?php

  // Get all config
  $result = $IF->DB->select('if_config');
  $config = array();

  while($row = $result->next())
  {
    $config[$row->config_key] = $row->config_value;
  }

?>

  <form action="?act=configuration_save" method="post">

    <div class="field">
      <div class="info">
        <span class="title">Board title</span>
        <p class="description">
          The global title of the board.
        </p>
      </div>
      <div class="value">
        <input type="text" name="board.title" value="<?php print $config['board_title']; ?>" />
      </div>
    </div>

    <div class="field">
      <div class="info">
        <span class="title">Board description</span>
        <p class="description">
          A brief description of the board.
        </p>
      </div>
      <div class="value">
        <textarea name="board.description"><?php print $config['board_description']; ?></textarea>
      </div>
    </div>

    <div class="field">
      <div class="info">
      </div>
      <div class="value">
        <input type="submit" value="Save Changes" />
      </div>
    </div>

  </form>