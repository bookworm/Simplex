<?php
  
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
    
// ------------------------------------------------------------------------

/**
 * Array Helpers. 
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
 * Clean print_r interface. Wraps a array in <pre> tags.
 *
 * Usage: 
 * {{{
 *    echo crpint_r($array); 
 * }}}   
 *
 * @param array $array Array of data to pretty print.
 * @return array 
 **/   
if(!function_exists('cprint_r'))
{
  function cprint_r($array) 
  {
    $prettyPrint = '<pre>' . print_r($array) . '</pre>';
    return $prettyPrint;
  }   
}

// ------------------------------------------------------------------------  
  
/**
 * Cleans An Array of junk 
 * 
 * Usage: 
 * {{{
 *    $cleanedarray = cleanArray($array, false, true, 'string');   
 * }}}         
 *
 * @param array  $array     Array to clean 
 * @param bool   $reKey     re-generate the numeric keys
 * @param bool   $typecheck Run each array item through a php type checking function; e.g is_array(), is_string.
 *    If the function returns true, the item will be removed. This is used for type checking of array; int, 
 *    string etc. Do not include "is_" in the function name it is automatically added. 
 * @param string $type      The type to check for. 
 * @return void
 **/  
if(!function_exists('cleanArray'))
{
  function cleanArray($array, $reKey = false, $typecheck = false, $type = null)
  {    
    $type            = 'is_' . $type;     
    
    foreach($array as $key => $arrayItem)  
    {
      if($arrayItem == null) { unset($array[$key]); } 
      
      if($typecheck == true) {   
        $checkresult = call_user_func($type, $arrayItem);
        if($checkresult) { unset($array[$key]); }
      }
    }  
         
    if($reKey == true) $array = array_values($array);
    return $array; 
  }
}

// ------------------------------------------------------------------------  

/**
 * Sorts array elements by length
 * 
 * Usage: 
 * {{{
 *    usort($array, 'sortByLength');
 * }}}
 *
 * @return type    
 */  
if(!function_exists('sortByLength'))
{
  function sortByLength($a, $b)
  {
    if($a == $b) return 0;
    return (strlen($a) > strlen($b) ? -1 : 1);
  }  
}   

// ------------------------------------------------------------------------  

/**
 * Removes duplicate items from a array.
 * 
 * Usage: 
 * {{{
 *    $uniqueArray = uniqueArray($array);   
 * }}}
 *
 * @param array $array The array to make unique.
 * @return type    
 */  
if(!function_exists('uniqueArray')) 
{
  function uniqueArray($array)
  {
    $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

    foreach ($result as $key => $value)
    {
      if (is_array($value)) {
        $result[$key] = uniqueArray($value);
      }   
    }
    return $result;  
  }   
}    

// ------------------------------------------------------------------------  

/**
 * Returns the value of a key, using dot notion e.g bob.name.bob
 * 
 * Usage: 
 * {{{
 *    $bobsName = keyString($array, 'people.bob.name');   
 * }}}
 *
 * @param   array   $array  array to search
 * @param   string  $keys   dot-noted string: foo.bar.baz
 * @return  string          if the key is found
 * @return  void            if the key is not found  
 */
if(!function_exists('keyString'))
{
  function keyString($array, $keys)
  {
    if (empty($array)) { return NULL; }

    // Prepare for loop
    $keys = explode('.', $keys);

    do 
    {
      // Get the next key
      $key  = array_shift($keys);

      if (isset($array[$key]))
      {
        if (is_array($array[$key]) AND ! empty($keys)) {
          // Dig down to prepare the next loop
          $array = $array[$key];
        }
        else {
          // Requested key was found
          return $array[$key];
        }
      }
      else {
        // Requested key is not set
        break;
      }
    }
    while (!empty($keys));
    return NULL;
  }  
} 
  
// ------------------------------------------------------------------------  

/**
 * Sets values in an array by using a 'dot-noted' string.
 * 
 * Usage: 
 * {{{
 *    keyStringSety($array, 'people.bob.name', 'bob joe walsh');   
 * }}}
 *
 * @param   array  $array array to set keys in (reference)
 * @param   string $keys  dot-noted string: foo.bar.baz
 * @return  mixed  $fill  fill value for the key
 * @return  void
 */ 
if(!function_exists('keyStringSet')) 
{
  function keyStringSet(&$array, $keys, $fill = NULL)
  {
    if (is_object($array) AND ($array instanceof ArrayObject))
    {
      // Copy the array
      $array_copy = $array->getArrayCopy();

      // Is an object
      $array_object = TRUE;
    }
    else
    {
      if (!is_array($array)) {
        // Must always be an array
        $array = (array) $array;
      }

      // Copy is a reference to the array
      $array_copy = $array;
    }

    if (empty($keys)) {
        return $array;
    }

    // Create keys
    $keys = explode('.', $keys);
           
    // Create reference to the array
    $row = $array_copy;

    for ($i = 0, $end = count($keys) - 1; $i <= $end; $i++)
    {
      // Get the current key
      $key = $keys[$i];

      if (!isset($row[$key]))
      {
        if (isset($keys[$i + 1])) {
          // Make the value an array
          $row[$key]  = array();
        }             
        else {
          // Add the fill key
          $row[$key]  = $fill;
        }
      }
      elseif (isset($keys[$i + 1])) {
        // Make the value an array
        $row[$key]    = (array) $row[$key];
      }

      // Go down a level, creating a new row reference
      $row            = $row[$key];
    }

    if (isset($array_object)) {
      // Swap the array back in
      $array->exchangeArray($array_copy);
    }
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
    
    foreach($haystack as $key => $value) {
      $what = ($searchKeys) ? $key : $value;
      if(strpos($what, $needle) !==false ) return $key;    
    } 
    
    return false; 
  }  
}