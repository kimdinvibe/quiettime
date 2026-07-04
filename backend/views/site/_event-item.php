<?php if($item->locationEventObject && $statistics): ?>
    <?php foreach ($item->locationEventObject as $event): ?>
        <?php if(isset($statistics['color'][$event->id])): ?>
            <div class="progress-group">
                <span class="progress-text"><?php echo $event->title ?></span>
                <span class="progress-number"><b><?php echo (int)$statistics['countEvents'][$event->id] ?></b>/<?php echo (int)$statistics['countEventsAll'] ?></span>

                <div class="progress sm">
                    <div class="progress-bar" style="width: <?php echo $statistics['countEventsAll']?(int)($statistics['countEvents'][$event->id] * 100 / $statistics['countEventsAll']):0 ?>%; background-color: <?php echo $statistics['color'][$event->id] ?>"></div>
                </div>
            </div>
            <!-- /.progress-group -->
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <div style="text-align: center; vertical-align: middle; display: inline-block; width: 100%; height: 100%;"><?php echo Yii::t('backend', 'Empty') ?></div>
<?php endif; ?>