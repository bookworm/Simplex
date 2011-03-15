<?php   

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );           

// Load Splex + Prequisites
$splex = getSplexInstance();
$splex->loader->load_include(array('splex_cssvalue.php', 'splex_cssurl.php','splex_cssString.php','splex_cssSize.php', 
'splex_cssrule.php','splex_cssruleset.php','splex_cssSelector.php','splex_csslist.php','splex_cssmediaquery.php',
'splex_cssimport.php','splex_cssdocument.php','splex_csscolor.php','splex_cssatrule.php', 'splex_css_parser.php'));   

// ------------------------------------------------------------------------

/**
 * Full Fledged CSS Parser.  
 *
 * @note I've split the methods up into two classes.
 *  Splex_CSS_Parser, which contains the parser methods; and this class which contains the public API 
 *  Based on the css parser by Raphael Schweikert https://github.com/sabberworm/PHP-CSS-Parse
 * 
 * @credits     Parts of this code were pulled from cssScaffold https://github.com/anthonyshort/Scaffold/
 * @package     simplex
 * @subpackage  libraries
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.  
 * @author      2010 Raphael Schweikert http://www.sabberworm.com/
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
class Splex_CSS extends Splex_CSS_Parser
{ 
  /**
   * @var string The CSS string.
   **/
  var $css;  
  
  /**
   * Constructor Function.
   *  
   * @param string $css CSS String.
   * @return Splex_CSSDocument
   */ 
  public function __construct($css = null) 
  {   
    if(!is_null($css))
      return $this->parse($css);
  }                                                                          
  
// ------------------------------------------------------------------------
  
  /**
   * Encodes a selector so that it's able to be used in regular expressions       
   *
   * @param $selector
   * @return string
   */
  public function escapeRegex($selector)
  {
    $selector = preg_quote($selector,'-');
    $selector = str_replace('#','\#',$selector);
    $selector = preg_replace('/\s+/',"\s+",$selector);
    return $selector;
  }    
  
// ------------------------------------------------------------------------
  
  /**
   * Finds a selector and returns it as string      
   * 
   * @todo This will break if the selector they try and find is actually part of another selector
   * @param $selector string        
   * @return string $match[2]
   */
  public function findSelectors($selector, $escape = true)
  {
    $selector = ($escape) ? self::escapeRegex($selector) : $selector;
    $regex = "/(^|[\}\s\;])* ( ($selector) \s*\{[^}]*\} )/sx";
    return preg_match_all($regex, $this->css, $match) ? $match[2] : array();
  } 
  
// ------------------------------------------------------------------------
  
  /**
   * Finds and returns all the selectors in the css.
   *     
   * @todo support multiple selectors.  
   * @return array $matches[1]
   */
  public function findAllSelectors()
  { 
    $matches = array();
    preg_match_all('/(.+?)\s?\{\s?(.+?)\s?\}/', $this->css, $matches);
    return $matches[1];
  }
}