<?php
/**
 * @author Eugene Terentev <eugene@terentev.net>
 * @var $model common\models\TimelineEvent
 */
?>
<div class="timeline-item">
    <span class="time">
        <i class="fa fa-clock-o"></i>
        <?php echo Yii::$app->formatter->asRelativeTime($model->created_at) ?>
    </span>

    <h3 class="timeline-header">
        <?php echo Yii::t('backend', 'You have new spot!') ?>
    </h3>

    <div class="timeline-body">
        <?php echo Yii::t('backend', 'New spot ({identity}) was registered at {created_at} from {user}', [
            'identity' => $model->data['spot_name'],
            'created_at' => Yii::$app->formatter->asDatetime($model->data['created_at']),
            'user' => 'User '.$model->data['user_id']
        ]) ?>
    </div>

    <div class="timeline-footer">
        <?php echo \yii\helpers\Html::a(
            Yii::t('backend', 'View spot'),
            ['/spot/view', 'id' => $model->data['spot_id']],
            ['class' => 'btn btn-success btn-sm']
        ) ?>
    </div>
</div>