<?php

namespace Dutchento\Vatfallback\Test\Unit\Plugin\Magento\Customer\Model;

use Dutchento\Vatfallback\Plugin\Magento\Customer\Model\Vat;
use PHPUnit\Framework\TestCase;

class VatGatewayObjectTest extends TestCase
{
    public function testCreatingSuccesfulGatewayObject()
    {
        $plugin = new Vat();

        $object = $plugin->createGatewayResponseObject('VATNUMBER', true, 'message');

        $this->assertEquals((new \DateTimeImmutable())->format('Y-m-d'), $object->getRequestDate());
        $this->assertEquals('VATNUMBER', $object->getRequestIdentifier());
        $this->assertEquals('message', $object->getRequestMessage());
        $this->assertTrue($object->getIsValid());
        $this->assertTrue($object->getRequestSuccess());
    }

    public function testCreatingFailedGatewayObject()
    {
        $plugin = new Vat();

        $object = $plugin->createGatewayResponseObject('VATNUMBER', false, 'message');

        $this->assertEquals((new \DateTimeImmutable())->format('Y-m-d'), $object->getRequestDate());
        $this->assertEquals('VATNUMBER', $object->getRequestIdentifier());
        $this->assertEquals('message', $object->getRequestMessage());
        $this->assertFalse($object->getIsValid());
        $this->assertFalse($object->getRequestSuccess());
    }
}