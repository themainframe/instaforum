<?php
/**
 * Defines the interface for Data Sources.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

/**
 * The interface for Data Sources.
 *
 * @package lightgroup
 */
interface IDataSource
{
  //
  // Structure/schema
  // 
  public static function createTable($tableName, array $columns);
  public static function deleteTable($tableName);

  //
  // Data
  //
  public static function insert($tableName, array $columns);
  public static function update($tableName, array $columns, $predicate);
  public static function delete($tableName, $predicate);
  public static function select($tableName, $predicate, $limitCount, $limitStart);
}