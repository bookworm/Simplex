<?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * A magical wrapper widget for all the edit widgets.
 * 
 * @package     simplex
 * @subpackage  muwt.widgets
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */ 
class Muwt_Widget_magic extends Muwt_Widget 
{ 
  /**
   * Constructor.
   * 
   * @param object $param The parameter object to construct the widget from.
   * @return void
   **/
  function __construct($param)
  {             
    $this->param     = $param; 
    $this->paramName = $this->param->name;     
    
    $this->_addResources(); 
  }  

// ------------------------------------------------------------------------

  /**
   * Adds needed resources to head.
   *
   * @return void
   **/  
  public function _addResources()
  {  
    $splex = getSplexInstance();    
    
    if(checkHead('magical', 'scripts') == false)
      $splex->muwt->addScript('magical');
    if(checkHead('magical', 'styleSheets') == false)
      $splex->muwt->addStyleSheet('magical');   
  }
  
// ------------------------------------------------------------------------

  /**
   * Renders the widget.
   *   
   * @return string
   **/
  public function render() 
  {  
    $splex = getSplexInstance();
    
    ob_start();
    ?>  
<div id="modes-list-wrap">
  <ul id="modes-list">
    <li id="style-mode"><a href="#" class="mode-select">Style Mode</a></li>
    <li id="settings-mode"><a href="#" class="mode-select">Settings Mode</a></li>
    <li id="edit-mode"><a href="#" class="mode-select">Edit Mode</a></li>
    <li id="layout-mode"><a href="#" class="mode-select">Layout Mode</a></li>       
  </ul>    
  <div id="mode-label" style="display: none;">
      <span class="icon"></span>
      <span class="label"></span>
  </div>
</div>
<div id="mode-divs">
    <div class="mode-wrap style-mode" style="display: none;"> 
      <div id="style-mode-selects-wrap">
        <ul id="style-mode-selects">
            <li id="css-mode" class="active">
                <a href="#" class="syle-mode-select active">CSS Mode</a> 
                <div class="icon"></div>
            </li>
            <li id="widget-mode">
                <a href="#" class="syle-mode-select">Widget Mode</a> 
                <div class="icon"></div>
            </li>
        </ul>
      </div>
      <div id="style-mode-top">
        <div id="selector">
          <?php
           // Create Selector widget
           $selector = $splex->jpog->get('selector');
           $selector->render();
          ?>
        </div>
      </div>    
      <div id="style-mode-center">
        <div id="style-modes">  
          <div id="style-css-mode" class="a-style-mode active">
            <?php
              $css = $splex->jpog->get('css');
              $css->render();    
            ?>
          </div> 
          <div id="style-widget-mode" class="a-style-mode" style="display: none;">
            <?php
              $color = $splex->jpog->create('color');     
              $color->widgetType = 'colorInput';   
              $color->name = 'color';
              $color->save(); 
              $color->render();    
            ?>
          </div>
        </div>   
        <a href="#" id="save-css"><span class="text">Save CSS</span><span class="icon"></span></a>
      </div>
      <div id="style-mode-bottom"></div>      
    </div>      
    <a href="#" id="going-back" style="display: none;"><span class="icon"></span><psan class="text">Go Back</span></a>
</div>
    <?php
    echo ob_get_clean();        
    
    // Create Editable Widget.
    $editable             = $splex->jpog->create('editable');     
    $editable->widgetType = 'editable';   
    $editable->name       = 'editable';  
    $editable->save();  
    $editable->render();
  } 
}