<?php

namespace App\Container;

class SUB
{
    /**
     * @var Subscriber
     */
    static private $instance;

    /**
     * @return Subscriber
     */
    public static function instance(): Subscriber
    {
        if (!isset(self::$instance)) {
            self::$instance = new Subscriber();
        }
        return self::$instance;
    }
}