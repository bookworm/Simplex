<?php  

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------

/**
 * JS Minify Script
 *
 * @note Not Sure Where This Regex Came From Originally, I'm guessing multiple sources,
 * but thanks whoever wrote it. 
 *    
 * @package     simplex
 * @subpackage  helpers
 * @version     0.7 alpha 
 * @author      Ken Erickson AKA Bookworm http://www.bookwormproductions.net
 * @copyright   Copyright 2009 - 2011 Design BreakDown, LLC.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2       
 * please visit the Simplex site http://www.simplex.designbreakdown.com  for support. 
 * Do not e-mail (or god forbid IM or call) me directly.
 */   
class Splex_Minify_JS 
{
  function minify($str) 
  {
    $res = '';
    $maybe_regex = true;
    $i=0;
    $current_char = '';
    while ($i+1<strlen($str)) {
      if ($maybe_regex && $str[$i]=='/' && $str[$i+1]!='/' && $str[$i+1]!='*' && @$str[$i-1]!='*') {//regex detected
        if (strlen($res) && $res[strlen($res)-1] === '/') $res .= ' ';
        do {
          if ($str[$i] == '\\') {
            $res .= $str[$i++];
          } elseif ($str[$i] == '[') {
            do {
              if ($str[$i] == '\\') {
                $res .= $str[$i++];
              }
              $res .= $str[$i++];
            } while ($i<strlen($str) && $str[$i]!=']');
          }
          $res .= $str[$i++];
        } while ($i<strlen($str) && $str[$i]!='/');
        $res .= $str[$i++];
        $maybe_regex = false;
        continue;
      } elseif ($str[$i]=='"' || $str[$i]=="'") {//quoted string detected
        $quote = $str[$i];
        do {
          if ($str[$i] == '\\') {
            $res .= $str[$i++];
          }
          $res .= $str[$i++];
        } while ($i<strlen($str) && $str[$i]!=$quote);
        $res .= $str[$i++];
        continue;
      } elseif ($str[$i].$str[$i+1]=='/*' && @$str[$i+2]!='@') {//multi-line comment detected
        $i+=3;
        while ($i<strlen($str) && $str[$i-1].$str[$i]!='*/') $i++;
        if ($current_char == "\n") $str[$i] = "\n";
        else $str[$i] = ' ';
      } elseif ($str[$i].$str[$i+1]=='//') {//single-line comment detected
        $i+=2;
        while ($i<strlen($str) && $str[$i]!="\n") $i++;
      }

      $LF_needed = false;
      if (preg_match('/[\n\r\t ]/', $str[$i])) {
        if (strlen($res) && preg_match('/[\n ]/', $res[strlen($res)-1])) {
          if ($res[strlen($res)-1] == "\n") $LF_needed = true;
          $res = substr($res, 0, -1);
        }
        while ($i+1<strlen($str) && preg_match('/[\n\r\t ]/', $str[$i+1])) {
          if (!$LF_needed && preg_match('/[\n\r]/', $str[$i])) $LF_needed = true;
          $i++;
        }
      }

      if (strlen($str) <= $i+1) break;

      $current_char = $str[$i];

      if ($LF_needed) $current_char = "\n";
      elseif ($current_char == "\t") $current_char = " ";
      elseif ($current_char == "\r") $current_char = "\n";

      // detect unnecessary white spaces
      if ($current_char == " ") {
        if (strlen($res) &&
          (
          preg_match('/^[^(){}[\]=+\-*\/%&|!><?:~^,;"\']{2}$/', $res[strlen($res)-1].$str[$i+1]) ||
          preg_match('/^(\+\+)|(--)$/', $res[strlen($res)-1].$str[$i+1]) // for example i+ ++j;
          )) $res .= $current_char;
      } elseif ($current_char == "\n") {
        if (strlen($res) &&
          (
          preg_match('/^[^({[=+\-*%&|!><?:~^,;\/][^)}\]=+\-*%&|><?:,;\/]$/', $res[strlen($res)-1].$str[$i+1]) ||
          (strlen($res)>1 && preg_match('/^(\+\+)|(--)$/', $res[strlen($res)-2].$res[strlen($res)-1])) ||
          preg_match('/^(\+\+)|(--)$/', $current_char.$str[$i+1]) ||
          preg_match('/^(\+\+)|(--)$/', $res[strlen($res)-1].$str[$i+1])// || // for example i+ ++j;
          )) $res .= $current_char;
      } else $res .= $current_char;

      // if the next charachter be a slash, detects if it is a divide operator or start of a regex
      if (preg_match('/[({[=+\-*\/%&|!><?:~^,;]/', $current_char)) $maybe_regex = true;
      elseif (!preg_match('/[\n ]/', $current_char)) $maybe_regex = false;

      $i++;
    }
    if ($i<strlen($str) && preg_match('/[^\n\r\t ]/', $str[$i])) $res .= $str[$i];
    return $res;
  }
}