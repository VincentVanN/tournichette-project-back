<?php

namespace App\Command;

use App\Repository\DepotRepository;
use App\Utils\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DepotSlugifyUnsluggedCommand extends Command
{
    protected static $defaultName = 'app:depot:slugify-unslugged';
    protected static $defaultDescription = 'A command to slugify unslugged Depots';

    private $DepotRepo;
    private $entityManager;
    private $mySlugger;
    public function __construct(EntityManagerInterface $entityManager, DepotRepository $depotRepository, MySlugger $mySlugger)
    {
        $this->depotRepo = $depotRepository;
        $this->entityManager = $entityManager;
        $this->mySlugger = $mySlugger;

        // pour initialiser correctement une Command il faut exécuter le code de 
        // Symfony\Component\Console\Command::__construct
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('depot-count', 'c', InputOption::VALUE_OPTIONAL, 'Number of depots to update ( default is 5)', 5)
            ->addOption('tolower', 'l', InputOption::VALUE_NONE, 'Force slugs to lowercase')
            ->addArgument('depot-count', InputArgument::OPTIONAL, 'Number of depots to update ( default is 5)', 5)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // . Préparation des données
        $io = new SymfonyStyle($input, $output);
      
        $numberOfdepotsToUpdate = $input->getOption('depot-count');
        $toLower = $input->getOption('tolower');
        $this->mySlugger->setToLower($toLower);
        $io->note("Updating {$numberOfdepotsToUpdate} depots");

        // ? récupérer les depots qui n'ont pas de slug ( le bon nombre svp)
        $depotList = $this->depotRepo->findBy(['slug' => ''], [], $numberOfdepotsToUpdate);

        // . lancer le traitement
        // ? Pour chaque depot
        foreach($depotList as $currentdepot)
        {
            $io->note('slugifing depot ' . $currentdepot->getId());
            //   ? slugifier le nom
            $slugifiedName = $this->mySlugger->slugify($currentdepot->getName());
            $currentdepot->setSlug($slugifiedName);
        }
        //   ? enregistrer en BDD
        $this->entityManager->flush();

        $io->success("Yaii job done for ${numberOfdepotsToUpdate} depots !");

        return Command::SUCCESS;
    }
}
