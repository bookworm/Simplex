<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * CSS Minify Script
 *
 * @note Class From The Minify Project http://code.google.com/p/minify/ By Stephen Clay
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
class Splex_Minify_CSS
{
  /**
   * Minify a CSS string
   * 
   * @param string $css
   * 
   * @param array $options available options:
   * 
   * 'preserveComments': (default true) multi-line comments that begin
   * with "/*!" will be preserved with newlines before and after to
   * enhance readability.
   * 
   * 'prependRelativePath': (default null) if given, this string will be
   * prepended to all relative URIs in import/url declarations
   * 
   * 'currentDir': (default null) if given, this is assumed to be the
   * directory of the current CSS file. Using this, minify will rewrite
   * all relative URIs in import/url declarations to correctly point to
   * the desired files. For this to work, the files *must* exist and be
   * visible by the PHP process.
   *
   * 
   * @return string
  */
  function minify($css, $options = array()) 
  {
    require_once 'minify' . DS . 'CSS' . DS . 'Compressor.php';  

    if (isset($options['preserveComments']) && !$options['preserveComments']) {
      $css = Minify_CSS_Compressor::process($css, $options);    
    } 
    else 
    {
      require_once 'minify' . DS . 'CommentPreserver.php';
      $css = Minify_CommentPreserver::process(
        $css
        ,array('Minify_CSS_Compressor', 'process')
        ,array($options)
      );
    }
    if (! isset($options['currentDir']) && ! isset($options['prependRelativePath'])) {
      return $css;
    }
    require_once 'minify' . DS . 'CSS' . DS . 'UriRewriter.php';
    if (isset($options['currentDir'])) 
    {
      return Minify_CSS_UriRewriter::rewrite(
        $css
        ,$options['currentDir']
        ,isset($options['docRoot']) ? $options['docRoot'] : $_SERVER['DOCUMENT_ROOT']
        ,isset($options['symlinks']) ? $options['symlinks'] : array()
      );        
    } 
    else 
    {
      return Minify_CSS_UriRewriter::prepend(
        $css
        ,$options['prependRelativePath']
      );  
    } 
  }  
}