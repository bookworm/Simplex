<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * Fake DOM Elements. Not nearly a full implementation of DOM in PHP; but for our purposes its just fine.
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_DOM_Element
{    
  /**
   * @var string Type of tag e.g div
   **/
  var $tag;   
  
  /**
   * @var array Contains attributes for the tag.
   **/ 
  var $attrs = array();        
  
  /**
   * @var object Parent DOM element
   **/
  var $parent = null;     
  
  /**
   * @var array Children DOM elements 
   **/
  var $children = array();               
  
  /**
   * @var bool Whether or not this is an empty tag. e.g img, hr etc.
   **/
  var $empty = false;      
  
  /**
   * @var string Element ID
   **/
  var $id; 
  
  /**
   * @var array An array of classes for the element
   **/
  var $classes = array();  
  
  /**
   * @var string Holds the tab string used for indentation
   **/
  var $tabString = "  "; 
  
  /**
   * Constructor Function
   * 
   * @param string $name Name of the container. 
   * @return void
   **/
  public function __construct($tag, $parent = null, $empty = false)
  {      
    $this->tag = $tag;      
    $this->empty = $emtpy; 
    
    if ($parent instanceof Element)
      $this->parent = $parent;
  }
   
// ------------------------------------------------------------------------

  /**
   * Add Child Element.
   * 
   * @param object $child DOM Child Object
   * @return void
   **/       
  public function addChild($child)
  {
    $this->children[] = $child;
    $child->parent = $this;   
  }    
   
// ------------------------------------------------------------------------
  
  /**
   * Returns an indentation string.
   * 
   * @param inte $count The level to indent 
   * @return string
   **/
  public function getTabString($count)
  {
    if ($count > 0)
      return str_repeat($this->tabString, $count);
    return '';
  }
   
// ------------------------------------------------------------------------
  
  /**
   * Generates all the opening tags - nested.
   * 
   * @param int $tabCount The level to indent 
   * @return string
   **/
  public function getOpenString($tabCount = 0)
  {              
    $tabs    = $this->getTabString($tabCount);
    $attrString = self::getAttrsString();
    $tag    =  '<' . $this->tagName .  "id='$this->id' class='$this->getClassesString()'" . $attrString;     
    if ($this->empty) {
      $tag .= " />\n";
      return $tabs . $tag;
    } 
    else {
      $tag .= ">";
    }  
     
    $content = '';       
    if (!empty($this->children)) 
    {
      foreach ($this->children as $child) {
        $content .= $child->getOpenString($tabCount + 1);
      }
      $content .= $tabs;
    }     
    
    return $tabs . $tag . $content . "\n";
  }
  
// ------------------------------------------------------------------------
 
  /**
   * Expands all of the DOM and all its children into a string.
   * 
   * @param int $tabCount The level to indent 
   * @return string
   **/ 
  public function getString()
  {
    $tabs    = $this->getTabString($tabCount);
    $attrString = self::getAttrsString();
    $tag    =  '<' . $this->tagName . $attrString;     
    if ($this->empty) {
      $tag .= " />\n";
      return $tabs . $tag;
    } 
    else {
      $tag .= ">";
    }  
     
    $content = '';       
    if (!empty($this->children)) 
    {
      foreach ($this->children as $child) {
        $content .= $child->getString($tabCount + 1);
      }
      $content .= $tabs;
    } 
    if (!empty($content)) $tag .= "\n";      
      
    $closeTag = '</' . $this->tag . '>';        
    
    return $tabs . $tag . $content . $closeTag . "\n";
  } 
  
// ------------------------------------------------------------------------
  
  /**
   * Returns all the attributes as a string.
   * 
   * @return string
   **/
  public function getAttrsString() 
  {
    if (empty($this->attrs)) return '';
    $attrs = array();
    foreach ($this->attrs as $attr => $value ) {
      $attrs[] = $attr . "='" . $value . "'";
    }
    return ' ' . implode(' ', $attrs);
  } 
  
// ------------------------------------------------------------------------
  
  /**
   * Return all classes as a string
   * 
   * @return string $class
   **/
  public function getClassesString() 
  { 
    $class = '';
    foreach($this->classes as $class) {
      $class .= ' ' . $class;
    }
    return $class;
  }
   
// ------------------------------------------------------------------------
  
  /**
   * Append a style to the styles attribute.
   * 
   * @return void
   **/
  public function setStyle($style = '')
  {
    $styles = $this->attrs['style'];
    $styles .= ' ' . $style;
    $this->attrs['style'] = $styles;
  } 
  
// ------------------------------------------------------------------------
  
  /**
   * Append styles to the styles attribute.
   * 
   * @param array $styles Array of styles to append. 
   *  Should be strings e.g array('width: 200px;', 'display: block;')
   * @return void
   **/
  public function setStyles($styles = array())
  {
    foreach($styles as $style) {
      $this->setStyle($style);
    }
  }
  
// ------------------------------------------------------------------------
  
  /**
   * Set an attribute. Will replace any existing values.
   *  
   * @param string $attr Name of attribute to set. 
   * @param mixed  $value Value to set to the attribute.
   * @return void
   **/
  public function setAttr($attr, $value)
  { 
    $this->attrs[$attr] = $value;
  }   
  
// ------------------------------------------------------------------------
   
  /**
   * Set an attribute. Will replace any existing values.
   *  
   * @param string $attrs Attributes to set and their corresponding values
   *  $attrs = array('attr' => $value)
   * @return void
   **/
  public function setAttrs($attrs)
  {
    foreach($attrs as $attr => $value) {
      $this->setAttr($attr, $value);
    }
  } 
}