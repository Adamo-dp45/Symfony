<?php

namespace Damo\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('recaptcha'); // Le nom du rootNode doit correspondre au nom du bundle en miniscule, en snakecase et sans le mot clé 'Bundle'
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('key') // Cléf
                    ->isRequired() // Réquis
                    ->cannotBeEmpty() // Ne doit pas être vide
                ->end()
                ->scalarNode('secret')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();
        return $treeBuilder;
    }
}