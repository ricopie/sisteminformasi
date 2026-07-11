<?php

return [
    // Define Module to Auto-Discover via Service Provider
    'enabled' => [
    ],

    // Path
    'paths' => [
        'domain' => 'src/Domain',
        'application' => 'src/Application',
        'infrastructure' => 'src/Infrastructure',
        'presentation' => 'src/Presentation',
    ],

    // Define Default Directory in layering module
    'structure' => [
        'domain' => ['Entities', 'ValueObjects', 'Repositories', 'Events', 'Exceptions'],
        'application' => ['UseCases', 'DTOs'],
        'infrastructure' => ['Models', 'Migrations', 'Providers', 'Repositories'],
        'presentation' => ['Console', 'Http/Controllers', 'Http/Requests', 'Http/Resources'],
    ],
];
