<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Validator;

use DomainException;

class ValidationException extends DomainException
{
    /**
     * @param  array<string, string[]>  $errors
     */
    public function __construct(private readonly array $errors)
    {
        $message = $this->formatMessage($this->errors);
        parent::__construct($message);
    }

    /**
     * @return array<string, string[]>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @param  array<string, string[]>  $errors
     */
    private function formatMessage(array $errors): string
    {
        $messages = [];
        foreach ($errors as $field => $fieldErrors) {
            $messages[] = sprintf('%s: %s', $field, implode(', ', $fieldErrors));
        }

        return implode('; ', $messages);
    }
}
