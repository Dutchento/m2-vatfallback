<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2023 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service;

use Dutchento\Vatfallback\Model\Cache\VatCheck;
use Dutchento\Vatfallback\Service\Exceptions\ValidationDisabledException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationFailedException;
use Dutchento\Vatfallback\Service\Exceptions\ValidationIgnoredException;
use Exception;
use Magento\Framework\App\CacheInterface as MagentoCacheInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Cache implements CacheInterface
{
    /** @var ConfigurationInterface */
    protected $configuration;

    /** @var MagentoCacheInterface */
    protected $cache;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var array<string> */
    protected $usedValidationServiceNames = [];

    /**
     * Cache constructor.
     * @param ConfigurationInterface $configuration
     * @param MagentoCacheInterface  $cache
     * @param SerializerInterface    $serializer
     */
    public function __construct(
        ConfigurationInterface $configuration,
        MagentoCacheInterface  $cache,
        SerializerInterface    $serializer
    ) {
        $this->configuration = $configuration;
        $this->cache         = $cache;
        $this->serializer    = $serializer;
    }

    public function load(string $vatNumber, string $countryIso2): bool
    {
        if (!$this->configuration->isCacheValidation()) {
            throw new ValidationDisabledException('Cache is disabled');
        }

        $cacheId = $this->getCacheId($vatNumber, $countryIso2);
        $data    = $this->cache->load($cacheId);
        if ($data === false) {
            throw new ValidationIgnoredException('Cache could not find result');
        }

        try {
            $data = $this->serializer->unserialize($data);
        } catch (Exception $e) {
            throw new ValidationFailedException("Cache error occured unserializing results for '{$vatNumber}'");
        }

        if (!isset($data['result'], $data['service'])) {
            throw new ValidationFailedException("Cache error occured invalid cache results for '{$vatNumber}'");
        }

        $this->usedValidationServiceNames[$cacheId] = $data['service'];

        return boolval($data['result']);
    }

    protected function getCacheId(string $vatNumber, string $countryIso2): string
    {
        return VatCheck::TYPE_IDENTIFIER . '_' . strtolower($vatNumber . '_' . $countryIso2);
    }

    public function save(string $vatNumber, string $countryIso2, bool $result, string $validationName): bool
    {
        if (!$this->configuration->isCacheValidation()) {
            return false;
        }
        return $this->cache->save(
            $this->serializer->serialize([
                                             'result'  => $result,
                                             'service' => $validationName
                                         ]),
            $this->getCacheId($vatNumber, $countryIso2),
            [VatCheck::CACHE_TAG],
            86400
        );
    }

    public function getValidationServiceName(): string
    {
        return 'Cache';
    }

    public function getUsedValidationServiceName(string $vatNumber, string $countryIso2): string
    {
        $cacheId = $this->getCacheId($vatNumber, $countryIso2);
        return isset($this->usedValidationServiceNames[$cacheId]) ? $this->usedValidationServiceNames[$cacheId] : '';
    }
}
