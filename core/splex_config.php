<?php     

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Simplex Configuration Handling Class.
 *
 * @package     simplex
 * @subpackage  core
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */    
class Splex_Config
{
  /**
   * @var mixed Contains configuration vars. 
   * @todo Add minimum config.
   **/
  var $configVars = array();     
  
  /**
   * @var mixed This will hold an xml or yaml config object.
   **/
  var $configParsed = null;     
  
  /**
   * Config Constructor     
   *
   * @param $options array
   *   string  ['configFilename'] Optional name of a Simplex (not template config) config file load.   
   *   boolean ['core']           Are we loading a core config object?
   * @return void
   **/
  public function __construct($options = array()) 
  { 
    $splex = getSplexInstance();          
    
    if(isset($options['configFilename'])) 
      $configFilename = $options['configFilename'];  
    else
      $configFilename = null;
    if(isset($options['core']))
      $core = $options['core'];       
    else 
      $core = true;
    
    // If We Have Simplex installed as a plugin we will over-ride some of the settings with params from the plugin.   
    if($core == true)       
    {              
      $this->setConfigDefaults();
      
      if(defined('SIMPLEX_LOADED_AS_PLUGIN'))
        $this->setPluginConfig();
      else
      {   
        if($splex->minimumMode) 
          $configFilename = $splex->minimumModeConf;  
        elseif($splex->ajaxMode) 
          $configFilename = $splex->ajaxModeConf;
        elseif($configFilename == null)
          $configFilename = 'simplex_config.php';
      }  
    }
    if(strpos($configFilename, 'php'))  
      $this->loadConfig_Class($configFilename);  
    elseif(strpos($configFilename, 'yaml')) 
      $this->loadConfig_Yaml($configFilename);
  } 
    
// ------------------------------------------------------------------------

  /**
   * Sets defaults.
   *
   * @return void
   **/
  private function setConfigDefaults()
  {
    $this->configVars['moduleTools']           = false;
    $this->configVars['muwtEnabled']           = false;  
    $this->configVars['structEnabled']         = false;
    $this->configVars['jpogstoragemech']       = 'yaml';
    $this->configVars['mediaCacheTime']        = 60;
    $this->configVars['gzipCSS']               = true;
    $this->configVars['gzipJS']                = true;
    $this->configVars['mediaConflictResolve']  = false;
  }  
  
// ------------------------------------------------------------------------
          
  /**
   * Overloading get.
   *
   * @note Pulls a var from the configVars array.
   *
   * @return mixed
   **/
  public function __get($name)
  {    
    return $this->configVars[$name];
  }     
   
// ------------------------------------------------------------------------

  /**
   * Overloading set.
   *
   * @note Sets a var in the configVars array.
   *
   * @return mixed
   **/  
  public function __set($name, $value)
  {  
    return $this->configVars[$name] = $value;
  }   
    
// ------------------------------------------------------------------------

  /**
   * Takes config parameters from the Simplex plugin and sets the relevant config vars to their values.   
   *
   * @return void
   **/  
  public function setPluginConfig()
  {
    $db            = JFactory::getDBO();   
    $tableName     = $db->nameQuote('#__plugins'); 
    $elementColumn = $db->nameQuote('element');  
    $paramsColumn  = $db->nameQuote('params');     
    
    $query = "SELECT $elementColumn, $paramsColumn " 
           . "FROM  $tableName " 
           . "WHERE $elementColumn = 'simplex' ";   
    $db->setQuery($query);
    $row = $db->loadRow();
        
    $paramsdata = $row[1];    
    $paramsdefs = JPATH_SITE . DS . 'plugins' . DS . 'system' . DS . 'simplex.xml';
    $params     = new JParameter($paramsdata, $paramsdefs );

    $this->configVars['moduleTools']           = (bool)   $params->get('moduletools'); 
    $this->configVars['muwtEnabled']           = (bool)   $params->get('muwt');  
    $this->configVars['jpogstoragemech']       =          $params->get('jpogstoragemech'); 
    $this->configVars['mediaCacheTime']        = (int)    $params->get('cacheTime');  
    $this->configVars['gzipCSS']               = (bool)   $params->get('gzipCSS');
    $this->configVars['gzipJS']                = (bool)   $params->get('gzipJS'); 
    $this->configVars['mediaConflictResolve']  = (bool)   $params->get('mediaConflictResolve');
  }
  
// ------------------------------------------------------------------------
  
  /**
   * Gets a config var value using a dot path.
   *  
   * @param string $path dot-noted key-path string: foo.bar.baz
   * @return mixed
   **/
  public function get($path)
  {    
    $keys  = explode('.', $path);   
    $last  = count($keys) - 1;
    $array = $this->configVars; 
    
    foreach($keys as $i => $key)
    {  
      if(!$i == $last) {
        $array = $array[$key];
      }
      else {       
        return $array[$key];
      } 
    }
  }   
  
// ------------------------------------------------------------------------

  /**
   * Sets a config var value using a dot path.
   *  
   * @param string $path dot-noted key-path string: foo.bar.baz   
   * @param mixed $value Value to set.
   * @return mixed
   **/  
  public function set($path, $value)
  {
    $keys  = explode('.', $path);   
    $last  = count($keys) - 1;
    $array =& $this->configVars;       
    
    foreach($keys as $i => $key)
    {  
      if(!$i == $last) {
        $array =& $array[$key];
      }
      else {       
        $array[$key] = $value;
      } 
    }
  }
  
// ------------------------------------------------------------------------

  /**
   * Loads php class and its vars into the config vars array.
   *  
   * @param string $configFilename Name of the Simplex (not template config) config file load.     
   * @return void
   **/  
  public function loadConfig_Class($configFilename = null)
  {      
    $splex            = getSplexInstance();
    $filename         = str_replace('.php', '', $configFilename);
    $configClass      = $splex->loader->load_class($filename, capitalizeWords($filename, '_'));     
    $this->configVars = array_merge($this->configVars, get_object_vars($configClass));
  }   
  
// ------------------------------------------------------------------------

  /**
   * Loads yaml config file into the config vars array.
   *  
   * @param string $configFilename Name of the Simplex (not template config) config file load.     
   * @return void   
   **/  
  public function loadConfig_Yaml($configFilename = null)   
  {
    $splex = getSplexInstance();      
    $splex->loader->load_include('sfYaml.php'); 
    $this->configFilepath = $splex->loader->getFilePath($configFilename, 'files');
    $this->configVars = array_merge($this->configVars, sfYaml::load($this->configFilepath));
  }
}