<?php     

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Simplex Base Class.
 * 
 * @note Allows us to build one super object to contain, classes, libraries, etc.
 * Idea Borrowed From CodeIgniter.  
 * 
 * @note Basically takes simplex and loads it into itself.
 *
 * @package     simplex
 * @subpackage  core
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
class Splex_Base 
{ 
  /**
   * @var object Holds the singleton instance. Have you never seen a singleton class implementation before? 
   **/                                                       
  private static $instance;   

  /**
   * Creates as instance of this class.   
   *
   * @return void  
   **/    
  public function Base() 
  {    
    self::$instance = $this;
  }  

// ------------------------------------------------------------------------

  /**
   * Returns a instance of this class.        
   *
   * @return class
   **/   
  public static function &getInstance() 
  {
    return self::$instance;
  }  
} 

// ------------------------------------------------------------------------

/**
 * Good old Singleton Return.         
 *
 * @return class
 **/  
function getSplexInstance()
{
  return Splex_Base::getInstance();
}