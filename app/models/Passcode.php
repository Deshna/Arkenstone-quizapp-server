<?php

class Passcode{
	public static function genCode(){
		$codeArray = array();
		for ($i=0; $i < 16; $i++) { 
			$codeArray[$i] = strtoupper(dechex(rand(1,4096)+$i*4096));
		}
		return $codeArray;
	}
};