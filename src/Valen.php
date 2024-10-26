<?php

namespace Kawsarahmad\Valen;

use Illuminate\Support\Facades\Validator;

class Valen
{
    public function validate(array $input)
    {
        $rules = [];

        foreach ($input as $key => $value) {
            $rules[$key] = $this->parseValidationRules($key);
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return true;
    }

    private function parseValidationRules($key)
    {
        $segments = explode('-', $key);
        $parsedRules = [];

        foreach ($segments as $segment) {
            if ($segment === 'req') {
                $parsedRules[] = 'required';
            } elseif (str_starts_with($segment, 'min')) {
                $parsedRules[] = 'min:' . filter_var($segment, FILTER_SANITIZE_NUMBER_INT);
            } elseif (str_starts_with($segment, 'max')) {
                $parsedRules[] = 'max:' . filter_var($segment, FILTER_SANITIZE_NUMBER_INT);
            } elseif (str_starts_with($segment, 'file-valen')) {
                $parsedRules[] = 'file';
            } elseif (str_starts_with($segment, 'email')) {
                $parsedRules[] = 'email';
            }
            // Add additional parsing logic for other rules (image types, unique, etc.)
        }

        return implode('|', $parsedRules);
    }
}
