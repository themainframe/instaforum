<?php
/**
 * Defines the file handling classes
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
 
/**
 * File Not Found exception
 *
 * @package lightgroup
 */
final class FileNotFoundException extends Exception
{
}

/**
 * Permission Denied exception
 *
 * @package lightgroup
 */
final class PermissionDeniedException extends Exception
{
}

/**
 * IO exception
 *
 * @package lightgroup
 */
final class IOException extends Exception
{
}
 
/**
 * File Class
 * Provides general filesystem functions.
 *
 * @package lightgroup
 */
class File
{
  /**
   * Check if a file:
   *   -  Exists, and
   *   -  is readable by the current user.
   *  
   * @param string $file The file to check.
   * @return boolean
   * @throws FileNotFoundException, PermissionDeniedException
   */
  public static function isReadable($file)
  {
    if(!file_exists($file))
    {
      throw new FileNotFoundException($file);
    }
    
    if(!is_readable($file))
    {
      throw new PermissionDeniedException($file);
    }
    
    return true;
  }
  
  /**
   * Check if a file:
   *   -  Exists, and
   *   -  is writable by the current user.
   *  
   * @param string $file The file to check.
   * @return boolean
   * @throws FileNotFoundException, PermissionDeniedException
   */
  public static function isWritable($file)
  {
    if(!file_exists($file))
    {
      throw new FileNotFoundException($file);
    }
    
    if(!is_writable($file))
    {
      throw new PermissionDeniedException($file);
    }
    
    return true;
  }
}