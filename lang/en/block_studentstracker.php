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
$string['days'] = 'Number of days to start tracking';
$string['days_desc'] = '';
$string['days_critical'] = 'Critical limit (in days)';
$string['days_critical_desc'] = '';
$string['color_days'] = 'Days color';
$string['color_days_critical'] = 'Critical days color';
$string['color_never'] = 'No access color';
$string['color_normal'] = 'Active students group color';
$string['header'] = 'Text near the users counter';
$string['text_header'] = 'All users';
$string['text_header_normal'] = 'Normal users';
// $string['header_fine'] = 'Text if everything\'s fine';
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
$string['checked'] = '';
$string['checked_desc'] = '';
$string['all_users'] = 'All users';
$string['group_group'] = 'Group students into absence groups';
