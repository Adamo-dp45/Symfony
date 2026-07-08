<?php

namespace App\DTO;

use App\Entity\Property;
use Symfony\Component\Validator\Constraints as Assert;

class ContactPropertyDTO {

    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max:200)]
    private ?string $firstname;

    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max:200)]
    private ?string $lastname;

    #[Assert\NotBlank()]
    #[Assert\Email()]
    private ?string $email;

    #[Assert\NotBlank()]
    #[Assert\Regex(
        pattern:"/[0-9]{10}/" // Nombre en 0 et 9 repété 10 fois
    )]
    private ?string $phone;

    #[Assert\NotBlank()]
    #[Assert\Length(min: 5)]
    private ?string $message;

    private ?Property $property;

    public function getFirstname(): string|null
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string|null
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): string|null
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): string|null
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMessage(): string|null
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getProperty(): Property|null
    {
        return $this->property;
    }

    public function setProperty(?Property $property): static
    {
        $this->property = $property;

        return $this;
    }
}