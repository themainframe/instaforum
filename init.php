<?php
/**
 * init.php
 * Initialises some key constants for the application.
 *   
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// ------------------------------------------------------
//  Application product name & version
// ------------------------------------------------------
define('IF_APP_NAME', 'Instaforum');
define('IF_APP_VERSION', 0.1);

// ------------------------------------------------------
//  Application root path.
// ------------------------------------------------------
// Try to derive the root path from __FILE__
define('IF_ROOT_PATH', dirname(__FILE__));

// ------------------------------------------------------
//  Application's AJAX responder path.
// ------------------------------------------------------
define('IF_AJAX_PATH', IF_ROOT_PATH . '/ajax' );