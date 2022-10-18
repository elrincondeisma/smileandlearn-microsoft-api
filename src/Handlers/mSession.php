<?php


namespace Smileandlearn\Microsoft\Handlers;
/**
 * 
 */
class mSession
{
    public static function set($key, $value)
    {
        $_SESSION['smileandlearn/microsoft'][$key] = $value;
    }
    public static function unset($key)
    {
        if ($this->get($key)) {
            unset($_SESSION['smileandlearn/microsoft'][$key]);
        }
    }
    public static function get($key)
    {
        return (isset($_SESSION['smileandlearn/microsoft'][$key]) ? $_SESSION['smileandlearn/microsoft'][$key] : null) ;
    }
}