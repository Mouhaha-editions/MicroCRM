<?php
/**
 * Created by PhpStorm.
 * User: pierr
 * Date: 07/11/2018
 * Time: 21:28
 */

namespace CustomerBundle\Enums;


class ECustomerType
{
    const UNDEFINED = 0;
    const SUSPECT = 100;
    const PROSPECT = 200;
    const CLIENT = 300;

    public static function ToString($type)
    {
        switch ($type) {
            case self::SUSPECT:
                return 'ecustomer.suspect';
            case self::PROSPECT:
                return 'ecustomer.prospect';
            case self::CLIENT:
                return 'ecustomer.client';
            default:
                return 'ecustomer.undefined';
        }
    }

}