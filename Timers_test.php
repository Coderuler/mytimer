<?php


function test ()
{
	print "\ntest() called";
}

require_once 'Timers.class.php';

echo "\nFirst Timeout: ", setTimeout('test', 11000000);

echo "\nFirst Interval: ", setInterval('print "\nTimers_test.php";', 1000000);

include 'Timers_test2.php';

//the timer will not execute on this page,
//because there is no declare(ticks=N); on this page

print "\nNo more calls to tick function";

$end = time() + 1;

while (time() < $end)
{

}

print "\nI told you,\nbut now the shutdown function will call the intervals one more time\nand the timeout that has not been hit yet";

