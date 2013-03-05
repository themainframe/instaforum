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

    // Set up hint behaviour
    $('.IF-hint').live('focus', function() {
      if($(this).val() == $(this).attr('hint'))
      {  
        $(this).val('')
               .css('color', '#000');
      }
    }).live('blur', function() {
      if($(this).val() == '')
      {
        $(this).val($(this).attr('hint'))
               .css('color', '#afafaf');
      }
    });

    // Build the forum
    this.modules.board.init();
    this.modules.user.init();

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
       * The current forum context
       */
      'forum_id' : -1,

      /**
       * The current topic context
       */
      'topic_id' : -1,

      /**
       * First-time init
       */
      'init' : function() {

        // Retrieve forums
        this.build();
      },

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

        // Set forum context
        IF.modules.board.forum_id = $(this).attr('id');

        // Retrieve the forum contents
        IF.remote.exec('Board', 'getTopics', {'id' : $(this).attr('id')},
          'IF.modules.board.got_topics', 1);

      },

      /**
       * The topics have been retrieved.
       */
      'got_topics' : function(topics) {

        // Hide the forum listing
        $('.IF-body').html('');

        // Set forum context
        IF.modules.board.forum_id = topics.forum_id;

        // Show all topics
        forumArea = $('<div />').addClass('IF-forum')
                                .attr('id', topics.forum_id);

        $.each(topics.topics, function(i, o) {

          // Generate the link
          link = $('<a />').html('&#9654; ' + o.topic_title)
                           .attr('href', '#')
                           .attr('class', 'IF-topic-link')
                           .attr('id', o.topic_id)
                           .click(IF.modules.board.display_topic);

          $('<div />').html(link)
                     .appendTo(forumArea);
        });

        $(forumArea).append($('<br />'));
        $(forumArea).appendTo('.IF-body');

        // Append the back link
        backLink = $('<a />').html('&#9664; Back')
                             .attr('href', '#')
                             .click(IF.modules.board.build);

        // Append the new topic link (if permissions allow)
        newLink = $('<a />').html('New Topic...')
                            .attr('href', '#')
                            .attr('id', topics.forum_id)
                            .css('margin-right', '20px')
                            .click(IF.modules.board.new_topic);

        $(forumArea).append($('<br />'))
                    .append(newLink)
                    .append(backLink);
      },

      /**
       * Display the "Create a new topic" form.
       */
      'new_topic' : function() {

        // Hide the forum listing
        $('.IF-body').html('');

        // Add a text box for the user to type a title
        $('<input />').addClass('IF-input IF-hint')
                      .attr('id', 'IF-newtopic-title')
                      .val('Type a title for the topic...')
                      .attr('hint', 'Type a title for the topic...')
                      .appendTo('.IF-body');

        // Add a box for the user to type a post
        $('<textarea />').addClass('IF-input-post')
                         .appendTo('.IF-body')
                         .attr('id', 'IF-newtopic-text')
                         .keypress(function(e) {

          if(e.which == 13)
          {
            // Submit
            IF.remote.exec('Board', 'addTopic', 
              {
                'id': IF.modules.board.forum_id,
                'name': $('#IF-newtopic-title').val(),
                'text': $('#IF-newtopic-text').val()
              },
              function(ret) 
              {
                // Re-get the list of posts
              }, 1
            );

            // Stop the insertion of linebreaks
            return false;
          }
        });
      },

      /**
       * Display the specified topic.
       */
      'display_topic' : function(topicID) {
      
        // Retrieve the forum contents
        IF.remote.exec('Board', 'getPosts',
          {'id' : $(this).attr('id')},
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

        // Hide the forum listing
        $('.IF-body').html('<h3>' + posts.topic_name + '</h3>');

        // Show all posts for the topic
        topicArea = $('<div />').addClass('IF-topic')
                                .attr('id', posts.topic_id);

        $.each(posts.posts, function(i, o) {

          // Generate the link
          post = $('<div />').html(o.post_text)
                             .attr('class', 'IF-post')
                             .attr('id', o.post_id);

          $('<div />').html(post)
                     .appendTo(topicArea);
        });


        $('<textarea />').addClass('IF-input-post')
                         .appendTo(topicArea)
                         .keypress(function(e) {

          if(e.which == 13)
          {
            // Grab the text
            text = $(this).val().trim();
            $(this).val('');

            // Submit
            IF.remote.exec('Board', 'addPost', 
              {
                'id': $('div.IF-topic:first').attr('id'),
                'text': text
              },
              function() 
              {
                // Re-get the list of posts
                IF.remote.exec('Board', 'getPosts',
                {'id' : $('div.IF-topic:first').attr('id') },
                'IF.modules.board.got_posts', 1);
              }, 1
            );

            // Stop the insertion of linebreaks
            return false;
          }

        });

        $(topicArea).appendTo('.IF-body');

        // Append the back link
        backLink = $('<a />').html('&#9664; Back')
                             .attr('href', '#')
                             .attr('id', posts.forum_id)
                             .click(IF.modules.board.display_forum);

        $(topicArea).append($('<br />'))
                    .append(backLink);
      },

      /** 
       * Retrieved a static element.
       * result is a hash that contains attribute and value.
       */
      'got_static' : function(result) {
        IF.cache.set('board.' + result.attribute, result.value);
        $('.IF-' + result.attribute).text(result.value);
      }

    },

    /**
     * User
     * Represents the user state.
     */
    'user' : {

      /**
       * My user ID
       */
      'user_id' : -1,

      /**
       * My user name
       */
      'user_name' : '',

      /**
       * My full name
       */
      'user_full_name' : '',

      /**
       * Initialise the user model.
       */
      'init' : function() {

        // Unpack values from the cache
        IF.modules.user.user_id = IF.cache.get('user_id');
        IF.modules.user.user_name = IF.cache.get('user_name');
        IF.modules.user.user_full_name = IF.cache.get('user_full_name');

        // Build the UI
        this.build();

      },

      /**
       * Build the user block if one exists.
       */
      'build' : function() {

        userArea = $('.IF-user:first').html('');

        // Found a user area?
        if($(userArea).length == 0)
        {
          console.log('Not rendering a user area.');
          return false;
        }

        // Logged in?
        if(this.user_id == -1)
        {
          // Render a login form
          username = $('<input />').addClass('IF-input IF-hint')
                      .val('Username')
                      .attr('id', 'IF-username-field')
                      .attr('hint', 'Username');

          password = $('<input />').addClass('IF-input IF-hint')
                      .val('Password')
                      .attr('type', 'password')
                      .attr('id', 'IF-password-field')
                      .attr('hint', 'Password')
                      .keypress(function(e) {

                        if(e.which == 13)
                        {
                          // Perform the login
                          IF.modules.user.do_login($('#IF-username-field').val(),
                            $('#IF-password-field').val());
                        }

                      });

          // Append it all
          $(userArea).append('<span id="IF-user-desc">Please log in to post:</span>')
                     .append('<br /><br />')
                     .append(username)
                     .append(password)
                     .append($('<br />'))
                     .append($('<br />'));
        }
        else
        {
          // Render information about the user and a logout button
          link = $('<a />').html('Log out')
                           .attr('href', '#')
                           .click(IF.modules.user.do_logout);

          // Append
          $(userArea).append('<span style="margin-right: 30px;">Logged in as ' + 
                             this.user_full_name + '</span>')
                     .append(link)
                     .append('<br /><br />');
        }

      },

      'do_login' : function(username, password) {

        // Perform the login
        IF.remote.exec('User', 'login', 
          {
            'username': username,
            'password': password
          },
          function(res) 
          {
            if(res.user_id)
            { 
              // Set cache values
              IF.cache.set('user_id', res.user_id);
              IF.cache.set('user_name', res.user_name);
              IF.cache.set('user_full_name', res.user_full_name);

              // Set local values
              IF.modules.user.user_id = res.user_id;
              IF.modules.user.user_name = res.user_name;
              IF.modules.user.user_full_name = res.user_full_name;

              // Rebuild UI
              IF.modules.user.build();
              IF.modules.board.build();
            }
            else
            {
              // There was an error
              $('#IF-user-desc').html('Invalid credentials.');
            }
          }, 1
        );

      },

      'do_logout' : function() {

        // Perform the logout
        IF.remote.exec('User', 'logout', {}, function() { }, 1);

        // Clear details in cache
        IF.cache.set('user_id', -1);
        IF.cache.set('user_name', '');
        IF.cache.set('user_full_name', '');

        // Clear local values
        IF.modules.user.user_id = -1;
        IF.modules.user.user_name = '';
        IF.modules.user.user_full_name = '';

        // Rebuild UI
        IF.modules.user.build();

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
      console.log('CACHE: Setting ' + name + ' to ' + value);

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
        $.ajax(IF.remote.responder_path + '?rand=' + Math.floor(Math.random(0,99999) * 1000), {
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
                  context[funcPart].call(context, response.params);
                }
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