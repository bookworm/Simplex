<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * The Simplex Core Class.
 *    
 * @package     simplex
 * @subpackage  core
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */  
class Simplex Extends Splex_Base
{    
  /**
   * @var mixed Reference Variable for the Joomla! template
   **/
  var $template;   
   
  /**
   * @var string Name of the template.
   **/
  var $templateName;

  /**
   * @var string Path to the template.
   **/
  var $templatePath;

  /**
   * @var string Path to the 
   **/
  var $directoryPath;  
  
  /**
   * @var string Name of custom Simplex config file.
   **/
  var $MY_SPLEX_CONFIG = null;    
  
  /**
   * @var bool Minimal operation mode doesn't load any frontend stuff js/css etc.
   **/
  var $minimumMode = false;
  
  /**
   * @var bool Special mode that doesn't load js/css etc. Currently doesn't differ form minimumMode but will in the future.
   **/
  var $ajaxMode = false; 
  
  /**
   * @var string Configuration filename for minimum mode.
   **/
  var $minimumModeConf = 'simplex_min_config.php';
  
  /**
   * @var string Configuration filename for ajax mode.
   **/
  var $ajaxModeConf = 'simplex_ajax_config.php';             
  
  var $jpog;
  var $muwt;
  
  /**
   * @var string Simplex Version Number
   **/ 
  const VERSION = "0.7 alpha";  

  /**
   * Simplex Constructor
   *
   * @param object $tmpl The Joomla template object passed by reference   
   * @param string $configFilename Optional name of a Simplex (not template config) config file load.
   * @return void
   **/
  function __construct(&$tmpl = null, $configFilename = null)
  {  
    /**
     * This is a reference to the Joomla! template object,
     * so it is available inside the $splex object. 
     */      
    $this->template = $tmpl;
    $this->MY_SPLEX_CONFIG = $configFilename;         
    global $mainframe;
    $this->templateName = $mainframe->getTemplate();

    // Initialize Simplex
    parent::Base();        
    $this->init();
  }  

// ------------------------------------------------------------------------

  /**
   * Initializes Simplex.         
   *
   * @note Instantiates all the classes, loads user files, loads helper etc etc
   *
   * @return void
   **/
  public function init() 
  {  
    $loaderpath   = FRAMEWORKPATH . DS . 'core' . DS . 'loader.php';
    $this->loader = splex_loadClass('Splex_Loader', $loaderpath);    
    $this->loadConfig(); 
    $this->loadSplex(); 
    $this->checkWriteIssues();      
    $this->loadMedia(); 
    $this->loadMuwt();  
    $this->loadStruct();     
  }   

// ------------------------------------------------------------------------

  /**
   * Loads Config Files. 
   *
   * @note Instantiates Simplex config into $splex->config and template config into $splex->tconfig object.
   *
   * @return void
   **/
  private function loadConfig()    
  {   
    $this->config = $this->loader->load_class('splex_config', 'Splex_Config', 'php', true, $this->MY_SPLEX_CONFIG);    
  }    

// ------------------------------------------------------------------------

  /**
   * Loads up Simplex libraries, helpers etc.
   *
   * @return void
   **/
  private function loadSplex()
  {    
    // Load The Helpers. Everyone needs a helper at sometime or another, except me, I can do verything on my own.   
    foreach($this->config->enabledHelpers as $key => $value) {   
      $prefix       = "helper";
      $filename     = $prefix . '_' . $value;  
      $this->loader->load_helper($filename); 
    }       
    
    // Load Custom User Functions   
    foreach($this->config->customTemplateFiles as $key => $value) {       
       $this->loader->load_helper($value); 
    }
    
    // Load Libraries. Browser class, media class etc. 
    foreach($this->config->enabledLibraries as $val) 
    {               
      $prefix       = "splex";
      $filename     = $prefix . '_' . $val;
      $classname    = capitalizeWords($filename, '_');     
      $this->{$val} = $this->loader->load_class($filename, $classname); 
    }
  }  

// ------------------------------------------------------------------------

  /**
   * Checks for issues with write permissions.
   *
   * @return void
   **/
  public function checkWriteIssues()
  { 
    if($this->config->muwtEnabled == true) 
    { 
      if($this->config->jpogStorageMech == 'yaml' OR $this->config->jpogStorageMech == 'xml')
      {                                                                           
        $tparamsfilename = $this->templateName . '_';
        if($this->config->jpogStorageMech     == 'yaml') $tparamsfilename = $tparamsfilename . 'params' . '.yaml';
        elseif($this->config->jpogStorageMech == 'xml')  $tparamsfilename = $tparamsfilename . 'params' . '.xml'; 
        
        $tparamspath = $this->loader->getFilePath($tparamsfilename);   
        if(!is_writable($tparamspath) AND isAdmin()) {   
          $alert     = 'Template Params File unwritable please check permissions of ' . '</br>' . $tparamspath; 
          flashMessage($alert);   
        }   
      }
    }
  } 
    
// ------------------------------------------------------------------------

  /**
   * Loads up MUWT.
   *
   * @return void
   **/
  public function loadMuwt()
  { 
    if($this->config->muwtEnabled == true) 
    {   
      $jpogconfig             = array();
      $jpogconfig['storage']  = $this->config->jpogStorageMech;           
      
      $muwtconfig             = array();  
      $muwtconfig['platform'] = 'joomla';           
      
      $this->muwt             = $this->loader->load_class('muwt_core', 'Muwt_Core', 'php', true, $muwtconfig);   
      $this->jpog             = $this->loader->load_class('jpog_core', 'Jpog_Core', 'php', true, $jpogconfig);     
    }
  }   
  
// ------------------------------------------------------------------------

  /**
   * Loads up Struct.
   *
   * @return void
   **/    
  public function loadStruct()
  {
    if($this->config->structEnabled == true) 
      $this->struct = $this->loader->load_class('splex_struct', 'Splex_Struct', 'php', true);   
  }
  
// ------------------------------------------------------------------------

  /**
   * Loads up jquery and other media.
   *
   * @return void
   **/
  private function loadMedia() 
  {     
    if(checkHead('jquery', 'scripts') == false AND $this->minimumMode == false AND $this->config->jquery != false)
    {  
      $this->loader->load_js('jquery.splex.min');
      ob_start();
      ?>
jQuery.noConflict();
      <?php 
      $document                = JFactory::getDocument();  
      $jQueryNoConflictDeclare = ob_get_clean();
      $document->addScriptDeclaration($jQueryNoConflictDeclare); 
    }
  }      
}