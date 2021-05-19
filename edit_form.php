<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Classes to enforce the various access rules that can apply to a quiz.
 *
 * @package    block_studentstracker
 * @copyright  2015 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->dirroot/blocks/studentstracker/locallib.php");
class block_studentstracker_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG, $COURSE, $USER;
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_studentstracker'));
        $mform->setDefault('config_title', get_string('pluginname', 'block_studentstracker'));
        $mform->setType('config_title', PARAM_TEXT);

        if (has_capability('block/studentstracker:editadvance', context_course::instance($COURSE->id)) or is_siteadmin($USER->id)) {

            $roles = studentstracker::get_roles();
            $rolesarray = array();
            foreach ($roles as $role) {
                $rolesarray[$role->id] = $role->shortname;
            }

            $select = $mform->addElement('select', 'config_role', get_string('role', 'block_studentstracker'), $rolesarray, null);
            $select->setMultiple(true);
            $mform->setDefault('config_role', get_config('studentstracker', 'roletrack'));

            $groups = groups_get_all_groups($this->block->courseid, $userid = 0, $groupingid = 0, $fields = 'g.*');
            $groupsarray = array();
            $groupsarray[0] = get_string('nogroups', 'block_studentstracker');
            foreach ($groups as $group) {
                $groupsarray[$group->id] = $group->name;
            }

            $select = $mform->addElement('select', 'config_groups', get_string('groups', 'block_studentstracker'),
                $groupsarray, null);
            $select->setMultiple(true);
            $mform->setDefault('config_groups', array());

            $mform->addElement('text', 'config_truncate', get_string('truncate', 'block_studentstracker'));
            $mform->setDefault('config_truncate', 10);
            $mform->setType('config_truncate', PARAM_INT);

            // $mform->addElement('text', 'config_text_header_fine', get_string('text_header_fine', 'block_studentstracker'));
            // $mform->setDefault('config_text_header_fine', get_string('text_header_fine', 'block_studentstracker'));
            // $mform->setType('config_text_header_fine', PARAM_TEXT);
            //
            // $mform->addElement('text', 'config_text_footer', get_string('text_footer', 'block_studentstracker'));
            // $mform->setDefault('config_text_footer', get_string('text_footer_content', 'block_studentstracker'));
            // $mform->setType('config_text_footer', PARAM_TEXT);
        }

        $mform->addElement('header', 'configheader', get_string('tracking_groups', 'block_studentstracker'));

        // ACTIVE

        $mform->addElement('html', '<p><h3 style="color: orange;">Active users</h3></p>');

        $mform->addElement('text', 'config_color_normal', get_string('group_color', 'block_studentstracker'));
        $mform->setDefault('config_color_normal', get_config('studentstracker', 'colordaysnormal'));
        $mform->setType('config_color_normal', PARAM_RAW);

        if (has_capability('block/studentstracker:editadvance', context_course::instance($COURSE->id)) or is_siteadmin($USER->id)) {

            $mform->addElement('text', 'config_text_active', get_string('group_desc', 'block_studentstracker'));
            $mform->setDefault('config_text_active', get_string('active_desc', 'block_studentstracker'));
            $mform->setType('config_text_active', PARAM_TEXT);
        }

        $mform->addElement('advcheckbox', 'active_tracking', get_string('group_track', 'block_studentstracker'));
        $mform->setDefault('active_tracking', get_config('studentstracker', 'activechecked'));
        $mform->setType('active_tracking', PARAM_BOOL);

        // GROUP1

        $mform->addElement('html', '<p><h3 style="color: orange;">Group 1</h3></p>');

        $mform->addElement('text', 'config_color_days', get_string('group_color', 'block_studentstracker'));
        $mform->setDefault('config_color_days', get_config('studentstracker', 'colordays'));
        $mform->setType('config_color_days', PARAM_RAW);

        if (has_capability('block/studentstracker:editadvance', context_course::instance($COURSE->id)) or is_siteadmin($USER->id)) {

            $mform->addElement('text', 'config_text_normal', get_string('group_desc', 'block_studentstracker'));
            $mform->setDefault('config_text_normal', get_string('group1_desc', 'block_studentstracker'));
            $mform->setType('config_text_normal', PARAM_TEXT);

            $mform->addElement('text', 'config_days', get_string('group_days', 'block_studentstracker'));
            $mform->setDefault('config_days', get_config('studentstracker', 'trackingdays'));
            $mform->setType('config_days', PARAM_INT);
        }

        $mform->addElement('advcheckbox', 'group1_tracking', get_string('group_track', 'block_studentstracker'));
        $mform->setDefault('group1_tracking', get_config('studentstracker', 'group1checked'));
        $mform->setType('group1_tracking', PARAM_BOOL);

        // GROUP 2

        $mform->addElement('html', '<p><h3 style="color: orange;">Group 2</h3></p>');

        $mform->addElement('text', 'config_color_days_critical', get_string('group_color', 'block_studentstracker'));
        $mform->setDefault('config_color_days_critical',  get_config('studentstracker', 'colordayscritical'));
        $mform->setType('config_color_days_critical', PARAM_RAW);

        if (has_capability('block/studentstracker:editadvance', context_course::instance($COURSE->id)) or is_siteadmin($USER->id)) {

            $mform->addElement('text', 'config_text_critical', get_string('group_desc', 'block_studentstracker'));
            $mform->setDefault('config_text_critical', get_string('group2_desc', 'block_studentstracker'));
            $mform->setType('config_text_critical', PARAM_TEXT);

            $mform->addElement('text', 'config_days_critical', get_string('group_days', 'block_studentstracker'));
            $mform->setDefault('config_days_critical', get_config('studentstracker', 'trackingdayscritical'));
            $mform->setType('config_days_critical', PARAM_INT);
        }

        $mform->addElement('advcheckbox', 'group2_tracking', get_string('group_track', 'block_studentstracker'));
        $mform->setDefault('group2_tracking', get_config('studentstracker', 'group2checked'));
        $mform->setType('group2_tracking', PARAM_BOOL);

        // GROUP 3

        $mform->addElement('html', '<p><h3 style="color: orange;">Group 3</h3></p>');

        $mform->addElement('text', 'config_color_days_fatal', get_string('group_color', 'block_studentstracker'));
        $mform->setDefault('config_color_days_fatal',  get_config('studentstracker', 'colordaysfatal'));
        $mform->setType('config_color_days_fatal', PARAM_RAW);

        if (has_capability('block/studentstracker:editadvance', context_course::instance($COURSE->id)) or is_siteadmin($USER->id)) {

            $mform->addElement('text', 'config_text_fatal', get_string('group_desc', 'block_studentstracker'));
            $mform->setDefault('config_text_fatal', get_string('group3_desc', 'block_studentstracker'));
            $mform->setType('config_text_fatal', PARAM_TEXT);

            $mform->addElement('text', 'config_days_fatal', get_string('group_days', 'block_studentstracker'));
            $mform->setDefault('config_days_fatal', get_config('studentstracker', 'trackingdaysfatal'));
            $mform->setType('config_days_fatal', PARAM_INT);
        }

        $mform->addElement('advcheckbox', 'group3_tracking', get_string('group_track', 'block_studentstracker'));
        $mform->setDefault('group3_tracking', get_config('studentstracker', 'group3checked'));
        $mform->setType('group3_tracking', PARAM_BOOL);

        // ABSENT

        $mform->addElement('html', '<p><h3 style="color: orange;">Enrolled users that never accessed the course</h3></p>');

        $mform->addElement('text', 'config_color_days_absent', get_string('group_color', 'block_studentstracker'));
        $mform->setDefault('config_color_days_absent',  get_config('studentstracker', 'colordayscritical'));
        $mform->setType('config_color_days_absent', PARAM_RAW);

        if (has_capability('block/studentstracker:editadvance', context_course::instance($COURSE->id)) or is_siteadmin($USER->id)) {

            $mform->addElement('text', 'config_text_never', get_string('group_desc', 'block_studentstracker'));
            $mform->setDefault('config_text_never', get_string('text_never_content', 'block_studentstracker'));
            $mform->setType('config_text_never', PARAM_TEXT);
        }

        $mform->addElement('advcheckbox', 'inactive_tracking', get_string('group_track', 'block_studentstracker'));
        $mform->setDefault('inactive_tracking', get_config('studentstracker', 'absentchecked'));
        $mform->setType('inactive_tracking', PARAM_BOOL);
    }
}
