<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * CSS Import.
 *
 * @note Based on the css parser by Raphael Schweikert https://github.com/sabberworm/PHP-CSS-Parser
 *
 * @package     simplex
 * @subpackage  libraries
 * @version     1.0 beta 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.  
 * @author      2010 Raphael Schweikert http://www.sabberworm.com/
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.   
 */
class Splex_CSSImport 
{  
  /**
   * @var string The location url the import points at.
   **/
  var $location;    
  
  /**
   * @var string The media query string.
   **/
  var $mediaQuery;
  
  /**
   * Constructor.
   *  
   * @param Splex_CSSURL $location The location url the import points at.
   * @param string $mediaQuery The media query string.
   * @return void
   */
  public function __construct(Splex_CSSURL $location, $mediaQuery) 
  {
    $this->location = $location;
    $this->mediaQuery = $mediaQuery;
  }
  
// ------------------------------------------------------------------------   
  
  /**
   * Turns this into a css import string.
   *
   * @return string
   */
  public function __toString() 
  {
    return "@import ".$this->location->__toString().($this->mediaQuery === null ? '' : ' '.$this->mediaQuery).';';
  }
}