<?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * Widget wrapper around Abstruction.
 * 
 * @todo Everything
 * @package     simplex
 * @subpackage  muwt.widgets
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */ 
class Muwt_Widget_modAbstruct extends Muwt_Widget 
{ 
  /**
   * Constructor.
   * 
   * @param object $param The parameter object to construct the widget from.
   * @return void
   **/
  function __construct($param)
  {             
    $this->param = $param; 
    $this->paramName = $this->param->name;     
  }    
  
// ------------------------------------------------------------------------

  /**
   * Renders the widget.
   *   
   * @return string
   **/
  public function render() 
  {
  } 
}