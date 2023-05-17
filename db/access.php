<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'enrol/cito1c:enrol' => [
        'archetypes' => [
            'manager' => CAP_ALLOW
        ],
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE
    ],
    'enrol/cito1c:manage' => [
        'archetypes' => [
            'manager' => CAP_ALLOW
        ],
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE
    ],
    'enrol/cito1c:unenrol' => [
        'archetypes' => [
            'manager' => CAP_ALLOW
        ],
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE
    ]
];
