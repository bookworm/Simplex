<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * CSS Selector. 
 *
 * @note Based on the css parser by Raphael Schweikert https://github.com/sabberworm/PHP-CSS-Parser
 *
 * @package     simplex
 * @subpackage  libraries
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.  
 * @author      2010 Raphael Schweikert http://www.sabberworm.com/
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.   
 */
class Splex_CSSSelector extends Splex_CSSRuleSet 
{ 
  /**
   * @var array Hold the selector.
   **/
  var $selector = array();

  /**
   * Constructor. Does nothing except call its parent at every constructions. 
   *  
   * @return void
   */
  public function __construct() 
  {
    parent::__construct();
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Sets the selector
   *
   * @param mixed $selector The selector (or selectors) to set can be a comma separated string or an array, 
   * @return void
   */
  public function setSelector($selector) 
  {
    if(is_array($selector))
      $this->selector = $selector;
    else 
      $this->selector = explode(',', $selector);
      
    foreach($this->selector as $key => $sSelector) {
      $this->selector[$key] = trim($sSelector);
    }
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Turns this into a string.
   *  
   * @return string
   */
  public function __toString() 
  {
    $result = implode(', ', $this->selector).' {';
    $result .= parent::__toString();
    $result .= "} \n\n" ;    
    
    return $result;
  }
}