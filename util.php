<?php

function num2UInt16Str($num){
	echo "开始short{$num}\n";
	$str = "";
	$bytes = 16/8;
	for($i = $bytes;$i > 0;$i--){
		$val = $i <= 1 ? floor($num%(16 * 16)) : floor($num/pow(16 * 16,$i - 1));
		echo "{$val}\n";
		$str .= pack("C",$val);
	}
	var_dump($str);
	echo "完成short\n";
	return $str;
}

function num2UInt32Str($num){
	$str = "";
	$bytes = 32/8;
	for($i = $bytes;$i > 0;$i--){
		$val = $i <= 1 ? floor($num%(16 * 16)) : floor($num/pow(16 * 16,$i - 1));
		$str .= pack("C",$val);
	}
	return $str;
}

function UInt32Binary2Int($binArray){
	return $binArray[0] * 16 * 16 * 16 + $binArray[1] * 16 * 16 + $binArray[2] * 16 + $binArray[3];
}

function UInt16Binary2Int($binArray){
	return $binArray[2] * 16 + $binArray[3];
}

function dumpBinary($binArray,$isHex=false){
	if($isHex){
		foreach($binArray AS $key => $val){
			$binArray[$key] = dechex($val);
		}
	}
	return ($isHex?"[HEX]":"[BIN]").implode(" ",$binArray);
}

function writeStr($str)
{
		return num2UInt16Str(strlen($str)) . $str;
}
