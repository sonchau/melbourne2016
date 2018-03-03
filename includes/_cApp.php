<?php

class AppConfig
{
    public static $CONFERENCE_YEAR = '2018';
    public static $SITE_URL = 'http://christianconference.org.au';
    public static $EARLY_BIRD = true;

    public static $DB_NAME      = 'vecamel1_daihoi'; //'vecamel1_daihoi';
    public static $DB_USERNAME  = 'root'; //'vecamel1_daihoi';
    public static $DB_PASSWORD  = ''; //?Eh9O[%#R(3q';


    public static $TINYURL_VIEW = 'https://tinyurl.com/y92xge86';


    public static $DEFAULT_EMAIL_ADDRESS = 'registration@christianconference.org.au';


    public static $APP_NAME = 'Dai Hoi 2018';


    //reference generation
    public static $REFERENCE_RANDOM_CHARACTERS  = 'ABCDEFGH1JKLMN2PQRSTUVWXYZ';
    public static $REFERENCE_RANDOM_LENGTH      = 6;
    public static $REFERENCE_APPENDED_WITH_ID   = true;


    public static function isEarlyBird() {
        return self::$EARLY_BIRD;
    }


}

?>