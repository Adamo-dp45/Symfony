<?php

namespace App\Factory;

use App\Entity\Tag;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Tag>
 */
final class TagFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Tag::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Tag $tag): void {})
            /*
                'cover' => self::faker()->text(255),  
                'editedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
                'isbn' => self::faker()->text(255),
                'pageNumber' => self::faker()->randomNumber(),
                'plot' => self::faker()->text(),
                'status' => self::faker()->randomElement(BookStatus::cases()),
                'title' => self::faker()->text(255),

                'cover' => self::faker()->imageUrl(330, 500, 'couverture', true),
                'editedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
                'editor' => EditorFactory::random(),
                'authors' => AuthorFactory::random(),
                'isbn' => self::faker()->isbn13(),
                'pageNumber' => self::faker()->randomNumber(),
                'plot' => self::faker()->text(),
                'status' => self::faker()->randomElement(BookStatus::cases()),
                'title' => self::faker()->unique()->sentence(),
            */
            /*
                Tag::createOne([
                    'name' => 'Tag #1'
                ])
                -- Test - $client->loginUser($user->_real())
            */
        ;
    }
}
