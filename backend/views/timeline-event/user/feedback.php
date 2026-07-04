<?php
/**
 * @author Eugene Terentev <eugene@terentev.net>
 * @var $model common\models\TimelineEvent
 */

use yii\helpers\Html;

?>
<div class="timeline-item">
    <span class="time">
        <i class="fa fa-clock-o"></i>
        <?php echo Yii::$app->formatter->asRelativeTime($model->created_at) ?>
    </span>

    <h3 class="timeline-header">
        <?php echo Yii::t('backend', 'You have new feedback!') ?>
    </h3>

    <div class="timeline-body">
        <?php echo Yii::t('backend', 'New feedback from ({identity}) was registered at {created_at}', [
            'identity' => $model->data['public_identity'],
            'created_at' => Yii::$app->formatter->asDatetime($model->data['created_at'])
        ]) ?>
        <br>
        <?php if($model->data['sticker_id']){$sticker = \common\models\Sticker::findOne($model->data['sticker_id']);} ?>
        <?php echo Yii::t('backend', 'Location: {location}, Event: {event}, Rating: {value}, Sticker: {sticker}', [
            'location' => $model->data['location_name']?Html::a($model->data['location_name'], ['location/view', 'id' => $model->data['location_id']]):null,
            'event' => $model->data['event_name']?Html::a($model->data['event_name'], ['location/view', 'id' => $model->data['event_id']]):null,
            'value' => $model->data['rating'],
            'sticker' => $model->data['sticker_path']?Html::a(Html::img($model->data['sticker_path'], ['style' => 'height: 40px;']), ['sticker/view', 'id' => $model->data['sticker_id']]):''
        ]) ?>
    </div>

    <div class="timeline-footer">
        <?php echo \yii\helpers\Html::a(
            Yii::t('backend', 'View user'),
            ['/user/view', 'id' => $model->data['user_id']],
            ['class' => 'btn btn-success btn-sm']
        ) ?>
        <?php echo \yii\helpers\Html::a(
            Yii::t('backend', 'View feedback'),
            ['/user-feedback/view', 'id' => $model->data['id']],
            ['class' => 'btn btn-success btn-sm']
        ) ?>
    </div>
</div>