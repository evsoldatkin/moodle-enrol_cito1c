<?php
$functions = [
    'enrol_cito1c_enrol_users' => [
        'classname'   => 'enrol_cito1c_external',
        'methodname'  => 'enrol_users',
        'classpath'   => 'enrol/cito1c/externallib.php',
        'description' => 'Enrol users from 1C',
        'capabilities'=> 'enrol/cito1c:enrol',
        'type'        => 'write',
    ],
    'enrol_cito1c_unenrol_users' => [
        'classname'   => 'enrol_cito1c_external',
        'methodname'  => 'unenrol_users',
        'classpath'   => 'enrol/cito1c/externallib.php',
        'description' => 'Unenrol users from 1C',
        'capabilities'=> 'enrol/cito1c:unenrol',
        'type'        => 'write',
    ]
];

$services = [
    'enrol_cito1c_web_services' => [
        'functions' => array_keys($functions),
        'restrictedusers' => 1,
        'enabled' => 1,
        'shortname' => 'cito1c'
    ]
];
