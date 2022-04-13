<?php
class Date {
	/**
	 *
	 * @param string $datetime
	 *        	- Sample: '1999-12-25 24:58:00'
	 * @param string $format
	 *        	- Sample: 'd/y' -> output: 25/99
	 */
	public static function formatDate($datetime, $format = 'd/m/Y H:i:s') {
		$date = new DateTime ( $datetime );
		return $date->format ( $format );
	}
	public static function getDate($format = 'd/m/Y H:i:s') {
		$date = new DateTime ();
		return $date->format ( $format );
	}
	public static function diffDate($beforeDate, $afterDate, $diffType = 'D') {
		if (is_string ( $beforeDate ))
			$beforeDate = date_create ( $beforeDate );
		if (is_string ( $afterDate ))
			$afterDate = date_create ( $afterDate );
		
		$diff = date_diff ( $beforeDate, $afterDate );
		switch (strtoupper ( $diffType )) {
			case "Y" :
				$total = $diff->y + $diff->m / 12 + $diff->d / 365.25;
				break;
			case "M" :
				$total = $diff->y * 12 + $diff->m + $diff->d / 30 + $diff->h / 24;
				break;
			case "D" :
				$total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h / 24 + $diff->i / 60;
				break;
			case "H" :
				$total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i / 60;
				break;
			case "I" :
				$total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s / 60;
				break;
			case "S" :
				$total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i) * 60 + $diff->s;
				break;
		}
		if ($diff->invert)
			return - 1 * $total;
		else
			return $total;
	}
	public static function monthToTextual($numberOfMonth) {
		$numberOfMonth = str_pad ( $numberOfMonth, 2, '0', STR_PAD_LEFT );
		switch ($numberOfMonth) {
			case '01' :
				$name = 'Janeiro';
				break;
			case '02' :
				$name = 'Fevereiro';
				break;
			case '03' :
				$name = 'Março';
				break;
			case '04' :
				$name = 'Abril';
				break;
			case '05' :
				$name = 'Maio';
				break;
			case '06' :
				$name = 'Junho';
				break;
			case '07' :
				$name = 'Julho';
				break;
			case '08' :
				$name = 'Agosto';
				break;
			case '09' :
				$name = 'Setembro';
				break;
			case '10' :
				$name = 'Outubro';
				break;
			case '11' :
				$name = 'Novembro';
				break;
			case '12' :
				$name = 'Dezembro';
				break;
		}
		return $name;
	}
}