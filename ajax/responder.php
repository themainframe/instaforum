<?php
/**
 * responder.php
 * Main AJAX responder
 *   
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */
 
// Header
header('Content-type: text/plain');

// --------------------------------------------------
// Initialise
// --------------------------------------------------

// Load application constants
require_once '../init.php';

// --------------------------------------------------
// Include classes
// --------------------------------------------------

require_once IF_ROOT_PATH . '/classes/IF_Kernel.class.php';

// --------------------------------------------------
// Initialise application kernel & DB
// --------------------------------------------------

$IF = new IF_Kernel();
$IF->init();

// --------------------------------------------------
// Obtain JSON
// --------------------------------------------------

$JSON = json_decode($_POST['IF']);

if(!$JSON)
{
  exit();
}

/**
 * @todo Sort by priority here
 */

$requests = array();
foreach($JSON as $request)
{
  $requests[] = array(
    'module' => $request->module,
    'method' => $request->method,
    'params' => $request->params,
    'callback' => $request->callback,
    'nonce' => intval($request->nonce)
  );
}

// --------------------------------------------------
// Select a module for each request & execute method
// --------------------------------------------------

// Build response collection
$responses = array();

// For each request in this queue...
foreach($requests as $request)
{
  // Safeify the name and build a filename
  $module = ucfirst(str_replace('.', '', $request['module']));

  // Check method exists
  if(!method_exists($IF->modules[$module], $request['method']))
  {
    // Add error response
    $responses[$request['nonce']] = array(
      //'request' => $request,
      'callback' => 'IF.remote.invalid_call',
      'params' => 
        array('The requested module does not have a matching method.')
    );

    continue;
  }

  // Try to call the method
  $result = call_user_func_array(
    array($IF->modules[$module], $request['method']),
    (array)$request['params']);

  // Build the response
  $responses[$request['nonce']] = array(
    //'request' => $request,
    'callback' => $request['callback'],
    'params' => is_array($result) ? $result : array($result)
  );
}

// --------------------------------------------------
// Produce the responses as a JSON document
// --------------------------------------------------

print json_encode($responses);