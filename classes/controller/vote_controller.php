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
     * Add a reply to a post
     *
     * @return json_response
     */
    public function vote_action() {
        global $PAGE, $USER;

        try {
            $postid = required_param('postid', PARAM_INT);

            $forum   = $PAGE->activityrecord;

            return $this->postservice->handle_vote($forum, $postid, $USER->id);
        } catch (\Exception $e) {
            return new json_response($e);
        }
    }
}
