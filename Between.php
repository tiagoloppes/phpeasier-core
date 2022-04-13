<?php
class Between {
	public $startValue;
	public $endValue;
	public function __construct($startValue, $endValue) {
		$this->startValue = $startValue;
		$this->endValue = $endValue;
	}
}