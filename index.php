<?php

require 'vendor/autoload.php';

ini_set('max_execution_time', '-1');

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vlst\SitemapChecker\SitemapClient;
use Symfony\Component\Console\SingleCommandApplication;


(new SingleCommandApplication())
    ->addArgument('url', InputArgument::REQUIRED, 'URL')
    ->addArgument('output', InputArgument::OPTIONAL, 'Where to output result file')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $client = new SitemapClient();

        $client->setUrl($input->getArgument('url'));

        $client->setOnError(function (string $url, int $statusCode) use ($input, $output) {
            if ($output->isVeryVerbose()) {
                $output->writeln("<error>[{$statusCode}] {$url} </error>");
            }

            if ($input->getArgument('output')) {
                file_put_contents(
                    $input->getArgument('output'),
                    PHP_EOL . "[{$statusCode}] {$url}" . PHP_EOL,
                    FILE_APPEND
                );
            }
        });

        $client->setOnFinished(fn () => $output->writeln("<info>[OK]</info>"));

        $client->check();
    })
    ->run();



