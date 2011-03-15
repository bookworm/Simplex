<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );   

// Load Simplex + Prequisites
$splex = getSplexInstance();      
$splex->loader->load_include('muwt_abstract.php');  
$splex->loader->load_include('muwt_widget.php'); 

// ------------------------------------------------------------------------

/**
 * Muwt. Markup Utilities and Widgets.
 *    
 * @package     simplex
 * @subpackage  libraries.muwt.core
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */ 
class Muwt_Core extends Muwt_Abstract 
{ 
  /**
   * Muwt Constructor.
   *
   * @param object $config An configuration object. Should take the form of a associative/hash array. 
   * $config['platform'] = 'joomla'
   * Options: joomla. Coming Soon, Wordpress and Drupal.
   * @return void
   **/
  function __construct($config)
  {    
    $splex  = getSplexInstance();      
    $splex->loader->load_include('muwt_document.php');     
    
    if($config['platform'] == 'joomla') { 
      $docmech = $splex->loader->load_class('muwt_document_joomla', 'Muwt_Document_Joomla');
    } 
    parent::addExt($docmech);     
  }
 
// ------------------------------------------------------------------------     

  /**
   * Creates a widget.
   *
   * @param array  $paramObj The parameter object from which to build the widget.   
   * @return void
   **/   
  public function create($paramObj)
  {
    $splex     = getSplexInstance();        
    
    $filename  = 'widget' . '_' . $paramObj->widgetType;    
    $classname = 'Muwt_Widget' . '_' . $paramObj->widgetType;    
    $widget    = $splex->loader->load_class($filename, $classname, 'php', true, $paramObj);   
    return $widget; 
  }  
  
// ------------------------------------------------------------------------     

  /**
   * Checks to see if a given widget type has a get method.
   *
   * @param string $widgetType The type of widget.
   * @return void
   **/   
  public function hasGet($paramObj) 
  {  
    $splex     = getSplexInstance(); 
    
    $filename  = 'widget' . '_' . $paramObj->widgetType;    
    $classname = 'Muwt_Widget' . '_' . $paramObj->widgetType;    
    $widget    = $splex->loader->load_class($filename, $classname, 'php', true, $paramObj);    
    if(method_exists($widget, 'get')) {
      return true;
    } 
    else {
      return false;
    }
  } 
  
// ------------------------------------------------------------------------     

  /**
   * Gets a parameters saved value using the widgets own get method.
   *
   * @param array  $paramObj The parameter object. Must have default and value attributes.
   * @return void
   **/   
  public function get($paramObj) 
  {    
    $splex     = getSplexInstance(); 
    
    $filename  = 'widget' . '_' . $paramObj->widgetType;    
    $classname = 'Muwt_Widget' . '_' . $paramObj->widgetType;    
    $widget    = $splex->loader->load_class($filename, $classname, 'php', true, $paramObj);
    return $widget->get();     
  }  
  
// ------------------------------------------------------------------------     

  /**
   * Checks to see if a given widget type has a save method.
   *
   * @param string $widgetType The type of widget.
   * @return void
   **/   
  public function hasSave($paramObj) 
  { 
    $splex     = getSplexInstance();     
    
    $filename  = 'widget' . '_' . $paramObj->widgetType;    
    $classname = 'Muwt_Widget' . '_' . $paramObj->widgetType;    
    $widget    = $splex->loader->load_class($filename, $classname, 'php', true, $paramObj);
    if(method_exists($widget, 'save')) {
      return true;
    } 
    else {
      return false;
    }
  }  

// ------------------------------------------------------------------------     

  /**
   * Saves a parameter's new value using the widgets own save method.
   *
   * @param string $widgetType The type of widget to create. 
   * @param array  $paramObj   The parameter object. Must have default and value attributes.
   * @return void
   **/   
  public function save($widgetType, $paramObj) 
  { 
    $splex     = getSplexInstance(); 
     
    $filename  = 'widget' . '_' . $widgetType;    
    $classname = 'Muwt_Widget' . '_' . $paramObj->widgetType;
    $widget    = $splex->loader->load_class($filename, $classname, 'php', true, $paramObj);   
    return $widget->save;
  } 
}