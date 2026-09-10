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
}
