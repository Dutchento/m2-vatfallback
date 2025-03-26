<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Test\Unit\Plugin\Magento\Customer\Model;

use DateTimeImmutable;
use Dutchento\Vatfallback\Plugin\Magento\Customer\Model\Vat;
use Dutchento\Vatfallback\Service\CleanNumberString;
use Dutchento\Vatfallback\Service\ValidateVatInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class VatGatewayObjectTest
 * @package Dutchento\Vatfallback\Test\Unit\Plugin\Magento\Customer\Model
 */
class VatGatewayObjectTest extends TestCase
{
    protected $vatPlugin;

    /**
     * @SuppressWarnings(PHPMD.LongVariableNames)
     */
    public function setUp()
    {
        $mockValidateVatService = $this->createMock(ValidateVatInterface::class);
        $mockCleanNumberService = $this->createMock(CleanNumberString::class);

        $this->vatPlugin = new Vat($mockValidateVatService, $mockCleanNumberService);
    }

    public function testCreatingSuccesfulGatewayObject()
    {
        $object = $this->vatPlugin->createGatewayResponseObject('VATNUMBER', true, 'message');

        $this->assertEquals((new DateTimeImmutable())->format('Y-m-d'), $object->getRequestDate());
        $this->assertEquals('VATNUMBER', $object->getRequestIdentifier());
        $this->assertEquals('message', $object->getRequestMessage());
        $this->assertTrue($object->getIsValid());
        $this->assertTrue($object->getRequestSuccess());
    }

    public function testCreatingFailedGatewayObject()
    {
        $object = $this->vatPlugin->createGatewayResponseObject('VATNUMBER', false, 'message');

        $this->assertEquals((new DateTimeImmutable())->format('Y-m-d'), $object->getRequestDate());
        $this->assertEquals('VATNUMBER', $object->getRequestIdentifier());
        $this->assertEquals('message', $object->getRequestMessage());
        $this->assertFalse($object->getIsValid());
        $this->assertFalse($object->getRequestSuccess());
    }
}
