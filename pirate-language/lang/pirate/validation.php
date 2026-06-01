<?php

return [
    'accepted' => 'The :attribute must be accepted, matey.',
    'active_url' => 'The :attribute be not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal ter :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal ter :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must contain between :min and :max items.',
    ],

    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute be not a valid date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',

    'email' => 'The :attribute must be a valid messenger address.',

    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field be required.',
    'image' => 'The :attribute must be an image.',

    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be a whole number.',
    'ip' => 'The :attribute must be a valid IP address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be longer than :max characters.',
        'array' => 'The :attribute may not contain more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file o\' type: :values.',
    'mimetypes' => 'The :attribute must be a file o\' type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters long.',
        'array' => 'The :attribute must contain at least :min items.',
    ],

    'numeric' => 'The :attribute must be a number.',

    'regex' => 'The :attribute format be invalid.',

    'required_with_all' => 'The :attribute field be required when :values be present.',

    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid timezone.',

    'url' => 'The :attribute format be invalid.',

    'attributes' => [],

    'internal' => [
        'variable_value' => ':env variable',
        'invalid_password' => 'The passphrase provided be invalid fer this account.',
    ],
];
