/** 
 * Instaforum
 * Core JS frontend.
 * 
 * @author Damien Walsh <walshd0@cs.man.ac.uk>
 */

// --------------------------------------------------
// Init the forum on load
// --------------------------------------------------

// "Smallest DOMready ever" - from http://dustindiaz.com/smallest-domready-ever
function r(f){/in/.test(document.readyState)?setTimeout('r('+f+')',9):f()}

// Run when ready
r(function() { Forum.init() });


// --------------------------------------------------
// The Forum object: provides interaction with IF.
// --------------------------------------------------
var Forum = {

  /**
   * Values required by IF.
   */
  'vars' : {

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
   * Initialise the forum
   */
  'init' : function() 
  {
    // Retrieve vars from cache if possible
    this.retrieve_cached_vars();
  },

  /**
   * Retrieve vars from the cache / cookies
   */
  'retrieve_cached_vars' : function()
  {

  }

};