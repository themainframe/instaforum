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
  public static function createTable(string $tableName, array $columns);
  public static function deleteTable(string $tableName);

  //
  // Data
  //
  public static function insert(string $tableName, array $columns);
  public static function update(string $tableName, array $columns, $predicate);
  public static function delete(string $tableName, $predicate);
  public static function select(string $tableName, $predicate, $limitCount, $limitStart);
}