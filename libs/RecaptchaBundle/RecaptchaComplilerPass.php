<?php

namespace Damo\RecaptchaBundle;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RecaptchaComplilerPass implements CompilerPassInterface
{
    public function process(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if($container->hasParameter('twig.form.resources')) { // La condition empêche
            $resources = $container->getParameter('twig.form.resources') ?: [];
            array_unshift($resources, '@Recaptcha/fields.html.twig'); // Rajoute dans ce tableau
            $container->setParameter('twig.form.resources', $resources);
        }
    }
}