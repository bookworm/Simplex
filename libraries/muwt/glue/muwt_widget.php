<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );  

// ------------------------------------------------------------------------

/**
 * Some shared methods in vars for widget classes.
 *    
 * @package     muwt
 * @subpackage  muwt.glue
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
class Muwt_Widget
{ 
  /**
   * @var mixed Holds the parameter from which the widget is constructed.
   **/
  var $param;
  
  /**
   * @var mixed Convenience var that holds the parameter's name
   **/   
  var $paramName;
  
  /**
   * @var mixed Holds the parameter's value.
   *  Usually just a convenience var, but not always, because the value of the parameter is often calculated within the widget.
   **/
  var $paramValue;
  
  /**
   * Shell Constructor.
   *
   * @return void
   **/
  function __construct() { }
}
