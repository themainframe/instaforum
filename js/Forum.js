/** 
 * Instaforum
 * Core JS frontend.
 * 
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// --------------------------------------------------
// Patch prototypes
// --------------------------------------------------

String.prototype.uc_first = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

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

    // Setup static values
    this.modules.board.convert_static('title');

    // Build the main body
    this.modules.board.build();
  },

  /** 
   * Modules
   */
  'modules': {

    /**
     * Board
     * Represents the forum state
     */
    'board' : {

      /** 
       * Convert title.
       */
      'convert_static' : function(name) {
        if(IF.cache.get('board.' + name) == undefined)
        {
          IF.remote.exec('Board', 'get' + name.uc_first(), {},
            'IF.modules.board.got_static', 1);
        }
        else
        {
          this.got_static(IF.cache.get('board.' + name));
        }
      },

      /** 
       * Retrieved title.
       */
      'got_static' : function(value) {
        IF.cache.set('board.title', value);
        $('.IF-forum-title').text(value);
      }

    }

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
      tag = document.cookie.substring(0, 3);
      existing = document.cookie.substring(3);

      // Split-off at the first non-quoted semicolon
      escape = false;
      quote = false;
      for(c = 0; c < existing.length; c ++)
      {
        if(existing[c] == '\\' && !escape)
        {
          escape = true;
          continue;
        }

        if(!escape && existing[c] == '"')
        {
          quote = !quote;
        }

        if(!quote && existing[c] == ';')
        {
          break;
        }
      }

      existing = existing.substring(0, c);

      // Empty?
      if(tag != 'IF=')
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
      tag = document.cookie.substring(0, 3);
      existing = document.cookie.substring(3);

      // Split-off at the first non-quoted semicolon
      escape = false;
      quote = false;
      for(c = 0; c < existing.length; c ++)
      {
        if(existing[c] == '\\' && !escape)
        {
          escape = true;
          continue;
        }

        if(!escape && existing[c] == '"')
        {
          quote = !quote;
        }

        if(!quote && existing[c] == ';')
        {
          break;
        }
      }

      existing = existing.substring(0, c);

      // Empty?
      if(tag != 'IF=')
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
    'responder_path' : './if/ajax/responder.php',

    /**
     * The request ID.
     */
    'request_id' : 1,

    /**
     * Enabled?
     */
    'enabled' : false,

    /**
     * Requests currently in the queue.
     */
    'queue' : [],

    /**
     * Temporary callbacks
     */
    'temp_callbacks' : {},

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
          'data': 'IF=' + JSON.stringify(IF.remote.queue),
          'dataType': 'json',
          'type': 'post',
          'success': function(data) {

            // For each response, invoke the callback
            $.each(data, function(request_nonce, response) {
              
              // Is a callback specified?
              if(response.callback)
              {
                // Execute callback function, which may be namespaced
                var arguments = Array.prototype.slice.call(
                  response.params ? response.params : Array());

                // Base context is the window object
                var context = window;

                // Follow the namespace path
                var namespaces = response.callback.split(".");
                var funcPart = namespaces.pop();

                for(var i = 0; i < namespaces.length; i++)
                {
                  // Update context
                  context = context[namespaces[i]];
                }

                // Call function
                if(context[funcPart])
                {
                  context[funcPart].apply(context, arguments);
                }

                // Clean up temporary callbacks
                delete context[funcPart];
              }

            });

          }
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
     * callback: The name of a callback function to run when the request finishes.
     * priority: The priority of the request.  Higher = sooner execution.
     * cache:    Is the request cachable?
     * now:      Dispatch the whole stack instantly after adding this request.
     */
    'exec' : function(module, method, params, callback, priority, now)
    {
      // Build temporary callback function
      if(typeof callback == 'function')
      {
        cb_name = 'IF_cb_' + this.request_id;
        this.temp_callbacks[cb_name] = callback;
        callback = 'IF.remote.temp_callbacks.' + cb_name;
      }

      // Add to the queue
      this.queue.push({

        'nonce' : this.request_id,
        'module' : module,
        'method' : method,
        'params' : params,
        'callback': callback,
        'priority' : priority

      });

      // Increment request ID
      this.request_id ++;

      // Clear the queue now?
      if(now)
      {
        this.dispatch();
      }

      return true;
    }

  }

};