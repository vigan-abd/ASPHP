<?php
namespace ASPHP\Core\Globalization;
use \ASPHP\Core\Types\StaticClass;

/**
 * @requires class \ASPHP\Core\Types\StaticClass
 * @requires class \ASPHP\Core\Globalization\EnumLanguage
 * @version 1.0
 * @author Vigan
 */
class CultureInfo extends StaticClass
{
    /**
     * @var string
     */
    protected static $language = null;
    
	/**
	 * @return string
	 */
	public static function GetLanguage()
    {
        if(static::$language == null)
        {
            if(empty($_SESSION['Lang']) && !isset($_COOKIE['Lang']))
            {
                static::$language = EnumLanguage::EN;
                $_SESSION['Lang'] = EnumLanguage::EN;
                setcookie("Lang", EnumLanguage::EN, time() + (86400 * 90), "/");
            }
            else if(isset($_COOKIE['Lang']))
            {
                if(EnumLanguage::GetFromString($_COOKIE['Lang']) != null)
                {
                    $_SESSION['Lang'] = $_COOKIE['Lang'];
                    static::$language = $_COOKIE['Lang'];
                }
                else
                {
                    static::$language = EnumLanguage::EN;
                }
            }
            else if(!empty($_SESSION['Lang']))
            {
                if(EnumLanguage::GetFromString($_SESSION['Lang']) != null)
                {
                    setcookie("Lang", $_SESSION['Lang'], time() + (86400 * 90), "/");
                    static::$language = $_SESSION['Lang'];
                }
                else
                {
                    static::$language = EnumLanguage::EN;
                }
            }
        }
        return static::$language;
    }

    /**
     * @param string $lang 
     * @throws \Exception 
     */
    public static function SetLanguage($lang)
    {
        $lang = EnumLanguage::GetFromString($lang);
        if($lang == null)
        {
            throw new \Exception("Language not supported");
        }
        else
        {
            $lang = EnumLanguage::GetAsString($lang);
            static::$language = $lang;
            setcookie("Lang", $lang, time() + (86400 * 90), "/");
            $_SESSION['Lang'] = $lang;
        }
    }
}