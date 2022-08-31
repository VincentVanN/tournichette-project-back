<?php

namespace App\Command;

use App\Repository\CartRepository;
use App\Utils\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CartSlugifyUnsluggedCommand extends Command
{
    protected static $defaultName = 'app:cart:slugify-unslugged';
    protected static $defaultDescription = 'A command to slugify unslugged carts';

    private $cartRepo;
    private $entityManager;
    private $mySlugger;
    public function __construct(EntityManagerInterface $entityManager, CartRepository $cartRepository, MySlugger $mySlugger)
    {
        $this->cartRepo = $cartRepository;
        $this->entityManager = $entityManager;
        $this->mySlugger = $mySlugger;

        // pour initialiser correctement une Command il faut exécuter le code de 
        // Symfony\Component\Console\Command::__construct
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('cart-count', 'c', InputOption::VALUE_OPTIONAL, 'Number of carts to update ( default is 5)', 5)
            ->addOption('tolower', 'l', InputOption::VALUE_NONE, 'Force slugs to lowercase')
            ->addArgument('cart-count', InputArgument::OPTIONAL, 'Number of carts to update ( default is 5)', 5)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // . Préparation des données
        $io = new SymfonyStyle($input, $output);
      
        $numberOfcartsToUpdate = $input->getOption('cart-count');
        $toLower = $input->getOption('tolower');
        $this->mySlugger->setToLower($toLower);
        $io->note("Updating {$numberOfcartsToUpdate} carts");

        // ? récupérer les carts qui n'ont pas de slug ( le bon nombre svp)
        $cartList = $this->cartRepo->findBy(['slug' => ''], [], $numberOfcartsToUpdate);

        // . lancer le traitement
        // ? Pour chaque cart
        foreach($cartList as $currentcart)
        {
            $io->note('slugifing cart ' . $currentcart->getId());
            //   ? slugifier le nom
            $slugifiedName = $this->mySlugger->slugify($currentcart->getName());
            $currentcart->setSlug($slugifiedName);
        }
        //   ? enregistrer en BDD
        $this->entityManager->flush();

        $io->success("Yaii job done for ${numberOfcartsToUpdate} carts !");

        return Command::SUCCESS;
    }
}