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
    ->addArgument('output', InputArgument::REQUIRED, 'Where to output result file')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $client = new SitemapClient();

        $client->setUrl($input->getArgument('url'));

        $client->setOnError(function (string $message, int $statusCode) use ($input, $output) {
            if ($output->isVeryVerbose()) {
                $output->writeln("<error>[{$statusCode}] {$message} </error>");
            }

            file_put_contents(
                $input->getArgument('output'),
                "[{$statusCode}] {$message}" . PHP_EOL,
                FILE_APPEND
            );
        });

        $client->setOnInfo(function (string $info) use ($input, $output) {
            if ($output->isVeryVerbose()) {
                $output->writeln("<info>{$info}</info>");
            }

            file_put_contents(
                $input->getArgument('output'),
                $info . PHP_EOL,
                FILE_APPEND
            );
        });

        $output->writeln('<info>Started working on...</info>');

        $client->check();
    })
    ->run();



