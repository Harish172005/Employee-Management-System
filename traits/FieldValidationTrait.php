<?php

trait FieldValidationTrait
{
    protected function validateRequiredFields(
        array $data,
        array $requiredFields
    ): ?array {
        foreach ($requiredFields as $field) {

            if (
                !array_key_exists($field, $data) ||
                trim((string) $data[$field]) === ''
            ) {
                return [
                    'success' => false,
                    'message' => ucfirst(
                        str_replace('_', ' ', $field)
                    ) . ' is required.',
                    'statusCode' => 400
                ];
            }
        }

        return null;
    }

    protected function validateInArray(
        mixed $value,
        array $allowedValues,
        string $fieldName
    ): ?array {
        if (!in_array($value, $allowedValues, true)) {
            return [
                'success' => false,
                'message' => "$fieldName is invalid.",
                'statusCode' => 400
            ];
        }

        return null;
    }

    protected function validateNumeric(
        mixed $value,
        string $fieldName
    ): ?array {
        if (!is_numeric($value)) {
            return [
                'success' => false,
                'message' => "$fieldName must be a valid number.",
                'statusCode' => 400
            ];
        }

        return null;
    }
}
