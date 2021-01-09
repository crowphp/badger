<?php declare(strict_types=1);

namespace Badger\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CoverageBadge extends Command
{
    protected static $defaultName = 'badger:coverage';
    private const PATH_LABEL = "path";
    private const BRANCH_LABEL = "branch";
    private string $coverageFolder = "coverage";

    protected function configure()
    {
        $this
            ->setDescription('Creates json for coverage badge shields.io')
            ->addArgument(
                CoverageBadge::PATH_LABEL,
                InputArgument::REQUIRED,
                'The path to the directory for crowPHP repo'
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
        $crowPHPPath = $input->getArgument(self::PATH_LABEL) . '/coverage/clover.xml';
        $branchName = $input->getArgument(self::BRANCH_LABEL);
        $branchCoverageFile = __DIR__ . "/../../coverage/" . $branchName . ".json";
        $output->writeln(["Reading file " . $crowPHPPath]);
        $coverage = simplexml_load_file($crowPHPPath);
        $elements = $coverage->project->metrics['elements'];
        $coveredElements = $coverage->project->metrics['coveredelements'];
        $coverage = round(("300" * 100) / $elements, 2);
        file_put_contents($branchCoverageFile, json_encode([
            "schemaVersion" => 1,
            "label" => "coverage",
            "message" => $coverage . "%",
            "color" => $this->getCoverageColor($coverage)
        ]));
        $output->writeln($branchCoverageFile);
        $output->writeln(shell_exec('
        #!/bin/bash
        git add .
        git commit -asm "Updating coverage file for' . $branchName . '";
        git push origin master;
        '));
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