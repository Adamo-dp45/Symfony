<?php

namespace App\Form\Type;

use App\Domain\Antispam\ChallengeInterface;
use App\Domain\Antispam\Puzzle\PuzzleChallenge;
use App\Validator\Challenge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CaptchaType extends AbstractType
{
    public function __construct(
        private readonly ChallengeInterface $challenge,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new NotBlank(),
                new Challenge()
            ],
            'route' => 'captcha'
        ]);
        parent::configureOptions($resolver);
    }

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options): void
    {
        $builder->add('challenge', HiddenType::class, [
            'attr' => [
                'class' => 'captcha-challenge'
            ]
        ])
        ->add('answer', HiddenType::class, [
            'attr' => [
                'class' => 'captcha-answer'
            ]
        ]);
        parent::buildForm($builder, $options);
    }

    public function buildView(\Symfony\Component\Form\FormView $view, \Symfony\Component\Form\FormInterface $form, array $options): void
    {
        $key = $this->challenge->generateKey();
        $view->vars['attr'] = [
            'width' => PuzzleChallenge::WIDTH,
            'height' => PuzzleChallenge::HEIGHT,
            'piece-width' => PuzzleChallenge::PIECE_WIDTH,
            'piece-height' => PuzzleChallenge::PIECE_HEIGHT,
            'src' => $this->urlGenerator->generate($options['route'], ['challenge' => $key])
        ];
        $view->vars['challenge'] = $key;
        parent::buildView($view, $form, $options);
    }
}