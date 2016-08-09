<?php

function num2UInt16Str($num){
	$str = "";
	$bytes = 16/8;
	for($i = $bytes;$i > 0;$i--){
		$val = $i <= 1 ? floor($num%(16 * 16)) : floor($num/pow(16 * 16,$i - 1));
		$str .= pack("C",$val);
	}
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

function writeDouble($val)
{
		$ret = "";
		for($i = 1;$i <= 8;$i++){
				$p = pow(256,8-$i);
				$val2 = floor($val / $p);
				$ret .= pack("C",$val2);
				$val = $val - $val2 * $p;
		}
		return $ret;
}

function readInt(&$data)
{
		$IntArr = array_splice($data, 0,4);
		return ($IntArr[0] << 24) + ($IntArr[1] << 16) + ($IntArr[2] << 8) + $IntArr[3];
}

function readShort(&$data)
{
		$IntArr = array_splice($data, 0,2);
		// $IntArr[0] << 8 + $IntArr[1];

		return ($IntArr[0] << 8) + $IntArr[1];
}

function readByte(&$data)
{
		$IntArr = array_splice($data, 0,1);
		return $IntArr[0];
}

function readDouble(&$data)
{
		$sum = 0;
		for($i = 1;$i<=8;$i++){
				$value = readByte($data) * pow(256,8-$i);
				$sum += $value;
		}
		return $sum;
}

function readStr(&$data)
{
		$len = readShort($data);
		$str = "";
		for($i = 0;$i < $len;$i ++)
		{
				$val = array_splice($data,0,1);
				$str .= chr($val[0]);
		}
		return $str;
}
