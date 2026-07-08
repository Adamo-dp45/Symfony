<?php

namespace App\Domain\Twig\Cache;

use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TokenParser\TokenParserInterface;

class CacheTokenParser extends AbstractTokenParser { // Ou implements TokenParserInterface

    /**
     * Permet de retourner un noeud
     * @param Token $token
     * @return Node
     */
    public function parse(Token $token)
    {
        $linero = $token->getLine(); // Le numéro de ligne
        $stream = $this->parser->getStream(); // Permet de lire l'ensemble de notre template twig au fur et à mésure

        $key = $this->parser->getExpressionParser()->parseExpression(); // Pour récupérer l'expression comme 'cache post', si on a d'autre expression ..
        // $v = $this->parser->getExpressionParser()->parseExpression(); -- On le refais
        $key->setAttribute('always_define', true); // Si on veut être sur que la clé ça soit définie

        $stream->expect(Token::BLOCK_END_TYPE); // 'expect' pour avoir la fin de notre bloc comme '%}'
        $body = $this->parser->subparse([$this, 'decideCacheEnd'], true); // Nous renvoi un noeud qui contient tous ce qui y'a à l'intérieur du tag, 'true' permet de ne pas prendre en compte l'élément qu'il a trouvé à la fin
        $stream->expect(Token::BLOCK_END_TYPE);

        // - On vas crée un nouveau noeud lui passé ces propriétés et c'est le noeud qui sera chargé de générer le code php qui va faire fonctionner ce tag - 'CacheNode'

        // return new Node();
        return new CacheNode($key, $body, $linero); // Prennait aussi '$this->getTag()'
    }

    /**
     * Permet de retourner le nom de notre tag
     * @return string
     */
    public function getTag()
    {
        return 'ca';
    }

    public function decideCacheEnd(Token $token): bool
    {
        return $token->test('endca');
    }
}

// Ensuite dans 'TwigCacheExtension'