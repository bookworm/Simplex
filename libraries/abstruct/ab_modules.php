<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------    

/**
 * Stuff shared between anything handling modules.
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_Modules 
{   
  /**
   * @var array The modules within the column/layout etc.   
   **/ 
  var $modules = array();     
  
  /**
   * Empty Constructor
   *  
   * @return void
   **/
  public function __construct() { }     
  
// ------------------------------------------------------------------------    
  
  /**
   * Adds a module to the column
   *  
   * @param object $module The module object.
   * @return void
   **/
  public function addModule($module)
  {  
    $this->modules[$module->name] = $module;
  }  
   
// ------------------------------------------------------------------------    
  
  /**
   * Moves a module position. 
   *
   * @note Essentially swaps the positions of two modules.
   *  
   * @param string $name Name of the module to move. 
   * @param string $destname Name of the destination module.
   * @return void
   **/
  public function moveModule($name, $destname)
  { 
    $source = $this->modules[$name];
    $dest   = $this->modules[$destname];
    $this->modules[$name] = $dest; 
    $this->modules[$destname] = $source;
    unset($source);
    unset($dest);
  }
}