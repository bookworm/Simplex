<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * CSS Document. 
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
class Splex_CSSDocument extends Splex_CSSList 
{ 
  /**
   * Returns all the selectors.
   *
   * @return array
   */
  public function getAllSelectors() 
  {
    $result = array();
    $this->allSelectors($result);
    return $result;
  } 
  
// ------------------------------------------------------------------------   
  
  /**
   * Find a selector.
   *        
   * @param string $search Name of selector to search for.
   * @return array
   */
  public function findSelector($search)
  {  
    $selectors = array();
    $this->allSelectors($selectors);   
    
    foreach($selectors as $selector)
    { 
      $key = array_search($search, $selector->selector);      
      
      if($key !== false)           
        return $selector; 
    }
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Returns all the rulesets.
   *
   * @return array
   */
  public function getAllRuleSets() 
  {
    $result = array();
    $this->allRuleSets($result); 
    
    return $result;
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Returns all the all the values or a specific one.
   *  
   * @param mixed $element Either 1. An element to search on or 2. A string to search for.
   * @return array
   */
  public function getAllValues($element = null) 
  {
    $search = null;    
    
    if($element === null) {
      $element = $this;
    } 
    else if(is_string($element)) 
    {
      $search = $element;
      $element = $this;
    }     
    
    $result = array(); 
    $this->allValues($element, $result, $search);     
    
    return $result;
  }    
}