<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------         

/**
 * Stuff shared between anything handling layoutd
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/  
class AB_Layouts  
{      
  /**
   * @var array The layouts within the container.   
   **/ 
  var $layouts = array();
  
  /**
   * Empty Constructor
   *  
   * @return void
   **/
  public function __construct() { }
  
// ------------------------------------------------------------------------         
  
  /**
   * Adds a layout to the container
   *  
   * @param object $layout The layout object.
   * @return void
   **/
  public function addLayout($layout)
  {
    $this->$layouts[$layout->name] = $layout; 
  }
   
// ------------------------------------------------------------------------         
  
  /**
   * Moves a layout position. 
   *
   * @note Essentially swaps the positions of two layouts.
   *  
   * @param string $name Name of the layout to move. 
   * @param string $destname Name of the destination layout.
   * @return void
   **/
  public function moveLayout($name, $destname)
  { 
    $source = $this->layouts[$name];
    $dest   = $this->layouts[$destname];
    $this->layouts[$name] = $dest; 
    $this->layouts[$destname] = $source;
    unset($source);
    unset($dest);
  }
}