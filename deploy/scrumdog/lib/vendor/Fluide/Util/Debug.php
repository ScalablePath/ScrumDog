<?php
/**
 *
 * @category   Fluide
 * @package    Fluide_Util
 */

class Fluide_Util_Debug
{
    public static function shout($var)
    {
		if($var===false)
			$var = '*FALSE*';
		
		if($var===true)
			$var = '*TRUE*';
			
		if($var===NULL)
			$var = '*NULL*';
		
		if (is_array($var))
			echo(nl2br(str_replace( ' ', '&nbsp;', htmlspecialchars(print_r( $var, true )))));
		elseif (is_object($var))
			echo(nl2br(str_replace( ' ', '&nbsp;', htmlspecialchars(print_r( $var, true )))));
		else
			echo htmlspecialchars($var).'<br />'."\n";
    }

    public static function shoutLog($var, $filename, $append = true)
    {
		$logFile = '/home/fluide/www/dev.scrumdog/current/scrumdog/data/log/'.$filename;

		if($var===false)
			$var = '*FALSE*';
		
		if($var===true)
			$var = '*TRUE*';
			
		if($var===NULL)
			$var = '*NULL*';
		
		if (is_array($var) || is_object($var))
			$var = print_r($var, true);

		if($append)
			$fh = fopen($logFile, 'a');
		else
			$fh = fopen($logFile, 'w');

		fwrite($fh, $var);

		fclose($fh);
    }
}
