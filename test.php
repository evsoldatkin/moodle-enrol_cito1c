<?php
require_once '../../config.php';
global $CFG, $USER;
require_login();
require_once $CFG->dirroot.'/lib/externallib.php';
$USER->ignoresesskey = true;
$params = [
    'courseid' => required_param('courseid', PARAM_INT),
    'roleid' => 3,
    'userid' => required_param('userid', PARAM_INT)
];
$params = [
    'enrolments' => [$params]
];
$result = external_api::call_external_function('enrol_cito1c_enrol_users', $params);
if ($result['error'] === false)
{
    $result = external_api::call_external_function('enrol_cito1c_unenrol_users', $params);
    if ($result['error'] === false)
    {
        echo 'Test passed';
        die;
    }
}
echo 'Test failed';
