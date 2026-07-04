<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

//Класс для рботы с запросами ajax
class AjaxController extends Controller
{
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    
    public function actionIndex()
	{
		if (Yii::$app->request->isAjax) 
        {
            if (Yii::$app->request->post('action') == 'add-comment'){
                $room_id = Yii::$app->request->post('id');
                $text = trim(strip_tags(Yii::$app->request->post('message')));
                
                $is_file = Yii::$app->request->post('is_file');
                $size = (int)Yii::$app->request->post('size');
                $base_url = Yii::$app->request->post('base_url');
                $path = Yii::$app->request->post('path');
                $type = Yii::$app->request->post('type');
                
                if(!$message){
                    $message = 'File:';
                }
                
                if($room_id && $message){
                    if($room = \common\models\MessageRoom::find()
                        ->where(['id' => $room_id])
                        ->one()
                    )
                    {
                        $message = new \common\models\Message([
                            'room_id' => $room_id,
                            'message' => $text,
                            //'is_file' => $is_file
                            //только для веба, отправка и создание сообщения в одно время
                            //'sended_at' => time()
                        ]);
                        
                        if($message->save()){
                            $message->refresh();
                            
                            if($is_file){
                                $attach = new \common\models\MessageAttachment([
                                    'message_id' => $message->id,
                                    'size' => $size,
                                    'type' => $type,
                                    'path' => $path,
                                    'base_url' => $base_url,
                                    'name' => $text,
                                ]);
                                
                                if($attach->save(false)){
                                    $message->file_id = $attach->id;
                                    $message->update(['file_id']);
                                }
                            }
                            
                            echo json_encode(array(
                                'response' => 'success',
                                'list' => $message->attributes,
                                'room_id' => $room_id,
                                'id' => $message->id,
                                'hash' => Yii::$app->request->post('hash')
                                /*'template' => $this->renderPartial('/message-room/message', [
                                    'data' => $message,
                                ]),*/                                
                            ));                            
                        }else{
                            echo json_encode(array(
                                'response' => 'error',
                                'message' => 'Ошибка сохранения',
                                'errors' => $message->errors,
                                'hash' => Yii::$app->request->post('hash')
                            ));
                        }                        
                        
                        return;
                    }else{
                        $message = 'Не удалось обнаружить комнату!';
                    }
                }else{
                    $message = 'Необходимо указать обязательные параметры!';
                }
    		}elseif (Yii::$app->request->post('action') == 'delete-comment'){
                $id = Yii::$app->request->post('id');
                
                if($id){
                    if($message = \common\models\Message::find()
                        ->where([
                            'id' => $id
                        ])
                        ->one()
                    )
                    {
                        if(($message->room && $message->room->author_id == Yii::$app->user->id) || \common\models\MessageRoomAccess::find()->where([
                            'user_id' => Yii::$app->user->id
                        ])){
                            $message->deleted_at = time();
                            if($message->update(['deleted_at'])){
                                echo json_encode(array(
                                    'response' => 'success',
                                    'list' => $message->attributes,
                                    'id' => $message->id
                                ));
                                return;
                            }else{
                                $message = 'Ошибка удаления';
                            }
                        }else{
                            $message = 'Access denied';
                        }                       
                    }else{
                        $message = 'Не удалось обнаружить комментарий!';
                    }
                }else{
                    $message = 'необходимо указать обязательные параметры!';
                }
    		}elseif (Yii::$app->request->post('action') == 'list-comments'){
                $id = Yii::$app->request->post('id');
                $time = Yii::$app->request->post('time');
                $newTime = time();
                
                if($id && $time){
                    if($messages = \common\models\Message::find()
                        ->where([
                            'and',
                            ['room_id' => $id],
                            ['>', 'updated_at', $time]
                        ])
                        //->active()
                        ->all()
                    )
                    {
                        foreach($messages as $message){
                            if($message->deleted_at){
                                $list['deleted'][] = [
                                    'id' => $message->id
                                ];
                            }elseif($message->readed_at){
                                $list['readed'][] = [
                                    'id' => $message->id
                                ];
                            }elseif($message->sended_at){
                                $list['sented'][] = [
                                    'id' => $message->id
                                ];
                            }else{
                                $list['added'][] = [
                                    'attributes' => $message->attributes,
                                    'template' => $this->renderPartial('/message-room/message', [
                                        'data' => $message,
                                    ]),
                                    'id' => $message->id
                                ];
                            }                            
                        }                      
                    }else{
                        $message = 'Не удалось обнаружить комментарий!';
                    }
                    
                    echo json_encode(array(
                        'response' => 'success',
                        'list' => $list,
                        'id' => $id,
                        'time' => $newTime,
                        'listTyped' => \common\models\MessageTyped::find()
                            //->where(['room_id' => $id, 'state' => \common\models\MessageTyped::STATE_TYPING])
                            ->where(['and', 'room_id=:room_id', 'user_id!=:user_id', 'state=:state'], [
                                ':room_id' => $id, 
                                ':user_id' => Yii::$app->user->id, 
                                ':state' => \common\models\MessageTyped::STATE_TYPING
                            ])
                            ->joinWith(['user'])
                            ->select('user.id, user.username')
                            ->asArray()
                            ->all()
                    ));
                    return;
                }else{
                    $message = 'необходимо указать обязательные параметры!';
                }
    		}elseif (Yii::$app->request->post('action') == 'send-comment'){
                $ids = Yii::$app->request->post('ids');
                
                if(!is_array($ids)){
                    $ids = [$ids];
                }
                
                if($ids){
                    foreach($ids as $id){
                        if($message = \common\models\Message::find()
                            ->where([
                                'id' => $id
                            ])
                            ->one()
                        )
                        {
                            $messageView = new \common\models\MessageSend([
                                'room_id' => $message->room_id,
                                'message_id' => $message->id
                            ]);
                            
                            if($messageView->save()){
                                $success[] = $messageView->id;
                                $message->updated_at = $message->sended_at = time();
                                
                                if($message->update(['updated_at', 'sended_at'])){
                                    //
                                }else{
                                    //
                                }
                            }else{
                                $errrors[] = $messageView->errors;
                            }
                        }else{
                            $message = 'Не удалось обнаружить комментарий!';
                        }
                    }
                    
                    echo json_encode(array(
                        'response' => 'success',
                        'success' => $success,
                        'errrors' => $errrors,
                        'ids' => $ids
                    ));                    
                }else{
                    $message = 'необходимо указать обязательные параметры!';
                }
    		}
            elseif (Yii::$app->request->post('action') == 'read-comment'){
                $ids = Yii::$app->request->post('ids');
                
                if(!is_array($ids)){
                    $ids = [$ids];
                }
                
                if($ids){
                    foreach($ids as $id){
                        if($message = \common\models\Message::find()
                            ->where([
                                'id' => $id
                            ])
                            ->one()
                        )
                        {
                            $messageView = new \common\models\MessageView([
                                'room_id' => $message->room_id,
                                'message_id' => $message->id
                            ]);
                            
                            if($messageView->save()){
                                $success[] = $messageView->id;
                                $message->updated_at = $message->readed_at = time();
                                
                                if($message->update(['updated_at', 'readed_at'])){
                                    //
                                }else{
                                    //
                                }
                            }else{
                                $errrors[] = $messageView->errors;
                            }
                        }else{
                            $message = 'Не удалось обнаружить комментарий!';
                        }
                    }
                    
                    echo json_encode(array(
                        'response' => 'success',
                        'success' => $success,
                        'errrors' => $errrors,
                        'ids' => $ids
                    ));                    
                }else{
                    $message = 'необходимо указать обязательные параметры!';
                }
    		}elseif (Yii::$app->request->post('action') == 'typing-comment'){
                
                $room_id = Yii::$app->request->post('room_id');
                $user_id = Yii::$app->user->id;
                
                if($room_id && $user_id){
                    if(!$model = \common\models\MessageTyped::find()->where(['room_id' => $room_id, 'user_id' => $user_id])->one()){
                        $model = new \common\models\MessageTyped(['room_id' => $room_id, 'user_id' => $user_id]);
                    }
                    
                    $model->state = \common\models\MessageTyped::STATE_TYPING;
                    
                    if($model->save()){
                        echo json_encode(array(
                            'response' => 'success',
                        ));
                    }else{
                        $message = 'Не удалось обнаружить комментарий!';
                    }                   
                }else{
                    $message = 'необходимо указать обязательные параметры!';
                }
    		}elseif (Yii::$app->request->post('action') == 'typed-comment'){
                $room_id = Yii::$app->request->post('room_id');
                $user_id = Yii::$app->user->id;
                
                if($room_id && $user_id){
                    if(!$model = \common\models\MessageTyped::find()->where(['room_id' => $room_id, 'user_id' => $user_id])->one()){
                        $model = new \common\models\MessageTyped(['room_id' => $room_id, 'user_id' => $user_id]);
                    }
                    
                    $model->state = \common\models\MessageTyped::STATE_TYPED;
                    
                    if($model->save()){
                        echo json_encode(array(
                            'response' => 'success',
                        ));
                    }else{
                        $message = 'Не удалось обнаружить комментарий!';
                    }                   
                }else{
                    $message = 'необходимо указать обязательные параметры!';
                }
    		}elseif (Yii::$app->request->post('action') == 'more-comment'){
                $room_id = Yii::$app->request->post('room_id');
                $idFrom = Yii::$app->request->post('id_from');
                $count = Yii::$app->request->post('count');
                
                if($room_id && $idFrom && $count){
                    if($messages = \common\models\Message::find()
                        ->with(['file', 'userFrom'])
                        ->where([
                            'and',
                            ['room_id' => $room_id],
                            ['<', 'id', $idFrom]
                        ])
                        ->active()
                        ->limit($count)
                        ->orderBy('id DESC')
                        ->all()
                    )
                    {
                        foreach($messages as $message){
                            $list[] = [
                                //'attributes' => $message->attributes,
                                'template' => $this->renderPartial('/message-room/message', [
                                    'data' => $message,
                                ]),
                                'id' => $message->id
                            ];
                        }
                    }else{
                        $message = 'Не удалось обнаружить комментарий!';
                    }
                    
                    echo json_encode(array(
                        'response' => 'success',
                        'idFrom' => $idFrom,
                        'list' => $list
                    ));
                    return;
                }else{
                    $message = 'необходимо указать обязательные параметры!';
                }
    		}else{
    	  		$message = 'Нет результатов по вашему запросу!';
            }	  
    	}
        else{
            $message = 'Ошибка запроса';
        }
            
        echo json_encode(array(
            'response' => 'error',
            'message' => $message
        ));        
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}
}