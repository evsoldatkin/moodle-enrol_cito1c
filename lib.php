<?php
defined('MOODLE_INTERNAL') || die();

class enrol_cito1c_plugin extends enrol_plugin
{
    public function can_delete_instance($instance)
    {
        $context = context_course::instance($instance->courseid);
        if (!has_capability('enrol/cito1c:manage', $context))
            return false;
        if (!enrol_is_enabled('cito1c'))
            return true;
        return false;
    }
    
    public function can_hide_show_instance($instance)
    {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/cito1c:manage', $context);
    }
    
    public function enrol_get_instance($courseid)
    {
        $instance = null;
        $enrolinstances = enrol_get_instances($courseid, true);
        foreach ($enrolinstances as $courseenrolinstance)
        {
            if ($courseenrolinstance->enrol == 'cito1c')
            {
                $instance = $courseenrolinstance;
                break;
            }
        }
        return $instance;
    }
    
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid)
    {
        global $DB;
        $record = $DB->get_record('enrol', [
            'courseid' => $data->courseid,
            'enrol' => 'cito1c'
        ]);
        $newid = $instances
            ? $instance->id
            : $this->add_instance($course, (array)$data);
        $step->set_mapping('enrol', $oldid, $newid);
    }
    
    public function restore_role_assignment($instance, $roleid, $userid, $contextid)
    {
        global $DB;
        $exists = $DB->record_exists('user_enrolments', [
            'enrolid' => $instance->id,
            'userid' => $userid
        ]);
        if ($exists)
            role_assign($roleid, $userid, $contextid, 'enrol_'.$instance->enrol, $instance->id);
    }

    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid, $oldinstancestatus)
    {
        global $DB;
        $exists = $DB->record_exists('user_enrolments', [
            'enrolid' => $instance->id,
            'userid' => $userid
        ]);
        if (!$exists)
            $this->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, $data->status);
    }
}
