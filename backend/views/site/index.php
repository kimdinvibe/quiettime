<?php

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */

use common\models\Order;
use common\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;

$this->title = Yii::t('backend', 'Application panel');

?>

<!-- Small boxes (Stat box) -->
<?php /*
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?php echo \common\models\Order::find()->where([
                        'and',
                        ['>=', 'created_at', strtotime(date('Y-m-d 00:00:00'))]
                    ])->count() ?></h3>
                <p><?php echo Yii::t('backend', 'New Orders Today') ?></p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="<?php echo Url::to(['/order']) ?>" class="small-box-footer"><?php echo Yii::t('backend', 'More info') ?> <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?php echo \common\models\Order::find()->where([
                        'and',
                        ['>=', 'created_at', strtotime(date('Y-m-d 00:00:00'))]
                    ])->count() ?></h3>
                <p><?php echo Yii::t('backend', 'Finished Orders Today') ?></p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="<?php echo Url::to(['/order']) ?>" class="small-box-footer"><?php echo Yii::t('backend', 'More info') ?> <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?php echo \common\models\Order::find()->where([
                        'and',
                        ['>=', 'created_at', strtotime(date('Y-m-d 00:00:00'))]
                    ])->count() ?></h3>
                <p><?php echo Yii::t('backend', 'Refused Orders Today') ?></p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="<?php echo Url::to(['/order']) ?>" class="small-box-footer"><?php echo Yii::t('backend', 'More info') ?> <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?php echo number_format((float) \common\models\Order::find()->select([
                    'SUM(price)'
                ])->where([
                        'and',
                        ['>=', 'created_at', strtotime(date('Y-m-d 00:00:00'))]
                    ])->scalar(), 0, '.', ' ' ) ?> RUB</h3>
                <p><?php echo Yii::t('backend', 'Earned Money Today') ?></p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="<?php echo Url::to(['/order']) ?>" class="small-box-footer"><?php echo Yii::t('backend', 'More info') ?> <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- /.row -->

<!-- solid sales graph -->
<div class="row main-graph">
    <section class="col-md-6">
        <?php echo $this->render('_graph', [
            'id' => 'order',
            'title' => Yii::t('backend', 'Orders'),
            'itemTitle' => Yii::t('backend', 'Order'),
            'class' => \common\models\Order::className()
        ]) ?>
    </section>
    <section class="col-md-6">
        <!-- TABLE: LATEST ORDERS -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo Yii::t('backend', 'Last orders') ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive" style="min-height: 296px;">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <?php $item = new \common\models\Order(); ?>
                                <th><?php echo Yii::t('common', 'Title') ?></th>
                                <th><?php echo $item->getAttributeLabel('user_id') ?></th>
                                <th><?php echo $item->getAttributeLabel('tariff_id') ?></th>
                                <th><?php echo $item->getAttributeLabel('status') ?></th>
                                <th><?php echo $item->getAttributeLabel('created_at') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($list = \common\models\Order::find()->with(['user', 'tariff'])->orderBy(['id' => SORT_DESC])->limit(7)->all()) : ?>
                                <?php foreach ($list as $item) : ?>
                                    <tr>
                                        <td><a href="<?php echo Url::to(['order/view', 'id' => $item->id]) ?>"><?php echo Yii::t('backend', 'Item {id}', ['id' => $item->id]) ?></a></td>
                                        <td><?php echo \yii\helpers\Html::a('User ' . $item->user->userProfile->getFullName(), ['user/view', 'id' => $item->user_id]) ?>
                                        <td><?php echo \yii\helpers\Html::a('Tariff ' . $item->tariff->title, ['tariff/view', 'id' => $item->tariff_id]) ?>
                                        <td><?php echo Order::getNameStatus($item->status) ?>
                                        <td>
                                            <div class="sparkbar" data-color="#00a65a" data-height="20">
                                                <?php echo Yii::$app->formatter->asDatetime($item->created_at) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="<?php echo Url::to(['/order']) ?>" class="btn btn-sm btn-default btn-flat pull-right"><?php echo Yii::t('backend', 'View All') ?></a>
            </div>
            <!-- /.box-footer -->
        </div>
    </section>
</div>
<!-- /.box -->
*/ ?>
<div class="row">
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?php echo \common\models\User::find()->where([
                        'and',
                        ['>=', 'created_at', strtotime(date('Y-m-d 00:00:00'))]
                    ])->count() ?></h3>
                <p><?php echo Yii::t('backend', 'New Users Today') ?></p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="<?php echo Url::to(['/timeline-event']) ?>" class="small-box-footer"><?php echo Yii::t('backend', 'More info') ?> <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?php echo \common\models\User::find()->where([
                        'and',
                        ['>=', 'created_at', strtotime("last Monday")]
                    ])->count() ?>
                </h3>
                <p><?php echo Yii::t('backend', 'New Users In Current Week') ?></p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="<?php echo Url::to(['/user-marker']) ?>" class="small-box-footer"><?php echo Yii::t('backend', 'More info') ?> <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?php echo \common\models\User::find()->count(); ?></h3>
                <p><?php echo Yii::t('backend', 'User Registrations') ?></p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="<?php echo Url::to(['/user']) ?>" class="small-box-footer"><?php echo Yii::t('backend', 'More info') ?> <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- solid sales graph -->
<div class="row main-graph">
    <section class="col-md-6">
        <!-- TABLE: LATEST ORDERS -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo Yii::t('backend', 'Latest user auth') ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive" style="min-height: 296px;">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <?php $item = new \common\models\UserLog(); ?>
                                <!--                            <th>--><?php //echo $item->getAttributeLabel('title') 
                                                                        ?>
                                <!--</th>-->
                                <th><?php echo Yii::t('common', 'Title') ?></th>
                                <th><?php echo $item->getAttributeLabel('user_id') ?></th>
                                <th><?php echo $item->getAttributeLabel('ip') ?></th>
                                <th><?php echo $item->getAttributeLabel('device_type') ?></th>
                                <th><?php echo $item->getAttributeLabel('device_name') ?></th>
                                <th><?php echo $item->getAttributeLabel('created_at') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($list = \common\models\UserLog::find()->with(['user'])->orderBy(['id' => SORT_DESC])->limit(7)->all()) : ?>
                                <?php foreach ($list as $item) : ?>
                                    <tr>
                                        <td><a href="<?php echo Url::to(['/user-log/view', 'id' => $item->id]) ?>"><?php echo Yii::t('backend', 'Item {id}', ['id' => $item->id]) ?></a></td>
                                        <td><?php echo \yii\helpers\Html::a($item->user && $item->user->userProfile ? $item->user->userProfile->getFullName() : Yii::t('backend', 'User {id}', ['id' => $item->user_id]), ['user/view', 'id' => $item->user_id])
                                            ?>
                                        </td>
                                        <td><?php echo $item->ip ?></td>
                                        <td><?php echo $item->device_type ?></td>
                                        <td><?php echo $item->device_name ?></td>
                                        <td>
                                            <div class="sparkbar" data-color="#00a65a" data-height="20">
                                                <?php echo Yii::$app->formatter->asDatetime($item->created_at) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="<?php echo Url::to(['/user-log']) ?>" class="btn btn-sm btn-default btn-flat pull-right"><?php echo Yii::t('backend', 'View All') ?></a>
            </div>
            <!-- /.box-footer -->
        </div>
    </section>
    <section class="col-md-6">
        <?php echo $this->render('_graph', [
            'id' => 'user-auth',
            'title' => Yii::t('backend', 'User Auth'),
            'itemTitle' => Yii::t('backend', 'User'),
            'class' => \common\models\UserLog::className()
        ]) ?>
    </section>
</div>
<!-- /.box -->

<!-- solid sales graph -->
<div class="row main-graph">
    <section class="col-md-6">
        <?php echo $this->render('_graph', [
            'id' => 'user',
            'title' => Yii::t('backend', 'User Registrations'),
            'itemTitle' => Yii::t('backend', 'User'),
            'class' => \common\models\User::className()
        ]) ?>
    </section>
    <section class="col-md-6">
        <!-- TABLE: LATEST ORDERS -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo Yii::t('backend', 'Latest user registration') ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive" style="min-height: 296px;">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <?php $item = new \common\models\User(); ?>
                                <!-- <th><?php echo $item->getAttributeLabel('title')
                                    ?>
                                </th> -->
                                <th><?php echo $item->getAttributeLabel('user_id')
                                    ?>
                                </th>
                                <th><?php echo $item->getAttributeLabel('phone') ?></th>
                                <th><?php echo $item->getAttributeLabel('status') ?></th>
                                <th><?php echo Yii::t('common', 'Role') ?></th>
                                <th><?php echo $item->getAttributeLabel('created_at') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($list = \common\models\User::find()->with()->orderBy(['id' => SORT_DESC])->limit(7)->all()) : ?>
                                <?php foreach ($list as $item) : ?>
                                    <tr>
                                        <!-- <td><a href="<?php echo Url::to(['user-log/view', 'id' => $item->id]) ?>"><?php echo Yii::t('backend', 'Item {id}', ['id' => $item->id]) ?></a></td> -->
                                        <td><?php echo \yii\helpers\Html::a($item->userProfile->getFullName(), ['user/view', 'id' => $item->id])
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $item->phone ?>
                                        </td>
                                        <td>
                                            <?php echo User::getStatuses($item->status) ?>
                                        </td>
                                        <td>
                                            <?php echo $item->rbacAuthAssignment ? $item->rbacAuthAssignment->item_name : null ?>
                                        </td>

                                        <td>
                                            <div class="sparkbar" data-color="#00a65a" data-height="20">
                                                <?php echo Yii::$app->formatter->asDatetime($item->created_at) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="<?php echo Url::to(['/user']) ?>" class="btn btn-sm btn-default btn-flat pull-right"><?php echo Yii::t('backend', 'View All') ?></a>
            </div>
            <!-- /.box-footer -->
        </div>
    </section>
</div>
<!-- /.box -->



<!-- solid sales graph -->
<div class="row main-graph">
    <section class="col-md-12">
        <!-- TABLE: LATEST ORDERS -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo Yii::t('backend', 'Devices') ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="chart" id="graph-devices-donut" style="height: 300px; position: relative;"></div>
            </div>
            <?php if ($statistics = \backend\components\Statistic::getDonutDevices()) : ?>
                <script type="text/javascript">
                    $(document).ready(function() {
                        //DONUT CHART
                        new Morris.Donut({
                            element: 'graph-devices-donut',
                            resize: true,
                            colors: <?php echo json_encode(array_values($statistics['colors'])) ?>,
                            data: <?php echo json_encode(array_values($statistics['items'])) ?>,
                            hideHover: 'auto'
                        });
                    })
                </script>
            <?php endif; ?>
            <!-- /.box-footer -->
        </div>
    </section>
</div>
<!-- /.box -->