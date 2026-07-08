<?php

namespace Damo\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class RecaptchaExtension extends Extension {

    public function load(array $configs, \Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $loader = new YamlFileLoader( // Le bundle sera capable de déclarer ses propres services
            $container,
            new FileLocator(__DIR__ . '/../Resources/config') // Pemret de spécifier le chemin vers le fichier
        );
        $loader->load('services.yaml'); // Comme ça notre système chargera notre service et sera en mésure d'être capable d'initialiser notre type avec la cléf qui va conténir la cléf correspondant à recaptcha

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs); // On demande à notre système d'utiliser la configuration qu'on a crée
        // dd($config); -- On verra nos cléf du fichier recaptcha.yaml
        $container->setParameter('recaptcha.key', $config['key']); // On récupère les clef pour les injecter dans les paramètres du système de service
        $container->setParameter('recaptcha.secret', $config['secret']);
    } // php bin/console debug:container --parameter=recaptcha.key : Pour debbuger un paramètre
}