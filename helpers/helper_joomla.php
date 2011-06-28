<?php     

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Simplex Joomla! Helpers. These Functions interact and work with Joomla! Stuff.
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
 * Load Modules From a Position
 * 
 * Usage: 
 * {{{  
 *    Using Defaults:
 *      loadModule('left', 'xhtml'); 
 *        
 *    Or even simpler with default chrome: 
 *      loadModule('left');
 *    
 *    Using Custom Settings:
 *      $module =  array('name' => 'left', 'chrome' => 'xhtml', 'class' => 'column', 'callback' => 'evenOdd');  
 *      loadModule($module);  
 * }}}
 *                             
 * @param array $module        The module array.
 *    $moudle['name']          Module Position to load modules from.   
 *    $module['chrome']        The Chrome Function to Use. This is just a default and is over-ridden by suffix classes 
 *    $module['class']         This is userFunction called every time a module is loaded. 
 *    $module['classCallback'] The Class Returned By the callbacked is added to the module suffixes. Commonly used for even odd classes.
 *    $module['css']           CSS for each individual module.
 *    $module['raw']           Specify a Raw Format. No wrapping divs at all just raw chrome markup.     
 * @return void  
 * @see cleanArray()   
 * @see widthCalc()
 * @see cssSetter() 
 * @see Splex_Modules->getModules()  
 * @see loadModuleSet()  
 **/
if(!function_exists('loadModule'))
{
  function loadModule($module, $chrome = null)
  {   
    $splex = getSplexInstance();  
       
    if(!is_array($module))
    {   
      $name             = $module;
      $module           = array();    
      $module['name']   = $name;
      $module['chrome'] = $chrome;  
    }         
      
    if(!array_key_exists('chrome', $module))   $module['chrome']        = null;   
    if(!array_key_exists('class', $module))    $module['class']         = null;
    if(!array_key_exists('callback', $module)) $module['classCallback'] = null; 
    if(!array_key_exists('css', $module))      $module['css']           = null; 
    if(!array_key_exists('raw', $module))      $module['raw']           = false;
    
    $name     = $module['name'];  
    $chrome   = $module['chrome'];
    $class    = $module['class']; 
    $callback = $module['classCallback'];
    $css      = $module['css']; 
    $raw      = $module['raw']; 
        
    if(!module($name)) return;
    $modules = $splex->modules->getModules($name);
    
    $css = "style=\"$css\"";        
    if(!$chrome == null) $chrome = 'chrome_' . $chrome;     
      
    foreach($modules as $module)   
    {      
      $params = new JParameter($module->params);  
      $moduleClass = $params->get('moduleclass_sfx');  
      
      if(!$callback == null)
      {  
        $previousID         = $module->id - 1; 
        $currentID          = $module->id;     
        $nextID             = $module->id + 1;      
        $classCallbackArgs  = array($currentID, $previousID, $nextID);
        $moduleClass             .= ' ' . call_user_func_array($classCallback, $classCallbackArgs); 
      }    
      
      if($module->content == null) {
        $module->content    = $splex->modules->renderModule($module); 
      }  
      
      $moduleClass          = ' ' . $class;     
       
      // Checks for a callback to an custom chrome in the module class suffix.
      $callBackCheck = strrpos($moduleClass, "cfunc_");      
      if(!$callBackCheck == null) 
      {    
        $chromeCallback = explode("cfunc_", $moduleClass);    
        $chromeCallback = explode(" ", $chromeCallback['1']); 
        $moduleClass    = str_replace('cfunc_', ' ', $moduleClass);  
      }   
      else {
        $chromeCallback = null; 
      }  
      
      $chromeArgs = array($module, $params, $extras = array($moduleClass, $css)); 
      
      if(!$chromeCallback == null) 
       {   
         $classFunc = 'chrome_' . $chromeCallback['0'];           
         if(function_exists($classFunc)) {   
           echo call_user_func_array($classFunc, $chromeArgs);   
         } 
         else {     
           echo $splex->modules->moduleDefaultRender($module, $params, $moduleClass, $css);
         } 
       } 

       else 
       {   
         if(!$chrome == null) {      
           echo call_user_func_array($chrome, $chromeArgs); 
         }   
         else {   
           echo $splex->modules->moduleDefaultRender($module, $params, $moduleClass, $css);     
         } 
       }
      
    }                 
  }  
} 

// ------------------------------------------------------------------------          

/**
 * Checks if a module position is published
 * 
 * Usage: 
 * {{{
 *    if(module('left'))
 *    {
 *      loadModule($leftmodule);   
 *    }     
 * }}} 
 *
 * @param string $published Module to check.
 * @return bool 
 * @see loadModule() 
 * @see loadModuleSet() 
 * @see modules()  
 * @see cleanDisabledModules()
 **/    
if(!function_exists('module'))
{
  function module($published)
  {   
    $splex = getSplexInstance(); 
    return $splex->template->countModules($published);       
  }  
} 

// ------------------------------------------------------------------------    
  
/**
 * Checks if any modules from a array of modules are published
 * 
 * Usage: 
 * {{{ 
 *    There are two ways to use this function;
 *    1. Use a list of module names: 
 *       $modules = array('left', 'right');
 *       if(modules($modules))
 *       {
 *         loadModule('left');
 *         loadModule('right');
 *       }  
 *    
 *    2. Use an array of module definitions
 *       $leftmodule   = array('name' => 'left', 'chrome' => 'xhtml',);
 *       $rightmodule  = array('name' => 'right', 'chrome' => 'xhtml');
 *       $modules = array($leftmodule, $rightmodule);   
 *       if(modules($modules))
 *       {
 *         // do something
 *       }  
 * }}} 
 *  
 * @param array $modules An array of modules to check 
 * @return bool   
 * @see loadModule() 
 * @see loadModuleSet() 
 * @see modules() 
 * @see module() 
 * @see cleanDisabledModules()
 **/   
if(!function_exists('modules')) 
{
  function modules($modules) 
  {  
    if(is_array($modules))  
    {   
      foreach($modules as $key => $value) 
      { 
         if($modules[$key]['name']) {
             $publishCheck = module($modules[$key]['name']);
         } else {
             $publishCheck = module($key);
         }
         if($publishCheck == true) return true;
      }   
      return false;  
    } 
  }  
}

// ------------------------------------------------------------------------    

/**
 * Removes unpublished Modules from an array of modules. 
 * 
 * Usage: 
 * {{{ 
 *    $modules = cleanDisabledMods($modules); 
 * }}} 
 *  
 * @param array $modules An array of modules to clean of un-published modules.
 * @return array    
 * @see loadModule() 
 * @see loadModuleSet() 
 * @see modules()  
 * @see module()
 **/  
if(!function_exists('cleanDisabledMods'))
{
  function cleanDisabledMods($modules)
  {   
    //Remove Any Unpublished Modules From Array 
    if(is_array($modules))  
    {   
      foreach($modules as $key => $value) 
      {  
         $publishCheck = module($modules[$key]['name']);
         if(!module($modules[$key]['name'])) {
           unset($modules[$key]);  
         }  
      }  
    } else { exit("You didn't pass an array into cleanDisabledMods()"); }
    return $modules;
  }   
}  
 
// ------------------------------------------------------------------------
    
/**
 * Loads the Component
 * 
 * Usage: 
 * {{{  
 *    echo component();
 * }}} 
 *
 * @return objbuffer     
 **/   
if(!function_exists('component'))
{
  function component()
  {   
    $splex =& getSplexInstance();

    ob_start();
    ?>
<div id="component-<?php echo $splex->joomla->currentComponent;?>">
  <jdoc:include type="component" />
</div>   
    <?php
    return ob_get_clean();
  }   
} 
  
// ------------------------------------------------------------------------
    
/**
 * Loads a OverRide.
 *
 * @note This is used so you can "child theme" a over-ride. Its important to realize that this amounts to 
 * creating multiple instances of simplex. This can result in an exponential load increase. Its recommended 
 * that you do not child theme a over-ride unless absolutely necessary.      
 *
 * Usage:
 * {{{
 *    // Get The Framework
 *    $mainframe = JFactory::getApplication();
 *    $templateName = $mainframe->getTemplate(); 
 *    $path = JPATH_SITE . DS . 'templates' .  DS . $templateName . '/simplex/core/framework.php';
 *    require_once($path);      
 *    echo loadOverRide();  
 * }}}
 *
 * @return obj     
 * @see Splex_Loader::loadFile()
 **/ 
if(!function_exists('loadOverRide'))
{
  function loadOverRide() 
  {
    $splex = getSplexInstance();   
    
    /**
     * We use backtrace to get the location that function is called on and to pass in the view object $this.
     * I feel fearful using something named debug in production code so hopefully this has no issues 
     * or security consequences.      
     **/
    $trace = debug_backtrace(); 
  
    // Reference The Obj So We can pass it into files   
    $obj = $trace[2]['object'];       

    // Get the Component Name 
    $overrideName  = $trace[0]['file'];  
    $overrideName  = preg_match('/(?=)com_[\w]*/i', $$overrideName, $matches);
    $overrideName  = str_replace('.php', '', $matches[0]);   

    // Clean Path
    $cleanPath = BASEPATH . DS . 'html' . DS . $$overrideName; 
    $cleanPath = str_replace($cleanPath, '', $trace[0]['file']);    

    $override  = $overrideName; 

    // @note may be necessary to pas $obj.
    $splex->loader->loadFile($override, 'override');     
  }
} 

// ------------------------------------------------------------------------
    
/**
 * Loads a Template File.     
 *
 * @note Replacement of Joomla API function for use in component templates.      
 *
 * Usage:
 * {{{
 *    echo $obj->loadTemplate('subcategories');
 * }}} 
 *
 * @param string $template The Name of The Template.
 * @return void  
 **/ 
if(!function_exists('loadTemplate'))
{
  function loadTemplate($template) 
  {   
    $splex = getSplexInstance();  
       
    /**
     * We use backtrace to get the location that function is called on and to pass in the view object $this.
     * I feel fearful using something named debug in production code so hopefully this has no issues 
     * or security consequences.      
     **/
    $trace    = debug_backtrace();
    $path     = dirname($trace[0]['file']);        
    $obj      = $trace[4]['object'];  
    $filePath = $path . '/'  . $template . '.php';      
  
    ob_start();
    include($filePath);
    $output   = ob_get_contents();
    ob_end_clean();

    return $output; 
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

    if($user->usertype == "Super Administrator") {
      return true;
    }
    else {
      return false;
    }  
  }
}    
  
// ------------------------------------------------------------------------
    
/**
 * Checks To See If Its the Homepage
 *
 * Usage:
 * {{{
 *    if(isHome()) 
 *    {
 *      // Do Something
 *    } 
 * }}}
 * 
 * @return bool 
 **/ 
if(!function_exists('isHome')) 
{
  function isHome()
  {
    $menu = JSite::getMenu();  

    if($menu->getActive() == $menu->getDefault()) {
      return true;
    } else { return false; }
  }
}

// ------------------------------------------------------------------------
    
/**
 * Checks If a User is Logged In
 *
 * Usage:
 * {{{
 *    if(loggedIn()) 
 *    {
 *      // Do Something
 *    } 
 * }}}
 * 
 * @return bool 
 **/ 
if(!function_exists('loggedIn')) 
{
  function loggedIn()
  {
    $user = JFactory::getUser();

    if($user->id) {
      return true;
    } else { return false; }
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
 * @param string $filname The Name of The File To Search For. 
 * @param string $type The Type of headata to check.
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