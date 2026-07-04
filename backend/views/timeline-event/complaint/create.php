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
        <?php echo Yii::t('backend', 'You have new complaint!') ?>
    </h3>

    <div class="timeline-body">
        <?php echo Yii::t('backend', 'New complaint from ({identity}) was registered at {created_at}', [
            'identity' => $model->data['user_id'],
            'created_at' => Yii::$app->formatter->asDatetime($model->data['created_at'])
        ]) ?>
        <br>
        <?php echo Yii::t('backend', 'Spot: {spot}, <br>Cause: {cause}', [
            'spot' => $model->data['spot_name']?Html::a($model->data['spot_name'], ['spot/view', 'id' => $model->data['spot_id']]):null,
            'cause' => $model->data['complaint_cause']
        ]) ?>
    </div>

    <div class="timeline-footer">
        <?php echo \yii\helpers\Html::a(
            Yii::t('backend', 'View complaint'),
            ['/spot-complaint/view', 'id' => $model->data['complaint_id']],
            ['class' => 'btn btn-success btn-sm']
        ) ?>
        <?php echo \yii\helpers\Html::a(
            Yii::t('backend', 'View spot'),
            ['/spot/view', 'id' => $model->data['spot_id']],
            ['class' => 'btn btn-success btn-sm']
        ) ?>
        <?php echo \yii\helpers\Html::a(
            Yii::t('backend', 'View user'),
            ['/user/view', 'id' => $model->data['user_id']],
            ['class' => 'btn btn-success btn-sm']
        ) ?>
    </div>
</div>