<?php   

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Media Class. Compresses and concats JS/CSS. 
 *
 * @note Based on RokGzipper by RocketTheme http://www.rocketTheme.com  
 * You might ask, why do we need this here? Cant we just use the RokGzipper plugin?
 * The problem is this: we create and add JS after the plugin events so we need to call the class 
 * after we've added our files to the document head. Joomal! really needs a new plugin event afterTemplate or something.
 *     
 * @package     simplex
 * @subpackage  libraries
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 *      
 * @author      RocketTheme http://www.rockettheme.com
 * @copyright   Copyright (C) 2007 - 2010 RocketTheme, LLC      
 *
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */                 
class Splex_Media  
{    
  var $_ignores = array();
   
  // I'll just leave this here.
  function __construct() { } 
  
// ------------------------------------------------------------------------
  
  /**
   * Class Ignition function. Causes everything to happen. 
   *
   * @note We need to be able to call the media Init function after class instantiation.
   * Thats why we place this code here instead of in the constructor function.
   *
   * @return void
   **/  
  public function initMediaGZIP()
  {
    // Setup The Instances 
    $mainframe = JFactory::getApplication();    
    $splex = getSplexInstance();     

    // Settings 
    $this->gzipCSS          = $splex->config->gzipCSS;
    $this->gzipJS           = $splex->config->gzipJS;
    $this->removeDuplicates = $splex->config->removeDuplicatesMedia; 
    $this->conflictResolve  = $splex->config->mediaConflictResolve;  
    $this->ignoredFiles     = $splex->config->ignoredFiles;

    // Setup The Vars
    $this->uri          = JURI::getInstance();
    $this->jPath        = JURI::Root(true);  
    $this->jDomain      = $this->uri->toString( array('scheme', 'host', 'port'));  

    if (isAdmin()) return;
    $this->_getIgnores();  

    // This Cleans Up The Head Before We Parse It. It attempts to prevent JS conflicts by; 
    // making sure mootools is always loaded first and only loading one copy of jQuery.  
    if($this->conflictResolve == 'true') {
      $this->_cleanHeadData();        
    }

    if($this->removeDuplicates == 'true') {
      $this->_removeDupes('styles');     
      $this->_removeDupes('scripts');   
    }     

    if($this->gzipCSS == 'true') {
      $this->_processCSSFiles(); 
    }     

    if($this->gzipJS == 'true') {
      $this->_processJSFiles(); 
    }  

    $this->_setHeadData(); 
  }       

// ------------------------------------------------------------------------

  /**
   * Clean The Head Data
   **/  
  public function _cleanHeadData()  
  {
    $doc   = JFactory::getDocument();  
    $splex = getSplexInstance();   
    jimport('joomla.filesystem.file');

    $headData = $doc->getHeadData();        
    $scripts  = $headData['scripts'];   
    $scripts  =  $this->_cleanFileLinks($scripts);             
          
    $newscripts = array();
     
    // Move Mootools To The Top
    foreach($scripts as $key => $script)
    {   
      $dir = $scripts[$key][0];   
      $filename = $scripts[$key][1];   
      $details =  $scripts[$key][2];
      $detailspath = $dir.DS.$filename; 
      if(stristr($filename, 'mootools') == true) {
        $newscripts["$details"] = 'text/javascript'; 
        unset($scripts[$key]);
      } 
    }        
    
    // Move jQuery Below Mootools  
    if(checkHead('jquery', 'scripts') == true) 
    {  
      foreach($scripts as $key => $script)
      {   
        $dir = $scripts[$key][0];   
        $filename = $scripts[$key][1];   
        $details =  $scripts[$key][2];
        $detailspath = $dir.DS.$filename; 
        if(stristr($filename, 'jquery') == true) {
          $newscripts["$details"] = 'text/javascript'; 
          unset($scripts[$key]);  
        } 
      }  
    }
    
    // Loop Through Again And Add The Rest
    foreach($scripts as $key => $script)
    {   
      $dir = $scripts[$key][0];   
      $filename = $scripts[$key][1];   
      $details =  $scripts[$key][2];
      $detailspath = $dir.DS.$filename; 
      $newscripts["$details"] = 'text/javascript'; 
    }    

    $headData['scripts'] = $newscripts;     
    $doc->setHeadData($headData);
  }
  
// ------------------------------------------------------------------------

  /**
   * Removes duplicate files. 
   *
   * @note This will nearly double processing time, so its very important that you have proper cache settings.
   * they should be high and you should intend to rarely re-generate css/js.       
   * 
   * @param string $type Type of files to remove duplicates from.
   *                     Options are: 'styles' and ;scripts'
   * @return void
   **/      
  public function _removeDupes($type)  
  {   
    jimport('joomla.filesystem.file');   

    $doc      = JFactory::getDocument();  
    $splex    = getSplexInstance();          
    $headData = $doc->getHeadData();        

    if($type = 'styles')
      $files = $headData['styleSheets'];
    else
      $files = $headData['scripts']; 
      
    // Original array of files.
    $files      =  $this->_cleanFileLinks($files);   

    // Files with a md5 key.
    $filesMD5   = array();   

    // Finalized files array that we will set the head data to.
    $filesClean = array();

    foreach ($files as $key => $file) 
    {  
      // Setup Paths and FileNames
      $dir      = $files[$key][0];   
      $filename = $files[$key][1];   
      $details  = $files[$key][2]; 

      $detailspath = $dir.DS.$filename;   
      if (JFile::exists($detailspath)) 
      {  
        $filecontent = JFile::read($detailspath);  
        $filesMD5[$key][0] = $dir;  
        $filesMD5[$key][1] = $filename; 
        $filesMD5[$key][2] = $details;
        $filesMD5[$key][3] = md5($filecontent); 
      }   
    }      
    $filesCleanMD5 = uniqueArray($filesMD5);

    foreach($filesCleanMD5 as $key => $file) {    
      $details = $filesCleanMD5[$key][2];
      $filesClean["$details"] = 'text/javascript';  
    }     

    if($type = 'styles')  
      $headData['styleSheets'] = $filesClean;
    else 
      $headData['scripts'] = $filesClean; 

    $doc->setHeadData($headData);   
  }  
  
// ------------------------------------------------------------------------

  /**
   * Pushes Any Files That Should Not Be Processed into an Ignored Files array.         
   *
   * @note Ignored files are defined in the simplex configuration file.
   * config/Core_Config.php
   *
   * @return void
   **/
  public function _getIgnores()
  {  
    jimport('joomla.filesystem.file');
    $splex        = getSplexInstance();     

    $ignoredFiles = $this->ignoredFiles;  

    if (!empty($ignoredFiles))
    {
      foreach($ignoredFiles as $ignoredFile) 
      {
        $filepath = $this->_getFilePath($ignoredFile); 
        
        if (JFile::exists($filepath))
          $this->_ignores[$filepath] = $ignoredFile;
      }   
    } 
  }    
    
// ------------------------------------------------------------------------
  
  /**
   * Gets a File's Extension.
   *
   * @return string
   **/   
  function _getFileExtension($filepath)
  {
    preg_match('/[^?]*/', $filepath, $matches);
    $string = $matches[0];
    $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE); 

    // check if there is any extension
    if(count($pattern) == 1) return ""; 
    
    if(count($pattern) > 1)
    {
      $filenamepart = $pattern[count($pattern)-1][0];
      preg_match('/[^?]*/', $filenamepart, $matches);
      return $matches[0]; 
    }   
  } 
     
// ------------------------------------------------------------------------
 
  /**
   * Processes the CSS files
   *
   * @return void
   **/   
  public function _processCSSFiles()
  {   
    // Setup The Instances
    $splex          = getSplexInstance();
    $uri            = JURI::getInstance();  
    $doc            = JFactory::getDocument();

    // Get The StyleSheets
    $headData       = $doc->getHeadData();        
    $styleSheets    = $headData['styleSheets'];  

    // Config Options
    $cacheTime = $splex->config->mediaCacheTime;  
    $stripCSS  = true;   
              
    // Generate The StylesSheets File Array
    $styleSheetsClean = $this->_cleanFileLinks($styleSheets);          
    
    // We need to generate a unique hash for our file
    // Generated from all the filenames in the array    
    $md5sum = "";     
    foreach($styleSheetsClean as $files) 
    {
      $dir = $files[0];
      $filename = $files[1];
      $details = $files[2];

      $md5sum .= md5($filename);
      $detailspath = $dir.DS.$filename; 
    } 

    // Cache File Generation Code
    $cacheFilename = "css-".md5($md5sum).".php";
    $cacheFullpath = JPATH_CACHE.DS.$cacheFilename;    

    //see if file is stale
    if (JFile::exists($cacheFullpath))
      $diff = (time()-filectime($cacheFullpath));
    else
      $diff = $cacheTime+1;

    // Setup Cache Contet
    if($diff > $cacheTime)
    {
      $outfile = $this->_getOutHeader("css"); 
      foreach ($styleSheetsClean as $files) 
      {
        // Setup Paths and FileNames
        $dir = $files[0];
        $filename = $files[1];
        $details = $files[2];  
         
        // Options
        $options = array('currentDir' => $dir);
        if($stripCSS == 0) $options['preserveComments'] = false;

        $detailspath = $dir.DS.$filename;     
        
        if (JFile::exists($detailspath)) 
        {  
          $cssFileContent = JFile::read($detailspath);
          $cssFileContent = $splex->minify_css->minify($cssFileContent, $options);
          $outfile .= "\n\n/*** " . $filename . " ***/\n\n" . $cssFileContent;            
        }   
      }
      JFile::write($cacheFullpath, $outfile); 
    }    

    // Write The Caches Content To A File and Add To Head
    $cacheFileURL = $this->jPath .  '/cache/'.$cacheFilename;
    $doc->addStyleSheet($cacheFileURL);   
  }   
      
// ------------------------------------------------------------------------

  /**
   * Processes the JS files
   *
   * @return void
   **/   
  public function _processJSFiles()
  {   
    // Setup The Instances
    $splex          = getSplexInstance();
    $uri            = JURI::getInstance();  
    $doc            = JFactory::getDocument();

    // Get The Scripts Array
    $headData       = $doc->getHeadData();        
    $scripts        = $headData['scripts'];  

    // Config Options
    $cacheTime = $splex->config->mediaCacheTime;  
                
    // Generate The Scripts File Array
    $scriptsClean = $this->_cleanFileLinks($scripts);  

    // We need to generate a unique hash for our file
    // Generated from all the filenames in the array    
    $md5sum = "";     
    foreach($scriptsClean as $files) 
    {
      $dir = $files[0];
      $filename = $files[1];
      $details = $files[2];

      $md5sum .= md5($filename);
      $detailspath = $dir.DS.$filename; 
    } 

    // Cache File Generation Code
    $cacheFilename = "js-".md5($md5sum).".php";
    $cacheFullpath = JPATH_CACHE.DS.$cacheFilename;    

    //see if file is stale
    if (JFile::exists($cacheFullpath))
      $diff = (time()-filectime($cacheFullpath));
    else
      $diff = $cacheTime+1;

    if($diff > $cacheTime)
    {
      $outfile = $this->_getOutHeader("js"); 
      foreach ($scriptsClean as $files) 
      {
        $dir = $files[0];
        $filename = $files[1];
        $details = $files[2];

        $detailspath = $dir.DS.$filename;
        if (JFile::exists($detailspath)) {
          $jsFileContent = JFile::read($detailspath); 
          $jsFileContent = $splex->minify_js->minify($jsFileContent);   
          $outfile .= "\n\n/*** " . $filename . " ***/\n\n" . $jsFileContent;            
        } 
      }
      JFile::write($cacheFullpath,$outfile);
    }    

    $cacheFileURL = $this->jPath .  '/cache/'.$cacheFilename;
    $doc->addScript($cacheFileURL);   
  }       
        
// ------------------------------------------------------------------------

  /**
   * Cleans the File array of ignored files etc and 
   * adds a full file path to the returned array
   *
   * @param array $files The Array of styleSheets or scripts from headData
   * @return array
   **/
  public function _cleanFileLinks($files)
  {    
    $orderedFiles = array();   

    foreach ($files as $file => $tag) 
    {
      // strip query string if there is one
      if (strpos($file, '?') !== false)
        $file = substr($file, 0, strpos($file, '?'));

      $filepath = $this->_getFilePath($file);         
      if (!array_key_exists($filepath, $this->_ignores) && $this->_getFileExtension($filepath) != "php" && file_exists($filepath)) {
       $orderedFiles[] = array(dirname($filepath),basename($filepath),$file);
      } 
    }  

    return $orderedFiles;   
  }     

// ------------------------------------------------------------------------

  /**
   * Returns an Associative Array For the Files
   *
   * @param array $files The Array of styleSheets or Scripts from headData
   * @return array
   **/
  public function _assocArrLinks($files)
  {    
    foreach ($files as $key => $tag) 
    {
      $files[$key]['dir']       = $files[$key][0];   
      $files[$key]['filename']  = $files[$key][1];                      
      $files[$key]['details']   = $files[$key][2]; 

      unset($files[$key][0]);  
      unset($files[$key][1]); 
      unset($files[$key][2]);   
    } 

    return $files;   
  }  

// ------------------------------------------------------------------------

  /**
   * Returns an Associative Array For the Files
   *
   * @param array $files The Array of styleSheets or Scripts from headData
   * @return array
   **/
  public function _rebuildHeadData($files)
  {   
    $rebuiltFiles = array(); 

    foreach ($files as $key => $tag) {   
      $filename = $files[$key][2];                      
      $rebuiltFiles[$filename] = 'text/javascript'; 
    }  
    
    return $rebuiltFiles;   
  }   

// ------------------------------------------------------------------------
   
  /**
   * Returns The Full Path to a file
   *
   * @param string $url The path to a file relative to the Joomla! install dir
   * @return string
   **/
  public function _getFilePath($url) 
  {  
    if ($url && $this->jDomain && strpos($url,$this->jDomain)!==false) $url = str_replace($this->jDomain,"",$url);
    if ($url && $this->jPath && strpos($url,$this->jPath)!==false) $url = str_replace($this->jPath,"",$url);
    if (substr($url,0,1) != DS) $url = DS.$url;
    $filepath = JPATH_SITE.$url;       
    
    return $filepath;    
  }  
  
// ------------------------------------------------------------------------

  /**
   * Returns The GZIp OutPut Header
   *
   * @param string $type The type of header needed 
   * @return ob
   **/  
  public function _getOutHeader($type="css") 
  {
    if ($type=="css") {  
          $header='<?php 
ob_start ("ob_gzhandler");
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 ;
$ExpStr = "Expires: " . 
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
                ?>';
     } else {
          $header='<?php 
ob_start ("ob_gzhandler");
header("Content-type: application/x-javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 ;
$ExpStr = "Expires: " . 
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
              ?>';
     }        
        
    return $header;
  }   
  
// ------------------------------------------------------------------------

  /**
   * Populate Joomla! Head With the Compressed Stylesheet and Script links 
   *
   **/ 
  public function _setHeadData()  
  {
    $doc =& JFactory::getDocument();  
    $splex =& getSplexInstance();

    $headData = $doc->getHeadData();        
    if($this->gzipCSS == 'true') $styles  = $headData['styleSheets'];    
    if($this->gzipJS == 'true')  $scripts = $headData['scripts'];  

    if($this->gzipCSS == 'true')
    {
      // Remove The un-compressed Styles
      foreach($styles as $key => $style) {   
        if(!strpos($key, 'php')) { unset($styles[$key]); }
      }     
    } 

    if($this->gzipJS == 'true') 
    {
      // Remove The un-compressed Scripts
      foreach($scripts as $key => $script) {   
        if(!strpos($key, 'php')) unset($scripts[$key]);
      }      
    }  

    // Set The HeadData Arrays To the Now Clean Arrays
    if($this->gzipCSS == 'true') $headData['styleSheets'] = $styles;  
    if($this->gzipJS == 'true')  $headData['scripts']     = $scripts;  
       
    $doc->setHeadData($headData);
  }         
}