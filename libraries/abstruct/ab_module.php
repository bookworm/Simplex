<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * Module Class.
 *
 * @package   Abstruct
 * @author    Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright Copyright 2009 - 2010 Design BreakDown, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 **/
class AB_Module extends AB_Markup
{  
  /**
   * @var string Name of the module should match the module position name.
   **/ 
  var $name;   
  
  /**
   * @var string Chrome to use to render the module
   **/
  var $chrome;  
  
  /**
   * @var array  Module properties the get passed to module rendering function.
   **/ 
  var $moduleProps = array();
  
  /**
   * Constructor Function
   * 
   * @param string $name Name/position of the module. Required. 
   * @param array $props Class properties. 
   * @param array $moduleProps Module properties the get passed to module rendering function.
   * @return void
   **/
  public function __construct($name, $props = array(), $moduleProps = array())
  {         
    $this->name   = $name;  
       
    if(!empty($props))
      $this->chrome = $props['chrome'];   
      
    $this->moduleProps = $moduleProps;  
  } 
  
// ------------------------------------------------------------------------
  
  /**
   * Renders the module and returns the rendered string.
   *   
   * @return string
   **/
  public function render()
  {  
    ob_start();
    ?>  
<?php echo $this->genOpenTag(); ?>
<?php echo $this->domWrap(); ?>  
<?php echo $this->getBefore(); ?> 
<?php echo ab_renderModule($this->name, $this->chrome, $this->moduleProps); ?>
<?php echo $this->getAfter(); ?>    
<?php echo $this->domClose(); ?>     
</div>       
    <?php    
    return ob_get_clean(); 
  }
  
// ------------------------------------------------------------------------  
  
  /**
   * Generates the opening div tag,
   *   
   * @return string
   **/
  public function genOpenTag()
  {       
    $result = "<div id='$this->name" . "_module'" .  " class='module_pos' ";    
    $result .= '>';
    return $result;
  } 
}     