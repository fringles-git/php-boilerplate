<?php

declare(strict_types=1);

namespace App;

final class Validation
{
    public static function check(array $input, array $rules): Validator
    {
        return DeclarativeValidator::check($input, $rules);
    }
}
