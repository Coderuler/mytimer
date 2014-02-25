<?php

declare(ticks=100);

echo "\nSecond Timeout: ", setTimeout('print "\ntimeout";', 1000000);

echo "\nSecond Interval: ", setInterval('print "\nTimers_test2.php";', 100000);

$end = time() + 1;

while (time() < $end)
{

}

include 'Timers_test3.php';

