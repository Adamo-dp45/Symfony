<?php

namespace App\Domain\Twig\Cache;

use App\Domain\Twig\TwigCacheExtension;
use Twig\Compiler;
use Twig\Node\Node;

class CacheNode extends Node {

    private static int $cacheCount = 1;

    public function __construct(Node $key, Node $body, int $lineno = 0)
    {
        return parent::__construct(['key' => $key, 'body' => $body], [], $lineno);
    }

    /**
     * Permet de compiler le code php qui va être mis dans notre template rendu dans twig
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler)
    {
        // return parent::compile($compiler);
        /*
            - 'addDebugInfo' permet de rajouter des informations sur les probèmes qu'on peut avoir
            - 'write' permet d'écrire du code mais doit être du php valide 
        */
        $i = self::$cacheCount++;
        $extension = TwigCacheExtension::class;
        $compiler
            ->addDebugInfo($this)
            ->write("\$twigCacheExtension = \$this->env->getExtension('{$extension}');\n") // Pour récupérer l'instance de mon extension on doit accéder à l'environnement
            ->write("\$twigCacheBody{$i} = \$twigCacheExtension->getCachedValue(")
            ->subcompile($this->getNode('key'))
            ->raw(");\n")
            ->write("if(\$twigCacheBody{$i} !== null) {\n")
            ->indent()
            ->write("echo \$twigCacheBody{$i};")
            ->outdent()
            ->write("} else { \n")
            ->indent()
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("\$twigCacheBody{$i} = ob_get_clean();\n")
            ->write("echo \$twigCacheBody{$i};\n")
            ->write("\$twigCacheExtension->setCachedValue(")
            ->subcompile($this->getNode('key'))
            ->raw(", \$twigCacheBody{$i});\n")
            ->outdent()
            ->write("}\n")
        ;
    }
}