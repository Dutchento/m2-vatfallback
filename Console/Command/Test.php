<?php
/**
 * Dutchento Vatfallback
 * Provides free VAT fallback mechanism
 * Copyright (C) 2018 Dutchento
 *
 * MIT license applies to this software
 */

namespace Dutchento\Vatfallback\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Test extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $country = $input->getArgument('country');
        $number = $input->getArgument('number');

        $output->writeln("{$country} {$number}");
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('vatfallback:test');
        $this->setDescription('Test VAT fallback with configured flow');
        $this->setDefinition([
            new InputArgument('country', InputArgument::REQUIRED, 'country'),
            new InputArgument('number', InputArgument::REQUIRED, 'number')
        ]);
        
        parent::configure();
    }
}
