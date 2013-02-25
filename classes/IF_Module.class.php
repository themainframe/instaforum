<?php
/**
 * IF_Module.class.php
 * Defines the base module class.
 *   
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
/**
 * The base module class.
 * Abstract.
 *
 * @package IF
 */
abstract class IF_Module
{
  /** 
   * A reference to the kernel class that created this object.
   * 
   * @var *IF_Kernel
   */
  public $parent = null;
}