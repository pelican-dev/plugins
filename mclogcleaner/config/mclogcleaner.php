<?php

return [
    'mclogcleaner_text_enabled' => filter_var(env('MCLOGCLEANER_TEXT_ENABLED', true), FILTER_VALIDATE_BOOL),
];
