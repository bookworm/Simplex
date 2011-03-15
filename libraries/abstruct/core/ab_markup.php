<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * The main parent class for layouts, columns, containers and modules. 
 *  Basically anything with markup inherits from this.
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
abstract class AB_Markup
{       
  /**
   * @var array Holds some DOM objects. 
   * Any DOM objects inside the array will be outputted so that they "wrap" the markup object.
   * @note The DOM objects are not true DOM objects and by no means a full implementation at all.
   **/   
  var $DOMS; 
  
  /**
   * @var array The DOM objects that will be outputted before the markup element.
   **/                  
  var $before;          
                        
  /**                   
   * @var array The DOM objects that will be outputted after the markup element.
   **/
  var $after;   
  
  /**                   
   * @var array Holds the methods for class extensions. Used in a decorator pattern.
   **/  
  var $methods = array();  
  
  /**                   
   * @var array Holds the vars for class extensions. Used in a decorator pattern.
   **/  
  var $vars = array();
  
  /**
   * Constructor Function.
   *  
   * @return void
   **/
  public function __construct()
  {  
    $_this = $this;
  }     
  
// ------------------------------------------------------------------------
  
  /**
   * Adds a DOM object.
   * 
   * @param object $dom The DOM Object to add/inject. 
   * @return void
   **/
  public function injectDOMWrap($dom)
  {
    $this->DOMS[] = $domOBJ; 
  }
  
// ------------------------------------------------------------------------
   
  /**
   * Adds a DOM before the markup element.
   * 
   * @param object $dom The DOM Object to add/inject. 
   * @return void
   **/
  public function injectBefore($dom)
  {
    $this->before[] = $dom;
  } 
  
// ------------------------------------------------------------------------
  
  /**
   * Adds a DOM after the markup element.
   * 
   * @param object $dom The DOM Object to add/inject. 
   * @return void
   **/
  public function injectAfter($dom)
  {
    $this->after[] = $dom; 
  }  
  
// ------------------------------------------------------------------------
  
  /**
   * Renders and then returns the BEFORE DOMS.
   * 
   * @return string $result
   **/
  public function getBefore()
  {
    $result = "";   
    if(!empty($this->before)) 
    {
      foreach($this->before as $dom) {
        $result .= $dom->getString();
      }
    }
    return $result;
  } 
  
// ------------------------------------------------------------------------
   
  /**
   * Renders and then returns the AFTER DOMS.
   * 
   * @return string $result
   **/
  public function getAfter()
  {
    $result = "";    
    if(!empty($this->after))  
    {
      foreach($this->after as $dom) {
        $result .= $dom->getString();
      }    
    } 
    return $result;
  }     
  
// ------------------------------------------------------------------------
 
  /**
   * Renders and then returns the wrapping DOMS.
   * 
   * @return string $result
   **/ 
  public function getDOMWrap()
  {  
    $result = "";   
    if(!empty($this->DOMS))
    {
      foreach($this->DOMS as $dom) {
        $result .= $dom->getOpenString();
      }
    }  
    return $result;
  }   
  
// ------------------------------------------------------------------------
  
  /**
   * Renders and then returns closing tags for WRAPPING DOMS.
   * 
   * @return string $result
   **/
  public function getDOMClose()
  {        
    $result = "";   
    if(!empty($this->DOMS))
    {
      foreach($this->DOMS as $dom) {
        $result .= '</' . strtolower($dom->tag) . '>';
      }
    } 
    return $result;
  }  
  
// ------------------------------------------------------------------------
  
  /**
   * Catches any methods that don't exist and re-routes them to an extended class. 
   *  
   * @note Returns false if the method cant be called.
   * 
   * @return mixed
   **/ 
  public function __call($method, $args)
  {                            
    if(array_key_exists($method, $this->methods)) {         
      $className =  $this->methods[$method];       
      return call_user_func_array(array($this->$className, $method), $args);    
    } 
    else {
      return false;
    }
  } 
   
// ------------------------------------------------------------------------

  /**
   * Catches any var calls to vars that don't exist and then re-routes the request to an extended class. 
   * 
   * @note Returns false if the method cant be called.
   * 
   * @return mixed
   **/
  public function __get($var)
  {
    if(array_key_exists($var, $this->vars)) {
      $className =  $this->vars[$var];       
      return $this->$className->$var;
    }
    else {
      return false;
    }
  }
  
// ------------------------------------------------------------------------
  
  /**
   * Allows one to extend the class using a decorator pattern.   
   * 
   * @note Classes are added into a matching class var e.g $this->AB_Markup.
   *  You should call this from a child class only.
   * 
   * @return void
   **/  
  public function extendClass($class)
  {         
    if(!is_array($class))  {
      $classes = array();
      $classes[] = $class; 
    } 
    else {
      $classes = $class;
    }       
    
    foreach($classes as $className)
    {
      $this->$className = new $className;    

      $methods = get_class_methods($className);     
      $vars    = get_class_vars($className); 

      foreach($methods as $method) {
        $this->methods[$method] = $className;
      }          

      foreach($vars as $var => $v) {  
        $this->vars[$var] = $className;
      }    
      
      unset($methods); 
    }
  }
}