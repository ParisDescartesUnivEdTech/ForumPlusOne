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
 * Edit Discussion or Post Controller
 *
 * @package    mod
 * @subpackage forumplusone
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author     Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forumplusone\controller;

use coding_exception;
use mod_forumplusone\response\json_response;
use mod_forumplusone\service\discussion_service;
use mod_forumplusone\service\post_service;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/controller_abstract.php');

class discussion_controller extends controller_abstract {
    protected $discussion_service;

    public function init($action) {
        parent::init($action);

        require_once(dirname(__DIR__).'/response/json_response.php');
        require_once(dirname(__DIR__).'/service/post_service.php');
        require_once(dirname(__DIR__).'/service/discussion_service.php');
        require_once(dirname(dirname(__DIR__)).'/lib.php');

        $this->discussion_service = new discussion_service();
    }

    /**
     * Do any security checks needed for the passed action
     *
     * @param string $action
     */
    public function require_capability($action) {
        // Checks are done in actions as they are more complex.
    }

    /**
     * Add a reply to a post
     *
     * @return json_response
     */
    public function changestate_action() {
        global $PAGE, $DB;

        $discussionid = required_param('discussionid', PARAM_INT);
        $state = required_param('state', PARAM_INT);

        $forum = $PAGE->activityrecord;


        $discussion = $DB->get_record("forumplusone_discussions", array("id" => $discussionid), '*', MUST_EXIST);
        $forum = $DB->get_record("forumplusone", array("id" => $discussion->forum), '*', MUST_EXIST);
        $course = $DB->get_record("course", array("id" => $discussion->course), '*', MUST_EXIST);
        if (! $cm = get_coursemodule_from_instance("forumplusone", $forum->id, $course->id)) {
            print_error('invalidcoursemodule');
        }


        // Make sure user can close this discussion
        if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
            $groupmode =  $cm->groupmode;
        } else {
            $groupmode = $course->groupmode;
        }
        if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $PAGE->context)) {
            if ($discussion->groupid == -1) {
                print_error('nopostforum', 'forumplusone');
            } else {
                if (!groups_is_member($discussion->groupid)) {
                    print_error('nopostforum', 'forumplusone');
                }
            }
        }

        if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $PAGE->context)) {
            print_error("activityiscurrentlyhidden");
        }


        require_capability('mod/forumplusone:change_state_discussion', $PAGE->context);



        try {
            return $this->discussion_service->handle_change_state($forum, $discussion, $state);
        } catch (\Exception $e) {
            return new json_response($e);
        }
    }
}
