<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------
 
/**
 * Parses CSS strings into DOM objects.
 *  
 * @note Code originally from http://code.google.com/p/zen-php/ re-coded for my uses.
 *
 * @package   Abstruct                
 * @author    waldsonpatricio http://code.google.com/p/zen-php/
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_Parser
{  
  /**
   * @var array All HTML tags that are empty and don't have a closing tag.
   **/  
  var $emptyTags = array('br',
    'hr',
    'meta',
    'link',
    'base',
    'link',
    'meta',
    'hr',
    'br',
    'img',
    'embed',
    'param',
    'area',
    'col',
    'input',
  );
  
  /**
   * Empty Constructor Function
   **/
  public function __construct() { }
  
// ------------------------------------------------------------------------    
  
  /**
   * Takes a css selector and creates/expands it into DOM elements.     
   *
   * @param string $selector CSS Formatted selector
   * @return object $root The parent DOM Object. 
   **/
  public function expand($selector) 
  {
    $levels  = preg_split('
              (>|\+(?=.+)|<) # Checks for the DOM level. E.g div.bob > li.joe
                             # In this case li.joe is an immediate child of div.bob
              is', $selector, - 1, PREG_SPLIT_DELIM_CAPTURE);    
    $root = new AB_Root_DOM_Element(); 
    $current = $root;
    $last = null;          
    
    foreach($levels as $level)
    {
      if (empty($level)) continue;  
      if (is_null($current)) return '';   
      if ($part == '+') {
        continue;
      } 
      else if ($part == '>') {
        $current = $last;
      } 
      else if ($part == '<') {
        $current = $current->parent;
      } 
      else 
      {
        $element = self::createDOMElement($part);
        if (is_null($element)) return '';
        $current->addChild($element);
        $last = $element;
      }
    } 
    
    return $root;
  }
  
// ------------------------------------------------------------------------
  
  /**
   * Takes a css selector and creates a DOM Element.
   *
   * @param string $selector CSS Formatted selector
   * @return object $element DOM Element
   **/
  public function createDOMElement($selector)
  {
    if (preg_match_all('/([a-z0-9]+)(#[^\.\[\{]+)?(\.[^\[\{]+)?(\[.+\])?(?:\{(.+)\})?/is', $selector, $matches, PREG_SET_ORDER)) 
    {
        
      /*
       * 1 - tagName
       * 2 - id
       * 3 - classes
       * 4 - attributes 
       * 5 - content   
       * */
       
      $matches = $matches[0];
      $tag = $matches[1];
      $element = new AB_DOM_Element($tag, null, self::isEmptyTag($tag));     
      
      if (isset($matches[2]) && !empty($matches[2])) {
        $element->id = trim($matches[2], '#');
      }
      
      if (isset($matches[3]) && !empty($matches[3])) {
        $classes = implode(' ', explode('.', trim($matches[3], '.')));
        $element->classes = $classes;
      }
      
      if (isset($matches[4]) && !empty($matches[4])) 
      {
        if (!preg_match_all('/\[([^=]+)=(.*?)\]/is', $matches[4], $attrs, PREG_SET_ORDER))
          return null;
        foreach ($attrs as $attr => $value) {
          $element->setAttr($attr[1], $value);
        }
      }      
      
      if (isset($matches[5]) && !empty($matches[5])) {
        $element->addChild(new AB_TextElement($content));       
      }  
      
      return $element;
    } 
    return null;
  } 
  
// ------------------------------------------------------------------------
  
  /**
   * If a tag is an empty tag type.
   *
   * @param string $tag The tag to check
   * @return bool True or False depending on whether or not the tag is empty.
   **/
  public static function isEmptyTag($tag)
  {
    return in_array(strtolower($tag), self::$emptyTags);
  } 
}