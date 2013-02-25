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
    // Setup static values
    this.modules.board.convert_static('title');

    // Build the forum
    this.modules.board.build();

    // Start remote timer
    this.remote.start();
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
       * Build the main forum body.
       */
      'build' : function() {

        // Retrieve forums
        IF.remote.exec('Board', 'getForums', {},
          'IF.modules.board.got_forums');
      },

      /**
       * The forums have been retrieved
       */
      'got_forums' : function(forums) {

        // Clear the body
        $('.IF-body').html('');

        boardArea = $('<div />').addClass('IF-board');

        $.each(forums, function(i, o) {

          // Generate the link
          link = $('<a />').html('&#9654; ' + o.forum_title)
                           .attr('href', '#f-' + o.forum_id)
                           .attr('class', 'IF-forum-link')
                           .attr('id', o.forum_id)
                           .click(IF.modules.board.display_forum);

          posts = $('<em />').html(' - ' + o.forum_topics + ' topics')
                             .addClass('grey');

          $('<div />').append(link).append(posts).append('<br />')
                      .appendTo(boardArea);
        });

        $(boardArea).appendTo('.IF-body');
      },

      /**
       * Display the specified forum.
       */
      'display_forum' : function() {

        // Retrieve the forum contents
        IF.remote.exec('Board', 'getTopics', {'id' : $(this).attr('id')},
          'IF.modules.board.got_topics', 1);

      },

      /**
       * The topics have been retrieved.
       */
      'got_topics' : function(topics) {

        console.log(topics);

        // Hide the forum listing
        $('.IF-body').html('');

        // Show all topics
        forumArea = $('<div />').addClass('IF-forum');

        $.each(topics, function(i, o) {

          // Generate the link
          link = $('<a />').html('&#9654; ' + o.topic_title)
                           .attr('href', '#')
                           .attr('class', 'IF-topic-link')
                           .attr('id', o.topic_id)
                           .click(IF.modules.board.display_topic);

          $('<div />').html(link)
                     .appendTo(forumArea);
        });

        $(forumArea).appendTo('.IF-body');

        // Append the back link
        backLink = $('<a />').html('&#9664; Back')
                             .attr('href', '#')
                             .click(IF.modules.board.build);

        $(forumArea).append($('<br />'));
        //            .append(backLink);
      },

      /**
       * Display the specified topic.
       */
      'display_topic' : function() {

        // Retrieve the forum contents
        IF.remote.exec('Board', 'getPosts', {'id' : $(this).attr('id')},
          'IF.modules.board.got_posts', 1);
      },

      /** 
       * Convert static elements by requesting them from the server/cache.
       */
      'convert_static' : function(name) {
        if(IF.cache.get('board.' + name) == undefined)
        {
          IF.remote.exec('Board', 'get' + name.uc_first(), {},
            'IF.modules.board.got_static', 1);
        }
        else
        {
          this.got_static(
            {'attribute' : name, 'value' : IF.cache.get('board.' + name)});
        }
      },


      /**
       * The posts have been retrieved.
       */
      'got_posts' : function(posts) {

        console.log(posts);

        // Hide the forum listing
        $('.IF-body').html('<h3>' + posts.topic_name + '</h3>');

        // Show all posts for the topic
        topicArea = $('<div />').addClass('IF-topic');

        $.each(posts.posts, function(i, o) {

          // Generate the link
          post = $('<div />').html(o.post_text)
                             .attr('class', 'IF-post')
                             .attr('id', o.post_id);

          $('<div />').html(post)
                     .appendTo(topicArea);
        });


        $('<textarea />').addClass('IF-input-post')
                         .appendTo(topicArea);

        $(topicArea).appendTo('.IF-body');
      },

      /** 
       * Retrieved a static element.
       * result is a hash that contains attribute and value.
       */
      'got_static' : function(result) {
        IF.cache.set('board.' + result.attribute, result.value);
        $('.IF-' + result.attribute).text(result.value);
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
    'responder_path' : './instaforum/ajax/responder.php',

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

            console.log(data);

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
                  context[funcPart].call(context, response.params);
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
     * adparam:  An aditional optional parameter to pass to the callback method.
     */
    'exec' : function(module, method, params, callback, priority, now, adparam)
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
        'priority' : priority,
        'adparam' : adparam

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