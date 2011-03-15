<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------  

/**
 * Text DOM Element.
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_TextElement extends AB_DOM_Element 
{    
  /**
   * @var string The content in this element
   **/
  public $content;
  
  /**
   * Constructor Function
   * 
   * @param string $content The content in the text element 
   * @param object $parent  The parent DOM element
   * @return void
   **/
  public function __construct($content, $parent = null) 
  {
    $this->content = $content; 
    $this->$parent = $parent;
  }
    
// ------------------------------------------------------------------------
  
  /**
   * Add this element as a child to the parent.
   * 
   * @param object $child DOM Child Object
   * @return void
   **/
  public function addChild($child)
  {
    if ($this->parent != null)
      $this->parent->addChild($child);
  }
  
// ------------------------------------------------------------------------
  
  /**
   * Expands this DOM element into a string.
   * 
   * @param int $tabCount The level to indent 
   * @return string
   **/
  public function getString($tabCount = 0) 
  {
    $tabs = $this->getTabString($tabCount);
    return $tabs . implode("\n" . $tabs, explode("\n", $this->content)) . "\n";
  }
}