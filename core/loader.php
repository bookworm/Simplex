<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );           
include_once 'core_helpers.php';  
       
// ------------------------------------------------------------------------

/**
 * Handles File Loading.
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
class Splex_Loader
{      
  /**
   * @var array Array weights to assign to files. This how we determine what file should be loaded. 
   * Larger weight equals higher priority.
   **/ 
  var $weights = array
  (
    'simplex'        => '1',
    'child_themes'   => '2',
    'template_files' => '3',
    'subfolder'      => '4' 
  );  

  /**
   * @var array Folders to exclude from the files array.     
   * @note One entry needs to be in array even its blank. i.e ' '
   **/    
  var $excludedfolders = array 
  ( 
    '/images'
  );  

  /**
   * @var array An array to hold all the files. To handle full sub-theming capabilities
   * we scan through the directories and add all the files to a files array.
   **/ 
  var $files;      

  /**
   * @var array Holds files of the PHP type.
   **/ 
  var $phpfiles;  

  /**
   * @var array Holds files of the css type.
   **/ 
  var $cssfiles;   
   
  /**
   * @var array Holds files of the js type.
   **/ 
  var $jsfiles;   
             
  /**
   * Constructor.
   *
   * @return void  
   * @see Splex_Loader::buildFilesArray()
   **/
  function __construct()
  {
    $this->buildFilesArray();
  }

// ------------------------------------------------------------------------

  /**
   * Creates the files array.
   *
   * @return void   
   * @see Splex_Loader::getFilesByType()   
   * @see Splex_Loader::genFileWeights() 
   * @see getDirFileInfo()  
   * @todo Might need some memory cleaning stuff here.
   **/
  private function buildFilesArray() 
  {   
    // Setup some objects and declare the needed paths.
    global $mainframe;
    $templatename    = $mainframe->getTemplate();     
    $templatesdir    = JPATH_SITE . DS . 'templates'; 
    $thistemplatedir = JPATH_SITE . DS . 'templates' . DS . $templatename;  
  
    // Build File Array
    $this->files     = getDirFileInfo($thistemplatedir, false, $this->excludedfolders);  
     
    if(defined('SIMPLEX_LOADED_AS_PLUGIN')) {  
      $files2        = getDirFileInfo(FRAMEWORKPATH, false, $this->excludedfolders); 
      $this->files   = array_merge($this->files, $files2);  
      unset($files2);
    }  
    
    // Push any files in a sub-theme/childtheme into the files array.
    $templatesdirlist = scandir($templatesdir);
    $tnamecheck       = $templatename . '_' . 'child';  
    foreach($templatesdirlist as $key => $value)
    {  
      if(strpos($value, $tnamecheck) !== false) 
      {   
        $dir          = $templatesdir . DS . $value; 
        $newfiles     = getDirFileInfo($dir, false, $this->excludedfolders);
        $this->files  = array_merge($this->files, $newfiles);  
        unset($newfiles);  
      }      
    } 
    
    $this->files = $this->genFileWeights($this->files);   
    
    // Create separate files arrays by type.
    $this->phpfiles   = $this->getFilesByType($this->files, 'php');  
    $this->cssfiles   = $this->getFilesByType($this->files, 'css');     
    $this->jsfiles    = $this->getFilesByType($this->files, 'js');  
  }         

// ------------------------------------------------------------------------

  /**
   * Builds a new files array with just files of the specified type.
   *
   * @param string $files The files array to build from
   * @param type   $type  The file type, uses just the ext for the moment.  
   * @return void 
   * @todo add optional mime type detection 
   * @see Splex_Loader::buildFilesArray()   
   **/
  public function getFilesByType($files, $type)
  {    
    $newfiles              = array();
    foreach($files as $key => $file) 
    {   
      $filename            = $key;
      if(strpos($filename, $type)) {
        $newfiles[$key]    = $file;
      }  
    }
    return $newfiles; 
  }       

// ------------------------------------------------------------------------

  /**
   * Generates a "weight" for each file.
   * 
   * @note Weights are defined in the $this->weights array.        
   * 
   * @note To support sub-theming/child-theming what we have to do is determine which copy of a file
   * should take priority. 
   * 
   * @param string $files The files array to generate the weights on.
   * @return void 
   * @see Splex_Loader::buildFilesArray()   
   **/
  private function genFileWeights($files)
  {  
    global $mainframe;
    $templatename = $mainframe->getTemplate(); 

    $splexdir     = $templatename . '/simplex';
    $childthemes  = $templatename . '/child_themes';
    $tfiles       = $templatename . '/template_files';
    $subtheme     = $templatename . '_/';     
  
    foreach($files as $key1 => $file) 
    {
      $filepaths  = $file['paths'];  
      foreach($filepaths as $key2 => $value) 
      {   
        $filepath = $key2;
        if(strpos($filepath, $splexdir)) {
          $files[$key1]['paths'][$key2]['weight'] = $this->weights['simplex'];
        }  
        elseif(strpos($filepath, $childthemes)) {
          $files[$key1]['paths'][$key2]['weight'] = $this->weights['simplex'];
        }
        elseif(strpos($filepath, $tfiles)) {
          $files[$key1]['paths'][$key2]['weight'] = $this->weights['template_files'];
        } 
        else {
          $files[$key1]['paths'][$key2]['weight'] = $this->weights['subfolder'];
        }   
      }    
    } 
    return $files;  
  }  

// ------------------------------------------------------------------------

  /**
   * Returns a full path to a file.
   * 
   * Usage:
   * {{{  
   *    Splex_Loader::getFilePath('config_doctypes.php')  
   *    $splex->loader->getFilePath('config_doctypes.php');    
   * }}}
   * 
   * @param string $filename  The name of the file to get the path for.    
   * @param string $array     The files array to get the path from. e.g phpfiles.
   * @return string   
   * @see Splex_Loader::loadFile()
   **/
  public function getFilePath($filename, $array = 'files')
  {  
    if(array_key_exists($filename, $this->{$array})) 
    {
      $paths   = $this->{$array}[$filename]['paths']; 
      $fattest = 0;   
      $path    = null;
      foreach($paths as $key => $value) 
      {
        if($fattest < $value) {
          $fattest = $value; 
          $path    = $key;     
        }     
      } 
      return $path . $filename;  
    } else { return null; } 
  }   
  
// ------------------------------------------------------------------------

  /**
   * Returns a relative (URL) path to a file.
   * 
   * Usage:
   * {{{  
   *    Splex_Loader::getFileURL('styles.css', 'cssfiles')  
   *    $splex->loader->getFileURL('styles.css', 'cssfiles');    
   * }}}
   * 
   * @param string $filename  The name of the file to get the path for.    
   * @param string $array     The files array to get the path from. e.g phpfiles.
   * @return string   
   * @see Splex_Loader::loadFile()
   **/
  public function getFileURL($filename, $array = 'files')
  {  
    $fullpath =  $this->getFilePath($filename, $array);
    $urlpath  =  str_replace(JPATH_SITE, '',  $fullpath);   
    if(strpos($urlpath, '\\') !== false) {
      $urlpath  =  str_replace('\\', '/', $urlpath);      
      $urlpath  = reduceDoubleSlashes($urlpath);
    }
    return JURI::base( true ) . $urlpath;
  }   

// ------------------------------------------------------------------------

  /**
   * Loads a file.
   *  
   * @note The default action will be to run a file_get_contents and then return 
   * the contents of the file.
   *
   * @note Separate filename and the type with a comma ",". 
   * Some types do not need a extension to be specified.  
   *
   * Usage:
   * {{{ 
   *    Load a txt file:
   *        $splex->loader->loadFile('bob.txt');
   *    Load a css file:
   *        $splex->loader->loadFile('bob', stylesheet); 
   *    Note how in the latter the extension is not needed.    
   *    Load a helper file: 
   *        $splex->loader->loadFile('bob', helper);     
   * }}}
   * 
   * @param string $filename The name of the file to load.
   * @param string $type     The type of file to load e.g column.
   *   The type will map to a function e.g Splex_Loader::load_column();
   * @param string $ext      The file ext e.g PHP.
   * @param mixed  $arg      An argument to pass to the load_funcname().
   *   when we are loading a class we often need to pass it some vars.     
   * @return void  
   * @see Splex_Loader::loadFileContents()
   **/ 
  public function loadFile($filename, $type = null, $ext = null, $arg = null)
  {  
    if($type) 
    {   
      $funcname    = 'load' . '_' . $type;
      if(method_exists($this, $funcname)) 
      {   
        if($ext) { 
          if($arg) { $this->{$funcname}($filename, $ext, $arg); } 
          else     { $this->{$funcname}($filename, $ext);      }   
        }
        elseif($arg) {
          if($ext) { $this->{$funcname}($filename, $ext, $arg); } 
          else     { $this->{$funcname}($filename, null, $arg); }   
        }
        else { $this->{$funcname}($filename); } 
      }  
      else { 
        return $this->loadFileContents($filename);   
      } 
    } 
    else { 
      return $this->loadFileContents($filename);   
    }
  }   
 
// ------------------------------------------------------------------------   

  /**
   * Returns the contents of a file.
   *
   * Usage:
   * {{{  
   *    Splex_Loader::loadFileContents('styles.css')  
   *    $splex->loader->loadFileContents('styles.css');    
   * }}}
   * 
   * @param string $filename The name of the file to load.
   * @return mixed  
   * @see Splex_Loader::getFilePath()
   **/   
  private function loadFileContents($filename)
  {
    if(array_key_exists($filename, $this->files)) 
    { 
      $path     = $this->getFilePath($filename, 'files');
      $contents = file_get_contents($path);
      return $contents;  
    }
    else {
      exit("Cant load non-existent file $filename");
    } 
  } 
 
// ------------------------------------------------------------------------   
  
  /**
   * Loads a helper file.
   * 
   * @param string $filename  The name of the file to load.    
   * @param string $ext       The file ext e.g PHP.
   * @return void   
   * @see Splex_Loader::load_include()    
   * @alias Splex_Loader::load_include()
   **/ 
  public function load_helper($filename, $ext = 'php')
  {    
    $filename = $filename . '.' . $ext;  
    $this->load_include($filename);      
  }    
 
// ------------------------------------------------------------------------   

  /**
   * Loads a column file.
   * 
   * @param string $filename  The name of the file to load.    
   * @param string $ext       The file ext e.g PHP.
   * @return void   
   * @see Splex_Loader::load_include()   
   * @alias Splex_Loader::load_include()
   **/  
  public function load_column($filename, $ext = 'php')
  {
    $filename = $filename . '.' . $ext;
    $this->load_include($filename);          
  } 

// ------------------------------------------------------------------------   

  /**
   *  Loads a layout file.
   * 
   * @param string $filename  The name of the file to load.    
   * @param string $ext       The file ext e.g PHP.
   * @return void  
   * @see Splex_Loader::load_include() 
   * @alias Splex_Loader::load_include() 
   **/  
  public function load_layout($filename, $ext = 'php')
  {
    $filename = $filename . '.' . $ext;
    $this->load_include($filename);      
  }   
 
// ------------------------------------------------------------------------   

  /**
   * Include files.
   * 
   * @param mixed $files       File or files to include.
   * @param bool  $includeOnce Whether or not to include_once() the file.  
   * @return void      
   * @see Splex_Loader::getFilePath()
   **/
  public function load_include($files, $includeOnce = true) 
  { 
    $splex      = getSplexInstance();  
    if (!is_array($files)) { $files = array($files); }
    foreach ($files as $file) 
    {    
      $filename = $file;  
      if(array_key_exists($filename, $this->phpfiles)) 
      { 
        $path   = $this->getFilePath($filename, 'phpfiles'); 
        if($includeOnce == true) { include_once($path); }
        else { include($path); }
      }
      else {
        exit("Cant include non-existent file $filename");
      }  
    }     
  }  
  
// ------------------------------------------------------------------------    

  /**
   * Loads a class.
   * 
   * @param string $filename     The name of the file to load.    
   * @param string $classname    The name of the class.
   * @param string $ext          The file ext e.g PHP.
   * @param bool   $instantiate  Allows you to load but not instantiate a class.       
   * @param mixed  $classArg     Arguments to pass to a class on instantiation.
   * @return object   
   * @see splex_loadClass()
   * @see Splex_Loader::getFilePath()
   **/
  public function load_class($filename, $classname, $ext = 'php', $instantiate = true, $classArg = null)
  {  
    $filename = $filename . '.' . $ext; 
    if(array_key_exists($filename, $this->phpfiles)) 
    {   
      $path   = $this->getFilePath($filename, 'phpfiles');   
      if($classArg == null) {
        return splex_loadClass($classname, $path, $instantiate);  
      }
      else {
        return splex_loadClass($classname, $path, $instantiate, $classArg);    
      }  
    }
    else {
      exit("Cant include non-existent file $filename");
    }   
  } 
  
// ------------------------------------------------------------------------   

  /**
   * Loads a JS file. Alias to loadMedia()  
   *
   * Usage:
   * {{{
   *    $splex->loader->load_js('jquery.min.js');
   * }}}
   *
   * @param mixed  $files Array List Files to add.     
   * @return void   
   * @see Splex_Loader::loadMedia()
   **/  
  public function load_js($files)
  {
    $this->loadMedia($files, 'scripts');           
  }  
  
// ------------------------------------------------------------------------   

  /**
   * Loads a css file. Alias to loadMedia()
   *
   * Usage:
   * {{{
   *    $splex->loader->load_css('styles.css');
   * }}}
   *
   * @param mixed  $files Array List Files to add.     
   * @return void   
   * @see Splex_Loader::loadMedia()
   **/  
  public function load_css($files)
  {
    $this->loadMedia($files, 'stylesheets');               
  } 
  
// ------------------------------------------------------------------------   

  /**
   * Loads a media file.
   *
   * Usage:
   * {{{
   *    $splex->loader->loadMedia('jquery.min.js', 'scripts);
   * }}}
   *
   * @param mixed  $files Array List Files to add.   
   * @param string $type  The type of media file to load. 
   *  options: scripts, stylesheets
   * @return void   
   * @see Splex_Loader::load_js()
   * @see Splex_Loader::load_css()  
   **/  
  public function loadMedia($files, $type)
  {
    $doc         = JFactory::getDocument();  
    
    if(!is_array($files))
      $files = array($files);       
    
    foreach($files as $filename)
    {
      if($type == 'scripts')
      { 
        $filename  = $filename . '.js';
        $fileurl   = $this->getFileURL($filename, 'jsfiles');
        $doc->addScript($fileurl);
      }  
      elseif($type == 'stylesheets')
      {
        $filename  = $filename . '.css'; 
        $fileurl   = $this->getFileURL($filename, 'cssfiles'); 
        $doc->addStyleSheet($fileurl);
      }
    }             
  } 
}