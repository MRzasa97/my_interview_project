<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class OrderValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
    
    /**
     * @param array<array{productId: int|string, quantity: int}> $data
     * @throws ValidatorException
     */
    public function validateCreateOrder(array $data): void
    {
        $constraints = new Assert\Collection([
            'items' => new Assert\All([
                new Assert\Type('array'),
                new Assert\Collection([
                    'productId' => new Assert\NotBlank(),
                    'quantity' => new Assert\Positive(),
                ]),
            ]),
        ]);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            throw new ValidatorException(implode(', ', $errors));
        }
    }

    public function validateGetOrder(int $id): void
    {
        
        $constraints = new Assert\Collection([
            'id' => [
                new Assert\NotBlank(),
                new Assert\Positive()
            ],
        ]);

        $violations = $this->validator->validate(['id' => $id], $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            throw new ValidatorException(implode(', ', $errors));
        }
    }
}
