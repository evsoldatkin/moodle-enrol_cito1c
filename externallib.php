<?php
defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/externallib.php';

class enrol_cito1c_external extends external_api
{
    public static function enrol_users_parameters()
    {
        return new external_function_parameters([
            'enrolments' => new external_multiple_structure(
                new external_single_structure([
                    'courseid' => new external_value(PARAM_INT, 'The course to enrol the user role in'),
                    'roleid' => new external_value(PARAM_INT, 'Role to assign to the user'),
                    'suspend' => new external_value(PARAM_INT, 'set to 1 to suspend the enrolment', VALUE_OPTIONAL),
                    'timestart' => new external_value(PARAM_INT, 'Timestamp when the enrolment start', VALUE_OPTIONAL),
                    'timeend' => new external_value(PARAM_INT, 'Timestamp when the enrolment end', VALUE_OPTIONAL),
                    'userid' => new external_value(PARAM_INT, 'The user that is going to be enrolled'),
                ])
            )
        ]);
    }

    public static function enrol_users($enrolments)
    {
        global $DB, $CFG;
        require_once $CFG->libdir.'/enrollib.php';
        $params = self::validate_parameters(
            self::enrol_users_parameters(),
            ['enrolments' => $enrolments]
        );
        $transaction = $DB->start_delegated_transaction();
        $enrol = enrol_get_plugin('cito1c');
        if (empty($enrol))
            throw new moodle_exception('pluginnotinstalled', 'enrol_cito1c');
        foreach ($params['enrolments'] as $enrolment)
        {
            $context = context_course::instance($enrolment['courseid'], IGNORE_MISSING);
            self::validate_context($context);
            require_capability('enrol/cito1c:enrol', $context);
            $roles = get_assignable_roles($context);
            if (!array_key_exists($enrolment['roleid'], $roles))
            {
                $errorparams = new stdClass();
                $errorparams->courseid = $enrolment['courseid'];
                $errorparams->roleid = $enrolment['roleid'];
                $errorparams->userid = $enrolment['userid'];
                throw new moodle_exception('wsusercannotassign', 'enrol_cito1c', '', $errorparams);
            }
            $instance = $enrol->enrol_get_instance($enrolment['courseid']);
            if (empty($instance))
            {
                $course = new \stdClass();
                $course->id = $enrolment['courseid'];
                $enrol->add_instance($course);
                $instance = $enrol->enrol_get_instance($enrolment['courseid']);
            }
            $enrolment['status'] = (isset($enrolment['suspend']) && !empty($enrolment['suspend']))
                ? ENROL_USER_SUSPENDED
                : ENROL_USER_ACTIVE;
            $enrolment['timeend'] = isset($enrolment['timeend']) ? $enrolment['timeend'] : 0;
            $enrolment['timestart'] = isset($enrolment['timestart']) ? $enrolment['timestart'] : 0;
            $enrol->enrol_user(
                $instance,
                $enrolment['userid'],
                $enrolment['roleid'],
                $enrolment['timestart'],
                $enrolment['timeend'],
                $enrolment['status']
            );
        }
        $transaction->allow_commit();
    }

    public static function enrol_users_returns()
    {
        return null;
    }

    public static function unenrol_users_parameters()
    {
        return new external_function_parameters([
            'enrolments' => new external_multiple_structure(
                new external_single_structure([
                    'courseid' => new external_value(PARAM_INT, 'The course to unenrol the user from'),
                    'roleid' => new external_value(PARAM_INT, 'The user role', VALUE_OPTIONAL),
                    'userid' => new external_value(PARAM_INT, 'The user that is going to be unenrolled')
                ])
            )
        ]);
    }

    public static function unenrol_users($enrolments)
    {
        global $CFG, $DB;
        require_once $CFG->libdir.'/enrollib.php';
        $params = self::validate_parameters(
            self::unenrol_users_parameters(),
            ['enrolments' => $enrolments]
        );
        $transaction = $DB->start_delegated_transaction();
        $enrol = enrol_get_plugin('cito1c');
        if (empty($enrol))
            throw new moodle_exception('pluginnotinstalled', 'enrol_cito1c');
        foreach ($params['enrolments'] as $enrolment)
        {
            $context = context_course::instance($enrolment['courseid']);
            self::validate_context($context);
            require_capability('enrol/cito1c:unenrol', $context);
            $instance = $DB->get_record('enrol', [
                'courseid' => $enrolment['courseid'],
                'enrol' => 'cito1c'
            ]);
            if (!$instance)
                throw new moodle_exception('wsnoinstance', 'enrol_cito1c', $enrolment);
            $user = $DB->get_record('user', ['id' => $enrolment['userid']]);
            if (!$user)
                throw new invalid_parameter_exception('User id not exist: '.$enrolment['userid']);
            $enrol->unenrol_user($instance, $enrolment['userid']);
        }
        $transaction->allow_commit();
    }

    public static function unenrol_users_returns()
    {
        return null;
    }
}
