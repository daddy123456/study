<?php

declare(strict_types=1);

namespace Src\Command;


use Src\HttpServer\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    protected function configure()
    {
        $this->setName('start')->setDescription('start server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start server');
        (new Server())->run();
    }
}
