<?php

namespace Dutchento\Vatfallback\Test\Unit\Service\Validate;

use Dutchento\Vatfallback\Model\VatNumber\ConfigInterface;
use Dutchento\Vatfallback\Service\Validate\Regex;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ConfigInterface
     */
    private $vatNumberConfigMock;

    /**
     * @var Regex
     */
    private $regex;

    protected function setUp()
    {
        $this->vatNumberConfigMock = $this->createMock(ConfigInterface::class);
        $this->vatNumberConfigMock->expects($this->any())
            ->method('get')
            ->willReturn($this->getRegexList());
        $this->regex = new Regex($this->vatNumberConfigMock);
    }

    /**
     * @dataProvider dataproviderValidNumbers
     * @param $vatNr
     */
    public function testValidNumbers($vatNr)
    {
        $countryCode = substr($vatNr, 0, 2);
        $vatNr = substr($vatNr, 2);

        $this->assertTrue($this->regex->validateVATNumber($vatNr, $countryCode));
    }

    /**
     * @dataProvider dataproviderInvalidNumbers
     * @param $vatNr
     */
    public function testInvalidNumbers($vatNr)
    {
        $countryCode = substr($vatNr, 0, 2);
        $vatNr = substr($vatNr, 2);

        $this->assertFalse($this->regex->validateVATNumber($vatNr, $countryCode));
    }

    /* Valid numbers */
    public function dataproviderValidNumbers()
    {
        return [
            ['ATU12345678'],
            ['BE0123456789'],
            ['BG1234567890'],
            ['CZ12345678'],
            ['CZ123456789'],
            ['CZ1234567890'],
            ['DE123456789'],
            ['CY12345678A'],
            ['CY12345678X'],
            ['CY12345678Z'],
            ['DK12345678'],
            ['EE123456789'],
            ['EL123456789'],
            ['GR123456789'],
            ['ESX12345678'],
            ['ES12345678X'],
            ['ESX2345678X'],
            ['ES54362315K'],
            ['ESX2482300W'],
            ['ESX5253868R'],
            ['ESM1234567L'],
            ['ESJ99216582'],
            ['ESB58378431'],
            ['ESB64717838'],
            ['ES54362315Z'],
            ['ESX2482300A'],
            ['ESJ99216583'],
            ['FI20774740'],
            ['FI20774741'],
            ['FR40303265045'],
            ['FR23334175221'],
            ['FRK7399859412'],
            ['FR4Z123456782'],
            ['FR84323140391'],
            ['FRAS323140391'],
            ['GB980780684'],
            ['GB802311781'],
            ['GBHA999'],
            ['GB999999999999'],
            ['HU99999999'],
            ['HU12892312'],
            ['HU12892313'],
            ['IE6433435F'],
            ['IE8D79739I'],
            ['IE8D79738J'],
            ['IE9S99999L'],
            ['IT12345678901'],
            ['LT999999999'],
            ['LT123456789'],
            ['LT123456789012'],
            ['LT999999999999'],
            ['LU99999999'],
            ['LU12345678'],
            ['LV12345679012'],
            ['LV99999999999'],
            ['MT99999999'],
            ['MT12345678'],
            ['NL123455789B01'],
            ['NL123455789B02'],
            ['NL123455789B10'],
            ['PL1234567890'],
            ['PL9999999999'],
            ['PT123456789'],
            ['PT999999999'],
            ['SE123456789012'],
            ['SE999999999999'],
            ['SI12345678'],
            ['SI99999999'],
            ['SK1234567890'],
            ['SK9999999999'],
        ];
    }

    /* Invalid numbers */
    public function dataproviderInvalidNumbers()
    {
        return [
            ['AT12345678'],
            ['ATU1234567'],
            ['BE1234567890'],
            ['BE12345678900'],
            ['DE1234567890'],
            ['DEA1234567890'],
            ['CY123456789'],
            ['CYA23456789'],
            ['DK1234567'],
            ['DK1234567890'],
            ['DK123456789A'],
            ['EE12345678'],
            ['EE1234567890'],
            ['EEA234567890'],
            ['EEA234D67890'],
            ['EEA234-67890'],
            ['EL12345678'],
            ['EL1234567890'],
            ['GR12345678'],
            ['GR1234567890'],
            ['ES123456789'],
            ['ES1234567890'],
            ['ESXX345678XX'],
            ['ESX345678X'],
            ['FI2077474'],
            ['FI207747411'],
            ['FIA0774741'],
            ['FR8432314O391'],
            ['FR8432314039I'],
            ['FR8432314039'],
            ['FR843231403912'],
            ['GB8023117810'],
            ['HU1289231'],
            ['HU128923112'],
            ['IE6433435OA'],
            ['IE643335AB'],
            ['IT1234567890'],
            ['IT123456789121'],
            ['ITA2345678912'],
            ['IT1234567891A'],
            ['LT99999999999'],
            ['LT9999999999'],
            ['LT99999999'],
            ['LT9999999999990'],
            ['LTA99999999999'],
            ['LT89999999999A'],
            ['LU9999999'],
            ['LU999999999'],
            ['LU9999999999'],
            ['LUA9999999'],
            ['LU9999999A'],
            ['LV999999999999'],
            ['LV9999999999'],
            ['LV99999999'],
            ['LV9999999999990'],
            ['LVA9999999999'],
            ['LV9999999999A'],
            ['MT9999999'],
            ['MT999999999'],
            ['MT9999999A'],
            ['MTA9999999'],
            ['NL12345667712B01'],
            ['NL2345667712B012'],
            ['NL23455789B10'],
            ['NL1234557890B10'],
            ['PL999999999'],
            ['PL99999999999'],
            ['PL999999999A'],
            ['PLA999999999'],
            ['PT99999999'],
            ['PT9999999999'],
            ['PT99999999A'],
            ['PTA99999999'],
            ['PTA999A9999'],
            ['SE99999999999'],
            ['SE9999999999999'],
            ['SE99999999999A'],
            ['SEA99999999999'],
            ['SE99999A999999'],
            ['SI9999999'],
            ['SI999999999'],
            ['SI9999999A'],
            ['SIA9999999'],
            ['SI999A9999'],
            ['SK99999999'],
            ['SK999999999'],
            ['SK99999999999'],
            ['SK999999999998'],
            ['SK999999999A'],
            ['SK9999A99999'],
            ['SKA999999999'],
        ];
    }

    private function getRegexList()
    {
        return [
            'AT' => ['pattern' => '/^U[0-9]{8}$/'],
            'BE' => ['pattern' => '/^[0]{0,1}[0-9]{9}$/'],
            'BG' => ['pattern' => '/^[0-9]{9,10}$/'],
            'CZ' => ['pattern' => '/^[0-9]{8,10}$/'],
            'DE' => ['pattern' => '/^[0-9]{9}$/'],
            'CY' => ['pattern' => '/^[0-9]{8}[A-Z]$/'],
            'DK' => ['pattern' => '/^[0-9]{8}$/'],
            'EE' => ['pattern' => '/^[0-9]{9}$/'],
            'GR' => ['pattern' => '/^[0-9]{9}$/'],
            'EL' => ['pattern' => '/^[0-9]{9}$/'],
            'ES' => ['pattern' => '/^([a-zA-Z]\d{7}[0-9])|([0-9]\d{7}[a-zA-Z])|([a-zA-Z]\d{7}[0-9a-zA-Z])$/'],
            'FI' => ['pattern' => '/^[0-9]{8}$/'],
            'FR' => ['pattern' => '/^[0-9A-Z]{2}[0-9]{9}$/'],
            'GB' => ['pattern' => '/^(([1-9]\d{8})|([1-9]\d{11})|(GD[1-9]\d{2})|(HA[1-9]\d{2}))$/'],
            'HU' => ['pattern' => '/^[0-9]{8}$/'],
            'IE' => ['pattern' => '/^[0-9][A-Z0-9\\+\\*][0-9]{5}[A-Z]$/'],
            'IT' => ['pattern' => '/^[0-9]{11}$/'],
            'LT' => ['pattern' => '/^([0-9]{9}|[0-9]{12})$/'],
            'LU' => ['pattern' => '/^[0-9]{8}$/'],
            'LV' => ['pattern' => '/^[0-9]{11}$/'],
            'MT' => ['pattern' => '/^[0-9]{8}$/'],
            'NL' => ['pattern' => '/^[0-9]{9}B[0-9]{2}$/'],
            'PL' => ['pattern' => '/^[0-9]{10}$/'],
            'PT' => ['pattern' => '/^[0-9]{9}$/'],
            'SE' => ['pattern' => '/^[0-9]{12}$/'],
            'SI' => ['pattern' => '/^[0-9]{8}$/'],
            'SK' => ['pattern' => '/^[0-9]{10}$/'],
        ];
    }
}
