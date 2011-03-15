<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * Root/Container DOM Element
 *  
 * @package   Abstruct                
 * @author    waldsonpatricio http://code.google.com/p/zen-php/
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_Root_DOM_Element extends AB_DOM_Element 
{ 
  /**
   * Empty Constructor Function
   **/
  public function __construct() { }  
  
// ------------------------------------------------------------------------
  
  /**
   * Expands this DOM element into a string.
   * 
   * @param int $tabCount The level to indent 
   * @return string
   **/
  public function getString($tabCount = 0) 
  {
    $content = '';
    
    foreach ($this->children as $child) {
      $content .= $child->getString($tabCount);
    }     
    
    return $content;  
  }    
}