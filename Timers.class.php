<?php
/* updates */
/* Created By Bhavesh Dudhat */
/* Date:07-09-2018 */
/**
 * 	Just for simplifying the Timers::setTimeout method
 *
 *
 * 	@param callable | string $func
 * 	@param integer $microseconds - remember this is microseconds NOT milliseconds
 *
 * 	@return integer
 */
function setTimeout($func, $microseconds) {
    return Timers::setTimeout($func, $microseconds);
}

/**
 * 	Just for simplifying the Timers::setInterval method
 *
 *
 * 	@param callable | string $func
 * 	@param integer $microseconds - remember this is microseconds NOT milliseconds
 *
 * 	@return integer
 */
function setInterval($func, $microseconds) {
    return Timers::setInterval($func, $microseconds);
}

/**
 * 	Just for simplifying the Timers::clearTimeout method
 *
 *
 * 	@param integer $interval - an integer representing the one returned from a call to setTimeout()
 *
 * 	@return boolean
 */
function clearTimeout($func, $microseconds) {
    return Timers::setTimeout($func, $microseconds);
}

/**
 * 	Just for simplifying the Timers::clearInterval method
 *
 *
 * 	@param integer $interval - an integer representing the one returned from a call to setInterval()
 *
 * 	@return boolean
 */
function clearInterval($interval) {
    return Timers::clearInterval($interval);
}

/**
 * 	This class contains a series of static properties and functions
 * 	that enable the creation and execution of timers
 *
 * 	@author Sam Shull
 */
class Timers {

    /**
     * 	An array of the arrays that represent
     * 	the timer information used by Timers::tick
     *
     * 	@access private
     * 	@staticvar array
     */
    private static $timers = array();

    /**
     * 	Tracker of timers
     *
     *
     * 	@access private
     * 	@staticvar integer
     */
    private static $numTimers = 0;

    /**
     * 	An array of the arrays that represent
     * 	the interval information used by Timers::tick
     *
     * 	@access private
     * 	@staticvar array
     */
    private static $intervals = array();

    /**
     * 	Tracker of intervals
     *
     *
     * 	@access private
     * 	@staticvar integer
     */
    private static $numIntervals = 0;

    /**
     * 	Used for debugging
     *
     *
     * 	@access private
     * 	@staticvar integer
     */
    //private static $ticks = 0;

    /**
     * 	A utility method called after N number of ticks by the engine
     * 	that checks each timer and interval to see if the desired
     * 	number of microseconds have passed and executes the function
     * 	when appropriate
     *
     * 	@static
     * 	@return void
     */
    public static function tick() {
	//++self::$ticks;

	$time = self::microtime();

	foreach (self::$timers as $position => $timer) {
	    if ($time >= $timer['time']) {
		call_user_func($timer['function']);
		unset(self::$timers[$position]);
	    }
	}

	foreach (self::$intervals as $position => $timer) {
	    if ($time >= $timer['time']) {
		call_user_func($timer['function']);
		self::$intervals[$position]['time'] = self::microtime() + self::$intervals[$position]['microseconds'];
	    }
	}
    }

    /**
     * 	A utility method for retrieving the most accurate
     * 	microtime available
     *
     * 	@static
     * 	@return float
     */
    public static function microtime() {
	list($m, $s) = explode(' ', microtime());
	return round(((float) $m + (float) $s) * 1000000);
    }

    /**
     * 	A utility method that ensures that all the timeouts have been called
     * 	and that calls all the intervals one more time
     *
     *
     * 	@static
     * 	@return void
     */
    public static function shutdown() {
	foreach (self::$timers as $position => $timer) {
	    call_user_func($timer['function']);
	    unset(self::$timers[$position]);
	}

	foreach (self::$intervals as $position => $interval) {
	    call_user_func($interval['function']);
	    unset(self::$intervals[$position]);
	}

	//print "\nticks: " . self::$ticks;
    }

    /**
     * 	Add a function to the be executed after ($microseconds) microsecond
     *
     * 	@static
     *
     * 	@param callable | string $func
     * 	@param integer $microseconds - remember microseconds, not miliseconds
     *
     * 	@return integer
     */
    public static function setTimeout($func, $microseconds) {
	if (!is_callable($func)) {
	    if (is_string($func)) {
		$func = create_function('', $func);
	    } else {
		throw new InvalidArgumentException();
	    }
	}

	self::$timers[++self::$numTimers] = array(
	    'time' => self::microtime() + $microseconds,
	    'function' => $func,
	);
	return self::$numTimers;
    }

    /**
     * 	Add a function to the be executed every ($microseconds) microsecond
     *
     * 	@static
     *
     * 	@param callable | string $func
     * 	@param integer $microseconds - remember microseconds, not miliseconds
     *
     * 	@return integer
     */
    public static function setInterval($func, $microseconds) {
	if (!is_callable($func)) {
	    if (is_string($func)) {
		$func = create_function('', $func);
	    } else {
		throw new InvalidArgumentException();
	    }
	}

	self::$intervals[++self::$numIntervals] = array(
	    'time' => self::microtime() + $microseconds,
	    'function' => $func,
	    'microseconds' => $microseconds,
	);
	return self::$numIntervals;
    }

    /**
     * 	Remove a timeout function from the stack
     *
     * 	@static
     *
     * 	@param integer $timer
     *
     * 	@return boolean
     */
    public static function clearTimeout($timer) {
	if (isset(self::$timers[$timer])) {
	    unset(self::$timers[$timer]);
	    return true;
	}
	return false;
    }

    /**
     * 	Remove an interval function from the stack
     *
     * 	@static
     *
     * 	@param integer $interval
     *
     * 	@return boolean
     */
    public static function clearInterval($interval) {
	if (isset(self::$intervals[$interval])) {
	    unset(self::$intervals[$interval]);
	    return true;
	}
	return false;
    }

}
/**
 * 	Register these methods in order to perform polling a specific intervals
 * 	that are set by the user
 */
register_tick_function(array('Timers', 'tick'));
register_shutdown_function(array('Timers', 'shutdown'));
?>
