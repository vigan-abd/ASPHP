<?php
namespace ASPHP\Core\Web;
use ASPHP\Core\Types\StaticClass;

/**
 * @requires ASPHP\Core\Types\StaticClass
 * @version 1.0
 * @author Vigan
 */
class CSRF extends StaticClass
{
    /**
	 * Generates html input hidden fields for csrf token
	 * @return string
	 */
    public static function GenerateToken()
    {
        if(!isset($_SESSION['token']) || empty($_SESSION['token']))
        {
            $_SESSION['token'] = md5(uniqid(rand(), TRUE));
            $_SESSION['tokenTimestamp'] = time();
        }
        return '<input type="hidden" name="token" value="'.$_SESSION['token'].'" />
            <input type="hidden" name="tokenTimestamp" value="'.$_SESSION['tokenTimestamp'].'" />';
    }

    /**
	 * @param string $token
	 * @param int $tokenTimestamp
	 */
    public static function VerifyToken($token, $tokenTimestamp)
    {
        if(isset($_SESSION['token']) && isset($_SESSION['tokenTimestamp']) && !empty($_SESSION['token']) && !empty($_SESSION['tokenTimestamp']))
        {
            if($token != $_SESSION['token'] || $tokenTimestamp != $_SESSION['tokenTimestamp'])
            {
                throw new \Exception("Invalid token or expired token");
            }
            else
            {
                unset($_SESSION['token']);
                unset($_SESSION['tokenTimestamp']);
            }
        }
        else
        {
            throw new \Exception("Invalid token");
        }
    }
}
?>