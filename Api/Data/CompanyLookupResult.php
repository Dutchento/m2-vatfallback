<?php

declare(strict_types=1);

namespace Dutchento\Vatfallback\Api\Data;

use Magento\Framework\DataObject;

class CompanyLookupResult extends DataObject implements CompanyLookupResultInterface
{
    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return (bool) $this->getData(self::STATUS);
    }

    /**
     * @param bool $status
     * @return mixed|void
     */
    public function setStatus(bool $status)
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * @param string $country
     * @return mixed|void
     */
    public function setCountry(string $country)
    {
        $this->setData(self::COUNTRY, $country);
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->getData(self::COMPANY_NAME);
    }

    /**
     * @param string $companyName
     * @return mixed|void
     */
    public function setCompanyName(string $companyName)
    {
        $this->setData(self::COMPANY_NAME, $companyName);
    }

    /**
     * @return string
     */
    public function getCompanyAddress(): string
    {
        return $this->getData(self::COMPANY_ADDRESS);
    }

    /**
     * @param string $companyAddress
     * @return mixed|void
     */
    public function setCompanyAddress(string $companyAddress)
    {
        $this->setData(self::COMPANY_ADDRESS, $companyAddress);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @param string $message
     * @return mixed|void
     */
    public function setMessage(string $message)
    {
        $this->setData(self::MESSAGE, $message);
    }
}
