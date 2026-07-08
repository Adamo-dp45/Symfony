<?php

namespace App\Domain\Twig;

use App\Domain\Twig\Cache\CachableInterface;
use App\Domain\Twig\Cache\CacheTokenParser;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;

class TwigCacheExtension extends AbstractExtension {

    public function __construct(
        private CacheItemPoolInterface $cache // 'AdapterInterface' pour grafikart
    )
    {
    }

    /**
     * 
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        // return parent::getTokenParsers(); -- Dans notre cas on vas retourner un tableau qui permettra de comprendre notre tag 'cache'
        return [
            new CacheTokenParser()
        ];
    }

    /**
     * Permet de récupérer la valeur par rapport à une clé
     */
    public function getCachedValue(CachableInterface $key): ?string
    {
        return $this->getItem($key)->get();
    }

    public function setCachedValue(CachableInterface $key, string $value)
    {
        $item = $this->getItem($key);
        $item->set($value);
        $this->cache->save($item);
    }

    private function getItem(CachableInterface $entity): CacheItem
    {
        $className = get_class($entity);
        $className = substr($className, strrpos($className, '\\') + 1); // Pour avoir la dernière partie
        $key = $entity->getId() . $className . $entity->getUpdatedAt()->getTimestamp();
        return $this->cache->getItem($key);
    }

}