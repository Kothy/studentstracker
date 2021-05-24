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
$string['studentstracker:addinstance'] = 'Add a new block Students tracker';
$string['pluginname'] = 'Students tracker';
$string['blocktitle'] = 'Block title';
$string['days'] = 'Group 1 days';
$string['days_desc'] = 'Group 1 settings';
$string['days_critical'] = 'Group 2 days';
$string['days_critical_desc'] = 'Group 2 settings';
$string['days_fatal'] = 'Group 3 days';
$string['days_fatal_desc'] = 'Group 3 settings';
$string['color_days'] = 'Group 1 color';
$string['color_days_critical'] = 'Group 2 color';
$string['color_days_fatal'] = 'Group 3 color';
$string['color_never'] = 'Never active users color';
$string['color_normal'] = 'Active users color';
$string['header'] = 'Text near the users counter';
$string['text_header'] = 'All users';
$string['text_header_normal'] = 'Normal users';
$string['text_header_fine'] = 'All absence groups are empty.';
$string['text_never_content'] = 'Never accessed the course';
$string['text_normal_content'] = 'access recently';
$string['text_footer'] = 'Contact invitation message';
$string['text_footer_content'] = 'Contact them!';
$string['role'] = 'Roles to track';
$string['roles'] = 'Roles allowed to see the block';
$string['groups'] = 'Course groups to track';
$string['nogroups'] = 'None (all users)';
$string['truncate'] = 'Maximal number of users displayed';
$string['roleview'] = "Roles allowed to see block";
$string['roleview_desc'] = "";
$string['roletrack'] = "Roles to track";
$string['roletrack_desc'] = "";
$string['color_days_desc'] = '';
$string['color_days_critical_desc'] = '';
$string['color_never_desc'] = '';
$string['color_normal_desc'] = '';
$string['studentstracker:view'] = 'View Students tracker results';
$string['studentstracker:editadvance'] = 'Advanced editing within block';
$string['group_desc'] = 'Group description';
$string['group_color'] = 'Group color';
$string['group_days'] = 'Absence to track in this group (in days)';
$string['group_track'] = 'Show this group';
$string['tracking_groups'] = 'Absence tracking settings';
$string['group1_desc'] = 'Inactive for 1+ days';
$string['group2_desc'] = 'Inactive for 7+ days';
$string['group3_desc'] = 'Inactive for 28+ days';
$string['active_desc'] = 'Active in the last 24h';
$string['group1_desc_title'] = 'Group 1 description';
$string['group2_desc_title'] = 'Group 2 description';
$string['group3_desc_title'] = 'Group 3 description';
$string['active_desc_title'] = 'Active users description';
$string['absent_desc_title'] = 'Never active users description';
$string['g1_checked'] = 'Track Group 1';
$string['g2_checked'] = 'Track Group 2';
$string['g3_checked'] = 'Track Group 3';
$string['active_checked'] = 'Track active users';
$string['absent_checked'] = 'Track never active users';
$string['grouping_checked'] = 'Group users into absence groups in the block';
$string['all_users'] = 'All users';
$string['group_group'] = 'Group students into absence groups';
$string['empty'] = '';
$string['roles_header'] = 'Roles to track settings';
$string['colors_header'] = 'Absence groups color settings';
$string['days_header'] = 'Absence groups days settings';
$string['desc_header'] = 'Absence groups description settings';
$string['box_header'] = 'Absence groups diplay settings';
$string['colors_header_info'] = '(choose a color for each absence group)';
$string['days_header_info'] = '(input a minimal number of days of inactivity of users in each absence group)';
$string['desc_header_info'] = '(input a description for each absence group)';
$string['box_header_info'] = '(choose, which absence groups will be tracked)';
