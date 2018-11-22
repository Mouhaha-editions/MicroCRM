<?php
/**
 * Created by PhpStorm.
 * User: pierr
 * Date: 07/11/2018
 * Time: 21:28
 */

namespace CustomerBundle\Enums;


class ECustomerCommunicationType
{
    const UNDEFINED = 0;

    const EMAIL = 100;
    const TELEPHONE = 200;
    const MOBILE = 300;
    const FAX = 400;
    const WEBSITE = 500;
    const SKYPE = 600;
    const FACEBOOK = 700;

    public static function ToString($type)
    {
        switch ($type) {
            case self::EMAIL:
                return 'ecustomercommunication.email';
            case self::TELEPHONE:
                return 'ecustomercommunication.telephone';
            case self::MOBILE:
                return 'ecustomercommunication.mobile';
            case self::FAX:
                return 'ecustomercommunication.fax';
            case self::WEBSITE:
                return 'ecustomercommunication.website';
            case self::SKYPE:
                return 'ecustomercommunication.skype';
            case self::FACEBOOK:
                return 'ecustomercommunication.facebook';
            default:
                return 'ecustomercommunication.undefined';
        }
    }

}