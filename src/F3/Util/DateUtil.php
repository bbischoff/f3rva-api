<?php
namespace F3\Util;

/**
 * Utility class for date functions.
 *
 * @author bbischoff
 */
class DateUtil {
	
	private const DATE_FORMAT = 'Y-m-d'; // 2021-05-01
	private const TIMEZONE = 'America/New_York';
	private const DATE_INTERVAL = 'P0M'; // Period 0 Months

	public static function getDefaultDate($date) {
		return self::getDefaultDateSubtractInterval($date, self::DATE_INTERVAL);
	}
	
	public static function getDefaultDateSubtractInterval($date, $dateInterval) {		
		$dateStr = $date;
		
		if (empty($dateStr)) {
			$newDate = new \DateTime('now', new \DateTimeZone(self::TIMEZONE));
			$newDate->sub(new \DateInterval($dateInterval));
			$dateStr = $newDate->format(self::DATE_FORMAT);
		}
		
		return $dateStr;
	}
	
	public static function subtractInterval($date, $dateInterval) {
		$newDate = \DateTime::createFromFormat(self::DATE_FORMAT, $date, new \DateTimeZone(self::TIMEZONE));
		$newDate->sub(new \DateInterval($dateInterval));
		$dateStr = $newDate->format(self::DATE_FORMAT);
		
		return $dateStr;
	}
	
	public static function validDate($date, $format = self::DATE_FORMAT) {
        $d = \DateTime::createFromFormat($format, $date, new \DateTimeZone(self::TIMEZONE));
        return $d && $d->format($format) == $date;
    }
}

?>