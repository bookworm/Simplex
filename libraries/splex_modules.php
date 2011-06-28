<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// ------------------------------------------------------------------------

/**
 * Module Helper Class. Expands on the core Joomla! module functions,
 *    
 * @package     simplex
 * @subpackage  libraries
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
class Splex_Modules
{   
  /**
   * Get module by name (real, eg 'Breadcrumbs' or folder, eg 'mod_breadcrumbs')
   *
   * @access  public
   * @param string  $name The name of the module
   * @param string  $title  The title of the module, optional
   * @return  object  The Module object
   */
  function &getModule($name, $title = null )
  {
    $result   = null;
    $modules  =& Splex_Modules::_load();
    $total    = count($modules);
    for ($i = 0; $i < $total; $i++)
    {
      // Match the name of the module
      if ($modules[$i]->name == $name)
      {
        // Match the title if we're looking for a specific instance of the module
        if (!$title || $modules[$i]->title == $title ) {
          $result =& $modules[$i];
          break;  // Found it
        }
      }
    }

    // if we didn't find it, and the name is mod_something, create a dummy object
    if (is_null( $result ) && substr( $name, 0, 4 ) == 'mod_')
    {
      $result       = new stdClass;
      $result->id     = 0;
      $result->title    = '';
      $result->module   = $name;
      $result->position = '';
      $result->content  = '';
      $result->showtitle  = 0;
      $result->control  = '';
      $result->params   = '';
      $result->user   = 0;
    }

    return $result;
  }        
  
// ------------------------------------------------------------------------  

  /**
   * Get modules by position
   *
   * @access public
   * @param string  $position The position of the module
   * @return array  An array of module objects
   */
  function &getModules($position)
  {
    $position = strtolower( $position );
    $result   = array();

    $modules =& Splex_Modules::_load();

    $total = count($modules);
    for($i = 0; $i < $total; $i++) {
      if($modules[$i]->position == $position) {
        $result[] =& $modules[$i];
      }
    }                       
    
    if(count($result) == 0) 
    {
      if(JRequest::getBool('tp')) 
      {
        $result[0] = Splex_Modules::getModule( 'mod_'.$position );
        $result[0]->title = $position;
        $result[0]->content = $position;
        $result[0]->position = $position;
      }
    }

    return $result;
  }
        
// ------------------------------------------------------------------------

  /**
   * Checks if a module is enabled
   *
   * @access  public
   * @param   string  $module The module name
   * @return  boolean
   */
  function isEnabled( $module )
  {
    $result = &Splex_Modules::getModule( $module);
    return (!is_null($result));
  }

  /**
   * Renders a Modules Content.
   * Only used in Simplex for rendering the module content.
   *
   * @param   string  $module  The module name
   * @param   array   $attribs Array of attributes
   * @return  object
   */ 
  function renderModule($module, $attribs = array())
  {
    static $chrome;  
    global $option;  
    $mainframe = JFactory::getApplication();

    $scope = $mainframe->scope; //record the scope
    $mainframe->scope = $module->module;  //set scope to component name

    // Handle legacy globals if enabled
    if ($mainframe->getCfg('legacy'))
    {
      // Include legacy globals
      global $my, $database, $acl, $mosConfig_absolute_path;

      // Get the task variable for local scope
      $task = JRequest::getString('task');

      // For backwards compatibility extract the config vars as globals
      $registry =& JFactory::getConfig();
      foreach (get_object_vars($registry->toObject()) as $k => $v) {
        $name = 'mosConfig_'.$k;
        $$name = $v;
      }
      
      $contentConfig = &JComponentHelper::getParams( 'com_content' );
      foreach (get_object_vars($contentConfig->toObject()) as $k => $v) {
        $name = 'mosConfig_'.$k;
        $$name = $v;
      }
      
      $usersConfig = &JComponentHelper::getParams( 'com_users' );
      foreach (get_object_vars($usersConfig->toObject()) as $k => $v) {
        $name = 'mosConfig_'.$k;
        $$name = $v;
      }
    }

    // Get module parameters
    $params = new JParameter( $module->params );

    // Get module path
    $module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);
    $path = JPATH_BASE.DS.'modules'.DS.$module->module.DS.$module->module.'.php';

    // Load the module
    if (!$module->user && file_exists( $path ) && empty($module->content))
    {
      $lang =& JFactory::getLanguage();
      $lang->load($module->module);

      $content = '';
      ob_start();
      require $path;
      $module->content = ob_get_contents().$content;
      ob_end_clean();
    }

    // Load the module chrome functions
    if (!$chrome) { $chrome = array(); }

    require_once (JPATH_BASE.DS.'templates'.DS.'system'.DS.'html'.DS.'modules.php');
    $chromePath = JPATH_BASE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'modules.php';
    if (!isset( $chrome[$chromePath]))
    {
      if (file_exists($chromePath)) {
        require_once ($chromePath);
      }
      $chrome[$chromePath] = true;
    }

    //make sure a style is set
    if(!isset($attribs['style'])) {
      $attribs['style'] = 'none';
    }

    //dynamically add outline style
    if(JRequest::getBool('tp')) {
      $attribs['style'] .= ' outline';
    }

    foreach(explode(' ', $attribs['style']) as $style)
    {
      $chromeMethod = 'modChrome_'.$style;

      // Apply chrome and render module
      if (function_exists($chromeMethod))
      {
        $module->style = $attribs['style'];

        ob_start();
        $chromeMethod($module, $params, $attribs);
        $module->content = ob_get_contents();
        ob_end_clean();
      }
    }

    $mainframe->scope = $scope; //revert the scope

    return $module->content;
  }  
  
// ------------------------------------------------------------------------ 

  /**
   * Get the path to a layout for a module
   *
   * @param string  $module The name of the module
   * @param string  $layout The name of the module layout
   * @return  string  $theme  A child theme location for the module layout.
   * @since 1.5
   */
  function getLayoutPath($module, $layout = 'default', $theme = null)
  {
    $mainframe = JFactory::getApplication();   
    $splex     = getSplexInstance();   
  
    if($themeName == null) 
    {
      if(empty($splex->tconfig->themeName)) {
        $themeName = 'default';
      }   else { $themeName = $splex->tconfig->themeName; } 
    } 
    
    // Build the template and base path for the layout
    $tPath = JPATH_BASE.DS.'templates'.DS.$mainframe->getTemplate().DS.'child_themes'.DS.$themeName.DS.'modules'.DS.$module.DS.$layout.'.php';
    $bPath = JPATH_BASE.DS.'modules'.DS.$module.DS.'tmpl'.DS.$layout.'.php';

    // If the template has a layout override use it
    if (file_exists($tPath)) {
      return $tPath;
    } else {
      return $bPath;
    }
  }
       
// ------------------------------------------------------------------------

  /**
   * Load published modules
   *
   * @access  private
   * @return  array
   */
  function &_load()
  {    
    $mainframe = JFactory::getApplication();
    global $Itemid;

    static $modules;

    if (isset($modules)) {
      return $modules;
    }

    $user =& JFactory::getUser();
    $db   =& JFactory::getDBO();

    $aid  = $user->get('aid', 0);

    $modules  = array();

    $wheremenu = isset( $Itemid ) ? ' AND ( mm.menuid = '. (int) $Itemid .' OR mm.menuid = 0 )' : '';

    $query = 'SELECT id, title, module, position, content, showtitle, control, params'
      . ' FROM #__modules AS m'
      . ' LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id'
      . ' WHERE m.published = 1'
      . ' AND m.access <= '. (int)$aid
      . ' AND m.client_id = '. (int)$mainframe->getClientId()
      . $wheremenu
      . ' ORDER BY position, ordering';

    $db->setQuery( $query );

    if (null === ($modules = $db->loadObjectList())) {
      JError::raiseWarning( 'SOME_ERROR_CODE', JText::_( 'Error Loading Modules' ) . $db->getErrorMsg());
      return false;
    }

    $total = count($modules);
    for($i = 0; $i < $total; $i++)
    {
      //determine if this is a custom module
      $file         = $modules[$i]->module;
      $custom         = substr( $file, 0, 4 ) == 'mod_' ?  0 : 1;
      $modules[$i]->user    = $custom;
      // CHECK: custom module name is given by the title field, otherwise it's just 'om' ??
      $modules[$i]->name    = $custom ? $modules[$i]->title : substr( $file, 4 );
      $modules[$i]->style   = null;
      $modules[$i]->position  = strtolower($modules[$i]->position);
    }

    return $modules;
  }    
  
// ------------------------------------------------------------------------

  /**
   * The default chrome function for Simplex
   */  
  function moduleDefaultRender($module, &$params, $moduleClass, $moduleCSS = null)
  {    
    if(!$moduleCSS == null) $moduleCSS = "style=\"$moduleCSS\"";
    ob_start();
    ?>              
<div id="module-<?php echo $module->id;?>" class="module <?php echo $module->name;?> <?php echo $moduleClass; ?>" <?php echo $moduleCSS; ?> >
<?php if ($module->showtitle != 0) : ?>
  <h3 class="mod-title"><?php echo $module->title; ?></h3>
<?php endif; ?>
  <div class="modulecontent"><?php echo $module->content; ?></div>
</div>   
    <?php
      return ob_get_clean();
  }  
  
// ------------------------------------------------------------------------

  /**
   * Module Details. This wraps a Module Dispalying all its details; chrome, name, type, suffixes etc.
   *
   * @return void 
   **/  
  function moduleDetails($classFunc, $module, &$params, $moduleClass, $moduleCSS = null, $position)
  {         
    ob_start();    
    ?>  
<div id="info-<?php echo $module->id;?>"class="mod-specific-details-wrap overlay">
  <div class="mod-specific-details">   
    <div class="mod-position">
      <span class="label-mod-info">Module Position:</span>
      <span class="mod-info"><?php echo $position; ?></span>     
    </div>
    <div class="mod-chrome">
      <span class="label-mod-info">Module Chrome:</span>
      <span class="mod-info"><?php echo $classFunc; ?></span>     
    </div>
    <div class="mod-name">    
      <span class="label-mod-info">Module Type:</span>
      <span class="mod-info"><?php echo $module->name; ?></span>     
    </div>
    <div class="mod-classes">     
      <span class="label-mod-info">Module Class Suffixes:</span>
      <span class="mod-info"><?php echo $moduleClass;?></span>
    </div>
  </div>    
</div>  
    <?php    
    echo ob_get_clean();   
  }   
}