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