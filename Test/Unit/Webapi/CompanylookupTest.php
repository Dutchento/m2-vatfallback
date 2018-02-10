<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Test\Unit\Webapi;

use Magento\TestFramework\TestCase\WebapiAbstract;

class CompanylookupTest extends WebapiAbstract
{
    public function testFailingLookup()
    {
        $vatNumber = 'FOOBAR';

        $response = $this->_webApiCall([
            'rest' => [
                'resourcePath' => '/V1/vat/companylookup/' . $vatNumber,
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => 'testModule1AllSoapAndRestV1',
                'operation' =>'testModule1AllSoapAndRestV1Item',
            ],
        ],[
            'vatNumber' => $vatNumber
        ]);

        $this->assertEquals(false, $item['status']);
   }
}