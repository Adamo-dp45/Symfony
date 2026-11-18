<?php

namespace App\Form;

use App\DTO\ContactPropertyDTO;
use Damo\RecaptchaBundle\Type\RecaptchaSubmitType;
// use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType; -- Bundle EWZ abandonné et retiré lors du passage à Symfony 8
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
// use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactPropertyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('phone', TextType::class)
            ->add('message', TextareaType::class)
            ->add('captcha', RecaptchaSubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn btn-secondary'
                ]
            ])

            # Karser Recaptcha3Bundle
            // ->add('captcha', Recaptcha3Type::class, [
            //    'constraints' => new Recaptcha3(),
            //    'action_name' => 'homepage', // Identifiant de l’action
            //    'script_nonce_csp' => $nonceCSP,
            //    'locale' => 'fr',
            // ])
            // ->add('recaptcha', EWZRecaptchaType::class, [
            //    'label' => false,
            //    'mapped' => false,
            //    'constraints' => [
            //        new RecaptchaTrue()
            //    ],
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactPropertyDTO::class,
        ]);
    }
}
