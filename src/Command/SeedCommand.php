<?php

namespace App\Command;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed',
    description: 'Seed demo users and messages for local development.',
)]
class SeedCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $admin = $this->upsertUser(
            'admin@messenger.local',
            'Admin User',
            'Symfony, Security, Management',
            ['ROLE_ADMIN'],
            false,
            'Admin123!'
        );

        $alice = $this->upsertUser(
            'alice@messenger.local',
            'Alice Jansen',
            'PHP, Symfony, SQL',
            ['ROLE_USER'],
            false,
            'User123!'
        );

        $bob = $this->upsertUser(
            'bob@messenger.local',
            'Bob de Vries',
            'JavaScript, API, Testing',
            ['ROLE_USER'],
            false,
            'User123!'
        );

        $blocked = $this->upsertUser(
            'blocked@messenger.local',
            'Blocked Gebruiker',
            'Legacy systems',
            ['ROLE_USER'],
            true,
            'User123!'
        );

        $this->upsertMessage($alice, $bob, 'Hoi Bob, wil je straks even naar de nieuwe Symfony setup kijken?');
        $this->upsertMessage($bob, $alice, 'Yes, ik pak het na de lunch op.');
        $this->upsertMessage($admin, $alice, 'Welkom! Vergeet niet je profielvaardigheden bij te werken.');

        $this->entityManager->flush();

        $io->success('Seeding complete.');
        $io->listing([
            'Admin: admin@messenger.local / Admin123!',
            'User: alice@messenger.local / User123!',
            'User: bob@messenger.local / User123!',
            'Blocked user: blocked@messenger.local / User123!',
        ]);
        $io->note('The seeder is idempotent: running it again updates records instead of duplicating users.');

        return Command::SUCCESS;
    }

    private function upsertUser(
        string $email,
        string $fullName,
        string $skills,
        array $roles,
        bool $isBlocked,
        string $plainPassword,
    ): User {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            $user = new User();
            $user->setEmail($email);
            $this->entityManager->persist($user);
        }

        $user->setFullName($fullName);
        $user->setSkills($skills);
        $user->setRoles($roles);
        $user->setIsBlocked($isBlocked);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        return $user;
    }

    private function upsertMessage(User $sender, User $recipient, string $content): void
    {
        $messageRepository = $this->entityManager->getRepository(Message::class);

        $existing = $messageRepository->findOneBy([
            'sender' => $sender,
            'recipient' => $recipient,
            'content' => $content,
        ]);

        if ($existing instanceof Message) {
            return;
        }

        $message = new Message();
        $message->setSender($sender);
        $message->setRecipient($recipient);
        $message->setContent($content);

        $this->entityManager->persist($message);
    }
}
