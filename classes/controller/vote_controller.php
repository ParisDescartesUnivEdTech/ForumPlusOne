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
 * @subpackage forumimproved
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author     Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forumimproved\controller;

use coding_exception;
use mod_forumimproved\response\json_response;
use mod_forumimproved\service\discussion_service;
use mod_forumimproved\service\post_service;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/controller_abstract.php');

class vote_controller extends controller_abstract {
    /**
     * @var post_service
     */
    protected $postservice;

    public function init($action) {
        parent::init($action);

        require_once(dirname(__DIR__).'/response/json_response.php');
        require_once(dirname(__DIR__).'/service/post_service.php');
        require_once(dirname(__DIR__).'/service/discussion_service.php');
        require_once(dirname(dirname(__DIR__)).'/lib.php');

        $discussionservice = new discussion_service();
        $this->postservice = new post_service($discussionservice);
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
     * Add a vote to a post
     *
     * @return json_response
     */
    public function vote_action() {
        global $PAGE, $USER, $DB;


        $postid = required_param('postid', PARAM_INT);



        if (! $post = forumimproved_get_post_full($postid)) {
            print_error('invalidpostid', 'forumimproved');
        }
        if (! $discussion = $DB->get_record("forumimproved_discussions", array("id" => $post->discussion))) {
            print_error('notpartofdiscussion', 'forumimproved');
        }
        if (! $forum = $DB->get_record("forumimproved", array("id" => $discussion->forum))) {
            print_error('invalidforumid', 'forumimproved');
        }
        if (! $course = $DB->get_record("course", array("id" => $discussion->course))) {
            print_error('invalidcourseid');
        }
        if (! $cm = get_coursemodule_from_instance("forumimproved", $forum->id, $course->id)) {
            print_error('invalidcoursemodule');
        }



        // Make sure user can vote here
        if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
            $groupmode =  $cm->groupmode;
        } else {
            $groupmode = $course->groupmode;
        }
        if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $PAGE->context)) {
            if ($discussion->groupid == -1) {
                print_error('nopostforum', 'forumimproved');
            } else {
                if (!groups_is_member($discussion->groupid)) {
                    print_error('nopostforum', 'forumimproved');
                }
            }
        }


        if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $PAGE->context)) {
            print_error("activityiscurrentlyhidden");
        }

        $forum   = $PAGE->activityrecord;
        if (!forumimproved_is_discussion_open($forum, $discussion)) {
            print_error('discussion_closed', 'forumimproved');
        }



        try {
            return $this->postservice->handle_vote($forum, $postid, $USER->id);
        } catch (\Exception $e) {
            return new json_response($e);
        }
    }

    /**
     * Show who vote for a post
     *
     * @return json_response
     */
    public function whovote_action() {
        global $PAGE, $USER, $DB;


        $postid = required_param('postid', PARAM_INT);
        $sort = optional_param('sort', '', PARAM_ALPHA);



        if (! $post = forumimproved_get_post_full($postid)) {
            print_error('invalidpostid', 'forumimproved');
        }
        if (! $discussion = $DB->get_record("forumimproved_discussions", array("id" => $post->discussion))) {
            print_error('notpartofdiscussion', 'forumimproved');
        }
        if (! $forum = $DB->get_record("forumimproved", array("id" => $discussion->forum))) {
            print_error('invalidforumid', 'forumimproved');
        }
        if (! $course = $DB->get_record("course", array("id" => $discussion->course))) {
            print_error('invalidcourseid');
        }
        if (! $cm = get_coursemodule_from_instance("forumimproved", $forum->id, $course->id)) {
            print_error('invalidcoursemodule');
        }



        // Make sure user can vote here
        if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
            $groupmode =  $cm->groupmode;
        } else {
            $groupmode = $course->groupmode;
        }
        if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $PAGE->context)) {
            if ($discussion->groupid == -1) {
                print_error('nopostforum', 'forumimproved');
            } else {
                if (!groups_is_member($discussion->groupid)) {
                    print_error('nopostforum', 'forumimproved');
                }
            }
        }


        if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $PAGE->context)) {
            print_error("activityiscurrentlyhidden");
        }

        $forum   = $PAGE->activityrecord;
        if (forumimproved_is_discussion_hidden($forum, $discussion)) {
            print_error('discussion_hidden', 'forumimproved');
        }

        if ($forum->vote_display_name) {
            if (!has_capability('mod/forumimproved:viewwhovote', $PAGE->context)) {
                print_error('vote_view_forbidden', 'forumimproved');
            }
        }
        else {
            if (!has_capability('mod/forumimproved:viewwhovote_annonymousvote', $PAGE->context)) {
                print_error('vote_view_forbidden', 'forumimproved');
            }
        }

        if (!$forum->enable_vote) {
            print_error('vote_disabled_error', 'forumimproved');
        }

        $sqlsort = '';
        switch ($sort) {
            case 'datetime': $sqlsort = "v.timestamp ASC"; break;
            default:         $sqlsort = "u.firstname ASC";
        }


        try {
            return $this->postservice->handle_whovote($postid, $sqlsort, $PAGE->context, $course->id);
        } catch (\Exception $e) {
            return new json_response($e);
        }
    }
}
