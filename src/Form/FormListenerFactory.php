<?php

namespace App\Form;

use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FormListenerFactory {

    public function autoSlug(string $field) 
    {
        return function (PreSubmitEvent $event) use($field) {
            $data = $event->getData();

            if (!is_array($data)) { // Si ce n'est pas un tableau, on sort
                return;
            }

            if (!array_key_exists($field, $data)) { // On ne fait rien si le champ n'existe pas dans le form
                return;
            }

            if(empty($data['slug']) && !empty($data[$field])) { // On génère le slug uniquement si vide
                $slugger = new AsciiSlugger();
                $data['slug'] = strtolower($slugger->slug($data[$field]));
                $event->setData($data);
            }
        };
    }

    public function timestamps(): callable
    {
        return function (PostSubmitEvent $event) {
            $data = $event->getData();
            $data->setUpdatedAt(new \DateTimeImmutable());
            if(!$data->getId()) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }
        };
    }

}