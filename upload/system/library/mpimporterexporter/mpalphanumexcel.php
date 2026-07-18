<?php
namespace MpImporterExporter;
class mpAlphaNumExcel {
	/**
	 * https://dev.to/kevinmel2000/alphabet-to-number-number-to-alphabet-on-php-52df
	 * functions: numberToAlphabet((int)$number) : return equalvalen alphabet string for number
	 * functions: alphabetToNumber((string)$string) : return equalvalent number for alphabet string
	*/

	public function numberToAlphabet(int $number) {
		$number = intval($number);
		if ($number <= 0) {
			return '';
		}
		$alphabet = '';
		while($number != 0) {
			$p = ($number - 1) % 26;
			$number = intval(($number - $p) / 26);
			$alphabet = chr(65 + $p) . $alphabet;
		}
		return $alphabet;
	}

	public function alphabetToNumber(string $string) {
		$string = strtoupper($string);
		$length = strlen($string);
		$number = 0;
		$level = 1;
		while ($length >= $level ) {
			$char = $string[$length - $level];
			$c = ord($char) - 64;
			$number += $c * (26 ** ($level-1));
			$level++;
		}
		return $number;
	}

}