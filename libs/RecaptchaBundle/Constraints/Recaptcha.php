<?php

namespace Damo\RecaptchaBundle\Constraints;

use Symfony\Component\Validator\Constraint;

class Recaptcha extends Constraint {

    public $message = 'Invalide captcha';
}