<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:fake',
    description: 'Commande de test',
)]
class FakeCommand extends Command
{
    public function __construct(private readonly ParameterBagInterface $params)
    {
        parent::__construct();
    }

    /**
     * Configuration ou avec l'attribut AsCommand
     */
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description') // Ajouter un argument
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description') // Ajouter une option

            // ->setName('app:fake') -- Ou dans l'attribut
            // ->setDescription('Description de la commande')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Démarrage des opérations...'); // Pour écrire dans le terminal

        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $dbUrl = $this->params->get('env(DATABASE_URL)');
        preg_match('/mysql:\/\/(.*):(.*)@(.*):(\d+)\/(.*)/', $dbUrl, $matches); // Extraction des infos Mysql
        [$full, $user, $pass, $host, $port, $dbname] = $matches;
        $backupDir = __DIR__ . '/../../var/backups'; // Chemin de la sauvegarde
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }
        $file = $backupDir . '/backup-' . date('Y-m-d_H-i-s') . '.sql';

        $command = sprintf( // Commande mysqldump
            'mysqldump -h%s -P%s -u%s -p%s %s > %s',
            $host,
            $port,
            $user,
            $pass,
            $dbname,
            $file
        );

        system($command, $result);

        if ($result === 0) {
            $output->writeln("<info>Sauvegarde effectuée : $file</info>");
        } else {
            $output->writeln("<error>Erreur lors de la sauvegarde</error>");
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.'); // Message de success
        // sprintf('%d article(s) supprimé(s) définitivement', $count)

        return Command::SUCCESS;
    }
}
