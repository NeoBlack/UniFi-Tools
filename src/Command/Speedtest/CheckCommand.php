<?php

namespace App\Command\Speedtest;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UniFi_API\Client;

#[AsCommand(
    name: 'speedtest:check',
    description: 'Check results',
)]
class CheckCommand extends Command
{
    public function __construct(private Client $client)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', 'f', InputOption::VALUE_OPTIONAL, 'Start time', '-2 days')
            ->addOption('to', 't', InputOption::VALUE_OPTIONAL, 'End time', 'now')
            ->addOption('minDownload', 'md', InputOption::VALUE_OPTIONAL, 'Minimum Download Rate (MBit/s)')
            ->addOption('minUpload', 'mu', InputOption::VALUE_OPTIONAL, 'Minimum Upload Rate (MBit/s)')
            ->addOption('maxLatency', 'ml', InputOption::VALUE_OPTIONAL, 'Maximum Latency Seconds (ms)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Speedtest Check');

        $minDownload = $input->getOption('minDownload');
        $minUpload = $input->getOption('minUpload');
        $maxLatency = $input->getOption('maxLatency');

        $commandResult = Command::SUCCESS;

        if (null !== $minDownload || null !== $minUpload || null !== $maxLatency) {
            $this->client->login();
            $start = (new \DateTime($input->getOption('from')))->getTimestamp() * 1000;
            $end = (new \DateTime($input->getOption('to')))->getTimestamp() * 1000;
            $stats = $this->client->stat_speedtest_results($start, $end);
            $lastResult = array_pop($stats);

            if (null !== $minDownload) {
                $io->section('Check Minimum Download Rate');
                if ($lastResult->xput_download <= $minDownload) {
                    $io->error(sprintf('Download Rate FAIL: %s', $lastResult->xput_download));
                    $commandResult = Command::FAILURE;
                } else {
                    $io->success(sprintf('Download Rate OK: %s', $lastResult->xput_download));
                }
            }
            if (null !== $minUpload) {
                $io->section('Check Minimum Upload Rate');
                if ($lastResult->xput_upload <= $minUpload) {
                    $io->error(sprintf('Upload Rate FAIL: %s', $lastResult->xput_upload));
                    $commandResult = Command::FAILURE;
                } else {
                    $io->success(sprintf('Upload Rate OK: %s', $lastResult->xput_upload));
                }
            }
            if (null !== $maxLatency) {
                $io->section('Check Minimum Upload Rate');
                if ($lastResult->latency >= $maxLatency) {
                    $io->error(sprintf('Latency FAIL: %s', $lastResult->latency));
                    $commandResult = Command::FAILURE;
                } else {
                    $io->success(sprintf('Latency OK: %s', $lastResult->latency));
                }
            }
        } else {
            $io->error('nothing to, you have to use at least on option: minDownload, minUpload or mayLatency');
            $commandResult = Command::FAILURE;
        }

        return $commandResult;
    }
}
