<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 9-2-18
 * Time: 18:10
 */

namespace Dutchento\Vatfallback\Service\Validate;

use Dutchento\Vatfallback\Service\Exceptions\GenericException;

/**
 * Interface ValidationServiceInterface
 * @package Dutchento\Vatfallback\Service\Validate
 */
interface ValidationServiceInterface
{

    /**
     * Which validation service is used
     *
     * @return string
     */
    public function getValidationServiceName(): string;

    /**
     * Validate a given VAT number via an external service or algorithm
     * @param string $vatNumber
     * @param string $countryIso2
     * @return bool
     * @throws GenericException
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool;
}
