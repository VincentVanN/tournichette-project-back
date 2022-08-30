<?php

namespace App\Command;

use App\Repository\CategoryRepository;
use App\Utils\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CategorySlugifyUnsluggedCommand extends Command
{
    protected static $defaultName = 'app:category:slugify-unslugged';
    protected static $defaultDescription = 'A command to slugify unslugged categorys';

    private $categoryRepo;
    private $entityManager;
    private $mySlugger;
    public function __construct(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, MySlugger $mySlugger)
    {
        $this->categoryRepo = $categoryRepository;
        $this->entityManager = $entityManager;
        $this->mySlugger = $mySlugger;

        // pour initialiser correctement une Command il faut exécuter le code de 
        // Symfony\Component\Console\Command::__construct
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('category-count', 'c', InputOption::VALUE_OPTIONAL, 'Number of categorys to update ( default is 5)', 5)
            ->addOption('tolower', 'l', InputOption::VALUE_NONE, 'Force slugs to lowercase')
            ->addArgument('category-count', InputArgument::OPTIONAL, 'Number of categorys to update ( default is 5)', 5)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // . Préparation des données
        $io = new SymfonyStyle($input, $output);
      
        $numberOfcategorysToUpdate = $input->getOption('category-count');
        $toLower = $input->getOption('tolower');
        $this->mySlugger->setToLower($toLower);
        $io->note("Updating {$numberOfcategorysToUpdate} categorys");

        // ? récupérer les categorys qui n'ont pas de slug ( le bon nombre svp)
        $categoryList = $this->categoryRepo->findBy(['slug' => ''], [], $numberOfcategorysToUpdate);

        // . lancer le traitement
        // ? Pour chaque category
        foreach($categoryList as $currentcategory)
        {
            $io->note('slugifing category ' . $currentcategory->getId());
            //   ? slugifier le nom
            $slugifiedName = $this->mySlugger->slugify($currentcategory->getName());
            $currentcategory->setSlug($slugifiedName);
        }
        //   ? enregistrer en BDD
        $this->entityManager->flush();

        $io->success("Yaii job done for ${numberOfcategorysToUpdate} categorys !");

        return Command::SUCCESS;
    }
}
