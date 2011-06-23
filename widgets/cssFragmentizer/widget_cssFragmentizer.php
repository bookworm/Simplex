<?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );    

// Load Some Needed Files     
$splex = getSplexInstance();
$splex->loader->load_include('splex_css.php');

// ------------------------------------------------------------------------

/**
 * Saves css fragments to custom.css.
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
class Muwt_Widget_cssFragmentizer extends Muwt_Widget 
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
    
    if(checkHead('codemirror', 'scripts') == false)
      $splex->muwt->addScript('codemirror');
    if(checkHead('cssFragmenter', 'scripts') == false)
      $splex->muwt->addScript('cssFragmenter');   
  }

// ------------------------------------------------------------------------

  /**
   * Generates The JS for widget.
   *                                                               
   * @return void
   **/   
  public function _createJS()
  {  
    $splex = getSplexInstance();    
    
    $relativePath = $splex->loader->getFileURL('codemirror.js', 'jsfiles');    
    $relativePath = str_replace('/js/codemirror.js', '', $relativePath);
    ob_start();
    ?> 
    var $$ = jQuery;
    var editor; 
    var cssString; 
    var parseRules;
    $$(document).ready(function() {
      editor = CodeMirror.fromTextArea('cssFragementer', {
        height: "dynamic",
        parserfile: "parsecss.js",
        stylesheet: "<?php echo $relativePath; ?>/css/csscolors.css",
        path: "<?php echo $relativePath; ?>/js/",
        onChange: function() {     
          cssString = editor.getCode();   
          parsedCSS = parseCSS(cssString);    
          var len = parsedCSS.length; 
          
          for (var i = 0; i < len; i++)
          {    
            var rules = parsedCSS[i]['rules'];   
            var selector = parsedCSS[i]['selector'];
            for (var key in rules) 
            { 
              if (rules.hasOwnProperty(key)) { 
                // console.log(key + " -> " + rules[key]); 
                $$(selector).globalcss(key, rules[key]); 
              }
            }
          }
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
<div class="param cssFragementer section">    
   <div class="code-editor-wrap">
     <div class="code-editor-top"></div>
     <div class="code-editor-center">
      <textarea class="cssFragementer" rows="8" cols="40" name="<?php echo $this->paramName;?>" id="cssFragementer"><?php echo $this->param->value; ?></textarea>     
     </div>
     <div class="code-editor-bottom"></div> 
   </div>  
</div> 
    <?php
    echo ob_get_clean(); 
  } 
  
// ------------------------------------------------------------------------

  /**
   * Saves.
   *   
   * @return string
   **/
  public function save()
  {
    $splex = getSplexInstance();
    jimport('joomla.filesystem.file'); 
    jimport('joomla.environment.request'); 
     
    $cssFilePath   = $splex->loader->getFilePath('custom.css', 'cssfiles');
    $cssString     = file_get_contents($cssFilePath);    
    $selector      = JRequest::getString('selector'); 
    $saveCSS       = JRequest::getString('cssFragment');   
  
    $css         = new Splex_CSS();  
    $sourceCSS   = $css->parse($cssString);
    $saveCSS     = $css->parse($saveCSS);    
    
    foreach($saveCSS->parsed as $k => $v) 
    {
      foreach($v->selector as $selector) 
      { 
        $found = $sourceCSS->findSelector($selector);         
        
        if($found != null)    
          $found->rules = $v->rules;
      } 
    }
    
    JFile::write($cssFilePath, $sourceCSS->__toString());        
    
    return JRequest::getString('cssFragment');
  }
}