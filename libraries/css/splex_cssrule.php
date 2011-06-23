<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * CSS Rule/Property. 
 *
 * @note Based on the css parser by Raphael Schweikert https://github.com/sabberworm/PHP-CSS-Parser
 *
 * @package     simplex
 * @subpackage  libraries
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.  
 * @author      2010 Raphael Schweikert http://www.sabberworm.com/
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.   
 */
class Splex_CSSRule 
{ 
  /**
   * @var string Holds the string representation of the rule.
   **/
  var $rule = '';   
  
  /**
   * @var array Values in this rule.
   **/
  var $values = array();  
  
  /**
   * @var bool Is the rule important?
   **/
  var $isImportant = false;
  
  /**
   * Constructor.
   * 
   * @param string $rule The string representation of the rule.     
   * @return void
   */
  public function __construct($rule) 
  {
    $this->rule = $rule;
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * A beautiful string method.
   * 
   * @return void
   */
  public function __toString() 
  {
    $result = " {$this->rule}: ";
    
    foreach($this->values as $values) {
      $result .= implode(', ', $values).' ';
    }              
    
    if($this->isImportant)
      $result .= '!important';
    else 
      $result = substr($result, 0, -1);
      
    $result .= '; ';   
    
    return $result;
  }
}