<?php

namespace App\Tests\EventSubscriber;

use App\DTO\ContactDTO;
use App\Event\ContactRequestEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MailingSubscriberTest extends KernelTestCase {

    public function testOnUserRegistered()
    {
        $contactDTO = new ContactDTO();
        $contactDTO->name = 'Adama';
        $contactDTO->email = 'bakayokoadama507@gmail.com';
        $contactDTO->message = 'Bonjour les gens';
        $contactDTO->service = 'bakayokoadama507@gmail.com';

        self::bootKernel();
        $dispatcher = self::getContainer()->get(EventDispatcherInterface::class);
        $dispatcher->dispatch(new ContactRequestEvent($contactDTO));

        $this->assertTrue(true);
    }
}