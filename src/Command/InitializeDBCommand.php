<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\DBRefresher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InitializeDBCommand extends Command
{
    public function __construct(private readonly DBRefresher $DBRefresher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('paysera:app:refresh-db');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $style->title('Refreshing database');

        $this->DBRefresher->refresh();

        $style->success('Database refreshed');

        return self::SUCCESS;
    }
}
