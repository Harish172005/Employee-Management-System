<?php

trait FieldValidationTrait
{
    protected function validateRequiredFields(array $data, array $fields): ?array
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                return [
                    'success' => false,
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.',
                    'statusCode' => 400
                ];
            }

            $value = $data[$field];
            if (is_string($value) && trim($value) === '') {
                return [
                    'success' => false,
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.',
                    'statusCode' => 400
                ];
            }

            if (is_null($value)) {
                return [
                    'success' => false,
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.',
                    'statusCode' => 400
                ];
            }
        }

        return null;
    }
}
