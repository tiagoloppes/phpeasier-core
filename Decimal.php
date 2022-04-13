<?php
class Decimal {
	/**
	 *
	 * @return float
	 */
	public static function toFloat($strDecimalNumber) {
		if (preg_match ( '/^([0-9,.]+)[,.]([0-9]+)$/', $strDecimalNumber, $matches )) {
			$part1 = preg_replace ( '/\D/', '', $matches [1] );
			$part2 = $matches [2];
			$floatNumber = $part1 . '.' . $part2;
			return ( float ) $floatNumber;
		} else {
			return ( float ) $strDecimalNumber;
		}
	}
	public static function roundToUp($number, $precision = 2) {
		$fig = ( int ) str_pad ( '1', $precision, '0' );
		return (ceil ( $number * $fig ) / $fig);
	}
	public static function roundToDown($number, $precision = 2) {
		$fig = ( int ) str_pad ( '1', $precision, '0' );
		return (floor ( $number * $fig ) / $fig);
	}
	public static function priceHighligth($floatPrice) {
		return preg_replace ( "/(^[0-9.]+),([0-9]+)$/", "$1<sup>,$2</sup>", number_format ( $floatPrice, 2, ',', '.' ) );
	}
	public static function floatToBrReal($floatPrice) {
		return number_format ( $floatPrice, 2, ',', '.' );
	}
	public static function floatToNfePattern($floatPrice) {
		$floatPrice = round ( $floatPrice, 2 );
		preg_match ( '/^([0-9]+)\.?([0-9]*)$/', $floatPrice, $matches );
		$decimal = str_pad ( $matches [2], 2, '0', STR_PAD_RIGHT );
		return $matches [1] . '.' . $decimal;
	}
}
