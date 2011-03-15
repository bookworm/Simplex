<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Jpog parameter. This is a parameter! 
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
class Jpog_Param
{   
  /**
   * @var string Name of the param
   **/ 
  var $name;  

  /**
   * @var string Type of widget that this param is.          
   **/
  var $widgetType;   

  /**
   * @var array Param attributes   
   **/
  var $attributes = array();  
  
  /**
   * @var array Param settings.        
   **/
  var $settings = array();        

  /**
   * @var mixed Saved Value of the Param.         
   **/
  var $value;     
  
  /**
   * @var object Holds the widget for this param.    
   **/
  var $widget;      
  
  var $noWidget = false;
   
  /**
   * Constructor.
   * 
   * @param mixed  $name Name of the param                
   * @param array  $param Param array. If this is set all the following args are ignored.
   * @param string $widgetType Type of widget that this param is.
   * @param array  $attributes An array of attributes.     
   * @param array  $settings An array of settings.  
   * @param mixed  $value Saved Value of the Param. 
   * @return void
   **/ 
  public function __construct($name, $param = null, $widgetType = null, $attributes = null, $settings = null, $value = null)
  {       
    $splex = getSplexInstance(); 
      
    $this->name       = $name;     
    
    if(is_null($param))   
    {
      $this->widgetType = $widgetType;
      $this->attributes = $attributes;  
      $this->settings   = $settings;
      $this->value      = $value;       
    } 
    else
    {
      $this->widgetType = $param['type'];
      $this->attributes = $param['attributes'];  
      $this->settings   = $param['settings'];
      $this->value      = $param['value'];  
       
      if(isset($param['noWidget']))
        $this->noWidget  = $param['noWidget'];  
    }  
    
    if($this->widgetType == 'noWidget')
      $this->noWidget = true; 
          
    if($this->widgetType != null && $this->noWidget == false)
    {  
      $this->widget = $splex->muwt->create($this);
    }  
  }
  
// ------------------------------------------------------------------------

  /**
   * Sets the value of an attribute or attributes.
   * 
   * @param mixed $attributes This can either be the name of the attribute to set or an array of attributes.  
   * @param mixed $value The value to set the attribute to if only passing one attribute.
   * @return void
   **/
  public function set($attributes, $value = null)
  { 
    if(!is_array($attributes))
      $attributes = array($attributes => $value);    
      
    foreach($attributes as $k => $v) {
      $this->attributes[$k] = $v;
    }
  } 
  
  
// -----------------------------------------------------------------------

  /**
   * Sets the value of an setting or settings.
   * 
   * @param mixed $settings This can either be the name of the setting to set or an array of settings.  
   * @param mixed $value The value to set the setting to if only passing one setting.
   * @return void
   **/
  public function setSetting($settings, $value = null)
  { 
    if(!is_array($settings))
      $settings = array($settings => $value);    

    foreach($settings as $k => $v) {
      $this->settings[$k] = $v;
    }
  }
  
// ------------------------------------------------------------------------

  /**
   * Renders the param.
   * 
   * @return string
   **/
  public function render()
  {  
    $splex = getSplexInstance();   
    
    return $splex->jpog->render($this->name);
  }    
   
// ------------------------------------------------------------------------
          
  /**
   * Saves the param.
   * 
   * @param bool $saveNow Whether or not to dump the param immediately to the jpog storage.
   *  Jpog will eventually save the param on its own but there are cases where you might need to acess the param from the 
   *  the saved file or db immediately.   
   * @param bool $passToWidget Whether or not to hand off the saving of a value to widgets save method if it has one.
   * In some cases a widgets save method will call saveParams itself and of course it wont want to hit 
   * its own save method again.
   * @return void
   **/
  public function save($saveNow = false, $passToWidget = true)
  { 
    $splex = getSplexInstance();  
   
    if($this->noWidget == false)
    { 
      if(empty($this->widget))
        $this->widget = $splex->muwt->create($this);
        
      if($splex->muwt->hasSave($this) AND $passToWidget == true)
        $this->value = $this->widget->save($this);
    }
                              
    $saveArray = (array) $this;
    $saveArray['type'] = $this->widgetType; 
    unset($saveArray['widget']); 
    unset($saveArray['widgetType']); 
    
    $splex->jpog->params[$this->name] = $saveArray;   
    
    unset($saveArray);  
    if($saveNow == true)
      $splex->jpog->dump(); 
  }   

// ------------------------------------------------------------------------

  /**
   * Alias to save method
   * 
   * @alias Jpog_Param::save()
   * @return void
   **/
  public function saveNow()
  {    
    $this->save(true);
  }     
}      