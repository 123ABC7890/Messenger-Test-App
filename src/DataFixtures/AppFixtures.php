<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];

        $users['admin'] = $this->createUser(
            $manager,
            email: 'admin@messenger.local',
            fullName: 'Admin User',
            skills: 'Symfony, Security, Management',
            roles: ['ROLE_ADMIN'],
            isBlocked: false,
            plainPassword: 'Admin123!'
        );

        $users['alice'] = $this->createUser(
            $manager,
            email: 'alice@messenger.local',
            fullName: 'Alice Jansen',
            skills: 'PHP, Symfony, SQL',
            roles: ['ROLE_USER'],
            isBlocked: false,
            plainPassword: 'User123!'
        );

        $users['bob'] = $this->createUser(
            $manager,
            email: 'bob@messenger.local',
            fullName: 'Bob de Vries',
            skills: 'JavaScript, API, Testing',
            roles: ['ROLE_USER'],
            isBlocked: false,
            plainPassword: 'User123!'
        );

        $users['charlotte'] = $this->createUser(
            $manager,
            email: 'charlotte@messenger.local',
            fullName: 'Charlotte Bakker',
            skills: 'UX, HTML, CSS, Design Systems',
            roles: ['ROLE_USER'],
            isBlocked: false,
            plainPassword: 'User123!'
        );

        $users['daan'] = $this->createUser(
            $manager,
            email: 'daan@messenger.local',
            fullName: 'Daan Smit',
            skills: 'Laravel, PHP, DevOps',
            roles: ['ROLE_USER'],
            isBlocked: false,
            plainPassword: 'User123!'
        );

        $users['emma'] = $this->createUser(
            $manager,
            email: 'emma@messenger.local',
            fullName: 'Emma Visser',
            skills: 'Projectmanagement, Scrum, Communicatie',
            roles: ['ROLE_USER'],
            isBlocked: false,
            plainPassword: 'User123!'
        );

        $users['farid'] = $this->createUser(
            $manager,
            email: 'farid@messenger.local',
            fullName: 'Farid El Amrani',
            skills: 'Python, Data, Automation',
            roles: ['ROLE_USER'],
            isBlocked: false,
            plainPassword: 'User123!'
        );

        $users['gina'] = $this->createUser(
            $manager,
            email: 'gina@messenger.local',
            fullName: 'Gina Peters',
            skills: 'React, TypeScript, Frontend Architecture',
            roles: ['ROLE_USER'],
            isBlocked: false,
            plainPassword: 'User123!'
        );

        $users['blocked'] = $this->createUser(
            $manager,
            email: 'blocked@messenger.local',
            fullName: 'Blocked Gebruiker',
            skills: 'Legacy systems, Support',
            roles: ['ROLE_USER'],
            isBlocked: true,
            plainPassword: 'User123!'
        );

        $baseTime = new \DateTimeImmutable('2026-03-10 09:00:00');

        $messages = [
            ['alice', 'bob', 'Hoi Bob, wil je straks even naar de nieuwe Symfony setup kijken?'],
            ['bob', 'alice', 'Ja, ik pak het na de lunch op en stuur feedback terug.'],
            ['admin', 'alice', 'Welkom! Vergeet niet je profielvaardigheden bij te werken.'],
            ['charlotte', 'gina', 'Kun jij de frontend cards nog responsive finetunen voor mobiel?'],
            ['gina', 'charlotte', 'Ja, ik werk de spacing en typografie vanmiddag bij.'],
            ['emma', 'daan', 'Kun je een korte inschatting maken voor de admin-module?'],
            ['daan', 'emma', 'Reken op ongeveer vijf uur inclusief tests en cleanup.'],
            ['farid', 'alice', 'Ik heb een script om demo-data sneller te analyseren als je wilt.'],
            ['alice', 'farid', 'Top, stuur maar door. Dat helpt voor de zoekfunctie.'],
            ['bob', 'gina', 'De API responses zijn stabiel. Jij kunt nu de UI eraan koppelen.'],
            ['admin', 'emma', 'Controleer nog even of alle user stories zijn afgedekt voor de demo.'],
            ['charlotte', 'alice', 'Je profielpagina ziet er netjes uit, maar ik zou de copy iets inkorten.'],
            ['gina', 'bob', 'Ik heb de inbox nu gekoppeld, inclusief sortering op nieuwste eerst.'],
            ['emma', 'admin', 'De demo accounts staan klaar voor de presentatie vanmiddag.'],
        ];

        foreach ($messages as $index => [$senderKey, $recipientKey, $content]) {
            $this->createMessage(
                $manager,
                $users[$senderKey],
                $users[$recipientKey],
                $content,
                $baseTime->modify(sprintf('+%d minutes', $index * 13))
            );
        }

        $manager->flush();
    }

    private function createUser(
        ObjectManager $manager,
        string $email,
        string $fullName,
        string $skills,
        array $roles,
        bool $isBlocked,
        string $plainPassword,
    ): User {
        $user = new User();
        $user->setEmail($email);
        $user->setFullName($fullName);
        $user->setSkills($skills);
        $user->setRoles($roles);
        $user->setIsBlocked($isBlocked);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $manager->persist($user);

        return $user;
    }

    private function createMessage(
        ObjectManager $manager,
        User $sender,
        User $recipient,
        string $content,
        \DateTimeImmutable $createdAt,
    ): void {
        $message = new Message();
        $message->setSender($sender);
        $message->setRecipient($recipient);
        $message->setContent($content);
        $message->setCreatedAt($createdAt);

        $manager->persist($message);
    }
}
