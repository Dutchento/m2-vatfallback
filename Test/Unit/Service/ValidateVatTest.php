<?php

namespace Dutchento\Vatfallback\Test\Unit\Service;

use Dutchento\Vatfallback\Service\CleanNumberString;
use Dutchento\Vatfallback\Service\Exceptions\GenericException;
use Dutchento\Vatfallback\Service\Exceptions\NoValidationException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationDisabledException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationFailedException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationIgnoredException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationUnavailableException;
use Dutchento\Vatfallback\Service\Validate\ValidationServiceInterface;
use Dutchento\Vatfallback\Service\ValidateVat;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ValidateVatTest extends TestCase
{

    /** @var LoggerInterface */
    private $loggerInterfaceMock;
    /** @var MockObject|CleanNumberString */
    private $cleanNumberStringMock;

    public function setUp()
    {
        $this->loggerInterfaceMock = $this->createMock(LoggerInterface::class);
        $this->cleanNumberStringMock = $this->getMockBuilder(CleanNumberString::class)
            ->disableOriginalConstructor()
            ->setMethods(['returnStrippedString'])
            ->getMock();
    }

    public function testByNumberAndCountryCallsCleanNumberStringWithoutValidators()
    {
        $cleanNumberString = $this->cleanNumberStringMock;

        $cleanNumberString->expects($this->once())
                ->method('returnStrippedString')
                ->will($this->returnArgument(0));

        $validateVat = new ValidateVat(
            $this->loggerInterfaceMock,
            $cleanNumberString
        );

        $this->expectException(NoValidationException::class);

        $validateVat->byNumberAndCountry('123465', 'xx');
    }

    public function testByNumberAndCountryExitAtFirstValidationIfInvalid()
    {
        $validatorMock = $this->createMock(ValidationServiceInterface::class);

        $validatorMock->expects($this->once())
                ->method('getValidationServiceName')
                ->willReturn('first', 'second');

        $validatorMock->expects($this->once())
                ->method('validateVATNumber')
                ->willReturn(false, false);

        $validateVat = new ValidateVat(
            $this->loggerInterfaceMock,
            $this->cleanNumberStringMock,
            [$validatorMock, $validatorMock]
        );

        $this->assertSame([
            'result' => false,
            'service' => 'first'
        ], $validateVat->byNumberAndCountry('123456', 'xx'));
    }

    public function testByNumberAndCountryFirstValidatorIfValid()
    {
        $validatorMock = $this->createMock(ValidationServiceInterface::class);

        $validatorMock->expects($this->once())
            ->method('getValidationServiceName')
            ->willReturn('first', 'second');

        $validatorMock->expects($this->once())
            ->method('validateVATNumber')
            ->with('123456', 'xx')
            ->willReturn(true, true);

        $cleanNumberString = $this->cleanNumberStringMock;

        $cleanNumberString->method('returnStrippedString')
                ->willReturnArgument(0);

        $validateVat = new ValidateVat(
            $this->loggerInterfaceMock,
            $cleanNumberString,
            [$validatorMock, $validatorMock]
        );

        $this->assertSame([
            'result' => true,
            'service' => 'first'
        ], $validateVat->byNumberAndCountry('123456', 'xx'));
    }

    public function testByNumberAndCountryExceptions()
    {
        $validationMock = $this->createMock(ValidationServiceInterface::class);

        $validationMock->expects($this->exactly(5))
            ->method('getValidationServiceName')
            ->willReturn(
                'disabled',
                'ignored',
                'unavailable',
                'failed',
                'valid'
            );

        $validationMock->expects($this->exactly(5))
            ->method('validateVATNumber')
            ->willReturn(
                $this->throwException(new ValidationDisabledException('disabled-exception')),
                $this->throwException(new ValidationIgnoredException('ignored-exception')),
                $this->throwException(new ValidationUnavailableException('unavailable-exception')),
                $this->throwException(new ValidationFailedException('failed-exception')),
                $this->throwException(new GenericException('generic-exception'))
            );

        $validateVat = new ValidateVat(
            $this->loggerInterfaceMock,
            $this->cleanNumberStringMock,
            [$validationMock, $validationMock, $validationMock, $validationMock, $validationMock]
        );

        $this->expectException(NoValidationException::class);
        $validateVat->byNumberAndCountry('123456', 'xx');
    }

    /**
     * @param $result
     * @param $validators
     * @dataProvider dataProviderForFallbackScenarios
     */
    public function testByNumberAndCountryFallback($result, $validators)
    {
        $validateVat = new ValidateVat(
            $this->loggerInterfaceMock,
            $this->cleanNumberStringMock,
            $validators
        );

        $this->assertSame($result, $validateVat->byNumberAndCountry('123456', 'xx'));
    }

    /**
     * @param $result
     * @param $validators
     * @dataProvider dataProviderForFallbackWithExceptionScenarios
     */
    public function testByNumberAndCountryFallbackWithException($result, $validators)
    {
        $validateVat = new ValidateVat(
            $this->loggerInterfaceMock,
            $this->cleanNumberStringMock,
            $validators
        );

        $this->expectException(NoValidationException::class);
        $validateVat->byNumberAndCountry('123456', 'xx');
    }

    /**
     * Data provider for remote services test and offline
     *
     * @return array
     */
    public function dataProviderForFallbackScenarios(): array
    {

        /**
         * Examples taken from https://github.com/Dutchento/m2-vatfallback/issues/20
         *
         * 1: Remote service 1 -> VIES
         * 2: Remote service 2 -> Vatlayer
         * 3: Offline -> RegExp
         *
         * ? = unknown | - unavailable | * disabled | # invalid
         *
         * - R: [false, vies]: 1: false
         * - R: [true, vies]: 1: true
         *
         * - R: [false, vatlayer]: 1: ?-* | 2: false
         * - R: [true, vatlayer]: 1: ?-* | 2: true
         *
         * - R: [false, regexp]: 1: ?-* | 2: ?-* | 3: false
         * - R: [true, regexp]: 1: ?-* | 2: ?-* | 3: true
         *
         * - R: [false, none]: 1: ?-* | 2: ?-* | 3: ?*
         *
         * - R: [false, vatlayer]: 1: ? | 2: # | 3: true
         *
         *
         */
        return [
            'R: [false, vies]: 1: false' => [
                $this->createValidatorResult(false, 'vies'),
                $this->createValidatorMock([
                    'vies' => false,
                    'vatlayer' => false,
                    'regexp' => false
                ])
            ],
            'R: [true, vies]: 1: true' => [
                $this->createValidatorResult(true, 'vies'),
                $this->createValidatorMock([
                    'vies' => true,
                    'vatlayer' => false,
                    'regexp' => false
                ])
            ],
            'R: [false, vatlayer]: 1: *, 2: true' => [
                $this->createValidatorResult(true, 'vatlayer'),
                $this->createValidatorMock([
                    'vies' => $this->throwException(new ValidationDisabledException),
                    'vatlayer' => true,
                    'regexp' => false
                ])
            ],
            'R: [false, regexp]: 1: *, 2: -, 3: false' => [
                $this->createValidatorResult(false, 'regexp'),
                $this->createValidatorMock([
                    'vies' => $this->throwException(new ValidationDisabledException),
                    'vatlayer' => $this->throwException(new ValidationIgnoredException),
                    'regexp' => false
                ])
            ],
        ];
    }

    /**
     * Data provider for remote services test and offline where an exception is thrown
     *
     * @return array
     */
    public function dataProviderForFallbackWithExceptionScenarios(): array
    {
        return [
            'R: [false, none]: 1: *, 2: -, 3: ?' => [
                $this->createValidatorResult(false, 'None'),
                $this->createValidatorMock([
                    'vies' => $this->throwException(new ValidationDisabledException),
                    'vatlayer' => $this->throwException(new ValidationIgnoredException),
                    'regexp' => $this->throwException(new ValidationUnavailableException)
                ])
            ],
            'R: [false, vatlayer]: 1: *, 2: #, 3: ?' => [
                $this->createValidatorResult(false, 'vatlayer'),
                $this->createValidatorMock([
                    'vies' => $this->throwException(new ValidationDisabledException),
                    'vatlayer' => $this->throwException(new ValidationFailedException),
                    'regexp' => $this->throwException(new ValidationUnavailableException)
                ])
            ],
        ];
    }

    public function createValidatorResult($result, $service)
    {
        return [
            'result' => $result,
            'service' => $service
        ];
    }

    public function createValidatorMock(array $services)
    {
        $validationMock = $this->createMock(ValidationServiceInterface::class);

        $numServices = count($services);

        $validationMock->method('getValidationServiceName')
                ->willReturn(...array_keys($services));
        $validationMock->method('validateVATNumber')
                ->willReturn(...array_values($services));

        return array_fill(0, $numServices, $validationMock);
    }



}
