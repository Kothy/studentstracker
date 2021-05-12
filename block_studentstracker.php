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
                $this->title = get_string('defaulttitle', 'block_studentstracker');
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    public function debug_to_console($data) {
      $output = $data;
      if (is_array($output))
          $output = implode(',', $output);

      echo "<script>console.log('" . $output . "');</script>";
    }

    public function count_days_from_access($lastaccess) {
      if ($lastaccess == 0) {
        return -1;
      } else {
        $dateNow = intval(time());
        $datediff = $dateNow - $lastaccess;
        // $this->debug_to_console(date("d-m-Y H:i:s", $dateNow));
        // $this->debug_to_console(date("d-m-Y H:i:s", $lastaccess));
        return $datediff / (60 * 60 * 24);
      }
    }

    public function get_content() {
        global $CFG, $COURSE, $USER, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $PAGE->requires->js_call_amd('block_studentstracker/ui', 'init', array());

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

            $days = !empty($this->config->days) ? '-'.$this->config->days.' day' : '-'.get_config(
                'studentstracker', 'trackingdays').' day';
            $dayscritical = !empty($this->config->days_critical) ? '-'.$this->config->days_critical.' day' : '-'.get_config(
                'studentstracker', 'trackingdays').' day';
            $colordays = !empty($this->config->color_days) ? $this->config->color_days : get_config(
                'studentstracker', 'colordays');
            $colordayscritical = !empty($this->config->color_days_critical) ? $this->config->color_days_critical : get_config(
                'studentstracker', 'colordayscritical');
            $colornever = !empty($this->config->color_never) ? $this->config->color_never : get_config(
                'studentstracker', 'colordaysnever');

            // pridala som
            $colornormal = !empty($this->config->color_normal) ? $this->config->color_normal : get_config(
                    'studentstracker', 'colordaysnormal');

            $trackedroles = !empty($this->config->role) ? $this->config->role : explode(",", get_config(
                'studentstracker', 'roletrack'));
            $trackedgroups = !empty($this->config->groups) ? $this->config->groups : array();
            $truncate = !empty($this->config->truncate) ? $this->config->truncate : 6;

            if (!empty($this->config->text_header)) {
                $this->text_header = $this->config->text_header;
            } else {
                //$this->text_header = get_string('text_header', 'block_studentstracker');
                $this->text_header = "All users";
            }

            if (!empty($this->config->text_header_fine)) {
                $this->text_header_fine = $this->config->text_header_fine;
            } else {
                $this->text_header_fine = get_string('text_header_fine', 'block_studentstracker');
            }
            if (!empty($this->config->text_never_content)) {
                $this->text_never_content = $this->config->text_never_content;
            } else {
                $this->text_never_content = get_string('text_never_content', 'block_studentstracker');
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
                $this->text_normal_content = get_string('text_normal_content', 'block_studentstracker');
            }

            if (!empty($this->config->text_footer_content)) {
                $this->text_footer = $this->config->text_footer_content;
            } else {
                $this->text_footer = get_string('text_footer_content', 'block_studentstracker');
            }

            $enrols = get_enrolled_users($context, '', 0, 'u.*', null, 0, 0, true);
            foreach ($enrols as $enrol) {
                $enrol->hasrole = studentstracker::has_role($trackedroles, $context->id, $enrol->id);
                if ((in_array("0", $trackedgroups) == false) && (count($trackedgroups) > 0)) {
                    if (!(studentstracker::is_in_groups($trackedgroups, $COURSE->id, $enrol->id))) {
                        continue;
                    }
                }
                $enrol->lastaccesscourse = $this->get_last_access($context->instanceid, $enrol->id);
            }

            $level1_days = 0;
            $level2_days = 0.01;
            $level3_days = 0.02;
            $level4_days = 0.2;
            $level5_days = 4;

            $level1_users = 0;
            $level2_users = 0;
            $level3_users = 0;
            $level4_users = 0;
            $level5_users = 0;

            $levelnever_users = 0;

            $groupify = True;

            //level 1 users
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  $this->debug_to_console("Pocet dni od prihlasenia: ".$result);
                  if ($result >= $level1_days && $result < $level2_days){
                    $usercount++;
                    $level1_users++;
                    // $this->debug_to_console("Nasiel som level 1");
                  }
                }
            }

            //level 2 users
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result >= $level2_days && $result < $level3_days){
                    $usercount++;
                    $level2_users++;
                    // $this->debug_to_console("Nasiel som level 2");
                  }
                }
            }

            //level 3 users
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result >= $level3_days && $result < $level4_days){
                    $usercount++;
                    $level3_users++;
                    //$this->debug_to_console("Nasiel som level 3");
                  }
                }
            }

            //level 4 users
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result >= $level4_days && $result < $level5_days){
                    $usercount++;
                    $level4_users++;
                    //$this->debug_to_console("Nasiel som level 4");
                  }
                }
            }

            //level 5 users
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result >= $level5_days){
                    $usercount++;
                    $level5_users++;
                    //$this->debug_to_console("Nasiel som level 5");
                  }
                }
            }

            //level never
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result == -1){
                    $usercount++;
                    $levelnever_users++;
                    //$this->debug_to_console("Nasiel som level never");
                  }
                }
            }

            // header level 1
            if ($level1_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group"><span class="badge badge-warning">'.$level1_users.'</span>';
              $headertext2 .= "Level 1 description".'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            // level1 items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result >= $level1_days && $result < $level2_days) {
                    $output = "<li class='studentstracker-l1' style='background:"."#9dff8c"."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".$this->profile($enrol, $context).
                            " - level1 item</span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                    // if ($enrol->lastaccesscourse < $level1_days) {
                    //     $output = "<li class='studentstracker-never' style='background:".$colornever."'>";
                    //     $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".$this->profile($enrol, $context).
                    //         " - $this->text_never_content</span></li>";
                    //     array_push($this->content->items, $output);
                    //     unset($output);
                    // }
                }
            }

            // header level2
            if ($level2_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group"><span class="badge badge-warning">'.$level1_users.'</span>';
              $headertext2 .= "Level 2 description".'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            // level2 items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result >= $level2_days && $result < $level3_days) {
                    $output = "<li class='studentstracker-l2' style='background:"."#9dff8c"."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".$this->profile($enrol, $context).
                            " - level2 item</span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                }
            }

            // header level3
            if ($level3_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group"><span class="badge badge-warning">'.$level1_users.'</span>';
              $headertext2 .= "Level 3 description".'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            // level3 items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result >= $level3_days && $result < $level4_days) {
                    $output = "<li class='studentstracker-l3' style='background:"."#9dff8c"."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".$this->profile($enrol, $context).
                            " - level3 item</span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                }
            }

            // header level4
            if ($level4_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group"><span class="badge badge-warning">'.$level1_users.'</span>';
              $headertext2 .= "Level 4 description".'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            // level4 items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result >= $level4_days && $result < $level5_days) {
                    $output = "<li class='studentstracker-l4' style='background:"."#9dff8c"."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".$this->profile($enrol, $context).
                            " - level4 item</span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                }
            }

            // header level5
            if ($level5_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group"><span class="badge badge-warning">'.$level1_users.'</span>';
              $headertext2 .= "Level 5 description".'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            // level5 items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result >= $level5_days) {
                    $output = "<li class='studentstracker-l5' style='background:"."#9dff8c"."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".$this->profile($enrol, $context).
                            " - level5 item</span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                }
            }

            // header level never
            if ($levelnever_users > 0 && $groupify){
              $headertext2 = '<br><div class="studentstracker_group"><span class="badge badge-warning">'.$levelnever_users.'</span>';
              $headertext2 .= $this->text_never_content.'</div><br>';
              array_push($this->content->items, $headertext2);
            }
            //level never items
            foreach ($enrols as $enrol) {
                if ($enrol->hasrole == true) {
                  $accessdays = intval($enrol->lastaccesscourse);
                  $result = $this->count_days_from_access($accessdays);
                  if ($result == -1){
                    $output = "<li class='studentstracker-never' style='background:".$colornever."'>";
                    $output .= $this->messaging($enrol)."<span> &nbsp;&nbsp;".$this->profile($enrol, $context).
                              " - $this->text_never_content</span></li>";
                    array_push($this->content->items, $output);
                    unset($output);
                  }
                }
            }

            if ($usercount > 0) {
                $headertext = '<div class="studentstracker_header"><span class="badge badge-warning">'.$usercount.'</span>';
                $headertext .= "All users".'</div>';

                $footertext = '<div class="studentstracker_footer">'.$this->text_footer.'</div>';

            } else {
                $headertext = '<div class="studentstracker_header">'.$this->text_header_fine.'</div>';
                $footertext = '';
            }

            $this->content->text = $headertext;
            $this->content->text .= "<ul id='studentstracker-list' data-show=". $truncate .">";
            foreach ($this->content->items as $item) {
                $this->content->text .= $item;
            }
            $this->content->text .= "</ul>";
            $this->content->text .= "<center><div id=\"tracker_showmore\"></div>\n<div id=\"tracker_showless\"></div></center>";
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
        return html_writer::link($url, "<img src=\"../pix/t/message.png\">", array());
    }

    private function profile($user, $context) {
        global $DB;
        $url = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $context->instanceid));
        return html_writer::link($url, "$user->firstname $user->lastname", array());
    }

    public function applicable_formats() {
        return array('all' => false, 'course' => true, 'course-index' => false);
    }

    private function get_last_access($courseid, $userid) {
        global $DB;
        $lastaccess = $DB->get_field('user_lastaccess', 'timeaccess', array('courseid' => $courseid, 'userid' => $userid));
        return $lastaccess;
    }
}
