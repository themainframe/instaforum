/** 
 * Instaforum
 * Core JS frontend.
 * 
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// --------------------------------------------------
// Init the forum on load
// --------------------------------------------------

// Run when ready
$(function() { IF.init(); });

// --------------------------------------------------
// The IF object: provides interaction with IF.
// --------------------------------------------------
var IF = {

  /**
   * Initialise the forum
   */
  'init' : function() 
  {
    // Start remote timer
    this.remote.start();
  },

  /**
   * Cache object
   * Implements caching of vars in cookies.
   */
  'cache' : {

    /** 
     * Setter
     * Adds or updates a value in the cookie-cache.
     */
    'set' : function(name, value)
    {
      // Retrieve existing cookie, skipping the name
      existing = document.cookie.substring(3);

      // Empty?
      if(existing == '')
      {
        existing_obj = {};
      }
      else
      {
        existing_obj = JSON.parse(existing);
      }

      // Set value
      existing_obj[name] = value;

      // Retrieve the cookie value
      document.cookie = "IF=" + JSON.stringify(existing_obj);

      return value;
    },

    /**
     * Getter
     * Gets a value from the cookie-cache.
     * Returns undefined if it doesn't exist.
     */
    'get' : function(name)
    {
      // Retrieve existing cookie, skipping the name
      existing = document.cookie.substring(3);

      // Empty?
      if(existing == '')
      {
        return undefined;
      }

      // Try to find the value
      existing_obj = JSON.parse(existing);

      if(existing_obj[name])
      {
        return existing_obj[name];
      }

      return undefined;
    }

  },

  /**
   * Remote object
   * Handles interaction with IF on the webserver.
   */
  'remote' : {

    /**
     * The delay between requests in ms.
     */
    'wait' : 1000,

    /**
     * The path to the responder program.
     */
    'responder_path' : 'if/ajax/responder.php',

    /**
     * Enabled?
     */
    'enabled' : false,

    /**
     * Requests currently in the queue.
     */
    'queue' : [],

    /**
     * Start periodic communication.
     */
    'start' : function()
    {
      // Dispatch now
      IF.remote.enabled = true;
      this.dispatch();
    },

    /**
     * Dispatch the queue now.
     */
    'dispatch' : function()
    {
      // Empty queue?
      if(IF.remote.queue.length > 0)
      {
        // Dispatch requests
        $.ajax(IF.remote.responder_path, {
          'data': IF.remote.queue,
          'dataType': 'json',
          'contentType': 'application/json',
          'type': 'post',
          'complete': function(data) {  }
        });

        // Clear queue
        IF.remote.queue = [];
      }

      // Rescheduling enabled?
      if(IF.remote.enabled)
      {
        setTimeout(IF.remote.dispatch, IF.remote.wait);
      }
    },

    /**
     * Enqueue a request.
     * 
     * module:   The module that should handle the request on the server side.
     * method:   The method that should be invoked.
     * params:   Any parameters for the method.
     * priority: The priority of the request.  Higher = sooner execution.
     * now:      Dispatch the whole stack instantly after adding this request.
     */
    'add' : function(module, method, params, priority, now)
    {
      // Add to the queue
      this.queue.push({

        'module' : module,
        'method' : method,
        'params' : params,
        'priority' : priority

      });

      // Clear the queue now?
      if(now)
      {
        this.dispatch();
      }

      return true;
    }

  }

};