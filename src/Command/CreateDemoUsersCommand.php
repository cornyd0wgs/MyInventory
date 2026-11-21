<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-demo-users', description: 'Create or reset the demo owner and employee accounts')]
final class CreateDemoUsersCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repository = $this->entityManager->getRepository(User::class);

        $accounts = [
            ['owner', 'owner123', 'ROLE_OWNER', 'Store Owner'],
            ['employee', 'employee123', 'ROLE_EMPLOYEE', 'Store Employee'],
        ];

        foreach ($accounts as [$username, $plainPassword, $role, $fullName]) {
            $user = $repository->findOneBy(['username' => $username]) ?? new User();
            $user
                ->setUsername($username)
                ->setRoles([$role])
                ->setFullName($fullName)
                ->setIsActive(true)
                ->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
            $this->entityManager->persist($user);
            $io->success(sprintf('%s / %s (%s)', $username, $plainPassword, $role));
        }

        $this->entityManager->flush();
        $io->note('Demo accounts are ready.');

        return Command::SUCCESS;
    }
}
