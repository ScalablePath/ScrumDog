<?php
/**
 *
 * @category   Fluide
 * @package    Fluide_Symfony
 */

class Fluide_Symfony_Util
{
    public static function checkRouteEquality($route1, $route2)
    {
       if(strpos($route1, '@'))
         $route1 = substr($route1, 1);

       if(strpos($route2, '@'))
         $route1 = substr($route2, 1);

       $qPos = strpos($route1, '?');
       if($qPos>0)
         $route1 = substr($route1, 0, $qPos);

       $qPos = strpos($route2, '?');
       if($qPos>0)
         $route2 = substr($route2, 0, $qPos);

       return $route1==$route2;
    }

    public static function getCurrentRouteScope()
	{
		$currentRoute = sfContext::getInstance()->getRouting()->getCurrentInternalUri(true);
    	$currentRouteArray = explode('_', $currentRoute);
		return(str_replace('@', '', $currentRouteArray[0]));
 	}

	public static function generateRandomKey($minlength, $maxlength, $useupper=true, $usenumbers=true, $usespecial=false)
	{
		/*
		Description: string str_makerand(int $minlength, int $maxlength, bool $useupper, bool $usespecial, bool $usenumbers)
		returns a randomly generated string of length between $minlength and $maxlength inclusively.
		
		Notes:
		- If $useupper is true uppercase characters will be used; if false they will be excluded.
		- If $usespecial is true special characters will be used; if false they will be excluded.
		- If $usenumbers is true numerical characters will be used; if false they will be excluded.
		- If $minlength is equal to $maxlength a string of length $maxlength will be returned.
		- Not all special characters are included since they could cause parse errors with queries.
		
		Modify at will.
		*/
		$charset = "abcdefghijklmnopqrstuvwxyz";
		if ($useupper) $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[":;'><,./";
		if($minlength > $maxlength)
			$length = mt_rand ($maxlength, $minlength);
		else
			$length = mt_rand ($minlength, $maxlength);
		for ($i=0; $i<$length; $i++)
		{
			$key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		}
		return $key;
	}

	public static function isValidEmail($email)
	{
		return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
	}

	public static function get_timezone_offset($remote_tz, $origin_tz = null)
	{		
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime("now", $origin_dtz);
		$remote_dt = new DateTime("now", $remote_dtz);
		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
		return $offset / 60 / 60;
	}

	public static function sign($x)
	{
 		return (int) ((abs($x)-$x) ? -1 : $x>0);
	}

	public static function emailLink($emailAddress)
	{
 		$email_array = explode('@', $emailAddress);

		$s = "<script class=\"jscrip\" type=\"text/javascript\">\n";
		$s .= "var name = '".$email_array[0]."'; var domain = '".$email_array[1]."'; document.write('<a class=\"more\" href=\"mailto:'+name+'@'+domain+'\">'+name+'@'+domain+'</a>');\n";
		$s .= "</script>.</p>";

		return $s;		
	}
}
