<?php

namespace App\Command;

use Exception;
use App\Utils\InitialDatas;
use Composer\Console\Application;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Runtime\Internal\ComposerPlugin;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class InstallCommand extends Command
{
    protected static $defaultName = 'app:install';
    protected static $defaultDescription = 'A command to install the application on a server.';

    private $baseUrl;
    private $mailerUrl;
    private $mainUrl;
    private $mailFrom;
    private $mailAdmin;
    private $initialDatas;
    private $appEnv;

    public function __construct(
        $baseUrl,
        $mailerUrl,
        $mainUrl,
        $mailFrom,
        $mailAdmin,
        $appEnv,
        InitialDatas $initialDatas
        )
    {
        $this->baseUrl = $baseUrl;
        $this->mailerUrl = $mailerUrl;
        $this->mainUrl = $mainUrl;
        $this->mailFrom = $mailFrom;
        $this->mailAdmin = $mailAdmin;
        $this->initialDatas = $initialDatas;
        $this->appEnv = $appEnv;

        parent::__construct();
    }

    protected function configure(): void
    {
        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;

        $this
            ->addOption('reset-db', 'r', InputOption::VALUE_NONE, 'Reset the database to initials values');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('reset-db')) {
            
            $fileSystem = new Filesystem;

            if ($fileSystem->exists('.env.local')) {

                if($this->resetDatabase($io, $output)) {
                    $io->text(PHP_EOL);
                    $io->success('Base de données réiniitalisée');
                    return Command::SUCCESS;
                } else {
                    $io->error('Une erreur est survenue pendant la réinitialisation.');
                    return Command::FAILURE;
                }

            } else {
                $io->warning('Les variables d\'environnement ne sont pas configurées.');
                $io->note('Installation de l\'application. Veuillez suivre les instructions.');
            }
        }

        $envLocal = [];
        
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

            $dbPassword = $io->askHidden('Entrez le mot de passe de la base de données');
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

        $envLocal['DATABASE_URL'] = $databaseUrl;
        $io->block('Base de données configurée', 'ok', 'info');

        //==========================
        // URLs configuration
        //==========================

        $io->title('Configuration des URLs');

        $urlMain = $io->ask('Entrez l\'URL du front office', $this->mainUrl);
        if ($this->isEmpty($urlMain, $io)) {
            return Command::FAILURE;
        }

        $urlBase = $io->ask('Entrez l\'URL du back office', $this->baseUrl);
        if ($this->isEmpty($urlBase, $io)) {
            return Command::FAILURE;
        }

        $urlMailer = $io->ask('Entre l\'URL du mailer', $this->mailerUrl);
        if ($this->isEmpty($urlMailer, $io)) {
            return Command::FAILURE;
        }

        $envLocal['MAIN_URL'] = $urlMain === $this->mainUrl ? null : $urlMain;
        $envLocal['BASE_URL'] = $urlBase === $this->baseUrl ? null : $urlBase;
        $envLocal['MAILER_URL'] = $urlMailer === $this->mailerUrl ? null : $urlMailer;
        $io->block('URLs configurées', 'ok', 'info');

        //==========================
        // Cores configuration
        //==========================

        $corsAllowOrigin = str_replace(['http', 'www.'], ['^https?', '(www.)?'], $urlMain);

        $envLocal['CORS_ALLOW_ORIGIN'] = $corsAllowOrigin;
        
        //==========================
        // Mailer configuration
        //==========================

        $io->title('Configuration du mailer');
        $io->section('Configuration du protocole SMTP');

        $smtpServer = $io->ask('Entrez l\'adresse du serveur SMTP (exemple : "smtp.example.com")');
        if ($this->isEmpty($smtpServer, $io)) {
            return Command::FAILURE;
        }

        $smtpPort = $io->ask('Entrez le port du serveur SMTP (optionnel)');
        $smtpPort = filter_var($smtpPort, FILTER_VALIDATE_INT);
        $smtpPort = $smtpPort === false ? null : $smtpPort;

        $smtpUser = urlencode($io->ask('Entrez le nom d\'utilisateur du serveur SMTP (optionnel)'));
        if(empty($smtpUser)) { $smtpUser = null; }
        
        $smtpPass = urlencode($io->askHidden('Entrez le mot de passe du serveur SMTP (optionnel)'));
        if(empty($smtpPass)) { $smtpPass = null; }

        $dsnMailer = 'smtp://';
        
        if ($smtpUser !== null && $smtpPass !== null) {
            $dsnMailer .= $smtpUser . ':' . $smtpPass;
        }
        
        $dsnMailer .= '@' . $smtpServer;

        if ($smtpPort !== null) {
            $dsnMailer .= ':' . $smtpPort;
        }

        $envLocal['MAILER_DSN'] = $dsnMailer;

        $io->section('Configuration des adresses mails');

        $mailFrom = $io->ask('Entrez l\'adresse email d\'envoi (FROM)', $this->mailFrom);
        if ($this->isEmpty($mailFrom, $io)) {
            return Command::FAILURE;
        }
        
        $mailFrom = filter_var($mailFrom, FILTER_VALIDATE_EMAIL);
        if ($mailFrom === false) {
            $io->error('Cette adresse n\'a pas une forme valide');
            return Command::FAILURE;
        }

        $mailAdmin = $io->ask('Entrez l\'adresse email d\'administration (laissez vide si idem que l\'adresse d\'envoi)');
        if (empty($mailAdmin)) {
            $mailAdmin = $mailFrom;
        } else {
            $mailAdmin = filter_var($mailAdmin, FILTER_VALIDATE_EMAIL);
            if ($mailAdmin === false) {
                $io->error('Cette adresse n\'a pas une forme valide');
                return Command::FAILURE;
            }
        }

        $envLocal['MAILER_FROM'] = $mailFrom === $this->mailFrom ? null : $mailFrom;
        $envLocal['MAILER_ADMIN'] = $mailAdmin === $this->mailAdmin ? null : $mailAdmin;
        $io->block('Mailer configuré', 'ok', 'info');

        //==========================
        // Stripe configuration
        //==========================

        $io->title('Configuration de stripe');

        $stripeSecretKey = $io->askHidden('Entrez votre clé privée Stripe');
        if ($this->isEmpty($stripeSecretKey, $io)) {
            return Command::FAILURE;
        }

        $envLocal['STRIPE_SECRET_KEY'] = $stripeSecretKey;
        $io->block('Stripe configuré', 'ok', 'info');

        //==========================
        // Secret configuration
        //==========================

        $characters = '0123456789abcdef';
        $appSecret = '';
        for ($i = 0; $i < 32; $i++) {
            $appSecret .= $characters[rand(0, strlen($characters) - 1)];
        }

        $envLocal['APP_SECRET'] = $appSecret;

        //==========================
        // Prod configuration
        //==========================

        $envLocal['APP_ENV'] = 'prod';

        //==========================
        // App installation
        //==========================

        $installationIteration = [
            'envLocalCreation' => [
                'parameters' => ['envLocal' => $envLocal, 'io' => $io],
                'progressBarMessage' => 'Variables d\'environnement créées (.env.local)'
            ],
            'composerUpdate' => [
                'parameters' => ['io' => $io, 'progressBarComposer' => new ProgressBar($output)],
                'progressBarMessage' => 'Bundles installés'

            ],
            'databaseCreate' => [
                'parameters' => ['io' => $io, 'progressBarDatabase' => new ProgressBar($output, 10)],
                'progressBarMessage' => 'Base de données créée'
            ],
            'clearCache' => [
                'parameters' => ['io' => $io],
                'progressBarMessage' => 'Cache effacé'
            ]
        ];
        
        $progressBar = new ProgressBar($output, count($installationIteration));
        $progressBar->setFormatDefinition('custom', '<fg=black;bg=green>%message%</>' . PHP_EOL . '%current%/%max% [%bar%]' . PHP_EOL);
        $progressBar->setFormat('custom');
        $progressBar->setEmptyBarCharacter('░');
        $progressBar->setProgressCharacter('');
        $progressBar->setBarCharacter('▓');
        $progressBar->setMessage('Installation...', 'message');

        foreach ($installationIteration as $function => $parameters) {

            if ($this->$function($parameters['parameters']) === true) {
                $progressBar->setMessage($parameters['progressBarMessage'], 'message');
                $progressBar->advance();
                usleep(1000);
            }
        }

        $progressBar->finish();
        $io->success('Backoffice installé et prêt à être utilisé !');

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

    private function envLocalCreation(array $parameters)
    {
        $envLocal = $parameters['envLocal'];
        $io = $parameters['io'];

        $fileSystem = new Filesystem;

        if ($fileSystem->exists('.env.local')) {
            try {
                $fileSystem->rename('.env.local', 'backup.env.local' . date("dmYHis"));
            } catch (Exception $error) {
                $io->error('Une erreur s\'est produite lors du backup du fichier .env.local : ' . $error->getMessage() . ' (' . $error->getCode() . ')');
                return Command::FAILURE;
            }
        }

        $content = '';
        foreach ($envLocal as $key => $value) {
            if ($value !== null) {
                $content .= $key . '=' . $value . PHP_EOL;
            }
        }

        try {
            $fileSystem->dumpFile('.env.local', $content);

            sleep(2);
            return true;

        } catch (IOExceptionInterface $exception) {
            $io->error('Une erreur s\'est produite lors de la création du fichier dans : ' . $exception->getPath());
            return Command::FAILURE;
        }

    }

    private function composerUpdate(array $parameters)
    { 
        $io = $parameters['io'];
        $progressBarComposer = $parameters['progressBarComposer'];

        $progressBarComposer->setFormatDefinition('minimal_nomax', '<fg=green>%message%</>' . PHP_EOL .  '[%bar%]');
        $progressBarComposer->setFormat('minimal');
        $progressBarComposer->setEmptyBarCharacter('<fg=red>⚬</>');
        $progressBarComposer->setProgressCharacter('<fg=green>➤</>');
        $progressBarComposer->setBarCharacter('<fg=green>⚬</>');
        $progressBarComposer->setMessage('Installation des bundles...', 'message');

        $composer = new Process(['composer', 'update']);
        $composer->start();

        $progressBarComposer->start();
        
        foreach ($composer as $type => $data) {
            $progressBarComposer->advance(1);
            // if ($composer::OUT === $type) {

            //     $progressBarComposer->advance(1);
            // } else { // $composer::ERR === $type
            //     $progressBarComposer->advance(1);
            // }
        }     

        $progressBarComposer->finish();

        return true;
    }

    private function databaseCreate(array $parameters)
    {
        $io = $parameters['io'];
        $progressBarDatabase = $parameters['progressBarDatabase'];

        $progressBarDatabase->setFormatDefinition('minimal', '<fg=green>%message%</>' . PHP_EOL .  '[%bar%]');
        $progressBarDatabase->setFormat('minimal');
        $progressBarDatabase->setEmptyBarCharacter('<fg=red>⚬</>');
        $progressBarDatabase->setProgressCharacter('<fg=green>➤</>');
        $progressBarDatabase->setBarCharacter('<fg=green>⚬</>');

        $doctrine = new Process(['bin/console', 'do:mi:mi', '--no-interaction']);
        $doctrine->start();
        
        $progressBarDatabase->start(1);

        while ($doctrine->isRunning()) {
            $progressBarDatabase->setMessage('Création de la structure de la base de données', 'message');
        }

        $progressBarDatabase->advance(1);
        $progressBarDatabase->setMessage('Création des données', 'message');

        $this->initialDatas->createCarts();
        $progressBarDatabase->advance(1);

        $this->initialDatas->createCategories();
        $progressBarDatabase->advance(1);

        $this->initialDatas->createProducts();
        $progressBarDatabase->advance(1);

        $this->initialDatas->createCartProducts();
        $progressBarDatabase->advance(1);

        $this->initialDatas->createDepots();
        $progressBarDatabase->advance(1);

        $this->initialDatas->createSalesStatus();
        $progressBarDatabase->advance(1);

        $this->initialDatas->createUsers(20);
        $progressBarDatabase->advance(1);

        $this->initialDatas->createOrders(50);
        $progressBarDatabase->advance(1);

        $progressBarDatabase->finish();

        return true;
    }

    private function clearCache(array $parameters)
    {
        $io = $parameters['io'];


        $dumpEnv = new Process( (['composer', 'dump-env', $this->appEnv]));
        $dumpEnv->start();
        $dumpEnv->wait();

        $appDebug = $this->appEnv === 'prod' ? '0' : '1';
        $clearCache = new Process(['APP_ENV=' . $this->appEnv, 'APP_DEBUG=' . $appDebug, 'php', 'bin/console', 'cache:clear']);
        $clearCache->start();
        $clearCache->wait();
        return true;
    }

    private function resetDatabase($io, $output)
    {
        $doctrine = new Process(['bin/console' , 'doctrine:schema:drop', '--full-database', '--force', '--no-interaction']);
        $doctrine->start();
        $doctrine->wait();

        if($this->databaseCreate(['io' => $io, 'progressBarDatabase' => new ProgressBar($output, 10)])) {
            return true;
        } else {
            $io->error('An error occur during database reset');
            return Command::FAILURE;
        }
    }
}
