<?php declare(strict_types=1);

namespace Badger\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class CoverageBadge extends Command
{
    const SERVER_LABEL = "server";
    protected static $defaultName = 'upload:coverage';
    private const PATH_LABEL = "path";
    private const BRANCH_LABEL = "branch";

    protected function configure()
    {
        $this
            ->setDescription('Creates json for coverage badge shields.io and uploads it to a given badger server')
            ->addArgument(
                CoverageBadge::SERVER_LABEL,
                InputArgument::REQUIRED,
                'URL for Badger Server'
            )
            ->addArgument(
                CoverageBadge::BRANCH_LABEL,
                InputArgument::REQUIRED,
                'The branch name for the coverage'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln(["Creating coverage badge", "=========================="]);
        $branchName = $input->getArgument(self::BRANCH_LABEL);
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $input->getArgument(self::SERVER_LABEL),
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);
        $output->writeln(["Reading Coverage file ..."]);
        $coverage = simplexml_load_file("./coverage/clover.xml");
        $elements = $coverage->project->metrics['elements'];
        $coveredElements = $coverage->project->metrics['coveredelements'];
        $coverage = round(($coveredElements * 100) / $elements, 2);
        $client->post("/coverage/$branchName", [
            RequestOptions::JSON => [
                "schemaVersion" => 1,
                "label" => "coverage",
                "message" => $coverage . "%",
                "color" => $this->getCoverageColor($coverage)
            ],
            "header" => [
                "secret-ket" => "yousaf"
            ]
        ]);
        $output->writeln("Done.");
        return Command::SUCCESS;
    }

    private function getCoverageColor($coverage): string
    {
        switch ($coverage) {
            case $coverage > 90:
                return "green";
            case $coverage < 90 && $coverage > 75:
                return "yellow";
            case $coverage < 75:
                return "red";
        }
    }

}