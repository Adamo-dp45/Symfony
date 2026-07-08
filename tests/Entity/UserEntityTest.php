<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[Small]
#[CoversClass(User::class)]
class UserEntityTest extends KernelTestCase /*
    - On.. définir des attributs sur l'entité
        - 'CoversClass' pour indiquer la class qu'elle couvre
        - 'Small' !! un petit test du genre unitaire
*/
{

    private function assertHasError(User $user, int $number = 0)
    {
        self::bootKernel();
        $validator = self::getContainer()->get('validator');
        $validator = $validator->validate($user);
        $this->assertCount($number, $validator);
    }

    #[Test]
    #[TestDox('La description..')]
    public function testValidEntity(): void /*
        - Pour créer un test la méthode doit commencer par 'test..' ou utiliser l'attribut
    */
    {
        $user = (new User())
            ->setUsername('Adama')
            ->setEmail('bakayokoadama507@gmail.com')
            ->setRoles(['ROLE_USER'])
            ->setApiToken('aazazazaaz')
            ->setAvatar(null)
        ;
        $this->assertHasError($user, 0);
    }

    public function testInvalidEntity()
    {
        $user = (new User())
            ->setUsername('Adama')
            ->setEmail('bakayokoadam') // --
            ->setRoles(['ROLE_USER'])
            ->setApiToken('aazazazaaz')
            ->setAvatar(null)
        ;
        self::bootKernel();
        $validator = self::getContainer()->get('validator')->validate($user);
        $this->assertCount(1, $validator);
    }

    public function testPasswordHashing()
    {
        $user = new User();
        $user->setPassword('password123');
        $hashedPassword = password_hash('password123', PASSWORD_BCRYPT);
        $this->assertTrue(password_verify('password123', $hashedPassword));
    }

    // Ne pas oublié les tests ou on entre des valeurs vides
}