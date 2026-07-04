Current Timestamp: <?php echo time(); ?><br>
Current Date <?php echo date('d.m.Y H:i:s', time()); ?><br>
<br>
Current Date convert <?php echo strtotime(date('d.m.Y H:i:s', time())); ?><br>
Current Begin date <?php echo date('d.m.Y 00:00:00', time()); ?><br>
<br>
Date <?php echo strtotime('-1 day', strtotime(date('d.m.Y 00:00:00', time()))); ?><br>
Date Timestamp <?php echo date('d.m.Y H:i:s', strtotime('-1 day', strtotime(date('d.m.Y 00:00:00', time())))); ?><br>
