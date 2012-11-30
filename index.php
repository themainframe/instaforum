<?php
/**
 * index.php
 * ACP index file.
 *
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// --------------------------------------------------
// Include required files
// --------------------------------------------------
require_once 'init.php';
require_once IF_ROOT_PATH . '/classes/IF_Kernel.class.php';

// --------------------------------------------------
// Initialise
// --------------------------------------------------
if(!IF_Kernel::init())
{
  print '<strong>Problems encountered during kernel init.</strong>';
}