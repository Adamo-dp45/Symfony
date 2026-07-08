<?php

namespace App\Domain\Helper;

use Parsedown;

class MarkdownHelper {

    private Parsedown $parser;

    public function __construct()
    {
        $this->parser = new Parsedown();
        $this->parser->setSafeMode(true); // Permet d'éviter le HTML dangereux
    }

    public function parse(string $markdown): string
    {
        return $this->parser->text($markdown);
    }

}