<?php
namespace F3\Util;

use PHPUnit\Framework\TestCase;

/**
 * @covers \F3\Util\DateUtil
 */
class DateUtilTest extends TestCase {

    public function testSubtractInterval() {
        $actual = DateUtil::subtractInterval('2021-05-30', 'P1D'); // period 1 day
        $this->assertEquals('2021-05-29', $actual, 'subtract one day');
    }

    public function testValidDate() {
        $this->assertTrue(DateUtil::validDate('2021-05-06', 'Y-m-d'), 'leading zeros');
        $this->assertTrue(DateUtil::validDate('2021-5-6', 'Y-n-j'), 'no leading zeros');
    }

    public function testValidDateDeafultFormat() {
        $this->assertTrue(DateUtil::validDate('2021-05-06'), 'leading zeros');
        $this->assertTrue(DateUtil::validDate('2021-12-30'), 'full date');
        $this->assertTrue(DateUtil::validDate('2020-02-29'), 'leap year');

        $this->assertFalse(DateUtil::validDate('2021-05-32'), 'invalid days');
        $this->assertFalse(DateUtil::validDate('2021-13-31'), 'invalid months');
        $this->assertFalse(DateUtil::validDate('2021-5-6'), 'no leading zeros');
    }

    public function testGetDefaultDateDefaultInterval() {
        $day1 = new \DateTime();
        $day2 = $day1->sub(new \DateInterval('P0M'));
        $this->assertEquals($day2->format('Y-m-d'), DateUtil::getDefaultDate(null), 'same day');

    }

    public function testGetDefaultDateSubtractInterval() {
        // test subtracting a day
        $today = new \DateTime();
        $yesterday = $today->sub(new \DateInterval('P1D'));
        $this->assertEquals($yesterday->format('Y-m-d'), DateUtil::getDefaultDateSubtractInterval(null, 'P1D'), 'yesterday');
        $this->assertEquals('2021-12-31', DateUtil::getDefaultDateSubtractInterval('2021-12-31', 'P0M'), 'same date');
    }
}
?>
