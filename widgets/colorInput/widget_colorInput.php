<?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Creates A Color Input Field.
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
class Muwt_Widget_colorInput extends Muwt_Widget
{ 
  /**
   * Constructor.
   * 
   * @param object $param The parameter object to construct the widget from.
   * @return void
   **/
  function __construct($param)
  { 
    $this->param = $param; 
    $this->paramName = $this->param->name;  
    
    $this->_addResources();   
    $this->_createJS();
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
      
    if(checkHead('colorpicker.min', 'scripts') == false)
      $splex->muwt->addScript('colorpicker.min');
    if(checkHead('colorpicker', 'styleSheets') == false) 
      $splex->muwt->addStyleSheet('colorpicker'); 
  }  
  
// ------------------------------------------------------------------------

  /**
   * Generates the JS for the widget.
   *                                                               
   * @return void
   **/  
  public function _createJS()
  {   
    $splex = getSplexInstance();
   
    ob_start();
    ?> 
    var $$ = jQuery;
    $$(document).ready(function() {
      jQuery('#colorPickerWrapper').ColorPicker({
          onShow: function (colpkr) {
            jQuery(colpkr).fadeIn(500);
            return false;
          },
          onHide: function (colpkr) {
            jQuery(colpkr).fadeOut(500);
            return false;
          },
          onChange: function (hsb, hex, rgb) {  
            $$('#color-picker-bg').css('backgroundColor', '#' + hex);
            var selector = $$('#param-css-selector').val();
            $$(selector).globalcss('color', '#' + hex);  
            editor.setCode(globalstylesheet.print());
          }
       });
    });      
    <?php              
    
    $declare = ob_get_clean(); 
    $splex->muwt->addScriptDeclaration($declare);
  }    
  
// ------------------------------------------------------------------------

  /**
   * Renders the widget.
   *   
   * @return string
   **/
  public function render() 
  {   
    ob_start();
    ?> 
<div id="color-picker-wrap">
  <div id="color-picker-bg">
  </div>   
  <input class="colorInput colorSelector"  id="colorPickerWrapper" value="" type="color">
</div>  
    <?php

echo ob_get_clean();
  }
}