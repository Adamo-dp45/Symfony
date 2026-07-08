<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:admin',
    description: 'Commande permettant de créer un administrateur',
)]
class AdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
            ->setUsername('admin')
            ->setIsVerified(true)
            ->setEmail('admin@gmail.com')
            ->setPhone('0544800412')
            ->setPassword($this->hasher->hashPassword($user, 'admin'));
        $this->em->persist($user);
        $this->em->flush();

        $io->success('Administrateur crée avec succès');
        return Command::SUCCESS;
    }
}
