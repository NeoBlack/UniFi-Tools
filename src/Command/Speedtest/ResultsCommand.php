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
    name: 'speedtest:results',
    description: 'Check results',
)]
class ResultsCommand extends Command
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Speedtest Results');

        $this->client->login();
        $start = (new \DateTime($input->getOption('from')))->getTimestamp() * 1000;
        $end = (new \DateTime($input->getOption('to')))->getTimestamp() * 1000;
        $stats = $this->client->stat_speedtest_results($start, $end);
        $data = [];
        foreach ($stats as $stat) {
            $data[] = [
                (new \DateTime('@'.$stat->time/1000))->format('Y-m-d H:i'),
                $stat->xput_download,
                $stat->xput_upload,
                $stat->latency,
            ];
        }
        $io->table(['Date', "Download", 'Upload', 'Latency'], $data);

        return Command::SUCCESS;
    }
}
