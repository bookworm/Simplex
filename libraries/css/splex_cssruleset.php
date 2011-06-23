<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * CSS RuleSet. 
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
abstract class Splex_CSSRuleSet 
{ 
  /**
   * @var array Holds an array of rules.
   **/
  var $rules = array();
  
  /**
   * Constructor. Does absolutely shit, like my cat.
   *  
   * @return void
   */
  public function __construct() { }  
  
// ------------------------------------------------------------------------  
  
  /**
   * Adds a rule.
   *   
   * @param Splex_CSSRule $rule A css rule object.
   * @return void
   */
  public function addRule(Splex_CSSRule $rule) 
  {
    $this->rules[$rule->rule] = $rule;
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Adds a rule.
   *   
   * @param mixed $rule A css rule object or rule to search for.
   * @return mixed
   */
  public function getRules($rule = null) 
  {
    if($rule === null) 
      return $this->rules;
      
    $result = array();  
    
    if($rule instanceof Splex_CSSRule) 
      $rule = $rule->rule;
      
    if(strrpos($rule, '-') === strlen($rule)-strlen('-')) 
    {
      $start = substr($rule, 0, -1);
      foreach($this->rules as $rule) 
      {
        if($rule->rule === $start || strpos($rule->rule, $rule) === 0) {
          $result[$rule->rule] = $this->rules[$rule->rule];
        }
      }
    } 
    else if(isset($this->rules[$rule]))
      $result[$rule] = $this->rules[$rule];
      
    return $result;
  }
  
// ------------------------------------------------------------------------   
 
  /**
   * Removes a rule.
   *   
   * @param mixed $rule A css rule object or rule to search for.
   * @return void
   */ 
  public function removeRule($rule) 
  {
    if($rule instanceof Splex_CSSRule)
      $rule = $rule->rule;
      
    if(strrpos($rule, '-') === strlen($rule)-strlen('-')) 
    {
      $start = substr($rule, 0, -1);  
      
      foreach($this->rules as $rule) 
      {
        if($rule->rule === $start || strpos($rule->rule, $rule) === 0) {
          unset($this->rules[$rule->rule]);
        }
      }
    } 
    else if(isset($this->rules[$rule])) {
      unset($this->rules[$rule]);
    }
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Returns a css rule string.
   *   
   * @return string
   */
  public function __toString() 
  {
    $result = '';  
    
    foreach($this->rules as $rule) {
      $result .= $rule->__toString();
    }    
    
    return $result;
  }
}