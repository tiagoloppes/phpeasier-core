<?php
class Text {
    private static $map1 = 'abcdefghijklpnopqrstuvwxyzABCDEFGHIJKLPNOPQRSTUVWXYZ0123456789';
	private static $map2 = 'taUQn4zvB2cTHpW65Iydw9XgVFJAuxblh0NpSPZDPsERfoC1Giqke7rK3LOj8Y';
	    
	public static function toUrl($str) {
		$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ
ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
		$b = 'aaaaaaaceeeeiiiidnoooooouuuuy
	bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
		$str = utf8_decode ( $str );
		$str = strtr ( $str, utf8_decode ( $a ), $b );
		$str = strtolower ( $str );
		$str = utf8_encode ( $str );
		$str = preg_replace ( '/[^a-z0-9_]/', '-', $str );
		$str = preg_replace ( '/-+|,|\./', '-', $str );
		$str = preg_replace ( '/(.+)-$/', '$1', $str );
		$str = preg_replace ( '/-{2,}/', '-', $str );
		return $str;
	}
	public static function nl2br($str, $tag = NULL, $boldIfRegExp = false) {
		if ($tag) {
			$lines = explode ( "\n", $str );
			$newStr = '';
			foreach ( $lines as $line ) {
				if ($boldIfRegExp != false && preg_match ( $boldIfRegExp, $line )) {
					$line = '<strong>' . $line . '</strong>';
				}
				$newStr .= '<' . $tag . '>' . $line . '</' . $tag . ">\n";
			}
			return $newStr;
		} else {
			return nl2br ( $str );
		}
	}
	public static function toLower($str) {
		return strtr ( strtolower ( $str ), "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß", "àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ" );
	}
	public static function toUpper($str) {
		return strtr ( strtoupper ( $str ), "àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß" );
	}
	public static function ucFirst($str) {
		return ucfirst ( self::toLower ( $str ) );
	}
	public static function ucWords($str) {
		return ucwords ( self::toLower ( $str ) );
	}
	public static function toKeywords($str, $stringMode = false, $glueChar = ',') {
		$str = self::toLower ( $str );
		$prep = array (
				' de ',
				' do ',
				' para ',
				' p/ ',
				' com ',
				' c/ ' 
		);
		$prepr = array (
				' ',
				' ',
				' ',
				' ',
				' ',
				' ' 
		);
		$str = preg_replace ( '/([0-9])\s*x\s*([0-9])/', '$1x$2', $str );
		$str = preg_replace ( '/([0-9]),([0-9])/', '$1.$2', $str );
		$str = preg_replace ( '/[_\s"\(\)\[\]\']/', ' ', $str );
		$str = preg_replace ( '/\s+/', ' ', $str );
		$str = preg_replace ( '/,+/', ',', $str );
		$str = preg_replace ( '/\s-\s/', ',', $str );
		$str = str_replace ( $prep, $prepr, $str );
		$akeys = array_unique ( explode ( ',', $str ) );
		$str = implode ( ',', $akeys );
		$str = preg_replace ( '/\s*,\s*/', ',', $str );
		$tags = explode ( ',', $str );
		$validTags = NULL;
		if (is_array ( $tags )) {
			foreach ( $tags as $tag ) {
				if (preg_match ( '/^[0-9]+$/', $tag ) || $tag == NULL)
					continue;
				$validTags [] = $tag;
			}
		}
		if ($stringMode)
			return implode ( $glueChar, $validTags );
		else
			return $validTags;
	}
	public static function ifNull($strTest, $strTrue, $strAlt=NULL) {
		if ($strTest == NULL)
			return $strTrue;
		else
		    return ($strAlt==NULL?$strTest:$strAlt);
	}
	public static function getFirstName($str) {
		$str = explode ( ' ', $str );
		return current ( $str );
	}
	public static function readMore($str, $length = 15, $points = true) {
		$points = strlen ( $str ) > $length ? '...' : '';
		return substr ( $str, 0, $length ) . $points;
	}
	public static function getOnlyNumbers($str) {
		return preg_replace ( '/[^0-9]+/', '', $str );
	}
	public static function urlToLink($str) {
		return preg_replace ( '/(https?:\/\/[a-zA-Z0-9_\.\/\?=&-]+)/', '<a href="$1" target="_blank">$1</a>', $str );
	}
	public static function shortName($text) {
		$text = preg_replace ( '/\\s[PC]\\/\\s/', " ", $text );
		$text = preg_replace ( '/ [\/-] /', " ", $text );
		$text = preg_replace ( '/[)(]/', "", $text );
		$text = str_replace ( array (
				' DE ',
				' PARA ',
				' COM ' 
		), array (
				' ',
				' ',
				' ' 
		), $text );
		preg_match ( '/^([^\s]+)\s(.*)/', $text, $matches );
		$firstName = $matches [1];
		$lastName = preg_replace ( '/([^\s]{4})[^\s]+\s/', "$1 ", $matches [2] );
		return $firstName . ' ' . $lastName;
	}
	public static function nshortName($text) {
		return $text;
	}
	public static function encrypt($text){
	    $newText = '';
	    for($i=0; $i<strlen($text);$i++){
	        $char       = substr($text, $i, 1);
	        $pos        = strpos(self::$map1,$char);
	        $newText    .= ($pos!==false) ? substr(self::$map2, $pos, 1) : $char;
	        
	    }
	    return base64_encode($newText);
	}
	public static function uncrypt($text){
	    $text = base64_decode($text);
	    $newText = '';
	    for($i=0; $i<strlen($text);$i++){
	        $char       = substr($text, $i, 1);
	        $pos        = strpos(self::$map2,$char);
	        $newText    .= ($pos!==false) ? substr(self::$map1, $pos, 1) : $char;
	    }
	    return $newText;
	}
}
