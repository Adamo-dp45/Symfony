<?php

namespace Damo\RecaptchaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RecaptchaBundle extends Bundle
{
    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RecaptchaComplilerPass()); // Permet de rajouter une logique qui va rajouter des comportements
    }
}