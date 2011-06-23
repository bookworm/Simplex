<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
                                                       
if(!defined('DS')) {
  define('DS', DIRECTORY_SEPARATOR);
}

// ------------------------------------------------------------------------

/**
 * Simplex File Helpers. Functions for working with files. 
 *
 * @package     simplex
 * @subpackage  helpers
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */

// ------------------------------------------------------------------------

/**
 * Get Filenames
 *
 * @note Reads the specified directory and builds an array containing the filenames.  
 * Any sub-folders contained within the specified path are read as well.
 *
 * Usage: 
 * {{{
 *    $filesArray = getFilenames('/bobs/user/home/dir');  
 * }}}      
 *
 * @param string $source_dir      Path to source
 * @param bool   $include_path    Whether to include the path as part of the filename
 * @param bool   $_recursion      Internal variable to determine recursion status - do not use in calls
 * @return array     
 */ 
if (!function_exists('getFilenames'))
{
  function getFilenames($source_dir, $include_path = false, $_recursion = false)
  {
    static $_filedata = array();
        
    if ($fp = @opendir($source_dir))
    {
      // reset the array and make sure $source_dir has a trailing slash on the initial call
      if ($_recursion === false) {
       $_filedata = array();
        $source_dir = rtrim(realpath($source_dir), DS).DS;
      }
      
      while (false !== ($file = readdir($fp)))
      {
        if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0) {
          getFilenames($source_dir.$file.DS, $include_path, true);
        }
        elseif (strncmp($file, '.', 1) !== 0) {
          $_filedata[] = ($include_path == true) ? $source_dir.$file : $file;
        }
      }
      return $_filedata;
    }
    else { return false; }
  }
}

// --------------------------------------------------------------------

/**
 * Get Directory File Information
 *
 * @note Reads the specified directory and builds an array containing the filenames,  
 * filesize, dates, and permissions
 *
 * @note Any sub-folders contained within the specified path are read as well.       
 *
 * Usage: 
 * {{{
 *    $filesArray = getDirFileInfo('/bobs/user/home/dir');  
 * }}}      
 *
 * @param  string  $source_dir      path to source
 * @param  bool    $include_path    whether to include the path as part of the filename       
 * @param  array   $exclude         array of directory names to exclude.
 * @param  bool    $_recursion      internal variable to determine recursion status - do not use in calls
 * @return array 
 * @prerequisite addEndSlash() 
 * @prerequisitefile string_helper.php
 */ 
if (!function_exists('getDirFileInfo'))
{
  function getDirFileInfo($source_dir, $include_path = false, $exclude = null, $_recursion = false)
  {     
    $source_dir = addEndSlash($source_dir);
    static $_filedata = array();
    $relative_path = $source_dir;  

    if ($fp = @opendir($source_dir))
    {
      // reset the array and make sure $source_dir has a trailing slash on the initial call
      if ($_recursion === false) {
        $_filedata = array();
        $source_dir = rtrim(realpath($source_dir),DS).DS;
      }

      while (false !== ($file = readdir($fp)))
      {
        if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0) 
        {  
          if(is_array($exclude)) 
          {  
            foreach($exclude as $val) {  
              $excludethisdir = strpos($source_dir, $val);     
            }                       
            if($excludethisdir == false) {
              getDirFileInfo($source_dir.$file.DS, $include_path, $exclude, true); 
            }   
          }   
          else {
            getDirFileInfo($source_dir.$file.DS, $include_path, $exclude, true);
          } 
        }  
        elseif (strncmp($file, '.', 1) !== 0) 
        {
          if($include_path == true) {
            $filekey = $relative_path . $file;     
          } 
          else {
            $filekey = $file;
          }  
          foreach($exclude as $val) {
            $excludethisdir = strpos($relative_path, $val);  
          }
          if($excludethisdir == false) 
          {
            if(array_key_exists($filekey, $_filedata)) {
              $_filedata[$filekey]['paths'][$relative_path] = array(); 
            } 
            else 
            {
              $_filedata[$filekey] = getDirFileInfo($source_dir.$file); 
              $_filedata[$filekey]['name'] = $file;  
              $_filedata[$filekey]['paths'][$relative_path] = array();    
            }  
          }   
        }
      }
      return $_filedata;
    }
    else {
      return false;
    }
  }
}  
 
// --------------------------------------------------------------------

/**
 * Get File Info
 *
 * @note Given a file and path, returns the name, path, size, date modified
 * Second parameter allows you to explicitly declare what information you want returned
 * Options are: name, server_path, size, date, readable, writable, executable, fileperms
 * Returns false if the file cannot be found.
 *
 * Usage: 
 * {{{
 *    $fileinfo = getFileInfo('/bobs/user/home/dir/bub.php');  
 * }}}      
 *
 * @param  string  $file            path to file
 * @param  mixed   $returned_values array or comma separated string of information returned
 * @return array      
*/
if (!function_exists('getFileInfo'))
{
  function getFileInfo($file, $returned_values = array('name', 'server_path', 'size', 'date'))
  {
    if (!file_exists($file)) { return false; }

    if (is_string($returned_values)) {
      $returned_values = explode(',', $returned_values);
    }

    foreach ($returned_values as $key)
    {
      switch ($key)
      {
        case 'name':
          $fileinfo['name'] = substr(strrchr($file, DS), 1);
          break;
        case 'server_path':
          $fileinfo['server_path'] = $file;
          break;
        case 'size':
          $fileinfo['size'] = filesize($file);
          break;
        case 'date':
          $fileinfo['date'] = filectime($file);
          break;
        case 'readable':
          $fileinfo['readable'] = is_readable($file);
          break;
        case 'writable':
          // There are known problems using is_weritable on IIS.  It may not be reliable - consider fileperms()
          $fileinfo['writable'] = is_writable($file);
          break;
        case 'executable':
          $fileinfo['executable'] = is_executable($file);
          break;
        case 'fileperms':
          $fileinfo['fileperms'] = fileperms($file);
          break;
      }
    }

    return $fileinfo;
  }
}

// --------------------------------------------------------------------

/**
 * Get Mime by Extension
 *
 * @note Translates a file extension into a mime type based on config/mimes.php. 
 * Returns false if it can't determine the type, or open the mime config file
 *
 * @note this is NOT an accurate way of determining file mime types, and is here strictly as a convenience
 * It should NOT be trusted, and should certainly NOT be used for security
 *
 * Usage: 
 * {{{
 *    $filemime = getMimeByExtension('/bobs/user/home/dir/bub.php');  
 * }}}      
 *
 * @param string $file path to file
 * @return  mixed    
 */ 
if (!function_exists('getMimeByExtension'))
{
  function getMimeByExtension($file)
  {
    $extension = substr(strrchr($file, '.'), 1);

    global $mimes;

    if (!is_array($mimes)) {
      if (!require_once(FRAMEWORKPATH.'/config/mimes.php')) {
        return false;
      }
    }

    if (array_key_exists($extension, $mimes))
    {
      if (is_array($mimes[$extension])) {
        // Multiple mime types, just give the first one
        return current($mimes[$extension]);
      }
      else { return $mimes[$extension]; }
    }
    else { return false; }
  }
}

// --------------------------------------------------------------------

/**
 * Symbolic Permissions
 *
 * @note Takes a numeric value representing a file's permissions and returns
 * standard symbolic notation representing that value           
 *
 * @param int $perms
 * @return  string 
 */ 
if (!function_exists('symbolicPermissions'))
{
  function symbolicPermissions($perms)
  { 
    if (($perms & 0xC000) == 0xC000) {
      $symbolic = 's'; // Socket
    }
    elseif (($perms & 0xA000) == 0xA000) {
      $symbolic = 'l'; // Symbolic Link
    }
    elseif (($perms & 0x8000) == 0x8000) {
      $symbolic = '-'; // Regular
    }
    elseif (($perms & 0x6000) == 0x6000) {
      $symbolic = 'b'; // Block special
    }
    elseif (($perms & 0x4000) == 0x4000) {
      $symbolic = 'd'; // Directory
    }
    elseif (($perms & 0x2000) == 0x2000) {
      $symbolic = 'c'; // Character special
    }
    elseif (($perms & 0x1000) == 0x1000) {
      $symbolic = 'p'; // FIFO pipe
    }
    else {
      $symbolic = 'u'; // Unknown
    }

    // Owner
    $symbolic .= (($perms & 0x0100) ? 'r' : '-');
    $symbolic .= (($perms & 0x0080) ? 'w' : '-');
    $symbolic .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $symbolic .= (($perms & 0x0020) ? 'r' : '-');
    $symbolic .= (($perms & 0x0010) ? 'w' : '-');
    $symbolic .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

    // World
    $symbolic .= (($perms & 0x0004) ? 'r' : '-');
    $symbolic .= (($perms & 0x0002) ? 'w' : '-');
    $symbolic .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

    return $symbolic;   
  }
}

// --------------------------------------------------------------------

/**
 * Octal Permissions
 *
 * @note Takes a numeric value representing a file's permissions and returns
 * a three character string representing the file's octal permissions
 *
 * @param int $perms
 * @return  string     
 */ 
if (!function_exists('octalPermissions'))
{
  function octalPermissions($perms) 
  {
    return substr(sprintf('%o', $perms), -3);
  }
}   

// --------------------------------------------------------------------

/**
 * Checks if a file is an image.
 *
 * Usage: 
 * {{{
 *    if(isImage('bob.jpg') {
 *      // logic
 *    } 
 * }}}      
 *
 * @param string $path
 */     
if(!function_exists('isImage'))  
{
  function isImage($path)
  {
    if (array_search(extension($path), array('gif', 'jpg', 'jpeg', 'png'))) { return true; }
    else { return false; }
  }  
}

// --------------------------------------------------------------------

/**
 * Checks if a file is css.
 *
 * Usage: 
 * {{{
 *    if(isImage('bob.css') {
 *        logic
 *    } 
 * }}}      
 *
 * @param string $path 
 */    
if(!function_exists('isCSS'))
{
  function isCSS($path) 
  {
    return (extension($path) == 'css') ? true : false;
  }  
}  

// --------------------------------------------------------------------

/**
 * Outputs a filesize in a human readable format
 *
 * @param int $val The filesize in bytes
 * @param int $round   
 */         
if(!function_exists('readableSize'))   
{
  function readableSize($val, $round = 0)
  {
    $unit = array('','K','M','G','T','P','E','Z','Y');

    while($val >= 1000) {
      $val /= 1024;
      array_shift($unit);
    }

    return round($val, $round) . array_shift($unit) . 'B';
  }  
}
    
// --------------------------------------------------------------------

/**
 * Takes a relative path, gets the full server path, removes
 * the www root path, leaving only the url path to the file/folder
 *
 * @param sring $relative_path    
 */   
if(!function_exists('urlpath'))
{
  function urlpath($relative_path)
  {
    return str_replace($_SERVER['DOCUMENT_ROOT'],'', realpath($relative_path) );
  } 
}     

// --------------------------------------------------------------------

/**
 * Joins any number of paths together
 *
 * @param $path
 * @prerequisite reduceDoubleSlashes()
 * @prerequisitefile helper_string.php     
 */  
if(!function_exists('joinPath'))
{
  function joinPath($path)
  {
    $num_args = func_num_args();
    $args = func_get_args();
    $path = $args[0];

    if($num_args > 1 ) {
      for ($i = 1; $i < $num_args; $i++) {
        $path .= DS.$args[$i];
      }
    }

    return reduceDoubleSlashes($path);
  }
}

// --------------------------------------------------------------------

/**
 * Returns a DirName for a path.
 *
 * @param $path   
 */   
if(!function_exists('load'))
{    
  function fixPath($path)
  {
    return dirname($path . './');
  }  
}  

// --------------------------------------------------------------------

/**
 * Loads and returns a file
 *
 * @param string $f name of file
 */
if(!function_exists('load'))
{
  function load($f)
  {
    if(!file_exists($f)) {
      exit("Cannot load file: $f");
    }
    elseif(is_dir($f)) { return loadDir($f); }
    else { return file_get_contents($f); }
  }  
} 

// --------------------------------------------------------------------

/**
 * Returns the extension of the file
 *  
 * @param $path 
 */
if(!function_exists('extension'))
{
  function extension($path) 
  {
    $qpos = strpos($path, "?");

    if ($qpos!==false) $path = substr($path, 0, $qpos);
    return pathinfo($path, PATHINFO_EXTENSION);  
  }  
} 

// --------------------------------------------------------------------

/**
 * Returns the filename of the most recent file in a directory
 *  
 * @param $path 
 */     
if(!function_exists('mostRecentFile'))
{
  function mostRecentFile($dir) 
  {
    $pattern = '\.([a-zA-Z])$';

    $newstamp = 0;
    $newname = "";
    $dc = opendir($dir);
    while ($fn = readdir($dc)) 
    {
      // Eliminate current directory, parent directory
      if (ereg('^\.{1,2}$',$fn)) continue;
      // Eliminate other pages not in pattern
      if (! ereg($pattern,$fn)) continue;   

      $timedat = filemtime("$dir/$fn");
      if ($timedat > $newstamp) {
        $newstamp = $timedat;
        $newname  = $fn;       
      }  
    }    
    return $newname;
  }  
}

// --------------------------------------------------------------------   

/**
 * Load php files with require_once in a given dir
 *
 * @param string $path Path in which are the file to load
 * @param string $pattern a regexp pattern that filter files to load
 * @param bool $prevents_output security option that prevents output
 * @return array paths of loaded files
 */ 
if(!function_exists('requireOnceDir'))
{
  function requireOnceDir($path, $pattern = "*.php", $prevents_output = true)
  {
    if($path[strlen($path) - 1] != "/") $path .= "/";
    $filenames = glob($path.$pattern);
    if(!is_array($filenames)) $filenames = array();
    if($prevents_output) ob_start();
    foreach($filenames as $filename) require_once $filename;
    if($prevents_output) ob_end_clean();
    return $filenames;
  } 
}