<?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// ------------------------------------------------------------------------

/**
 * Selector widget creates a css DOM selector.
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
class Muwt_Widget_selector extends Muwt_Widget 
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
    jimport('joomla.filesystem.file'); 
     
    $cssFilePath = $splex->loader->getFilePath('custom.css', 'cssfiles');
    $cssString   = file_get_contents($cssFilePath);    
    $css         = new Splex_CSS($cssString);       
    $selectors   = $this->param->attributes['selectors'];     
     
    ob_start();
    ?>
<div id="selector-wrap">   
  <input style="height: 0px; width: 0px; display: none;" type="text" name="param-css-selector; ?>" value="<?php echo $this->param->attributes['selector']; ?>" id="param-css-selector"></input>
  <div id="element-selector-wrap">
    <select id="element-selector">
      <?php foreach($selectors as $key => $selector): ?>     
      <option value="<?php echo $selector['name']; ?>"<?php if($key == $this->param->attributes['selector']): ?> selected="true" default <?php endif; ?> selector="<?php echo $key; ?>">
        <?php echo $selector['name']; ?>
      </option>
      <?php endforeach; ?>       
    </select>
  </div>
  <a href="#" id="select-it">Selector</a>
</div>    
    <?php
    echo ob_get_clean(); 
  } 
}