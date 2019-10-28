<?php

declare(strict_types=1);

namespace Dutchento\Vatfallback\Api\Data;

interface CompanyLookupResultInterface
{
    const STATUS = 'status';
    const COUNTRY = 'country';
    const COMPANY_NAME = 'company_name';
    const COMPANY_ADDRESS = 'company_address';
    const MESSAGE = 'message';

    /**
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * @param bool $status
     * @return mixed
     */
    public function setStatus(bool $status);

    /**
     * @return string
     */
    public function getCountry(): string;

    /**
     * @param string $country
     * @return mixed
     */
    public function setCountry(string $country);

    /**
     * @return string
     */
    public function getCompanyName(): string;

    /**
     * @param string $companyName
     * @return mixed
     */
    public function setCompanyName(string $companyName);

    /**
     * @return string
     */
    public function getCompanyAddress(): string;

    /**
     * @param string $companyAddress
     * @return mixed
     */
    public function setCompanyAddress(string $companyAddress);

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return mixed
     */
    public function setMessage(string $message);
}
