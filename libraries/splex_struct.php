<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

// Load Simplex + Prequisites
$splex = getSplexInstance();
$splex->loader->load_include(array('ab_markup.php', 'ab_registry.php', 'ab_column.php', 'ab_columns.php',
  'ab_grid.php', 'ab_grid_layouts.php', 'ab_layout.php', 'ab_layouts.php', 'ab_module.php', 'ab_modules.php'));

// ------------------------------------------------------------------------

/**
 * Wrapper Around AbSTRUCT The Template Layout Abstraction Framework.
 *
 * @package     simplex
 * @subpackage  libraries
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */ 
class Splex_Struct
{ 
  /**
   * Holds the AB_Registry:: Object.
   *
   * @var object
   **/
  var $ab;      
  
  /**
   * Holds a list of the AB_Registry:: Variabkes.
   *
   * @var array
   **/
  var $abVars = array();
  
  /**
   * Holds a list of the AB_Registry:: Methods.
   *
   * @var array
   **/
  var $abMethods;
  
  /**
   * Whether or not the ab registry has been built yet. 
   *
   * @var bool
   **/     
  var $built = false;   
  
  /**
   * Whether or not the ABA registry has been serialized to be save yet.
   *
   * @var bool
   **/  
  var $serialized = false;   
  
  /**
   * Holds the container parameters.
   *
   * @var array
   **/
  var $containers = array();    
  
  /**
   * Holds the layout parameters.
   *
   * @var array
   **/
  var $layouts = array();   
  
  /**
   * Holds the column parameters.
   *
   * @var array
   **/
  var $columns = array(); 
  
  /**
   * Holds the module parameters.
   *
   * @var array
   **/  
  var $modules = array();   
  
  /**
   * Holds the serialized AB_Registry::
   *
   * @var array
   **/  
  var $serializedAB = array();
  
  /**
   * Empty Constructor.
   *  
   * @return void.
   */ 
  public function __construct() 
  {             
    $splex = getSplexInstance();              
    $this->ab = new AB_Registry(); 
    
    if(isset($splex->jpog->params['abstruct'])) 
    {   
      if(!empty($splex->jpog->params['abstruct']['value']))
      {     
        $abstruct = $splex->jpog->get('abstruct', true);    
        $this->built = true;

        if(isset($abstruct['value']['containers']))
          $this->ab->containers = unserialize($abstruct['value']['containers']);      
        if(isset($abstruct['value']['modules']))
          $this->ab->modules = unserialize($abstruct['value']['modules']);    
        if(isset($abstruct['value']['layouts']))                        
          $this->ab->layouts = unserialize($abstruct['value']['layouts']);      
        if(isset($abstruct['value']['columns']))                        
          $this->ab->columns = unserialize($abstruct['value']['columns']);
      }                                                                 
    } 
       
    $this->containers = $splex->jpog->getByType('containerAbstruct', true);   
    $this->layouts    = $splex->jpog->getByType('layoutAbstruct', true);       
    $this->columns    = $splex->jpog->getByType('columnAbstruct', true);       
    $this->modules    = $splex->jpog->getByType('moduleAbstruct', true);       
    
    if($this->built == false)
    {  
      if(!empty($this->containers)) $this->buildContainers();         
      if(!empty($this->layouts))    $this->buildLayouts(); 
      if(!empty($this->columns))    $this->buildColumns();         
      if(!empty($this->modules))    $this->buildModules();   
    }  
  } 
 
// ------------------------------------------------------------------------ 
 
  /**
   * Builds the containers from the container parameter objects.     
   *  
   * @return void.
   */    
  public function buildContainers()
  {
    $containers = $this->containers;   
    
    foreach($containers as $k => $container) {
      $this->ab->containers[$k] = $this->buildContainer($k, $container);    
      unset($this->containers[$k]);
    }
  }
  
// ------------------------------------------------------------------------ 

  /**
   * Builds the layouts from the layout parameter objects.
   *  
   * @return void.
   */ 
  public function buildLayouts()
  {
    $layouts = $this->layouts; 
    
    foreach($layouts as $k => $layout) {
      $this->ab->layouts[$k] = $this->buildlayout($k, $layout);    
      unset($this->layouts[$k]);
    }
  }   
  
// ------------------------------------------------------------------------ 

  /**
   * Builds the columns from the column parameter objects.
   *  
   * @return void.
   */ 
  public function buildColumns()
  {
    $columns = $this->columns; 

    foreach($columns as $k => $column) {
      $this->ab->columns[$k] = $this->buildcolumn($k, $column);    
      unset($this->columns[$k]);
    }
  }  
       
// ------------------------------------------------------------------------ 

  /**
   * Builds the modules from the module parameter objects.
   *  
   * @return void.
   */ 
  public function buildModules($value='')
  {
    $modules = $this->modules; 

    foreach($modules as $k => $module) {
      $this->ab->modules[$k] = $this->buildModule($k, $module);    
      unset($this->modules[$k]);
    }
  }
  
// ------------------------------------------------------------------------ 

  /**
   * Builds a container from a containerAbstruct param.
   * 
   * @param string $name Name of the container.
   * @param array $container A containerAbstruct param. 
   * @return object AB_Container::
   */
  public function buildContainer($name, $container)
  { 
    $splex = getSplexInstance();
         
    if(isset($container['settings']['grid'])) {
      $gridParam = $splex->jpog->get($container['settings']['grid']); 
      $gridParam->settings['name'] = $gridParam->name;
      $grid = new AB_Grid($gridParam->settings);
    }
    else 
      $grid = null;              
      
    $containerAB = $this->ab->newContainer($name, $grid);      
    
    if(isset($container['value']['children']))
    {
      foreach($container['value']['children'] as $child) 
      {       
        $layoutName = $child['name'];     
        
        $layoutParam = $splex->jpog->get($layoutName, true);    
        
        $layout = $this->buildLayout($layoutName, $layoutParam);
        $containerAB->addLayout($layout);    
        unset($this->layouts[$layoutName]);
      }
    }
    
    return $containerAB;
  }   
  
// ------------------------------------------------------------------------ 

  /**
   * Builds a layout from a layoutAbstruct param.
   * 
   * @param string $name Name of the layout.
   * @param array $layout A layoutAbstruct param. 
   * @return object AB_Layout::
   */
  public function buildLayout($name, $layout)
  { 
    $splex = getSplexInstance(); 
    
    if(isset($layout['settings']['grid']))
     {
      $gridParam = $splex->jpog->get($layout['settings']['grid']); 
      $gridParam->settings['name'] = $gridParam->name; 
      $grid = new AB_Grid($gridParam->settings);
    }
    else 
      $grid = null;
       
    $layoutAB = $this->ab->newLayout($name, $layout['settings']);       
    
    if($grid != null) $layoutAB->grid = $grid;
    
    if(isset($layout['value']['children']))
    { 
      foreach($layout['value']['children'] as $child) 
      {  
        if($child['type'] == 'columnAbstruct')
        {
          $columnName = $child['name'];  
          $columnParam = $splex->jpog->get($columnName, true);        
          
          $column = $this->buildColumn($columnName, $columnParam, $layoutAB);
          $layoutAB->addColumn($column);    
          unset($this->columns[$columnName]);
        } 
        elseif($child['type'] == 'moduleAbstruct')
        {
          $moduleName = $child['name'];      
          
          $moduleParam = $splex->jpog->get($moduleName, true);    
          
          $module = $this->buildModule($moduleName, $moduleParam);
          $layoutAB->addModule($module);    
          unset($this->modules[$moduleName]);
        }
      }
    }   
    
    return $layoutAB;
  }  
  
// ------------------------------------------------------------------------ 

  /**
   * Builds a column from a columnAbstruct param.
   * 
   * @param string $name Name of the column.
   * @param array $column A columnAbstruct param. 
   * @param object $parentLayout A parent layout. Should NOT be a parameter but rather an AB_Layout:: object.
   * @return object AB_Column::
   */   
  public function buildColumn($name, $column, $parentLayout = null)
  {         
    $splex = getSplexInstance();
      
    if(isset($column['settings']['grid']))
    {
      $gridParam = $splex->jpog->get($column['settings']['grid']); 
      $gridParam->settings['name'] = $gridParam->name; 
      $grid = new AB_Grid($gridParam->settings);
    }
    elseif($parentLayout->grid != null) 
      $grid = $parentLayout->grid;
    else
      $grid = null;  
      
    $columnAB = $this->ab->newColumn($name, $column['settings'], $grid, $parentLayout); 

    if(isset($column['value']['children']))
    {
      foreach($column['value']['children'] as $child) 
      { 
        $moduleName = $child['name'];      
        
        $moduleParam = $splex->jpog->get($moduleName, true);
        
        $module = $this->buildModule($moduleName, $moduleParam);
        
        $columnAB->addModule($module);
        unset($this->modules[$moduleName]);
      }
    }
    
    return $columnAB;
  }      
  
// ------------------------------------------------------------------------ 

  /**
   * Builds a module from a moduleAbstruct param.
   * 
   * @param string $name Name of the column.
   * @param array $module A moduleAbstruct param. 
   * @return object AB_Module::
   */
  public function buildModule($name, $module)
  {
    return $this->ab->newmodule($name, $module['settings'], $module['attributes']);
  }
  
// ------------------------------------------------------------------------ 

  /**
   * Serializes and saves AB_Registry objects.
   *
   * @return void
   */   
  public function save()
  { 
    $splex = getSplexInstance();   
    
    $this->serialize(); 
    
    $splex->jpog->paramObjs['abstruct']->value = $this->serializedAB;           
    $splex->jpog->paramObjs['abstruct']->save(true);
  } 
  
// -----------------------------------------------------------------------       
  
  /**
   * Serializes the AB_Registry::
   *
   * @return void
   **/
  public function serialize()
  {  
    $this->serializedAB['containers'] = serialize($this->ab->containers);
    $this->serializedAB['layouts']    = serialize($this->ab->layouts);
    $this->serializedAB['columns']    = serialize($this->ab->columns);
    $this->serializedAB['modules']    = serialize($this->ab->modules);
    
    $this->serialized = true;
  }
  
// ------------------------------------------------------------------------

  /**
   * Overload method function. Passes everything to the AB_Registy::
   *
   * @return mixed
   **/
  public function __call($method, $args)
  {    
    if(method_exists($this->ab, $method))
      return call_user_func_array(array($this->ab, $method), $args);        

    throw new Exception("This Method {$method} doesn't exist");
  } 

// ------------------------------------------------------------------------

  /**
   * Overload Get. Passes everything to the AB_Registy::    
   *
   * @return array
   **/
  public function __get($name)
  {
    if(isset($this->$name)) 
      return $this->$name;
    
    throw new Exception("This Variable {$name} doesn't exist");
  }  
}