<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends Command
{
    protected static $defaultName = 'app:install';
    protected static $defaultDescription = 'A command to installe the application on a server.';

    protected function configure(): void
    {
        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        //==========================
        // Database configuration
        //==========================

        $io->title('Configuration de la base de données');

        $dbType = $io->choice(
            'Selectionnez le type de base de données ',
            ['mySQL/mariaDB', 'sqLite', 'postgreSQL', 'Oracle'],
            0
        );

        if ($dbType === 'mySQL/mariaDB' || $dbType === 'postgreSQL') {
            $dbVersion = $io->ask('Entrez la version de votre base de données');
            
            if ($this->isEmpty($dbVersion, $io)) {
                return Command::FAILURE;
            }
            
            $dbVersion = str_replace(' ', '', $dbVersion);
        } else {
            $dbVersion = null;
        }
        
        switch ($dbType) {
            case 'mySQL/mariaDB':
                $dbType = 'mysql';
                break;
            case 'sqLite':
                $dbType = 'sqlite';
                break;
            case 'postgreSQL':
                $dbType = 'postgresql';
                break;
            case 'Oracle':
                $dbType = 'oci8';
                break;
        }

        if ($dbType === 'sqlite') {
            $databaseUrl = 'sqlite:///%kernel.project_dir%/var/app.db';
        } else {

            $dbName = $io->ask('Entrez le nom de la base de données');
            if ($this->isEmpty($dbName, $io)) {
                return Command::FAILURE;
            }
            $dbName = str_replace(' ', '', $dbName);
            $dbName = urlencode($dbName);

            $dbUser = $io->ask('Entrez le nom d\'utilisateur de la base de données');
            if ($this->isEmpty($dbUser, $io)) {
                return Command::FAILURE;
            }
            $dbUser = urlencode($dbUser);

            $dbPassword = $io->ask('Entrez le mot de passe de la base de données');
            if ($this->isEmpty($dbPassword, $io)) {
                return Command::FAILURE;
            }
            $dbPassword = urlencode($dbPassword);

            $dbHost = $io->ask('Entrez l\'adresse du serveur et le port de la base de données', '127.0.0.1:3306');
            if ($this->isEmpty($dbHost, $io)) {
                return Command::FAILURE;
            }

            $databaseUrl = $dbType . '://' . $dbUser . ':' . $dbPassword . '@' . $dbHost . '/' . $dbName . '?serverVersion=' . $dbVersion . '&charset=utf8mb4';

        }

        $io->block('Base de données configurée', 'ok', 'info');

        //==========================
        // URLs configuration
        //==========================

        // TODO

        // $output->writeln('Selected : ' . $DBType);
        // $io = new SymfonyStyle($input, $output);
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    private function isEmpty(?string $string, $io)
    {
        if ($string === null || trim($string) == '') {
            $io->error('Cette valeur est obligatoire');
            return true;
        }

        return false;
    }
}
