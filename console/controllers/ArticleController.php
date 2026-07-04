<?php

/**
 * Eugine Terentev <eugine@terentev.net>
 */

namespace console\controllers;

use common\models\Article;
use Yii;
use common\models\Message;
use common\models\MessageLog;
use common\models\MessageRoom;
use common\models\MessageRoomAccess;
use common\models\MessageRoomApns;
use common\models\MessageView;
use common\models\Price;
use common\models\PriceField;
use common\models\User;
use common\models\UserDevice;
use paragraph1\phpFCM\Recipient\Device;
use yii\console\Controller;
use yii\helpers\Console;

use yii\db\Query;

/**
 * Class ExtendedMessageController
 * @package console\controllers
 */
class ArticleController extends Controller
{
    public function actionSentNotification()
    {
        if ($articles = Article::find()->where([
            'status' => Article::STATUS_ACTIVE,
            'sent_to_phone_at' => null,
        ])->all()) {
            foreach ($articles as $article) {
                // to do sent to phones

                $article->sent_to_phone_at = time();
                $article->update(false, ['sent_to_phone_at']);
            }
        }
    }
}
