<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 

// Load Splex and Class Prequisites 
$splex = getSplexInstance();      
$splex->loader->load_include('jpog_abstract.php');  
$splex->loader->load_include('jpog_param.php');

// ------------------------------------------------------------------------

/**
 * Jpog. Handles parameter/widget loading, saving and generating.
 *    
 * @package     simplex
 * @subpackage  libraries.jpog
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
class Jpog_Core extends Jpog_Abstract
{     
  /**
   * @var string Path to the params file.
   **/
  var $paramsFilepath;
  
  /**
   * @var array Holds the params in an array. 
   *  These are the are the params ready to be saved and or generate form the xml/yaml params fle etc.
   **/ 
  var $params = array(); 
  
  /**
   * @var array Holds the parameter objects.
   **/ 
  var $paramObjs = array();
  
  /**
   * Jpog Constructor
   *
   * @param object $config An configuration object. Should take the form of a associative/hash array. 
   * $config['storage'] = 'yaml'
   * Options:
   * @todo put options.
   * @return void
   **/
  function __construct($config)
  {    
    $splex = getSplexInstance();      
    $splex->loader->load_include('jpog_storage_base.php');
    if($config['storage'] == 'yaml') { 
      $storagemech = $splex->loader->load_class('jpog_storage_yaml', 'Jpog_Storage_Yaml');
    } 
    parent::addExt($storagemech);          
    
    # Due to scope the jpog storage class doesn't have true acess to these vars when it first laods 
    # so must set them to themselves
    $this->paramObjs = $splex->jpog->paramObjs; 
    $this->params    = $splex->jpog->params;
  } 
  
// ------------------------------------------------------------------------

  /**
   * Creates a new param object.
   * 
   * @param string $name Name of the parem.
   * @param string $widgetType Type of widget that this param is.
   * @param array  $attributes An array of attributes.
   * @param array  $settings An array of settings.
   * @param mixed  $value Saved Value of the Param. 
   * @return object
   **/
  public function create($name, $widgetType = null, $attributes = null, $settings = null, $value = null)
  { 
    $splex = getSplexInstance();
    $param = new Jpog_Param($name, null, $widgetType, $attributes, $settings, $value);   
    $this->paramObjs[$name] = $param;  
    return $param;
  }    
  
// ------------------------------------------------------------------------

  /**
   * Prints a Param or params.        
   *
   * @param mixed $params The param or params to print. Use just the names only not param objects.
   * @return mixed
   **/
  public function render($params)
  {   
    $splex = getSplexInstance();        

    if(!is_array($params)) {
      $params = array($params);
    }

    foreach($params as $key)
    { 
      if(array_key_exists($key, $splex->jpog->paramObjs))
      {     
        if(!is_null($splex->jpog->paramObjs[$key]->widget))      
          $widget = $splex->jpog->paramObjs[$key]->widget;     
        else
        {
          $type      = $splex->jpog->paramObjs[$key]->widgetType;    
          $param     = $splex->jpog->paramObjs[$key];  
          $paramName = $key;
          $widget    = $splex->muwt->create($type, $param, $paramName);
        }
        echo $widget->render(); 
      } 
    }
  }  
  
// ------------------------------------------------------------------------     

  /**
   * Gets a specific param.
   *  
   * @param mixed $paramName The param to get.
   * @param bool $asArray Whether or not to return the array/original yaml parsed objects.
   * @return mixed
   **/      
  public function get($paramName, $asArray = false)
  {  
    if($asArray == true)
      return $this->params[$paramName];
    else
      return $this->paramObjs[$paramName];
  }  
  
// ------------------------------------------------------------------------     

  /**
   * Gets the value for a specific param.
   *  
   * @param mixed $params The param to get.
   * @return mixed
   **/      
  public function getValue($paramName)
  {  
    return $this->paramObjs[$paramName]->value;
  }
  
// ------------------------------------------------------------------------     

  /**
   * Gets params of a specific type.
   *  
   * @param string $type The type of params to get. 
   * @param bool $asArray Whether or not to return the array/original yaml parsed objects.
   *
   * @return array $result Array of parameter objects.
   **/      
  public function getByType($type, $asArray = false)
  {          
    $result = array();     
    
    if($asArray == true)
    {
      foreach($this->params as $key => $param) {
        if($param['type'] == $type)  
          $result[$key] = $param;
      }
    }
    else
    {
      foreach($this->paramObjs as $key => $param) {
        if($param->widgetType == $type)  
          $result[$key] = $param;
      } 
    }
    
    return $result;
  }  
}