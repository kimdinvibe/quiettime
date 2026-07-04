<!-- Custom tabs (Charts with tabs)-->
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs pull-right">
        <li class="pull-left header"><i class="fa fa-inbox"></i> <?php echo $title ?></li>
        <li class="pull-left">
            <div class="pull-right reportrange" style="background: #fff; cursor: pointer; padding: 2px 4px; margin-top: 3px; border: 1px solid #ccc" obj="<?php echo $id ?>" model="<?php echo $class ?>" itemtitle="<?php echo $itemTitle ?>">
                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                <span><?php echo \backend\components\Statistic::getCurrentDate('firstDateName') ?> - <?php echo \backend\components\Statistic::getCurrentDate('lastDateName') ?></span> <b class="caret"></b>
            </div>
        </li>
    </ul>
    <div class="tab-content no-padding">
        <!-- Morris chart - Sales -->
        <?php if($statistics = \backend\components\Statistic::getStatisticForTemplateModelByDays($class, $itemTitle)): ?>
            <div class="chart tab-pane active" style="position: relative; min-height: 300px;">
                <div class="box-body">
                    <p class="text-center">
                        <strong><?php echo $title ?>: <span class="title-date"><?php echo \backend\components\Statistic::getCurrentDate('firstDateName') ?> - <?php echo \backend\components\Statistic::getCurrentDate('lastDateName') ?></span></strong>
                    </p>

                    <div class="chart">
                        <!-- Sales Chart Canvas -->
                        <div id="graph-<?php echo $id ?>" style="height: 325px;">
                            <?php if(!isset($statistics['morris'])): ?>
                                <div style="text-align: center; vertical-align: middle; display: inline-block; width: 100%; height: 100%;"><?php echo Yii::t('backend', 'Empty') ?></div>
                            <?php endif ?>
                        </div>
                    </div>
                    <!-- /.chart-responsive -->
                </div>
                <!-- ./box-body -->
            </div>

        <?php if(isset($statistics['morris'])): ?>
            <script type="text/javascript">
                $(document).ready(function(){
                    // LINE CHART
                    if(!jQuery.graph){
                        jQuery.graph = {};
                    }

                    jQuery.graph['<?php echo $id ?>'] = new Morris.Area({
                        element: 'graph-<?php echo $id ?>',
                        resize: true,
                        data: <?php echo json_encode(array_values($statistics['morris'])) ?>,
                        xkey: 'y',
                        ykeys: <?php echo json_encode([$statistics['title']]) ?>,
                        labels: <?php echo json_encode([$statistics['title']]) ?>,
                        lineColors: <?php echo json_encode([$statistics['color']]) ?>,
                        hideHover: 'auto'
                    });
                })
            </script>
        <?php endif; ?>
        <?php else:  ?>
            <div style="text-align: center; vertical-align: middle; display: inline-block; width: 100%; height: 100%;"><?php echo Yii::t('backend', 'Empty') ?></div>
        <?php endif; ?>
    </div>
</div>
<!-- /.nav-tabs-custom -->