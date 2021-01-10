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
    const SECRET_KEY_LABEL = "secret-key";
    protected static $defaultName = 'upload:coverage';
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
            )->addArgument(
                CoverageBadge::SECRET_KEY_LABEL,
                InputArgument::REQUIRED,
                'Secret api key for badger server'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln(["Creating coverage badge", "=========================="]);
        $branch = explode("/", $input->getArgument(self::BRANCH_LABEL));
        $branchName = end($branch);
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
            "headers" => [
                "secret-key" => $input->getArgument(self::SECRET_KEY_LABEL)
            ]
        ]);
        $output->writeln("Done.");
        return Command::SUCCESS;
    }

    private function getCoverageColor($coverage): string
    {
        switch ($coverage) {
            case $coverage > 90:
                return "success";
            case $coverage < 90 && $coverage > 75:
                return "orange";
            case $coverage < 75:
                return "red";
        }
    }

}