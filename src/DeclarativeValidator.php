<?php

declare(strict_types=1);

namespace App;

final class DeclarativeValidator
{
    /** @param array<string, array<string, mixed>> $rules @param array<string, mixed> $input */
    public static function check(array $input, array $rules): Validator
    {
        $validator = new Validator();
        foreach ($rules as $field => $fieldRules) {
            $value = $input[$field] ?? null;
            $text = is_scalar($value) ? trim((string) $value) : '';
            $optional = ($fieldRules['optional'] ?? false) === true;
            $label = (string) ($fieldRules['label'] ?? ucfirst(str_replace('_', ' ', $field)));
            if ($optional && $text === '') {
                continue;
            }
            if (($fieldRules['required'] ?? false) === true) {
                $validator->required($field, $text, $label);
            }
            if (isset($fieldRules['min']) && $text !== '') {
                $validator->minLength($field, $text, (int) $fieldRules['min'], $label);
            }
            if (isset($fieldRules['max']) && mb_strlen($text, 'UTF-8') > (int) $fieldRules['max']) {
                $validator->maxLength($field, $text, (int) $fieldRules['max'], $label);
            }
            if (($fieldRules['email'] ?? false) === true) {
                $validator->email($field, $text);
            }
            if (($fieldRules['username'] ?? false) === true) {
                $validator->username($text);
            }
            if (isset($fieldRules['password']) && $fieldRules['password'] === true) {
                $validator->password($text, (int) ($fieldRules['min_password'] ?? 12));
            }
            if (isset($fieldRules['match']) && is_string($fieldRules['match'])) {
                $other = $input[$fieldRules['match']] ?? null;
                if (!is_string($other) || !hash_equals($other, $text)) {
                    $validator->add($field, $label . ' must match ' . str_replace('_', ' ', $fieldRules['match']) . '.');
                }
            }
        }
        return $validator;
    }
}
