<?php

namespace frontend\modules\api\v1\forms;

use Yii;
use yii\base\Model;
use common\models\Card;
use yii\base\Exception;
use common\models\Order;
use common\models\Tariff;
use common\models\ChatGroup;
use common\models\ChatAccess;
use common\models\OrderPoint;


/**
 * Create user form
 */
class OrderCreateForm extends Model
{
    public $tariff_id;
    public $points;
    public $model;
    public $comment;
    public $price;
    public $payment_sum;
    public $payment_bonus;
    public $to_contact_fio;
    public $to_contact_phone;
    public $to_at;
    public $card_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['points', 'required'],
            ['points', function ($attribute, $params) {
                if (!is_array($this->points)) {
                    $this->addError('points', Yii::t('common', 'Points is not array!'));
                } else {
                    $requiredItems = [
                        'latitude',
                        'longitude'
                    ];

                    foreach ($this->points as $key => $value) {
                        $countItems = 0;

                        foreach ($value as $name => $item) {
                            if (in_array($name, $requiredItems) && $item) {
                                $countItems += 1;
                            }
                        }

                        if ($countItems != count($requiredItems)) {
                            $this->addError('points', Yii::t('common', 'Point is incorrect!'));
                        }
                    }
                }
            }],

            ['tariff_id', 'required'],
            ['tariff_id', 'integer'],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],

            [['card_id', 'payment_sum', 'payment_bonus', 'to_at'], 'integer'],
            [['price'], 'number'],
            [['to_contact_phone', 'to_contact_fio'], 'string', 'max' => 128],
            [['comment'], 'string', 'max' => 1024],
            [['card_id'], 'exist', 'skipOnError' => true, 'targetClass' => Card::className(), 'targetAttribute' => ['card_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tariff_id' => Yii::t('common', 'Tariff Id'),
            'points' => Yii::t('common', 'Points'),
        ];
    }

    public function save()
    {
        if ($this->validate()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $this->model = new Order([
                    'tariff_id' => $this->tariff_id,
                    'status' => Order::STATUS_CREATED,
                    'comment' => $this->comment,
                    'price' => $this->price,
                    'payment_sum' => $this->payment_sum,
                    'payment_bonus' => $this->payment_bonus,
                    'to_at' => $this->to_at,
                    'to_contact_fio' => $this->to_contact_fio,
                    'to_contact_phone' => $this->to_contact_phone,
                    'card_id' => $this->card_id,
                    'user_id' => Yii::$app->user->id,

                    // for test
                    // 'driver_id' => 3,
                ]);

                if ($this->model->save()) {
                    $order = 1;

                    foreach ($this->points as $key => $value) {
                        $orderPoint = new OrderPoint([
                            'latitude' => $value['latitude'],
                            'longitude' => $value['longitude'],
                            'descr' => $value['descr'],
                            'region_id' => $value['region_id'],
                            'price' => $value['price'],
                            'order_id' => $this->model->id,
                            'order' => $order
                        ]);

                        if (!$orderPoint->save()) {
                            throw new Exception("Error points");
                        }

                        $order += 1;
                    }
                } else {
                    throw new Exception("Error");
                }

                // added chat for order
                // $modelChat = new ChatGroup([
                //     'type' => ChatGroup::TYPE_WITH_USER,
                //     'title' => Yii::t('api', 'Created from api for order when added'),
                //     'author_id' => Yii::$app->user->id,
                // ]);

                // $isSuccess = false;

                // if ($modelChat->save()) {
                //     $modelChat->refresh();
                //     $chatAccess = new ChatAccess([
                //         'group_id' => $modelChat->id,
                //         'user_id' => Yii::$app->user->id
                //     ]);

                //     if ($chatAccess->save()) {
                //         $isSuccess = true;

                //         if ($this->model->driver_id) {
                //             $chatAccess = new ChatAccess([
                //                 'group_id' => $modelChat->id,
                //                 'user_id' => $this->model->driver_id,
                //             ]);

                //             if (!$chatAccess->save()) {
                //                 $isSuccess = false;
                //             }
                //         }
                //     }

                //     if ($isSuccess) {
                //         $this->model->chat_group_id = $modelChat->id;
                //         if (!$this->model->update(false, ['chat_group_id'])) {
                //             $isSuccess = false;
                //         }
                //     }
                // }

                // if ($isSuccess) {
                    $transaction->commit();
                    return true;
                // } else {
                //     throw new Exception("Error chat group");
                // }
            } catch (\Throwable $th) {
                $transaction->rollBack();
            }
        }

        return null;
    }

    public function login()
    {
        if ($this->validate()) {
            if (Yii::$app->user->login($this->getModel(), 0)) {
                return true;
            }
        }
        return false;
    }

    public function sendCodeToUser($phone, $code)
    {
        //
        return true;
    }
}
