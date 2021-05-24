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
 * Settings file
 *
 * @package    block_studentstracker
 * @copyright  2015 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once("$CFG->dirroot/blocks/studentstracker/locallib.php");

if ($ADMIN->fulltree) {
    $roles = studentstracker::get_roles();
    $rolesarray = array();
    foreach ($roles as $role) {
        $rolesarray[$role->id] = $role->shortname;
    }

    $settings->add(new admin_setting_heading(
      'studentstracker/rolesheading',
      get_string('roles_header', 'block_studentstracker'),
      get_string('empty', 'block_studentstracker'),
    ));

    $settings->add(new admin_setting_configmultiselect(
        'studentstracker/roletrack',
        get_string('roletrack', 'block_studentstracker'),
        get_string('roletrack_desc', 'block_studentstracker'),
        array('5'),
        $rolesarray));



    $settings->add(new admin_setting_heading(
      'studentstracker/days_heading',
      get_string('days_header', 'block_studentstracker'),
      get_string('days_header_info', 'block_studentstracker'),
    ));

    $settings->add(new admin_setting_configtext(
        'studentstracker/trackingdays',
        get_string('days', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'), '1'));

    $settings->add(new admin_setting_configtext(
        'studentstracker/trackingdayscritical',
        get_string('days_critical', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        '7'));

    $settings->add(new admin_setting_configtext(
        'studentstracker/trackingdaysfatal',
        get_string('days_fatal', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        '28'));



    $settings->add(new admin_setting_heading(
      'studentstracker/colors_heading',
      get_string('colors_header', 'block_studentstracker'),
      get_string('colors_header_info', 'block_studentstracker'),
    ));

    $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordays',
        get_string('color_days', 'block_studentstracker'),
        get_string('color_days_desc', 'block_studentstracker'),
        '#FFD9BA', null));

    $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordayscritical',
        get_string('color_days_critical', 'block_studentstracker'),
        get_string('color_days_critical_desc', 'block_studentstracker'),
        '#FECFCF', null));

    $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordaysfatal',
        get_string('color_days_fatal', 'block_studentstracker'),
        get_string('color_days_fatal_desc', 'block_studentstracker'),
        '#FF0000', null));

    $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordaysnormal',
        get_string('color_normal', 'block_studentstracker'),
        get_string('color_normal_desc', 'block_studentstracker'),
        '#5efa46', null));

    $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordaysnever',
        get_string('color_never', 'block_studentstracker'),
        get_string('color_never_desc', 'block_studentstracker'),
        '#e0e0e0', null));




    $settings->add(new admin_setting_heading(
      'studentstracker/desc_heading',
      get_string('desc_header', 'block_studentstracker'),
      get_string('desc_header_info', 'block_studentstracker'),
    ));

    $settings->add(new admin_setting_configtext(
      'studentstracker/desc_group1',
      get_string('group1_desc_title', 'block_studentstracker'),
      get_string('empty', 'block_studentstracker'),
      'Inactive for 1+ days'));

    $settings->add(new admin_setting_configtext(
      'studentstracker/desc_group2',
      get_string('group2_desc_title', 'block_studentstracker'),
      get_string('empty', 'block_studentstracker'),
      'Inactive for 7+ days'));

    $settings->add(new admin_setting_configtext(
        'studentstracker/desc_group3',
        get_string('group3_desc_title', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        'Inactive for 28+ days'));

    $settings->add(new admin_setting_configtext(
        'studentstracker/desc_active_group',
        get_string('active_desc_title', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        'Active users'));

    $settings->add(new admin_setting_configtext(
        'studentstracker/desc_absent_group',
        get_string('absent_desc_title', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        'Never active users'));


    $settings->add(new admin_setting_heading(
          'studentstracker/box_heading',
          get_string('box_header', 'block_studentstracker'),
          get_string('box_header_info', 'block_studentstracker'),
        ));

    $settings->add(new admin_setting_configcheckbox(
        'studentstracker/group1checked',
        get_string('g1_checked', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        1));

    $settings->add(new admin_setting_configcheckbox(
        'studentstracker/group2checked',
        get_string('g2_checked', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        1));

    $settings->add(new admin_setting_configcheckbox(
        'studentstracker/group3checked',
        get_string('g3_checked', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        1));

    $settings->add(new admin_setting_configcheckbox(
        'studentstracker/activechecked',
        get_string('active_checked', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        1));

    $settings->add(new admin_setting_configcheckbox(
        'studentstracker/absentchecked',
        get_string('absent_checked', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        1));

    $settings->add(new admin_setting_configcheckbox(
        'studentstracker/groupgrouping',
        get_string('grouping_checked', 'block_studentstracker'),
        get_string('empty', 'block_studentstracker'),
        1));
}
