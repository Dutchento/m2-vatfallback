<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Service\Validate;

use Dutchento\Vatfallback\Service\Exceptions\ValidationFailedException;

/**
 * Class FailedValidationException
 * @package Dutchento\Vatfallback\Service\Validate
 * @deprecated in favor of \Dutchento\Vatfallback\Service\Exceptions\*
 */
class FailedValidationException extends ValidationFailedException
{
}
