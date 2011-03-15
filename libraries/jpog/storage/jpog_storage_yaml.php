<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Implements a YAML interface for the Jpog paramater storage class.
 *  
 * @package     simplex
 * @subpackage  libraries.jpog.storage
 * @version     2.0 pre-alpha. 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
class Jpog_Storage_Yaml extends Jpog_Storage 
{    
  /**
   * The constructor function.
   * 
   * @note What the constructor function of storage classes normally does is;
   *  1. Load the parameters from a file or database.
   *  2. Build the parameters as an array.
   *  3. Put that array into the $splex->jpog->params variable.
   *
   * @return void
   **/
  function __construct()
  {
    $splex = getSplexInstance(); 
         
    $splex->loader->load_include('sfYaml.php'); 
    $this->paramsFilepath = $splex->loader->getFilePath($splex->templateName .'_params.yaml', 'files');     
      
    if(empty($splex->jpog->params))
      $splex->jpog->params = sfYaml::load($this->paramsFilepath);      
    else
      $splex->jpog->params = $splex->jpog->params + sfYaml::load($this->paramsFilepath); 
               
    foreach($splex->jpog->params as $k => $v) {   
      $param = new Jpog_Param($k, $v); 
      $splex->jpog->paramObjs[$k] = $param;         
    }  
  }          

// ------------------------------------------------------------------------     

  /**
   * Dumps a modified params array to the storage system. In this case yaml.
   *
   * @param array $saveDump A modified $paramsdump ready for dumping
   * @return void
   **/
  public function dump($saveDump = null)
  {                   
    $splex = getSplexInstance();
    jimport('joomla.filesystem.file');   
    
    if($saveDump == null) 
    {
      $yamlstring = sfYaml::dump($splex->jpog->params);  
      JFile::write($this->paramsFilepath, $yamlstring); 
      unset($yamlstring);
    }
    else
    {
      $yamlstring = sfYaml::dump($saveDump);  
      JFile::write($this->paramsFilepath, $yamlstring); 
      unset($yamlstring);
    }
  }  
}