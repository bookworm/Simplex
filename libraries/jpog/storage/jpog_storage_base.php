<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );   

// ------------------------------------------------------------------------

/**
 * Jpog base class for storage mechanisms. 
 * 
 * @note Everything in these classes is wrapped up into splex itself and place in the jpog object; i.e $splex->.
 *
 * @note Explanation of terminology. Getting a param refers to getting its saved value, printing a param refers to 
 * echoing the params associated widget out. A widget is essentially the front-end (css, html and js) of a parameter.
 * Saving a param does exactly what the name implies, it saves a value for the paramater.
 *
 * Usage: 
 * {{{
 *    echo $splex->jpog->render('colorInput');  
 * }}}
 *    
 * @package     simplex
 * @subpackage  libraries.jpog.storage
 * @version     2.0 pre-alpha. 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */
class Jpog_Storage 
{    
  /**
   * Dumps a modified params array to the storage system. In this case yaml.
   *
   * @param array $saveDump A modified parent::$paramsdump ready for dumping
   * @return void
   **/
  public function dump($saveDump)
  {  
    jimport('joomla.filesystem.file');   
    
    $yamlstring = sfYaml::dump($saveDump);  
    JFile::write($this->paramsFilepath, $yamlstring); 
    unset($yamlstring);
  }  
}