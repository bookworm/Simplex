<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * Registry for all the objects..
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_Registry
{     
  /**
   * @var array Layout objects
   **/
  var $layouts;   
  
  /**
   * @var array Column objects
   **/
  var $columns;  
    
  /**
   * @var array Container objects
   **/
  var $containers;        
  
  /**
   * @var array Module objects
   **/ 
  var $modules;
  
  /**
   * Empty Constructor Function.
   * 
   * @return void
   **/
  public function __construct() { }  
  
// ------------------------------------------------------------------------
  
  /**
   * Creates a new layout.
   * 
   * @param string $name Give the layout a name.
   * @param array $props Layout properties.
   * @return AB_Layout:;
   **/
  public function newLayout($name, $props = array())
  {               
    $layout = new AB_Layout($name, $props);
    $this->layouts[$name] = $layout;   
    return self::getLayout($name);
  } 
    
// ------------------------------------------------------------------------
                
  /**
   * Creates a new column.
   * 
   * @param string $name Give the column a name. Required.
   * @param array $props Column properties.
   *  $props['alpha'], 
   *  $props['omega']
   *  $props['equalize'], 
   *  $props['width'],
   *  $props['margin']
   * @param object $grid Does this column belong to a grid?
   * @param object $layout Does this have a parent layout?
   * @return void
   **/                
  public function newColumn($name, $props = array(), $grid = null, $layout = null)
  {               
    $column = new AB_Column($name, $props, $grid, $layout);
    $this->columns[$name] = $column;
    return self::getColumn($name);
  }               
   
// ------------------------------------------------------------------------
   
  /**
   * Creates a new container.
   * 
   * @param string $name Name of the container. Required. 
   * @param object $grid Grid object. Optional.
   * @return void
   **/               
  public function newContainer($name, $grid = null)
  {
    $container = new AB_Container($name, $grid);
    $this->containers[$name] = $container;  
    return self::getContainer($name); 
  }   
  
// ------------------------------------------------------------------------
  
  /**
   * Creates a new module.
   * 
   * @param string $name Name/position of the module. Required.  
   * @param array $props Class properties. 
   * @param array $moduleProps Module properties the get passed to module rendering function.
   * @return void
   **/
  public function newModule($name, $props = array(), $moduleProps = array())
  {
    $module = new AB_Module($name, $props, $moduleProps);
    $this->modules[$name] = $module;     
    return self::getModule($name); 
  }     
   
// ------------------------------------------------------------------------
  
  /**
   * Creates new layouts.
   * 
   * @param array $layouts Names of the layouts.
   * @return void
   **/
  public function newLayouts($layouts = array())
  {               
    foreach($layouts as $layout) {
      self::newLayout($layout);
    }
  }               
  
// ------------------------------------------------------------------------
  
  /**
   * Creates new columns.
   * 
   * @param array $columns Names of the columns
   * @return void
   **/
  public function newColumns($columns = array())
  {               
    foreach($columns as $column) {
      self::newColumn($column);
    }     
  }               
  
// ------------------------------------------------------------------------
  
  /**
   * Creates new containers.
   * 
   * @param array $containers Names of the containers.
   * @return void
   **/
  public function newContainers($containers = array())
  {
    foreach($containers as $container) {
      self::newContainer($container);
    }
  }   
  
// ------------------------------------------------------------------------
  
  /**
   * Creates new containers.
   * 
   * @param array $containers Names/Positions of the modules.
   * @return void
   **/
  public function newModules($modules = array())
  {
    foreach($modules as $module) {
      self::newModule($module);
    }
  }
  
// ------------------------------------------------------------------------
  
  /**
   * Returns a layout.
   * 
   * @param string $name Name of the layout to retrieve.
   * @return object
   **/
  public function getLayout($name)
  {               
    return $this->layouts[$name];    
  }               
  
// ------------------------------------------------------------------------
  
  /**
   * Returns a column.
   * 
   * @param string $name Name of the column to retrieve.
   * @return object
   **/
  public function getColumn($name)
  {               
    return $this->columns[$name];    
  }               
  
// ------------------------------------------------------------------------
  
  /**
   * Returns a container.
   * 
   * @param string $name Name of the container to retrieve.
   * @return object
   **/
  public function getContainer($name)
  {
    return $this->containers[$name];
  }   
  
// ------------------------------------------------------------------------
  
  /**
   * Returns a module.
   * 
   * @param string $name Name of the module to retrieve.
   * @return object
   **/
  public function getModule($name)
  {
    return $this->modules[$name];
  } 
}