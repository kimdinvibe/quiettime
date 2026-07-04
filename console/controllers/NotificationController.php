<?php

/**
 * Eugine Terentev <eugine@terentev.net>
 */

namespace console\controllers;

use Yii;
use yii\db\Query;
use common\models\User;
use yii\helpers\Console;
use common\models\Message;
use yii\console\Exception;
use yii\helpers\VarDumper;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;
use common\models\MessageLog;
use common\models\UserDevice;
use common\models\MessageRoom;
use common\models\MessageView;
use common\models\Notification;
use common\models\MessageRoomApns;
use common\models\NotificationLog;
use common\models\MessageRoomAccess;
use common\models\UserProfile;
use yii\base\InvalidConfigException;
use paragraph1\phpFCM\Recipient\Device;

/**
 * Class ExtendedMessageController
 * @package console\controllers
 */
class NotificationController extends Controller
{
   

    public function actionFcm($begin)
    {
        $this->checkFile('fcm');
        $this->sendMessages("Android", "fcm");
    }

    private function checkFile($name)
    {
        $lock = fopen('/tmp/service.crone.' . $name . '.lock', 'w');
        if (!($lock && flock($lock, LOCK_EX | LOCK_NB))) {
            exit('already running');
        }
    }

    private function sendMessages($type, $service)
    {
        $currentDayOfWeek = date('w');
        $time = date('H:i');
        // $time = "05:00";

        if ($userIds = UserProfile::find()
            ->select('user_id')
            ->innerJoinWith(['user'])
            ->where([
                'and',
                // ['id' => 44],
                ['user.status' => User::STATUS_ACTIVE],
                // ['sent_to_phone_at' => null],
                ['is not', 'notification_time', null],
                ['is not', 'notification_days', null],
                ['is not', 'notification_on', null],
                ['like', 'notification_days', $currentDayOfWeek-1],
                ['like', 'notification_time', $time],
                ['notification_on' => 1],

            ])
            // ->limit(100)
            // ->asArray()
            ->column()
        ) {
            // echo count($userIds);
            // return;

            foreach ($userIds as $userId) {
                if ($userDevices = UserDevice::find()->where([
                    'and',
                    ['user_id' => $userId],
                    // send to all devices
                    // ['state' => UserDevice::STATE_AVAILABLE],
                    //send to all devices android and ios
                    // ['like', 'device_type', $type . '%', false],
                ])->all()) {
                    foreach ($userDevices as $device) {
                        if ($device->locale) {
                            \Yii::$app->language = str_replace("_", "-", $device->locale);
                        } else {
                            \Yii::$app->language = 'en-US';
                        }

                        if ($device_id = $device->device_id) {
                            // $text = $message->text ? Yii::t("api", $message->text, $message->getTextArgs()) : null;
                            $text = "Проведите сейчас Тихое Время с Богом";

                            if ($text) {
                                try {
                                    $result = null;

                                    $messageData = [
                                        'user_id' => $userId,
                                        'day' => $currentDayOfWeek - 1,
                                        'time' => $time,
                                    ];

                                    // var_dump($messageData);
                                    // return;

                                    // $messageBadge = (int) Notification::find()
                                    //     ->where([
                                    //         'and',
                                    //         ['status' => Notification::STATUS_NEW],
                                    //         ['sent_to_phone_at' => null],
                                    //         ['is not', 'user_id', null]

                                    //     ])
                                    //     ->count();

                                    $messageBadge = 0;

                                    if ($service == "apns") {
                                        // $result = $this->sendMessageToApns(
                                        //     $device_id,
                                        //     $message,
                                        //     $messageData,
                                        //     $messageBadge
                                        // );
                                    } else {
                                        $result = $this->sendMessageToFcms(
                                            $device_id,
                                            $text,
                                            $messageData,
                                            $messageBadge
                                        );
                                    }

                                    if ($result) {
                                        Console::output("Sent a message for user: {$userId}");
                                    } else {
                                        Console::output("Didn't send a message for user: {$userId}");
                                    }
                                } catch (\Exception $e) {
                                    Console::output("Exception for user: {$userId}, exception: " . $e->getMessage());
                                }
                            } else {
                                Console::output("Text is null");
                            }
                        }
                    }
                }
            }
        } else {
            Console::output("List messages with devices is null");
        }
    }

    private function sendMessageToApns($device_id, $message, $data, $badgeCount)
    {
        return Yii::$app->apns->send(
            $device_id,
            Yii::t('console', 'New message: {0}', [
                $message['message']['text']
            ]),
            $data,
            [
                'sound' => 'default',
                'badge' => $badgeCount
            ]
        );
    }

    private function sendMessageToFcms($device_id, $text, $data, $badgeCount)
    {
        // return true;
        // $data['pushMessageId'] = $data['message_id'];

        $note = Yii::$app->fcm->createNotification(
            Yii::t('api', "Уведомление"),
            $text
        );
        $note->setIcon('notification_icon')
            ->setColor('#ffffff')
            ->setSound("default")
            // ->setBadge($badgeCount);
            ->setBadge(0);      // always 0 it's problem in flutter
            // ->setClickAction("NOTIFICATION_ACTIVITY");

        // if set tag update current notifications

        $message = Yii::$app->fcm->createMessage();
        $message->addRecipient(new Device($device_id));
        $message->setNotification($note)
            ->setData($data);

        $response = Yii::$app->fcm->send($message);

        if ($response->getStatusCode() == 200) {
            return true;
        }
    }

    public function actionFcmTest($device_id, $message = null, $data = [])
    {
        if (!$message) {
            $message = 'Test message from ' . Yii::$app->name;
        }

        $note = Yii::$app->fcm->createNotification("test title", $message);
        $note->setIcon('notification')
            ->setColor('#ffffff')
            ->setBadge(1)
            ->setSound("default");
            // ->setClickAction("NOTIFICATION_ACTIVITY");

        $message = Yii::$app->fcm->createMessage();
        $message->addRecipient(new Device($device_id));
        //$message->addRecipient(new Device($device_id));
        $message->setNotification($note)
            ->setData(['id' => 69, 'type' => 1]);

        $response = Yii::$app->fcm->send($message);

        if ($response->getStatusCode() == 200) {
            Console::output("Success");
        } else {
            Console::output("Error");
        }

        // test
        //        $client = new \understeam\fcm\Client();
        //        $tr = $client->createNotification("asd", "asd");
        ////        $tr->setSound(1);
        ////        $tr->setClickAction("NOTIFICATION_ACTIVITY");
        //
        //        $message = $client->createMessage();
        //        $message->addRecipient()
    }

    public function actionTestLocale($text = "Finished Without Payment") {
        \Yii::$app->language = str_replace("_", "-", 'ru_RU');
        echo \Yii::$app->language;
        echo "\n";
        echo Yii::t('common', $text);
        echo "\n";
    }
}
