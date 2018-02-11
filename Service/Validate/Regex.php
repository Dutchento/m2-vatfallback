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
     * @var ConfigInterface
     */
    private $vatNumberConfig;

    /**
     * @var array
     */
    private $regexMap;

    /**
     * Regex constructor.
     * @param ConfigInterface $vatNumberConfig
     */
    public function __construct(ConfigInterface $vatNumberConfig)
    {
        $this->vatNumberConfig = $vatNumberConfig;
    }

    /**
     * @inheritdoc
     */
    public function validateVATNumber(string $vatNumber, string $countryIso2): bool
    {
        $regex = $this->getRegexMapping($countryIso2);

        return (bool)preg_match($regex, $vatNumber);
    }

    /**
     * Get regex by countryIso2
     *
     * @param string $countryIso2
     * @return string
     */
    public function getRegexMapping(string $countryIso2): string
    {
        $mapping = $this->getRegexMap();

        return $mapping[strtoupper($countryIso2)] ?? '';
    }

    /**
     * @return array
     */
    public function getRegexMap(): array
    {
        if ($this->regexMap === null) {
            $this->regexMap = $this->vatNumberConfig->get();
            $this->regexMap = array_map(function ($vatNumber) {
                return '/' . trim($vatNumber['pattern'], '/') . '/';
            }, $this->regexMap);
        }

        return $this->regexMap;
    }
}
