<?php

/*** This script is not currently being used ***/
$load_threshold = 6.0
$load = shell_exec('/bin/uptime');
// format the output to our likings
$load = trim(substr($load, strpos($load, 'load average:')+13));
$load = str_replace(',', '', trim(substr($load, 0, strpos($load, ' '))));
echo('CPU load ' . $load . ' ... ');
$load = floatval($load);
// test if cpu load is above the threshold
if ($load > $load_threshold) {
   shell_exec('/sbin/reboot');
}
