<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Brick\Money\Money;

class ProductValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
    
    /**
     * @param array{name: string, price: float|int} $data
     * @throws ValidatorException
     */
    public function validateCreateProduct(array $data): void
    {
        $constraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank([
                    'message' => 'Name should not be blank.',
                ]),
                new Assert\Type([
                    'type' => 'string',
                    'message' => 'Name must be a string.',
                ]),
                new Assert\Length([
                    'max' => 255,
                    'maxMessage' => 'The name cannot be longer than {{ limit }} characters.',
                    'min' => 3,
                    'minMessage' => 'The name cannot be shoerter than {{ limit }} characters.'     
                ])
            ],
            'price' => [
                new Assert\NotBlank([
                    'message' => 'Price should not be blank.',
                ]),
                new Assert\Type([
                    'type' => 'numeric',
                    'message' => 'Price must be a numeric value.',
                ]),
                new Assert\PositiveOrZero(),
                new Assert\Callback(function ($price, $context) {
                    try {
                        Money::of($price, 'USD');
                    } catch (\Exception $e) {
                        $context->buildViolation('Price must be a valid money format.')
                            ->addViolation();
                    }
                }),
            ],
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
}
