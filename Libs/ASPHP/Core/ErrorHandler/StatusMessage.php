<?php
namespace ASPHP\Core\ErrorHandler;
use \ASPHP\Core\Types\Singleton;

/**
 * @requires class \ASPHP\Core\Types\Singleton
 */
final class StatusMessage extends Singleton
{
    const ERROR = "ERROR", SUCCESS = "SUCCESS";

    /**
     * @var int
     */
    private $code = 400;

    /**
     * @var string
     */
    private $message = "Bad Request";

    /**
     * @var string
     */
    private $statusType = "ERROR";

    /**
     * @return integer
     */
    public function GetCode()
    {
        return $this->code;
    }

    /**
     * @param integer $value 
     */
    public function SetCode($value)
    {
        $this->code = $value;
    }

    /**
     * @return string
     */
    public function GetMessage()
    {
        return $this->message;
    }

    /**
     * @param string $value 
     */
    public function SetMessage($value)
    {
        $this->message = $value;
    }

    /**
     * @return string
     */
    public function GetStatusType()
    {
        return $this->statusType;
    }

    /**
     * @param string $value 
     */
    public function SetStatusType($value)
    {
        $this->statusType = $value;
    }
}
?>