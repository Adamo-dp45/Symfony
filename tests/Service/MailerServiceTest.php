<?php

namespace App\Tests\Service;

use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerServiceTest extends TestCase
{
    private MailerInterface $mailer;

    #[Override]
    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
    }

    public function testSend(): void
    {
        $mailer = $this->createMock(MailerInterface::class); /*
            - Pour créer un 'mock' du mailer
            - 'expects()' !! indiquer qu'on veut utiliser la fonction 'send' une seule fois
                - 'exactly(2)' !! 2 fois
                - 'never()' !! jamais appelé
            - 'with' une fonction dans laquel on peut faire des assertions, 'email' reçu en paramètre lors du 'send' dans le service
        */
        $customer = 'adamo.dp45@gmail.com';
        $mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($customer): bool {
                $this->assertSame($customer, $email->getTo()[0]->toString());

                return true;
            }))
        ;

    }
}