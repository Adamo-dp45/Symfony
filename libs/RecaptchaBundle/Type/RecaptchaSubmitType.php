<?php

namespace Damo\RecaptchaBundle\Type;

use Damo\RecaptchaBundle\Constraints\Recaptcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RecaptchaSubmitType extends AbstractType {

    public function __construct(private string $key)
    {
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false,
            'constraints' => new Recaptcha()
        ]);
    }

    /**
     * Préfix du block dans notre vue fields.twig.html
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'recaptcha_submit';
    }

    public function buildView(\Symfony\Component\Form\FormView $view, \Symfony\Component\Form\FormInterface $form, array $options)
    {
        $view->vars['label'] = false;
        $view->vars['key'] = $this->key;
        $view->vars['button'] = $options['label'];
    }

    public function getParent()
    {
        return TextType::class; // On y hérite car on veut le système de validation
    }
}