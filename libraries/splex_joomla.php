<?php      

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Joomla. Provides methods for working with Joomla!
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
class Splex_Joomla
{    
  /**
   * Constructor Function. Gets a splex instance and initializes some variables 
   *
   * @return void
   */            
  function __construct()
  {
    $splex =& getSplexInstance();      
    $this->_initJoomlaVars();
  }   
  
// ------------------------------------------------------------------------
         
  /**
   * Creates and generate some useful Joomla! Related vars.
   */
  private function _initJoomlaVars()    
  {
    $document = JFactory::getDocument();    
    global $mainframe; 
    
    $this->url              = clone(JURI::getInstance());
    $this->site_uri         = JURI::root();      
    $this->currentComponent = JRequest::getVar('option');    
    
    // The Page refers to your html title i.e <title/>
    // Item title is generated from the last element in the path.        
    $this->pageTitle = $document->title;
    $this->itemTitle = $this->_contentItemTitle();    
    
    // MainFrame Config Objects
    $this->siteName = $mainframe->getCfg('sitename');   
    $this->MetaDesc = $mainframe->getCfg('MetaDesc');
  }
     
// ------------------------------------------------------------------------

  /**
   * Function For Outputting the Joomla! Head
   * jHead can be over-riden by simply defining a j_head(); function.
   */   
  public function jHead() 
  {    
    $splex = getSplexInstance();   
        
    if(!function_exists('jHead'))
    {
        ob_start();
        ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $splex->template->language; ?>" lang="<?php echo $splex->template->language; ?>">       
<head> 
<jdoc:include type="head" />
      <?php
        return ob_get_clean(); 
    }  
  }   
       
// ------------------------------------------------------------------------
  
  /**
   * Future Component Buffer Modification Function. 
   * @note The intent is to allow you to run a post process filter on the fully rendered template.
   * Like you often do with object buffering.
   * @todo  Finish    
   */
  public function renderComponent($component)
  {       
    jimport('joomla.application.component.helper'); 
    
    $doc = JFactory::getDocument();
    $doc->setBuffer( 'This is some content', 'component');
    
    $component = JComponentHelper::getComponent($component);
    $params    = new JParameter($component->params);    
    return $component; 
  }  
   
// ------------------------------------------------------------------------

  /**
   * Generates a Page Title From Breadcrumb Path.
   *
   * @return void 
   **/
  private function _contentItemTitle() 
  {
    $doc = JFactory::getDocument();  
    global $mainframe;

    // Get the PathWay object from the application
    $pathway  = $mainframe->getPathway();
    $crumbs   = $pathway->getPathWay();  

    $l = count($crumbs);     
    foreach($crumbs as $key => $crumb) {
      if ($key == $l - 1) return $crumb->name;     
    }   
  }  
}