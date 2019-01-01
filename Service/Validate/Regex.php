<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

use Dutchento\Vatfallback\Model\VatNumber\ConfigInterface;

class Regex implements ValidationServiceInterface
{
    /**
     * @var array
     */
    private $vatPatternMap = [];

    /**
     * Regex constructor.
     * @param ConfigInterface $vatNumberConfig
     */
    public function __construct(ConfigInterface $vatNumberConfig)
    {
        // get the patterns from the configuration
        $this->vatPatternMap = $vatNumberConfig->get();
    }

    /**
     * @inheritdoc
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool
    {
        // as fallback use a pattern that always validates
        $regex = $this->vatPatternMap[$countryIso2] ?? '.*' ;

        return (bool)preg_match($regex, $vatNumber);
    }
}
