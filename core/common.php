<?php     

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
   
// ------------------------------------------------------------------------

/**
 * Common Functions needed across framework.
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
  * Load a class into a object.
  * 
  * @note This acts as a Class Registry providing us the ability to create a Super Object and then
  * do things like $splex->browser->getBrowser() etc. For Design Patterns enthusiasts this is called 
  * a Singleton Class Registry. 
  * 
  * @note Its probably preferable to use Splex_Loader::load_class() instead of this function directly. The loader class 
  * will determine the location of the file and allow user over-rides/subtheming. Inside simplex itself we only use this function twice
  * to load the simplex core class and the loader class itself.
  *
  * Usage: 
  * {{{
  *    $splexCorePath = FRAMEWORKPATH . '/core/simplex.php';    
  *    $splex = splex_loadClass('Simplex', $splexCorePath, true, $templateOBJ)    
  * }}} 
  * 
  * @param string $class        The name of the class to instantiated.  
  * @param string $fullpath     Full Path To A PHP Class File. Required.
  * @param bool   $instantiate  Allows you to load but not instantiate a class.
  * @param mixed  $classArg     Arguments to pass to a class on instantiation.
  * @return class 
  * @see splex_instantiateClass()  
  * @see Splex_Loader::load_class()
  */   
function splex_loadClass($class, $fullpath = null, $instantiate = true, $classArg = null)
{
  static $objects = array();    

  // Check To see if the class is already instantiated. If it is don't instantiate again. 
  if (isset($objects[$class])) { return $objects[$class]; }  
  if (file_exists($fullpath)) { require_once($fullpath); }
  else { exit('No class of that name: ' . $class); }   

  $name = $class;     

  // This allows us to pass in some stuff from other classes on the init of the class.
  if (!$classArg == null) { $objects[$class] = splex_instantiateClass(new $name($classArg)); } 
  else { $objects[$class] = splex_instantiateClass(new $name()); }

  return $objects[$class];  
}

// ------------------------------------------------------------------------

/**
 * Instantiate A Class.
 *
 * @note Returns a new class object by reference, used by splex_loadClass()
 *
 * Usage: 
 * {{{
 *    $obj = splex_instantiateClass(new Foo());     
 * }}} 
 * 
 * @param object $class_object The object to instantiate
 * @return object
 * @see splex_loadClass()
 */
function splex_instantiateClass($classObject)
{
  return $classObject;
}