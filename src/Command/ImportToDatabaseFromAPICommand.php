<?php

namespace App\Command;

use App\Api\ApiConnectionHandler;
use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportToDatabaseFromAPICommand extends Command
{
    protected static $defaultName = 'api:import';
    public $em;

    public function __construct(EntityManagerInterface $em, $name = null)
    {
        parent::__construct($name);
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import all data from the Rick & Morty API locally')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Choose: character, location, episode, all')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $entity = $input->getArgument('entity');

        if ($entity) {
            $apiConnection = new ApiConnectionHandler($entity, array(), $this->em, true);
            $apiConnection->handleData();
        }

        if ($input->getOption('option1')) {
            // ...
        }
    }
}
