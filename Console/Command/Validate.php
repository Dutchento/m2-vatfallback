<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Console\Command;

use Dutchento\Vatfallback\Service\Exceptions\NoValidationException;
use Dutchento\Vatfallback\Service\ValidateVatInterface;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Validate
 * @package Dutchento\Vatfallback\Console\Command
 */
class Validate extends Command
{
    /**
     * @var ValidateVatInterface
     */
    private $validationService;

    /**
     * @param ValidateVatInterface $validationService
     */
    public function __construct(
        ValidateVatInterface $validationService
    ) {
        $this->validationService = $validationService;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $country = $input->getArgument('country');
        $number = $input->getArgument('number');

        try {
            $result = $this->validationService->byNumberAndCountry($number, $country);

            if ($result['result']) {
                $output->writeln("Success is: {$result['result']}, with service {$result['service']}");
                return Cli::RETURN_SUCCESS;
            } else {
                $output->writeln("Invalid VAT number, with service {$result['service']}");
                return Cli::RETURN_FAILURE;
            }
        } catch (NoValidationException $exception) {
            $output->writeln("No validation took place");
            return Cli::RETURN_FAILURE;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('vat:validate');
        $this->setDescription('Test VAT fallback with configured flow');
        $this->setDefinition([
            new InputArgument('country', InputArgument::REQUIRED, 'country'),
            new InputArgument('number', InputArgument::REQUIRED, 'number')
        ]);

        parent::configure();
    }
}
