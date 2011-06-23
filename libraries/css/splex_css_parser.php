<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ------------------------------------------------------------------------   

/**
 * Holds the parse and consume methods for the css parser.
 *
 * @note Not the best composition but its good enough.    
 *  Based on the css parser by Raphael Schweikert https://github.com/sabberworm/PHP-CSS-Parser
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
class Splex_CSS_Parser
{      
  /**
   * @var int $pos The current position in the css string.        
   */
  var $pos = 0;    
  
  /**
   * @var int $length The length of the css string.
   */
  var $length = 0;   
  
  /**
   * @var array $cssDocs Array holding all the css docs.
   */
  var $cssDocs = array();
  
  /**
   * Constructor Function.
   *
   * @note Pretty much empty for now like the soul of your next door neighbor.   
   *  He is the devil, trust me.  Those hedges? Yep no coincidence, they were meant too look like Hitler.
   *
   * @return void
   */ 
  public function __construct() { }  
  
                     // Core Parse Methods //
// ------------------------------------------------------------------------
  
  /**
   * Starts the parsing process.
   * 
   * @param string $css CSS String.
   * @return void
   */
  public function parse($css = null)
  {   
    if(!is_null($css)) 
      $this->css = $css;   
      
    $this->pos       = 0;
    $this->length    = mb_strlen($this->css);   
    $this->cssDocs[] = new Splex_CSSDocument();  
    
    $this->consumewhiteSpace();           
    $this->parseList(end($this->cssDocs));   
    return end($this->cssDocs);
  }   
  
// ------------------------------------------------------------------------  
  
  /**
   * Parses The CSS Document.
   *   
   * @param Splex_CSSList $cssList A css list document.
   * @param bool $isRoot Is thtis the root css list?
   * @return void
   */
  public function parseList(Splex_CSSList $cssList, $isRoot = false) 
  {
    while(!$this->isEnd()) 
    {
      if($this->comes('@')) {
        $cssList->append($this->parseAtRule());
      } 
      else if($this->comes('}')) 
      {
        $this->consume('}');
        
        if($isRoot)
          throw new Exception("Unopened {");
        else
          return;
      } 
      else {
        $cssList->append($this->parseSelector());
      }        
      
      $this->consumeWhiteSpace();
    }    
    
    # if(!$isRoot) 
     # throw new Exception("Unexpected end of document");
  }
  
                     // Parse Methods //
// ------------------------------------------------------------------------  
 
  /**
   * Parses An At Rule.
   *   
   * @return object $atRule
   */
  public function parseAtRule() 
  {
    $this->consume('@');  
    
    $identifier = $this->parseIdentifier();  
    
    $this->consumeWhiteSpace();  
    
    if($identifier === 'media')
    {
      $result = new Splex_CSSMediaQuery();   
      
      $result->query = trim($this->consumeUntil('{'));
      $this->consume('{');
      $this->consumeWhiteSpace();
      $this->parseList($result);    
      
      return $result;
    } 
    else if($identifier === 'import') 
    {
      $location = $this->parseURLValue();
      $this->consumeWhiteSpace();  
      
      $mediaQuery = null;
      if(!$this->comes(';')) {
        $mediaQuery = $this->consumeUntil(';');
      }
      $this->consume(';'); 
      
      return new Splex_CSSImport($location, $mediaQuery);
    } 
    else 
    {
      //Unknown other at rule (font-face or such)
      $this->consume('{');
      $this->consumeWhiteSpace();  
      
      $atRule = new Splex_CSSAtRule($identifier);     
      
      $this->parseRuleSet($atRule);   
      
      return $atRule;
    }
  }     
  
// ------------------------------------------------------------------------  
  
  /**
   * Parses identifiers such as @import
   *   
   * @return mixed Usually a string
   */
  public function parseIdentifier() 
  {
    $result = $this->parseCharacter(true); 
    
    if($result === null)
      throw new Exception("Identifier expected, got {$this->peek(5)}");
      
    $character;   
    
    while(($character = $this->parseCharacter(true)) !== null)
      $result .= $character;         
    
    return $result;
  }
  
// ------------------------------------------------------------------------  
      
  /**
   * Character parsing.
   *   
   * @param bool $isForIndentifier
   * @return mixed Usually a string
   */
  public function parseCharacter($isForIdentifier) 
  {
    if($this->peek() === '\\') 
    {
      $this->consume('\\');  
      
      if($this->comes('\n') || $this->comes('\r')) 
        return '';     
        
      $matches;       
      
      if(preg_match('/[0-9a-fA-F]/Su', $this->peek()) === 0) 
        return $this->consume(1);           
        
      $unicode = $this->consumeExpression('/[0-9a-fA-F]+/u'); 
      
      if(mb_strlen($unicode) < 6) 
      {
        //Consume whitespace after incomplete unicode escape
        if(preg_match('/\\s/isSu', $this->peek())) 
        {
          if($this->comes('\r\n')) 
            $this->consume(2);
          else 
            $this->consume(1);
        }
      }
      $utf16 = ''; 
      
      if((strlen($unicode) % 2) === 1) {
        $unicode = "0$unicode";
      }        
      
      for($i = 0; $i < strlen($unicode); $i+=2) {
        $utf16 .= chr(intval($unicode[$i].$unicode[$i+1]));
      }            
      
      return iconv('utf-16', 'utf-8', $utf16);   
    }
    if($isForIdentifier) 
    {
      if(preg_match('/[a-zA-Z0-9]|-|_/u', $this->peek()) === 1) 
        return $this->consume(1);
      else if(ord($this->peek()) > 0xa1)
        return $this->consume(1);
      else 
        return null;
    } 
    else {
      return $this->consume(1);
    }             
    
    // Does not reach here
    return null;
  }
  
// ------------------------------------------------------------------------  
  
  /**
   * Parses URL Values.
   *   
   * @return Splex_CSSURL
   */
  public function parseURLValue() 
  {
    $useUrl = $this->comes('url');   
    
    if($useUrl) 
    {
      $this->consume('url');
      $this->consumeWhiteSpace();
      $this->consume('(');
    }   
    
    $this->consumeWhiteSpace();
    $result = new Splex_CSSURL($this->parseStringValue());      
    
    if($useUrl) {
      $this->consumeWhiteSpace();
      $this->consume(')');
    }      
    
    return $result;
  }
  
// ------------------------------------------------------------------------  
    
  /**
   * Parses a string.
   *   
   * @return Splex_CSSString
   */
  public function parseStringValue() 
  {
    $begin = $this->peek();
    $quote = null;   
    
    if($begin === "'")
      $quote = "'";
    else if($begin === '"')
      $quote = '"';
    
    if($quote !== null)
      $this->consume($quote);  
    
    $result = "";
    $content = null;      
    
    if($quote === null) 
    {
      //Unquoted strings end in whitespace or with braces, brackets, parentheses
      while(!preg_match('/[\\s{}()<>\\[\\]]/isu', $this->peek()))
        $result .= $this->parseCharacter(false);
    } 
    else 
    {
      while(!$this->comes($quote)) 
      {
        $content = $this->parseCharacter(false);           
        
        if($content === null) 
          throw new Exception("Non-well-formed quoted string {$this->peek(3)}");     
          
        $result .= $content;
      }               
      
      $this->consume($quote);
    }         
    
    return new Splex_CSSString($result);
  } 
  
// ------------------------------------------------------------------------  
  
  /**
   * Parses a selector.
   *   
   * @return Splex_CSSSelector
   */
  public function parseSelector() 
  {
    $result = new Splex_CSSSelector();
    $result->setSelector($this->consumeUntil('{'));         
    
    $this->consume('{');
    $this->consumeWhiteSpace();
    $this->parseRuleSet($result);  
    
    return $result;
  }
   
// ------------------------------------------------------------------------  
  
  /**
   * Parses a ruleset.
   *   
   * @param object $ruleSet A ruleset object
   * @return void
   */
  public function parseRuleSet($ruleSet) 
  {
    while(!$this->comes('}')) {
      $ruleSet->addRule($this->parseRule());
      $this->consumeWhiteSpace();
    }   
    
    $this->consume('}');
  }
  
// ------------------------------------------------------------------------  
        
  /**
   * Parses a rule.
   *   
   * @return Splex_CSSRule
   */
  public function parseRule() 
  {
    $rule = new Splex_CSSRule($this->parseIdentifier());   
    
    $this->consumeWhiteSpace();
    $this->consume(':');
    $this->consumeWhiteSpace();   
    
    while(!($this->comes('}') || $this->comes(';') || $this->comes('!'))) {
      $rule->values[] = $this->parseValue();
      $this->consumeWhiteSpace();
    }  
    
    if($this->comes('!')) 
    {
      $this->consume('!');
      $this->consumeWhiteSpace();
      $importantMarker = $this->consume(strlen('important'));     
      
      if(mb_convert_case($importantMarker, MB_CASE_LOWER) !== 'important') 
        throw new Exception("! was followed by “".$importantMarker."”. Expected “important”"); 
        
      $rule->isImportant = true;
    }             
    
    if($this->comes(';'))
      $this->consume(';'); 
    
    return $rule;
  } 
   
// ------------------------------------------------------------------------  
  
  /**
   * Parses a css value.
   *   
   * @return object
   */
  public function parseValue() 
  {
    $result = array();  
    
    do 
    {
      $this->consumeWhiteSpace(); 
      
      if(is_numeric($this->peek()) || $this->comes('-') || $this->comes('.'))
        $result[] = $this->parseNumericValue();
      else if($this->comes('#') || $this->comes('rgb') || $this->comes('hsl'))
        $result[] = $this->parseColorValue();
      else if($this->comes('url'))
        $result[] = $this->parseURLValue();
      else if($this->comes("'") || $this->comes('"'))
        $result[] = $this->parseStringValue();
      else 
        $result[] = $this->parseIdentifier();
        
      $this->consumeWhiteSpace();
    } while($this->comes(',') && is_string($this->consume(',')));
    
    return $result;
  }
  
// ------------------------------------------------------------------------  
  
  /**
   * Parses a numeric/size value.
   *   
   * @return Splex_CSSSize
   */
  public function parseNumericValue() 
  {
    $size = ''; 
    
    if($this->comes('-'))
      $size .= $this->consume('-');     
      
    while(is_numeric($this->peek()) || $this->comes('.')) 
    {
      if($this->comes('.'))
        $size .= $this->consume('.');
      else 
        $size .= $this->consume(1);
    }   
    
    $fSize = floatval($size);
    $unit = null;       
    
    if($this->comes('%'))
      $unit = $this->consume('%');
    else if($this->comes('em'))
      $unit = $this->consume('em');
    else if($this->comes('ex'))
      $unit = $this->consume('ex');
    else if($this->comes('px'))
      $unit = $this->consume('px');
    else if($this->comes('cm'))
      $unit = $this->consume('cm');
    else if($this->comes('pt'))
      $unit = $this->consume('pt'); 
    else if($this->comes('in'))
      $unit = $this->consume('in'); 
    else if($this->comes('pc'))
      $unit = $this->consume('pc');
    else if($this->comes('cm'))
      $unit = $this->consume('cm');
    else if($this->comes('mm')) 
      $unit = $this->consume('mm');
      
    return new Splex_CSSSize($fSize, $unit);
  }
  
// ------------------------------------------------------------------------  
  
  /**
   * Parses a color value
   *   
   * @return mixed Either Splex_CSSColor or Splex_CSSSize
   */
  public function parseColorValue() 
  {
    $color = array();
    
    if($this->comes('#')) 
    {
      $this->consume('#');
      $value = $this->parseIdentifier();  
      
      if(mb_strlen($value) === 3)
        $value = $value[0].$value[0].$value[1].$value[1].$value[2].$value[2];      
        
      $color = array('r' => new Splex_CSSSize(intval($value[0].$value[1], 16)), 'g' => new Splex_CSSSize(intval($value[2].$value[3], 16)), 'b' => new Splex_CSSSize(intval($value[4].$value[5], 16)));
    } 
    else 
    {
      $colorMode = $this->parseIdentifier();
      
      $this->consumeWhiteSpace();
      $this->consume('(');          
      
      $length = mb_strlen($colorMode);  
      
      for($i = 0; $i < $length; $i++)
      {
        $this->consumeWhiteSpace();     
        
        $color[$colorMode[$i]] = $this->parseNumericValue();
        $this->consumeWhiteSpace();   
        
        if($i < ($length - 1))
          $this->consume(',');
      }         
      
      $this->consume(')');
    }    
    
    return new Splex_CSSColor($color);
  }

                // General Consume Methods //     
         /* Consume methods essentially just ignore/skip 
            stuff preventing it from being parsed */     
// ------------------------------------------------------------------------
  
  /**
   * Skips the position ($this->pos) along a certain value.
   *     
   * @param int $value A value to add to the position.
   * @return void
   */
  public function consume($value = 1) 
  {
    if(is_string($value)) 
    {
      $length = mb_strlen($value);   
      
      if(mb_substr($this->css, $this->pos, $length) !== $value)
        throw new Exception("Expected $value, got ".$this->peek(5));

      $this->pos += mb_strlen($value);    
            
      return $value;
    } 
    else 
    {
      if($this->pos + $value > $this->length)
        throw new Exception("Tried to consume $value chars, exceeded file end");   
        
      $result = mb_substr($this->css, $this->pos, $value);
      $this->pos += $value; 
        
      return $result;
    }
  } 
  
// ------------------------------------------------------------------------  
  
  /**
   * Skips until a position is reached.
   *     
   * @param int $end The end position.
   * @return void
   */
  public function consumeUntil($end) 
  {
    $endPos = mb_strpos($this->css, $end, $this->pos);

    if($endPos === false) 
      throw new Exception("Required $end not found, got {$this->peek(5)}");
      
    return $this->consume($endPos - $this->pos);
  } 
  
                     // Specific Consume Methods //     
// ------------------------------------------------------------------------  
  
  /**
   * Consumes whitespace.
   * 
   * @return void
   */  
  public function consumewhiteSpace() {
    do 
    {
      while(preg_match('/\\s/isSu', $this->peek()) === 1) {
        $this->consume(1);
      }
    } while($this->consumeComment());
  }
    
// ------------------------------------------------------------------------  
  
  /**
   * Consumes comments.
   * 
   * @return bool
   */
  public function consumeComment() 
  { 
    if($this->comes('/*')) 
    {       
      $this->consumeUntil('*/');
      $this->consume('*/');
      return true;
    }
    return false;
  }
  
// ------------------------------------------------------------------------  
  
  /**
   * Consumes expressions
   *           
   * @param string $expression The expression to devour.
   * @return bool
   */  
  public function consumeExpression($expression) 
  {
    $matches;      
    
    if(preg_match($expression, $this->inputLeft(), $matches) === 1) 
    {
      if($matches[0][1] === $this->pos) 
        return $this->consume($matches[0][0]);
    }       
    else {
      throw new Exception("Expected pattern $expression not found, got: {$this->peek(5)}"); 
    }
  }

                     // Position Methods. //
         /* These methods help looking ahead and behind. */
// ------------------------------------------------------------------------
     
  /**
   * Is this the end of the string as we know it?
   * 
   * @return bool
   */
  public function isEnd() 
  {
    return $this->pos >= $this->length;
  }  
  
  /**
   * Peek ahead and behind.
   *   
   * @param int $length How far to look ahead?
   * @param int $offset offset the look ahead.
   * @return string
   */
  public function peek($length = 1, $offset = 0) 
  {
    if($this->isEnd())
      return '';     
      
    if(is_string($length))
      $length = mb_strlen($length);  
    
    if(is_string($offset)) 
      $offset = mb_strlen($offset);
      
    return mb_substr($this->css, $this->pos + $offset, $length);
  }

// ------------------------------------------------------------------------   
  
  /**
   * Whats coming around the bend?
   * 
   * @param string $string The string to peek at.  
   * @param int $offset offset the look ahead.  
   * @return string
   */
  public function comes($string, $offset = 0) 
  {
    if($this->isEnd())
      return false; 
       
    return $this->peek($string, $offset) == $string;
  }  
  
// ------------------------------------------------------------------------   
  
  /**
   * Gets the string to the left of the current position.
   * 
   * @return string
   */
  public function inputLeft() 
  {
    return mb_substr($this->text, $this->pos, -1);
  }
}