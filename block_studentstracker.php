0<?php
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
 * Studentstracker block
 *
 * @package    block_studentstracker
 * @copyright  2015 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/blocks/studentstracker/locallib.php");

class block_studentstracker extends block_base {
    public function init() {
        global $COURSE;
        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', 'block_studentstracker');
        $this->courseid = $COURSE->id;
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return true;
    }

    public function instance_allow_config() {
        return true;
    }

    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle',
                'block_studentstracker');
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    public function debug_to_console($data) {
      $output = $data;
      if (is_array($output))
          $output = implode(',', $output);

      echo "<script>console.log('".$output."');</script>";
    }

    public function count_days_from_access($lastaccess) {
      if ($lastaccess == 0) {
        return -1;
      } else {
        $dateNow = intval(time());
        $datediff = $dateNow - $lastaccess;
        return $datediff / (60 * 60 * 24);
      }
    }

    public function getGroup($array, $lastaccess){
      if (empty($array) && $lastaccess <= 1){
        return 0;
      } elseif (empty($array)) {
        return -1;
      }
      $oldArray = $array;
      rsort($array);
      $i = count($array);
      foreach ($array as $item) {
        if ($lastaccess <= 1){
          return 0;
        }
        if ($lastaccess >= $item && $item >= 0) {
          return array_search($item, $oldArray) + 1;
        }
        $i--;
      }
      return -1;
    }

    public function get_content() {
        global $CFG, $COURSE, $USER, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $PAGE->requires->js_call_amd('block_studentstracker/ui', 'init',
        array());

        $context = context_course::instance($COURSE->id);
        if (has_capability('block/studentstracker:view', $context)) {
            $isgranted = true;
        }

        if ($isgranted == false && !is_siteadmin($USER->id)) {
            return $this->content;
        } else {
            $usercount = 0;
            $activeusercount = 0;

            $this->content = new stdClass();
            $this->content->items = array();

            $active_days = 0;
            // $level2_days = 0.5;
            // $level3_days = 1;
            // $level4_days = 2;

            //group 1
            $level2_days = !empty($this->config->days) ? $this->config->days
            : get_config('studentstracker', 'trackingdays');
            $this->debug_to_console("Pocet dni group1: ".$level2_days);

            //group2
            $level3_days = !empty($this->config->days_critical) ? $this->
            config->days_critical : get_config('studentstracker', 'trackingdays');
            $this->debug_to_console("Pocet dni group2: ".$level3_days);

            // group3
            $level4_days = !empty($this->config->days_fatal) ? $this->
            config->days_fatal : get_config(
                'studentstracker', 'trackingdays');
            $this->debug_to_console("Pocet dni group3: ".$level4_days);

            $group1Color = "";
            $group2Color = "";
            $group3Color = "";
            $colorActive = "";
            $colorNever = "";

            // active color
            $colorActive = !empty($this->config->color_normal) ?
            $this->config->color_normal :
            get_config('studentstracker', 'colordaysnormal');
            //$this->debug_to_console("Color active: ".$colorActive);

            // color group1
            $group1Color = !empty($this->config->color_days) ?
            $this->config->color_days : get_config(
                'studentstracker', 'colordays');
            //$this->debug_to_console("Group1 color: ".$group1Color);

            //color group2
            $group2Color = !empty($this->config->color_days_critical) ?
            $this->config->color_days_critical : get_config(
                'studentstracker', 'colordayscritical');
            //$this->debug_to_console("Color group2: ".$group2Color);

            // color group3
            $group3Color = !empty($this->config->color_days_fatal) ?
            $this->config->color_days_fatal : get_config('studentstracker', 'colordaysfatal');
            //$this->debug_to_console("Color group3: ".$group3Color);

            // never color
            $colorNever = !empty($this->config->color_days_absent) ?
            $this->config->color_days_absent : get_config('studentstracker', 'colordaysnever');
            $this->debug_to_console("Color never: ".$colorNever);

            $group1Desc = "";
            $group2Desc = "";
            $group3Desc = "";
            $descActive = "";
            $descNever = "";

            //desc active
            $descActive = !empty($this->config->text_active) ?
            $this->config->text_active : get_string('active_desc', 'block_studentstracker');
            $this->debug_to_console("Popis active: ".$descActive);

            //desc group1
            $group1Desc = !empty($this->config->text_normal) ?
            $this->config->text_normal : get_string('group1_desc', 'block_studentstracker');
            //$this->debug_to_console("Popis group1: ".$group1Desc);

            //desc group2
            $group2Desc = !empty($this->config->text_critical) ?
            $this->config->text_critical: get_string('group2_desc', 'block_studentstracker');
            //$this->debug_to_console("Popis group2: ".$group2Desc);

            //desc group3
            $group3Desc = !empty($this->config->text_fatal) ?
            $this->config->text_fatal : get_string('group3_desc', 'block_studentstracker');
            //$this->debug_to_console("Popis group3: ".$group3Desc);

            //desc never
            $descNever = !empty($this->config->text_never) ?
            $this->config->text_never : get_string('text_never_content', 'block_studentstracker');
            //$this->debug_to_console("Popis never: ".$descNever);

            $trackedroles = !empty($this->config->role) ?
            $this->config->role : explode(",", get_config(
                'studentstracker', 'roletrack'));
            $trackedgroups = !empty($this->config->groups) ?
            $this->config->groups : array();
            $truncate = !empty($this->config->truncate) ?
            $this->config->truncate : 6;

            if (!empty($this->config->text_header)) {
                $this->text_header = $this->config->text_header;
            } else {
                $this->text_header =  get_string('all_users',
                'block_studentstracker');
            }

            if (!empty($this->config->text_header_fine)) {
                $this->text_header_fine = $this->config->text_header_fine;
            } else {
                $this->text_header_fine = get_string('text_header_fine',
                'block_studentstracker');
            }
            if (!empty($this->config->text_never_content)) {
                $this->text_never_content = $this->config->text_never_content;
            } else {
                $this->text_never_content = get_string('text_never_content',
                'block_studentstracker');
            }

            if (!empty($this->config->text_header_normal)) {
                $this->text_header_normal = $this->config->text_header_normal;
            } else {
                $this->text_header_normal = "Users recently present";
            }

            // pridala som
            if (!empty($this->config->text_normal_content)) {
                $this->text_normal_content = $this->config->text_normal_content;
            } else {
                $this->text_normal_content = get_string('text_normal_content',
                'block_studentstracker');
            }

            if (!empty($this->config->text_footer_content)) {
                $this->text_footer = $this->config->text_footer_content;
            } else {
                $this->text_footer = get_string('text_footer_content',
                'block_studentstracker');
            }

            $enrols = get_enrolled_users($context, '', 0, 'u.*', null,
            0, 0, true);
            foreach ($enrols as $enrol) {
                $enrol->hasrole = studentstracker::has_role($trackedroles,
                $context->id, $enrol->id);
                if ((in_array("0", $trackedgroups) == false) &&
                (count($trackedgroups) > 0)) {
                    if (!(studentstracker::is_in_groups($trackedgroups,
                    $COURSE->id, $enrol->id))) {
                        continue;
                    }
                }
                $enrol->lastaccesscourse = $this->get_last_access(
                  $context->instanceid, $enrol->id);
            }

            $active_users = 0;
            $level2_users = 0;
            $level3_users = 0;
            $level4_users = 0;
            $levelnever_users = 0;

            //$groupify = False;

            $groupify = !empty($this->config->group_grouping) ?
            $this->config->group_grouping : get_config('studentstracker', 'groupgrouping');

            $groupifyGroupNeverDisplay = !empty($this->config->inactive_tracking) ?
            $this->config->inactive_tracking : get_config('studentstracker', 'absentchecked');

            $groupifyGroupActiveDisplay = !empty($this->config->active_tracking) ?
            $this->config->active_tracking : get_config('studentstracker', 'activechecked');

            $groupifyGroup1Display = !empty($this->config->group1_tracking) ?
            $this->config->group1_tracking : get_config('studentstracker', 'group1checked');

            $groupifyGroup2Display = !empty($this->config->group2_tracking) ?
            $this->config->group2_tracking : get_config('studentstracker', 'group2checked');

            $groupifyGroup3Display = !empty($this->config->group3_tracking) ?
            $this->config->group3_tracking : get_config('studentstracker', 'group3checked');

            $this->debug_to_console("Zobrazuj activnych ".$groupifyGroupActiveDisplay);
            $this->debug_to_console("Zobrazuj nikdy ".$groupifyGroupNeverDisplay);
            $this->debug_to_console("Zobrazuj group1 ".$groupifyGroup1Display);
            $this->debug_to_console("Zobrazuj group2 ".$groupifyGroup2Display);
            $this->debug_to_console("Zobrazuj group3 ".$groupifyGroup3Display);

            // $groupifyGroup1Display = True;
            // $groupifyGroup2Display = True;
            // $groupifyGroup3Display = True;
            // $groupifyGroupActiveDisplay = True;
            // $groupifyGroupNeverDisplay = True;

            $groupArray = array();
            if ($groupifyGroup1Display){
              array_push($groupArray, $level2_days);
            } else {
              array_push($groupArray, -1);
            }
            if ($groupifyGroup2Display){
              array_push($groupArray, $level3_days);
            } else {
              array_push($groupArray, -1);
            }
            if ($groupifyGroup3Display){
              array_push($groupArray, $level4_days);
            } else {
              array_push($groupArray, -1);
            }
            //$this->debug_to_console($groupArray);

            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $this->debug_to_console($enrol->id);
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  //$this->debug_to_console("Pocet dni: ". $result);
                  //$this->debug_to_console("Patri do skupiny: ".$this->getGroup($groupArray, $result));
                }
            }

            //active users
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true && $groupifyGroupActiveDisplay == True) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  //$this->debug_to_console("Pocet dni aktiv: ". $result);
                  if ($result >= 0 && $result <= 1) {//($result >= $active_days && $result < $level2_days)
                    $usercount++;
                    $active_users++;
                  }
                }
            }

            //group1 users
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true && $groupifyGroup1Display == True) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($this->getGroup($groupArray, $result) == 1){ //($result >= $level2_days && $result < $level3_days)
                    $usercount++;
                    $level2_users++;
                  }
                }
            }
            //group2 users
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true && $groupifyGroup2Display == True) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($this->getGroup($groupArray, $result) == 2){//($result >= $level3_days && $result < $level4_days)
                    $usercount++;
                    $level3_users++;
                  }
                }
            }
            //group3 users
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true && $groupifyGroup3Display == True) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($this->getGroup($groupArray, $result) == 3) {//($result >= $level4_days)
                    $usercount++;
                    $level4_users++;
                  }
                }
            }
            //level never
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true && $groupifyGroupNeverDisplay == True) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result == -1){
                    $usercount++;
                    $levelnever_users++;
                  }
                }
            }
            // header active
            if ($active_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group">
              <span class="badge badge-warning">'.$active_users.'</span>';
              $headertext2 .= $descActive.'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            // active items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true && $groupifyGroupActiveDisplay == True) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result >= 0 && $result <= 1){//($result >= $active_days && $result < $level2_days)
                    $output = "<li class='studentstracker-l1'
                    style='background:".$colorActive."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".
                    $this->profile($enrol, $context).
                            " - ".$descActive."</span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                }
            }
            // header group1
            if ($level2_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group">
              <span class="badge badge-warning">'.$level2_users.'</span>';
              $headertext2 .= $group1Desc.'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            // group1 items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true && $groupifyGroup1Display == True) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if  ($this->getGroup($groupArray, $result) == 1) {//($result >= $level2_days && $result < $level3_days)
                    $output = "<li class='studentstracker-l2'
                    style='background:".$group1Color."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".
                    $this->profile($enrol, $context)." - ".$group1Desc."</span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                }
            }
            // header group2
            if ($level3_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group">
              <span class="badge badge-warning">'.$level3_users.'</span>';
              $headertext2 .= $group2Desc.'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            // group2 items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true  && $groupifyGroup2Display == True) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if  ($this->getGroup($groupArray, $result) == 2) {//($result >= $level3_days && $result < $level4_days)
                    $output = "<li class='studentstracker-l3'
                    style='background:".$group2Color."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".
                    $this->profile($enrol, $context)." - ".$group2Desc." </span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                }
            }
            // header group3
            if ($level4_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group">
              <span class="badge badge-warning">'.$level4_users.'</span>';
              $headertext2 .= $group3Desc.'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            // group3 items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true  && $groupifyGroup3Display == True) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if  ($this->getGroup($groupArray, $result) == 3) {//($result >= $level4_days)
                    $output = "<li class='studentstracker-l4'
                    style='background:".$group3Color."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".
                    $this->profile($enrol, $context).
                            " - ".$group3Desc."</span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                }
            }
            // header level never
            if ($levelnever_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group">
              <span class="badge badge-warning">'
              .$levelnever_users.'</span>';
              $headertext2 .= $descNever.'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            //level never items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true && $groupifyGroupNeverDisplay == True) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result == -1){
                    $output = "<li class='studentstracker-never'
                    style='background:".$colorNever."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".
                    $this->profile($enrol, $context).
                              " - $descNever</span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                }
            }

            if ($usercount > 0) {
                $headertext = '<div class="studentstracker_header">
                <span class="badge badge-warning">'.$usercount.'</span>';
                $headertext .= "All users".'</div>';
                $footertext = '<div class="studentstracker_footer">'
                .'<button type="button" class="badge badge-warning">'
                .$this->text_footer.'</button>'.'</div>';
            } else {
                $headertext = '<div class="studentstracker_header">'.
                $this->text_header_fine.'</div>';
                $footertext = '';
            }

            $this->content->text = $headertext;
            $this->content->text .= "<ul id='studentstracker-list' data-show=".
            $truncate .">";
            foreach ($this->content->items as $item) {
                $this->content->text .= $item;
            }
            $this->content->text .= "</ul>";
            $this->content->text .= "<center><div id=\"tracker_showmore\">
            </div>\n<div id=\"tracker_showless\"></div></center>";
            $this->content->text .= $footertext;

            return $this->content;
        }
    }

    private function messaging($user) {
        global $DB;
        $userid = optional_param('user2', $user->id, PARAM_INT);
        $url = new moodle_url('/message/index.php');
        if ($user->id) {
            $url->param('id', $userid);
        }
        return html_writer::link($url, "<img src=\"../pix/t/message.png\">",
        array());
    }

    private function profile($user, $context) {
        global $DB;
        $url = new moodle_url('/user/view.php', array('id' => $user->id,
        'course' => $context->instanceid));
        return html_writer::link($url, "$user->firstname $user->lastname",
        array());
    }

    public function applicable_formats() {
        return array('all' => false, 'course' => true, 'course-index' => false);
    }

    private function get_last_access($courseid, $userid) {
        global $DB;
        $lastaccess = $DB->get_field('user_lastaccess', 'timeaccess', array(
          'courseid' => $courseid, 'userid' => $userid));
        return $lastaccess;
    }
}
