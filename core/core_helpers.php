<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
   
// ------------------------------------------------------------------------

/**
 * These helpers are needed by Simplex itself.
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

// ------------------------------------------------------------------------

/**
 * Makes sure the string ends with a /
 *
 * @param $str string 
 */ 
if(!function_exists('addEndSlash'))  
{
  function addEndSlash($str)
  {
    return rtrim($str, '/') . '/';
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
        $_filedata  = array();
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
              $_filedata[$filekey]                          = getDirFileInfo($source_dir.$file); 
              $_filedata[$filekey]['name']                  = $file;  
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
 
// ------------------------------------------------------------------------

/**
 * Checks the Joomla Document to see if a file has already been added.
 *
 * Usage:
 * {{{
 *    if(checkHead('jquery', 'scripts') == false)
 *    {  
 *      $this->loader->load_js('jquery.splex.min');
 *    }
 * }}}
 * 
 * @param string $filename The Name of The File To Search For. 
 * @param string $type    The Type of headata to check.
 *  options: scripts, stylesSheets
 * @return bool
 * @see arrayFind()
 * @prerequisites array.php  
 **/ 
if(!function_exists('checkHead')) 
{
  function checkHead($filename, $type)
  {
    $doc       = JFactory::getDocument();  
    $headData  = $doc->getHeadData();
    $headData  = $headData[$type];  
    return arrayFind($filename, $headData, true); 
  }
}  

// ------------------------------------------------------------------------  

/**
 * Searches a array for given string.      
 * 
 * Usage: 
 * {{{
 *    if(array_find('bob', $array))
 *    {
 *      // do something
 *    }  
 * }}}
 *
 * @param   string $needle      String to search for
 * @param   array  $haystack    Array to search in
 * @return  bool   $searchKeys  Whether or not to search the keys.
 * @return  mixed
 */
if(!function_exists('arrayFind'))
{
  function arrayFind($needle, $haystack, $searchKeys = false) 
  {
    if(!is_array($haystack)) return false;
    foreach($haystack as $key => $value) 
    {
      $what = ($searchKeys) ? $key : $value;
      if(strpos($what, $needle) !==false ) return $key;    
    }
    return false; 
  }  
}

// ------------------------------------------------------------------------

/**
 * Capitalize all words    
 *
 * @param string $words    Data to capitalize
 * @param string $charList Word delimiters
 * @return string Capitalized words
 */ 
if(!function_exists('capitalizeWords'))
{
  function capitalizeWords($words, $charList = null) 
  {
    // Use ucwords if no delimiters are given
    if (!isset($charList)) {
      return ucwords($words);
    }

    // Go through all characters
    $capitalizeNext = true;

    for ($i = 0, $max = strlen($words); $i < $max; $i++) 
    {
      if (strpos($charList, $words[$i]) !== false) {
        $capitalizeNext = true;
      } 
      else if ($capitalizeNext) {
        $capitalizeNext = false;
        $words[$i] = strtoupper($words[$i]);      
      } 
    }

    return $words;
  }
} 

// --------------------------------------------------------------------

/**
 * Flashes a message
 *
 * @param string $message The message to flash.
 * @return string  
 */   
if(!function_exists('flashMessage'))
{
  function flashMessage($message)
  {
    ob_start();
    ?>  
<div class="flashMessage" style="background-color:red; color:black; padding:14px; position:absolute; top:0px; left:38%; right:38%; width:400px;">
  <?php echo $message; ?>
</div>  
    <?php
    echo ob_get_clean();
  } 
}  

// ------------------------------------------------------------------------
    
/**
 * Checks To See If a User is an Administrator
 *
 * Usage:
 * {{{
 *    if(isAdmin()) 
 *    {
 *      // Do Something
 *    } 
 * }}}
 * 
 * @return bool       
 **/ 
if(!function_exists('isAdmin')) 
{
  function isAdmin()
  {
    $user = JFactory::getUser();

    if($user->usertype == "Super Administrator") 
      return true;
    else 
      return false;  
  }
}  

// ------------------------------------------------------------------------

/**
 * Replaces double slashes in urls with singles
 *
 * @param $str string
 */ 
if(!function_exists('reduceDoubleSlashes'))
{
  function reduceDoubleSlashes($str)
  {
    return preg_replace("#([^:])//+#", "\\1/", $str);
  }   
}