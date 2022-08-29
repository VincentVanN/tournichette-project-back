<?php

namespace App\Command;

use App\Repository\ProductRepository;
use App\Utils\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProductSlugifyUnsluggedCommand extends Command
{
    protected static $defaultName = 'app:product:slugify-unslugged';
    protected static $defaultDescription = 'A command to slugify unslugged products';

    private $productRepo;
    private $entityManager;
    private $mySlugger;
    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository, MySlugger $mySlugger)
    {
        $this->productRepo = $productRepository;
        $this->entityManager = $entityManager;
        $this->mySlugger = $mySlugger;

        // pour initialiser correctement une Command il faut exécuter le code de 
        // Symfony\Component\Console\Command::__construct
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('product-count', 'c', InputOption::VALUE_OPTIONAL, 'Number of products to update ( default is 5)', 5)
            ->addOption('tolower', 'l', InputOption::VALUE_NONE, 'Force slugs to lowercase')
            ->addArgument('product-count', InputArgument::OPTIONAL, 'Number of products to update ( default is 5)', 5)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // . Préparation des données
        $io = new SymfonyStyle($input, $output);
      
        $numberOfproductsToUpdate = $input->getOption('product-count');
        $toLower = $input->getOption('tolower');
        $this->mySlugger->setToLower($toLower);
        $io->note("Updating {$numberOfproductsToUpdate} products");

        // ? récupérer les products qui n'ont pas de slug ( le bon nombre svp)
        $productList = $this->productRepo->findBy(['slug' => ''], [], $numberOfproductsToUpdate);

        // . lancer le traitement
        // ? Pour chaque product
        foreach($productList as $currentproduct)
        {
            $io->note('slugifing product ' . $currentproduct->getId());
            //   ? slugifier le nom
            $slugifiedName = $this->mySlugger->slugify($currentproduct->getName());
            $currentproduct->setSlug($slugifiedName);
        }
        //   ? enregistrer en BDD
        $this->entityManager->flush();

        $io->success("Yaii job done for ${numberOfproductsToUpdate} products !");

        return Command::SUCCESS;
    }
}
