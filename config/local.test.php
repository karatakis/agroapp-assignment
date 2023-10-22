<?php

// Phpunit test environment

return function (array $settings): array {
    $settings['error']['display_error_details'] = true;
    $settings['error']['log_errors'] = true;

    // Mocked Logger settings
    $settings['logger'] = [
        'test' => new \Monolog\Handler\TestHandler(),
    ];

    return $settings;
};
