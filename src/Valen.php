<?php

namespace Kawsarahmad\Valen;

use Illuminate\Support\Facades\Validator;

class Valen
{
    public function validate(array $input)
    {
        $rules = [];
        $errorMessages = [];

        foreach ($input as $key => $value) {
            $rules[$key] = $this->parseValidationRules($key);
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $field => $messages) {
                $errorMessages[$field . '_error'] = $messages;
            }
            return $errorMessages;
        }

        return true;
    }

    private function parseValidationRules($key)
    {
        $segments = explode('-', $key);
        $parsedRules = [];

        // Determine the main type of validation based on the prefix
        $fieldType = array_shift($segments); // e.g., "name", "price", "image", etc.

        foreach ($segments as $segment) {
            switch ($segment) {
                case 'req':
                    $parsedRules[] = 'required';
                    break;

                case (str_starts_with($segment, 'min') ? true : false):
                    $parsedRules[] = 'min:' . filter_var($segment, FILTER_SANITIZE_NUMBER_INT);
                    break;

                case (str_starts_with($segment, 'max') ? true : false):
                    $parsedRules[] = 'max:' . filter_var($segment, FILTER_SANITIZE_NUMBER_INT);
                    break;

                case 'int':
                    if ($fieldType === 'price') {
                        $parsedRules[] = 'integer';
                    }
                    break;

                case 'float':
                case 'double':
                    if ($fieldType === 'price') {
                        $parsedRules[] = 'numeric';
                    }
                    break;

                case 'email':
                    if ($fieldType === 'email') {
                        $parsedRules[] = 'email';
                    }
                    break;

                case 'file':
                    $parsedRules[] = 'file';
                    break;

                case 'image':
                    $parsedRules[] = 'image';
                    break;

                case (str_starts_with($segment, 'all') ? true : false):
                    if ($fieldType === 'image') {
                        $parsedRules[] = 'mimes:jpeg,png,jpg,gif,svg,webp';
                    } elseif ($fieldType === 'file') {
                        $parsedRules[] = 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx';
                    }
                    break;

                default:
                    if ($fieldType === 'image') {
                        $parsedRules[] = $this->parseImageFormat($segment);
                    } elseif ($fieldType === 'file') {
                        $parsedRules[] = $this->parseFileFormat($segment);
                    }
                    break;
            }
        }

        return implode('|', array_filter($parsedRules));
    }

    private function parseImageFormat($segment)
    {
        $allowedFormats = ['jpeg', 'png', 'jpg', 'gif', 'svg', 'webp'];
        $formats = explode('-', $segment);
        $filteredFormats = array_filter($formats, fn($format) => in_array($format, $allowedFormats));

        return $filteredFormats ? 'mimes:' . implode(',', $filteredFormats) : null;
    }

    private function parseFileFormat($segment)
    {
        $allowedFormats = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'];
        $formats = explode('-', $segment);
        $filteredFormats = array_filter($formats, fn($format) => in_array($format, $allowedFormats));

        return $filteredFormats ? 'mimes:' . implode(',', $filteredFormats) : null;
    }
}
