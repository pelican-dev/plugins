<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Ye must accept the :attribute, or walk the plank!',
    'active_url' => 'The :attribute be not a proper sea chart.',
    'after' => 'The :attribute must be a date after :date, arr!',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date, savvy?',
    'alpha' => 'The :attribute may only contain letters from A to Z, no numbers allowed!',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes, matey.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be a treasure list.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max doubloons.',
        'file' => 'The :attribute must be between :min and :max kilobytes of treasure.',
        'string' => 'The :attribute must be between :min and :max characters long.',
        'array' => 'The :attribute must have between :min and :max items in the cargo.',
    ],

    'confirmed' => 'The :attribute confirmation don\'t match, ye scurvy dog!',
    'date' => 'The :attribute be not a valid date on the calendar.',
    'date_format' => 'The :attribute don\'t match the format :format.',
    'different' => 'The :attribute and :other must be different as day and night.',
    'digits' => 'The :attribute must be :digits digits long.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has wrong treasure map dimensions.',

    'email' => 'The :attribute must be a proper message bottle address.',

    'file' => 'The :attribute must be a scroll.',
    'filled' => 'The :attribute field be required, no skippin\'!',
    'image' => 'The :attribute must be a portrait or map.',

    'in_array' => 'The :attribute field be not found in the :other cargo hold.',
    'integer' => 'The :attribute must be a whole number.',
    'ip' => 'The :attribute must be a valid harbor location.',
    'json' => 'The :attribute must be a proper treasure map.',
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],

    'numeric' => 'The :attribute must be a number.',

    'regex' => 'The :attribute format be invalid.',

    'required_with_all' => 'The :attribute field be required when :values is present.',

    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',

    'url' => 'The :attribute format be invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

    // Internal validation logic for Panel
    'internal' => [
        'variable_value' => ':env variable',
        'invalid_password' => 'The password provided was invalid fer this account.',
    ],
];
