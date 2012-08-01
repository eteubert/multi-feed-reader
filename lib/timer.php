<?php
namespace MultiFeedReader;

/**
 * @author   <dh@squidcode.com>
 * http://code.google.com/p/phptimer/
 */
class Timer {

	var $timeArray = Array ();
	
	function getTime() {
		$time = microtime();
		$time = explode (' ', $time);
		$time = $time [1] + $time [0];
		return $time;
	}
	
	function extendRecord ($label) {
		$value = &$this->timeArray [$label];
		if ($value ["stop"] > 0) {
			$value ["range"] = $value ["stop"]-$value["start"];
			$value ["status"] = "stopped";
		} else {
			$value ["range"] = $this->getTime() - $value ["start"];
			$value ["status"] = "running";
		}
		$value ["range_human"] = sprintf ("%01.2f", $value ["range"]); 
	}
	
	function start ($label) {
		$this->timeArray[$label]["start"] = $this->getTime();
		$this->timeArray[$label]["stop"] = 0;
	}
	
	function stop ($label) {
		if (isset ($this->timeArray[$label]["stop"])) {
			$this->timeArray[$label]["stop"] = $this->getTime();
		}
	}
	
	function stopAll () {
		foreach ($this->timeArray as $label => $value) $this->stop ($label);
	}
	
	function del ($label) {
		if (isset ($this->timeArray[$label])) {
			unset ($this->timeArray[$label]);
		}
	}
	
	function delAll () {
		$this->timeArray = Array ();
	}
	
	function get ($label, $key = false) {
		if (isset ($this->timeArray[$label])) {
			$this->extendRecord ($label);
			if ( ! $key ) {
				return $this->timeArray[$label];
			} else {
				return $this->timeArray[$label][$key];
			}
			
		} else {
			return false;
		}
	}
	
	function getAll () {
		foreach ($this->timeArray as $label => $value) $this->extendRecord ($label);
		return $this->timeArray;
	}
	
}