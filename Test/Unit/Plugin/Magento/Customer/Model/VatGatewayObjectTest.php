<?php

namespace Dutchento\Vatfallback\Test\Unit\Plugin\Magento\Customer\Model;

use Dutchento\Vatfallback\Plugin\Magento\Customer\Model\Vat;
use Dutchento\Vatfallback\Service\ValidateVatInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class VatGatewayObjectTest extends TestCase
{
    protected $vatPlugin;

    public function setUp()
    {
        $mockValidateVatService = $this->createMock(ValidateVatInterface::class);

        $this->vatPlugin = new Vat($mockValidateVatService);
    }

    public function testCreatingSuccesfulGatewayObject()
    {
        $object = $this->vatPlugin->createGatewayResponseObject('VATNUMBER', true, 'message');

        $this->assertEquals((new \DateTimeImmutable())->format('Y-m-d'), $object->getRequestDate());
        $this->assertEquals('VATNUMBER', $object->getRequestIdentifier());
        $this->assertEquals('message', $object->getRequestMessage());
        $this->assertTrue($object->getIsValid());
        $this->assertTrue($object->getRequestSuccess());
    }

    public function testCreatingFailedGatewayObject()
    {
        $object = $this->vatPlugin->createGatewayResponseObject('VATNUMBER', false, 'message');

        $this->assertEquals((new \DateTimeImmutable())->format('Y-m-d'), $object->getRequestDate());
        $this->assertEquals('VATNUMBER', $object->getRequestIdentifier());
        $this->assertEquals('message', $object->getRequestMessage());
        $this->assertFalse($object->getIsValid());
        $this->assertFalse($object->getRequestSuccess());
    }
}
