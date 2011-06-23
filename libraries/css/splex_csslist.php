<?php     

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * CSS List. Holds the parsed css. 
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
abstract class Splex_CSSList 
{
  /**
   * @var string The Parsed Results
   **/
  var $parsed = array();
  
  /**
   * Constructor Function.
   *
   * @return void
   */
  public function __construct() { } 

// ------------------------------------------------------------------------   
  
  /**
   * Appends an item to the parsed array.
   *     
   * @param $object $item The item to append.
   * @return void
   */
  public function append($item) 
  {
    $this->parsed[] = $item;
  }   
  
// ------------------------------------------------------------------------   
  
  /**
   * Loops over the parsed array and generates the css string.
   *
   * @return string
   */
  public function __toString() 
  {
    $result = '';    
    
    foreach($this->parsed as $item) {
      $result .= $item->__toString();
    }      
    
    return $result;
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Returns all the selectors.
   *       
   * @param array $result The result array by reference. 
   *  This is so we can get selectors in a nested/recursive manner. 
   *  This method is not called directly by users so the need for the referenced var is transparent.
   * @return array
   */
  public function allSelectors(&$result) 
  {
    foreach($this->parsed as $item) 
    {
      if($item instanceof Splex_CSSSelector)
        $result[] = $item;
      else if($item instanceof Splex_CSSList) 
        $item->allSelectors($result);
    }
  }  
  
// ------------------------------------------------------------------------   
  
  /**
   * Returns all the rulesets.
   *       
   * @param array $result The result array by reference. 
   *  This is so we can get selectors in a nested/recursive manner. 
   *  This method is not called directly by users so the need for the referenced var is transparent.
   * @return array
   */
  public function allRuleSets(&$result) 
  {
    foreach($this->parsed as $item) 
    {
      if($item instanceof Splex_CSSRuleSet)
        $result[] = $item;
      else if($item instanceof Splex_CSSList)
        $item->allRuleSets($result);
    }
  }  
  
// ------------------------------------------------------------------------   
  
  /**
   * Returns all values.
   *   
   * @param object $element The Element to get the values from.   
   * @param array $result The result array by reference. 
   *  This is so we can get selectors in a nested/recursive manner. 
   *  This method is not called directly by users so the need for the referenced var is transparent.   
   * @param string $search A string to search for.
   * @return array
   */
  public function allValues($element, &$result, $search = null) 
  {
    if($element instanceof Splex_CSSList) 
    {
      foreach($element->parsed as $item) {
        $this->allValues($item, $result, $search);
      }
    } 
    else if($element instanceof Splex_CSSRuleSet) 
    {
      foreach($element->getRules($search) as $rule) {
        $this->allValues($rule, $result, $search);
      }
    } 
    else if($element instanceof Splex_CSSRule) 
    {
      foreach($element->getValues() as $values) 
      {
        foreach($values as $value) {
          $result[] = $value;
        }
      }
    }
  }
}