<?php
/**
 * Eugine Terentev <eugine@terentev.net>
 */

namespace console\controllers;

use common\models\Dictionary;
use common\models\DictionaryCategory;
use common\models\Request;
use common\models\RequestDictionary;
use common\models\UserDictionaryValue;
use common\models\UserProfile;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;


/**
 * Class ExtendedMessageController
 * @package console\controllers
 */
class TestController extends Controller
{
    /**
     * @param $user_id
     * @param int $ipay
     * @param int $youpay
     * @param int $wesplit
     */
    public function actionAddPayment($user_id, $ipay = 1, $youpay = 1, $wesplit = 1) {
        if (UserDictionaryValue::deleteAll([
            'user_id' => $user_id,
            'dictionary_category_id' => DictionaryCategory::CATEGORY_WHO_PAY
        ])) {
            Console::output("Deleted all rows for user [$user_id]", Console::FG_GREEN);
        }

        $countRequests = 0;

        foreach ([
                    1 => $ipay,
                    2 => $youpay,
                    3 => $wesplit
                 ] as $code => $value) {
            if ($state = Dictionary::getItemByCodeAndCategory($code, DictionaryCategory::CATEGORY_WHO_PAY)) {
                Console::output("$state->title: $state->id [$state->code]", Console::FG_GREEN);

                $model = new UserDictionaryValue([
                    'user_id' => $user_id,
                    'dictionary_id' => $state->id,
                    'dictionary_category_id' => DictionaryCategory::CATEGORY_WHO_PAY,
                    'value' => (string) $value
                ]);

                if ($model->save()) {
                    $countRequests += $value;
                    Console::output("Save model", Console::FG_GREEN);
                } else {
                    Console::output("Don't save value", Console::FG_RED);
                    var_dump($model->errors);
                }
            } else {
                Console::output("State is null", Console::FG_RED);
            }
        }

        if (UserProfile::updateAll([
            'count_finish_request' => $countRequests ? $countRequests : null
        ], [
            'user_id' => $user_id
        ])) {
            Console::output("Updated profile", Console::FG_GREEN);
        } else {
            Console::output("Don't update profile", Console::FG_RED);
        }

    }

    public function actionCalculateFinishedState($user_id) {
        $stateFinished = Dictionary::getItemByCodeAndCategory(
            Request::STATUS_FINISHED,
            DictionaryCategory::CATEGORY_REQUEST_STATE
        );

        $transaction = Yii::$app->db->beginTransaction();

        try {
           if (UserProfile::updateAll([
                'count_finish_request' => (int) Request::find()->where([
                    'state_id' => $stateFinished->id,
                    'user_id' => $user_id
                ])->count()
            ], [
                'user_id' => $user_id
            ])) {
               Console::output("Updated UserProfile [count_finish_request]", Console::FG_GREEN);
            }

            if (UserDictionaryValue::deleteAll([
                'user_id' => $user_id,
                'dictionary_category_id' => DictionaryCategory::CATEGORY_WHO_PAY
            ])) {
                Console::output("Deleted all rows from UserDictionaryValue for user [$user_id]", Console::FG_GREEN);
            }

            $items = RequestDictionary::find()
                ->select('count(dictionary_id) AS count_request, dictionary_id')
                ->where([
                    'dictionary_category_id' => DictionaryCategory::CATEGORY_WHO_PAY
                ])
                ->innerJoin(Request::tableName().' r', 'r.id='.RequestDictionary::tableName().'.request_id AND r.user_id=:user_id AND r.state_id=:state_id', [
                    ':user_id' => $user_id,
                    ':state_id' => $stateFinished->id
                ])
                ->groupBy('dictionary_id')
                ->asArray()
                ->all()
            ;

            if ($items) {
                foreach ($items as $item) {
                    $model = new UserDictionaryValue([
                        'user_id' => $user_id,
                        'dictionary_id' => $item['dictionary_id'],
                        'dictionary_category_id' => DictionaryCategory::CATEGORY_WHO_PAY,
                        'value' => $item['count_request']
                    ]);

                    if ($model->save()) {
                        Console::output("Saved UserDictionaryValue [".$item['dictionary_id']."] [".$item['count_request']."]", Console::FG_GREEN);
                    } else {
                        new \Exception("Don't save UserDictionaryValue [".$item['dictionary_id']."] [".$item['count_request']."]");
                    }
                }
            } else {
                new \Exception("List is empty");
            }

            echo 'end'; exit;
            $transaction->commit();
        } catch (\Exception $e) {
            Console::output($e->getMessage(), Console::FG_RED);
            $transaction->rollBack();
        }
    }

    public function actionSetFinishStateForRequest($request_id) {
        if ($request = Request::findOne($request_id)) {
            $stateFinished = Dictionary::getItemByCodeAndCategory(
                Request::STATUS_FINISHED,
                DictionaryCategory::CATEGORY_REQUEST_STATE
            );

            $request->state_id = $stateFinished->id;
            $request->save();
        }
    }
}
