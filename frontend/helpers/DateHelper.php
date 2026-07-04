<?php
/**
 * Created by IntelliJ IDEA.
 * User: admin
 * Date: 02/12/2018
 * Time: 14:56
 */

namespace frontend\helpers;


use DateTime;
use DateTimeZone;

class DateHelper
{
    static function getFormatDateForRequest() {
        if (\Yii::$app->language == 'ja') {
//            return 'Y/m/d';
            return 'n月j日';
        }

        return 'F j';
    }

    static function getFormatDateForChat() {
        if (\Yii::$app->language == 'ja') {
            return 'Y/m/d';
        }

        return 'Y-m-d';
    }

    static function formateDate($from, $to = null, $useTimezone = true, $useOnlyUTCTimezone = false) {

        if ($useTimezone || $useOnlyUTCTimezone) {
            $timezone = $useOnlyUTCTimezone ? 'UTC' : \Yii::$app->device->timezone;
            $timezoneDefault = date_default_timezone_get();
            date_default_timezone_set('UTC');

            //        var_dump($from, date("Y-m-d H:i:s", $from), self::_date("Y-m-d H:i:s", $from, $timezone)); exit();

            $value = self::_date(self::getFormatDateForRequest(), $from, $timezone).
                ($to && !(self::_date('m d', $from, $timezone) == self::_date('m d', $to, $timezone))
                    ?'–'.
                    (self::_date('m', $from, $timezone) != self::_date('m', $to, $timezone)
                        ? self::_date(self::getFormatDateForRequest(), $to, $timezone)
                        : self::_date(\Yii::$app->language == 'ja' ? self::getFormatDateForRequest() : 'j', $to, $timezone))
                    : ''
                );

            date_default_timezone_set($timezoneDefault);
        } else {
            $value = date(self::getFormatDateForRequest(), $from).
                ($to && !(date('m d', $from) == date('m d', $to))
                    ?'–'.
                    (date('m', $from) != date('m', $to)
                        ? date(self::getFormatDateForRequest(), $to)
                        : date(\Yii::$app->language == 'ja' ? self::getFormatDateForRequest() : 'j', $to))
                    : ''
                );
        }

        return $value;
    }

    static function formateTime($from) {
        $timezone = \Yii::$app->device->timezone;
        $timezoneDefault = date_default_timezone_get();
        date_default_timezone_set('UTC');

        $value = self::_date('g:i A', $from, $timezone);

        date_default_timezone_set($timezoneDefault);

        return $value;
    }

    static function formateDateForWeek($from) {
        $timezone = \Yii::$app->device->timezone;
        $timezoneDefault = date_default_timezone_get();
        date_default_timezone_set('UTC');

        if ($from > strtotime(date("Y-m-d 00:00:00", time()))) {
            $value = self::_date('g:i A', $from, $timezone);
        } elseif ($from > strtotime(date("Y-m-d 00:00:00", time() - 24 * 60 * 60))) {
            $value = \Yii::t("api", "Yesterday");
        } elseif ($from > strtotime(date("Y-m-d 00:00:00", time() - 24 * 60 * 60 * 6))) {
            $value = \Yii::t("api", self::_date('l', $from, $timezone));
        } else {
            $value = self::_date(self::getFormatDateForRequest(), $from, $timezone);
        }

        date_default_timezone_set($timezoneDefault);

        return $value;
    }

    static function leftTime($date, $to = null) {
        $timezoneDefault = date_default_timezone_get();
        date_default_timezone_set('UTC');

        $value = null;

        if (!$to) {
            $to = time();
        }

        if ($date > $to) {
            $date = $date - $to;

            $days = floor($date / (60 * 60 * 24));
            $hours = floor(($date - $days * (60 * 60 * 24)) / (60 * 60));

            $value = ($days ? \Yii::t('frontend', '{0} days', [
                    $days
                ]) : "")
                .($hours ? ($days ? " " : "").\Yii::t('frontend', '{0} hours', [
                    $hours
                    ]): "");

        }

        date_default_timezone_set($timezoneDefault);

        return $value;
    }

    static function convertTime($time, $timezone) {
        return strtotime(self::_date("Y-m-d H:i:s", $time, $timezone));
    }

    static function convertTimeToServerTime($time) {
        return strtotime(self::_date("Y-m-d H:i:s", $time, date_default_timezone_get()));

//        date_default_timezone_set('Asia/Krasnoyarsk');
//        $time = time();
//        $check = $time+date("Z",$time);
//        echo strftime("%B %d, %Y @ %H:%M:%S UTC", $check);

//        echo $time;
//        echo "\n";
//        echo date("Y-m-d H:i:s", $time);
//        echo "\n";
//        echo "\n";
//        echo self::_date("Y-m-d H:i:s", $time, $timezone);
//        echo "\n";
//        echo strtotime(self::_date("Y-m-d H:i:s", $time, $timezone));
//        echo "\n";
//        $date = new DateTime(self::_date("Y-m-d H:i:s", $time, $timezone), new DateTimeZone($timezone));
//        echo $date->format('U');
//        echo "\n";
//        echo "\n";
//        echo self::_date("Y-m-d H:i:s", $time, 'Asia/Krasnoyarsk');
//        echo "\n";
//        echo strtotime(self::_date("Y-m-d H:i:s", $time, 'Asia/Krasnoyarsk'));
//        echo "\n";
//        $date = new DateTime(self::_date("Y-m-d H:i:s", $time, 'Asia/Krasnoyarsk'), new DateTimeZone('Asia/Krasnoyarsk'));
//        echo $date->format('U');
//        echo "\n";
//        echo "\n";
//        echo self::_date("Y-m-d H:i:s", $time, date_default_timezone_get());
//        echo "\n";
//        echo strtotime(self::_date("Y-m-d H:i:s", $time, date_default_timezone_get()));
//
//        exit();
    }

    // somewhere in the code
    static function _date($format="r", $timestamp=false, $timezone=false)
    {
        $userTimezone = new DateTimeZone(!empty($timezone) ? $timezone : 'GMT');
        $gmtTimezone = new DateTimeZone('GMT');
        $myDateTime = new DateTime(($timestamp!=false?date("r",(int)$timestamp):date("r")), $gmtTimezone);
        $offset = $userTimezone->getOffset($myDateTime);
        return date($format, ($timestamp!=false?(int)$timestamp:$myDateTime->format('U')) + $offset);
    }
}