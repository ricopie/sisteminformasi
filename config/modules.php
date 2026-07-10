<?php

return [
    // Define Module to Auto-Discover via Service Provider
    'enabled' => [
        'Organization',
        'Beneficiary',
    ],

    // Path
    'paths' => [
        'domain' => 'src/Domain',
        'application' => 'src/Application',
        'infrastructure' => 'src/Infrastructure/Persistence',
        'presentation' => 'src/Presentation',
    ],

    // Define Default Directory in layering module
    'structure' => [
        'domain' => ['Entities', 'ValueObjects', 'Repositories', 'Events', 'Exceptions'],
        'application' => ['UseCases', 'DTOs'],
        'infrastructure' => ['Models', 'Migrations', 'Providers', 'Repositories'],
        'presentation' => ['Http/Controllers', 'Http/Requests', 'Http/Resources'],
    ],
];
