<?php
/**
 * Created by IntelliJ IDEA.
 * User: admin
 * Date: 30.12.17
 * Time: 9:55
 */

namespace backend\components;


use common\models\User;
use common\models\UserDevice;
use common\models\UserFeedback;
use common\models\UserLog;
use common\models\UserMarker;
use Yii;
use yii\db\Query;
use yii\helpers\VarDumper;

class Statistic
{
    public static function getCurrentDate($key = null) {
        $list = [
            'firstDate' =>  strtotime(date("Y-m-d 00:00:00", strtotime("first day of this month"))),
            'lastDate' => strtotime(date("Y-m-d 23:59:59", strtotime("last day of this month"))),
            'firstDateName' =>  Yii::$app->formatter->asDate(strtotime(date("Y-m-d 00:00:00", strtotime("first day of this month")))),
            'lastDateName' => Yii::$app->formatter->asDate(strtotime(date("Y-m-d 23:59:59", strtotime("last day of this month")))),

            'firstDatePrevious' =>  strtotime(date("Y-m-d 00:00:00", strtotime("first day of previous month"))),
            'lastDatePrevious' => strtotime(date("Y-m-d 23:59:59", strtotime("last day of previous month"))),
            'firstDatePreviousName' =>  Yii::$app->formatter->asDate(strtotime(date("Y-m-d 00:00:00", strtotime("first day of previous month")))),
            'lastDatePreviousName' => Yii::$app->formatter->asDate(strtotime(date("Y-m-d 23:59:59", strtotime("last day of previous month")))),

            'countDays' => date('t')
        ];

        if($key){
            if(isset($list[$key])){
                return $list[$key];
            } else {
                return null;
            }
        }

        return $list;
    }

    public static function getDaysForCurrentMonth($currentDate = null) {
        if(!$currentDate) {
            $currentDate = self::getCurrentDate();
        }

        $days = [];

        if($currentDate['from'] && $currentDate['to'] && $currentDate['from'] == $currentDate['to']) {
            for($i = $currentDate['firstDate'], $j = 0; $j <= 24; $i = strtotime('+1 hour', $i), $j++){
                $days[$i] = [
                    'from_date' => Yii::$app->formatter->asDatetime($i),
                    'to_date' => Yii::$app->formatter->asDatetime(strtotime('+1 hour', $i)),
                    'from' => $i,
                    'to' => strtotime('+1 hour', $i),
                    'events' => []
                ];
            }
        } else {
            for($i = $currentDate['firstDate'], $j = 0; $j <= $currentDate['countDays']; $i = strtotime('+1 day', $i), $j++){
                $days[$i] = [
                    'from_date' => Yii::$app->formatter->asDatetime($i),
                    'to_date' => Yii::$app->formatter->asDatetime(strtotime('+1 day', $i)),
                    'from' => $i,
                    'to' => strtotime('+1 day', $i),
                    'events' => []
                ];
            }
        }

        return $days;
    }

    public static function getMainStatisticsForLocation($location, $from = null, $to = null) {
        $currentDate = self::getCurrentDate();

        if($from && $to) {
            $currentDate = [
                'from' => $from,
                'to' => $to,
                'firstDate' => strtotime(date("Y-m-d 00:00:00", $from)),
                'lastDate' => strtotime(date("Y-m-d ".($from == $to?'23:59:59':'00:00:00'), $to)),
                'countDays' => (int)(($to - $from) / 24 / 60 / 60) + ((float)(($to - $from) / 24 / 60 / 60) > (int)(($to - $from) / 24 / 60 / 60) ? 1 : 0)
            ];

            $currentDate['firstDateName'] = Yii::$app->formatter->asDatetime($currentDate['firstDate']);
            $currentDate['lastDateName'] = Yii::$app->formatter->asDatetime($currentDate['lastDate']);

            if($currentDate['countDays'] == 0) {
                $currentDate['countDays'] = 1;
            }

            //var_dump($currentDate); exit;
        }

        $result = [];
        if($location->locationEventObject){
            $result['location'] = $location->id;
            $result['days'] = self::getDaysForCurrentMonth($currentDate);

            foreach ($location->locationEventObject as $event) {
                $result['title'][$event->id] = $event->title;
                $result['color'][$event->id] = '#'.dechex(rand(0x000000, 0xFFFFFF));

                if($result['days']){
                    foreach ($result['days'] as &$day) {
                        $day['events'][$event->id]['count'] = 0;
                    }
                }

                if($listUserMarkers = \common\models\UserMarker::find()->where(['and',
                    ['location_id' => $location->id],
                    ['event_id' => $event->id],
                    ['>=', 'created_at', $currentDate['firstDate']],
                    ['<=', 'created_at', $currentDate['lastDate']]
                ])->all()
                ){
                    if($result['days']){
                        foreach ($result['days'] as &$day) {
                            foreach ($listUserMarkers as $marker) {
                                if($marker->created_at >= $day['from'] && $marker->created_at < $day['to']) {
                                    $day['events'][$event->id]['count']++;
                                    $result['countEvents'][$event->id]++;
                                    $result['countEventsAll']++;
                                }
                            }
                        }
                    }
                }
            }

        }

        if($result['days'] && $result['title']){
            foreach ($result['days'] as $day => $value) {
                $listBuf = [
                    'y'=> date('Y-m-d'.($from && $to && $from == $to?' H:i':''), $day),
                ];

                foreach ($result['title'] as $event_id => $title) {
                    $listBuf[$title] = (int) $value['events'][$event_id]['count'];
                }

                $result['morris'][] = $listBuf;
            }
        }

        return $result;

        //VarDumper::dump($result, 10, true); exit;
    }

    public static function getAllStatics(){
        $currentDate = self::getCurrentDate();
        $result = [];

        $queries = [
            'registration' => User::className(),
            'feedback' => UserFeedback::className(),
            'markers' => UserMarker::className(),
            'auth' => UserLog::className()
        ];

        foreach ($queries as $name => $className) {
            $result[$name] = [
                'current' => $className::find()->where(['and',
                    ['>=', 'created_at', $currentDate['firstDate']],
                    ['<=', 'created_at', $currentDate['lastDate']]
                ])->count(),
                'previous' => $className::find()->where(['and',
                    ['>=', 'created_at', $currentDate['firstDatePrevious']],
                    ['<=', 'created_at', $currentDate['lastDatePrevious']]
                ])->count()
            ];

            $result[$name]['perc'] = !$result[$name]['previous']?100: (int)($result[$name]['current'] * 100 / $result[$name]['previous']) - 100;
        }

        return $result;
    }

    public static function getStatisticForTemplateModelByDays($class, $title = 'item', $from = null, $to = null) {
        $currentDate = self::getCurrentDate();

        if($from && $to) {
            $currentDate = [
                'from' => $from,
                'to' => $to,
                'firstDate' => strtotime(date("Y-m-d 00:00:00", $from)),
                'lastDate' => strtotime(date("Y-m-d ".($from == $to?'23:59:59':'00:00:00'), $to)),
                'countDays' => (int)(($to - $from) / 24 / 60 / 60) + ((float)(($to - $from) / 24 / 60 / 60) > (int)(($to - $from) / 24 / 60 / 60) ? 1 : 0)
            ];

            $currentDate['firstDateName'] = Yii::$app->formatter->asDatetime($currentDate['firstDate']);
            $currentDate['lastDateName'] = Yii::$app->formatter->asDatetime($currentDate['lastDate']);

            if($currentDate['countDays'] == 0) {
                $currentDate['countDays'] = 1;
            }
        }

        $result = [
            'days' => self::getDaysForCurrentMonth($currentDate),
            'title' => $title,
            'color' => '#'.dechex(rand(0x000000, 0xFFFFFF))
        ];

        if($list= (new \yii\db\Query())
            ->select(['count(id) as count_as_date, created_at'])
            ->from($class::tableName())
            ->where(['and',
                ['>=', 'created_at', $currentDate['firstDate']],
                ['<=', 'created_at', $currentDate['lastDate']]
            ])
            ->groupBy('created_at')
            ->all()
        ){
            if($result['days']){
                if($result['days']){
                    foreach ($result['days'] as &$day) {
                        $day['count'] = 0;
                    }
                }

                foreach ($result['days'] as &$day) {
                    foreach ($list as $marker) {
                        if($marker['created_at'] >= $day['from'] && $marker['created_at'] < $day['to']) {
                            $day['count'] += $marker['count_as_date'];
                            $result['countAll'] += $marker['count_as_date'];
                        }
                    }
                }
            }
        }

        if($result['days'] && $result['title']){
            foreach ($result['days'] as $day => $value) {
                $listBuf = [
                    'y'=> date('Y-m-d'.($from && $to && $from == $to?' H:i':''), $day),
                ];

                $listBuf[$result['title']] = (int) $value['count'];
                $result['morris'][] = $listBuf;
            }
        }

        //VarDumper::dump($result, 10, true); exit;
        return $result;
    }

    public static function getDonutDevices() {
        if($list = (new Query())
            ->select('COUNT(id) as summ, device_type')
            ->from(UserDevice::tableName())
            ->where(['is not', 'device_type', null])
            ->groupBy('device_type')
            ->all()
        ) {
            foreach ($list as $item){
                $result['colors'][] = '#'.dechex(rand(0x000000, 0xFFFFFF));
                $result['items'][] = [
                    'label' => $item['device_type'],
                    'value' => $item['summ']
                ];
            }

            //VarDumper::dump($result, 10, true); exit;
            return $result;
        }

        return null;
    }
}