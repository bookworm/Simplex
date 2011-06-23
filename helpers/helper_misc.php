<?php    

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * Simplex Misc Helpers for working with misc stuff. 
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

// --------------------------------------------------------------------

/**
 * Prints out the value and exits
 *
 * @param $var
 * @return void
 */    
if(!function_exists('stop'))     
{
  function stop($var) 
  {
    header('Content-Type: text/plain');
    print_r($var);
    exit;
  }  
}

// --------------------------------------------------------------------

/**
 * Convert links, hash tags, @s etc in a Twitter post to links
 *
 * @param string $ret The String to 'Twitterify'
 * @return string  
 */      
if(!function_exists('twitterify'))  
{
  function twitterify($ret) 
  {
    $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
    $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
    $ret = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
    $ret = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret);
    return $ret;
  } 
}

// --------------------------------------------------------------------

/**
 * Flashes a message
 *
 * @param string $message The message to flash.
 * @return void  
 */   
if(!function_exists('flashMessage'))
{
  function flashMessage($message)
  {
    ob_start();
    ?>  
<div class="flashMessage" style="background-color:red; color:black; 
padding:14px; position:absolute; top:0px; left:38%; right:38%; width:400px;">
  <?php echo $message; ?>
</div>  
    <?php
    echo ob_get_clean();
  } 
}