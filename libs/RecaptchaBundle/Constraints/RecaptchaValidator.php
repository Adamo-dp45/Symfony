<?php

namespace Damo\RecaptchaBundle\Constraints;

use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintValidator;

class RecaptchaValidator extends ConstraintValidator {

    public function __construct(
        private RequestStack $requestStack, // Permet de récupérer différents infos sur la requête
        private ReCaptcha $recaptcha
    )
    {
    }

    public function validate(mixed $value, \Symfony\Component\Validator\Constraint $constraint)
    {
        $request = $this->requestStack->getCurrentRequest(); // Requête courante
        $recaptchaResponse = $request->request->get('g-recaptcha-response'); // request car on n'est en post
        if(empty($recaptchaResponse)) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }
        $response = $this->recaptcha
            ->setExpectedHostname($request->getHost())
            ->verify($recaptchaResponse, $request->getClientIp());
        if(!$response->isSuccess()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}