<?php

class EmailValidator
{
    public static function validate(string $email): array
    {
        $errors = [];

        if ($email === '') {
            $errors[] = 'Email is required.';
            return $errors;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }

        return $errors;
    }
}
