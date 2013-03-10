<?php
/**
 * installer_tools.php
 * Instaforum installer tools.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
/**
 * Perform a clean installation of instaforum in the data directory.
 * Uses the schema file specified by $schemaFile.
 * 
 * @param string $schemaFile The schema file to use.
 * @return string The new admin password.
 */
function do_install($schemaFile)
{
  global $IF;

  // Load the schema
  $schema = @file_get_contents($schemaFile);
  $db = json_decode($schema, true);

  if(!$schema || !is_array($db))
  {
    return false;
  }

  // Read the data
  foreach($db as $tableName => $table)
  {
    $IF->DB->createTable($tableName, $table['columns']);

    // Create rows
    foreach($table['rows'] as $row)
    {
      $IF->DB->insert($tableName, $row);
    }
  }

  // Create admin account
  $password = substr(md5(rand(0,9999999999)), -6);
  $IF->DB->insert('if_admins', array(
    'admin_id' => null,
    'admin_name' => 'admin',
    'admin_password' => md5($password . IF_PW_SALT)
  ));

  return $password;
}