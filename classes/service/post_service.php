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
 * Post services
 *
 * @package   mod_forumimproved
 * @copyright Copyright (c) 2013 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forumimproved\service;

use mod_forumimproved\attachments;
use mod_forumimproved\event\post_created;
use mod_forumimproved\event\post_updated;
use mod_forumimproved\response\json_response;
use mod_forumimproved\upload_file;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__DIR__).'/response/json_response.php');
require_once(dirname(__DIR__).'/upload_file.php');
require_once(dirname(dirname(__DIR__)).'/lib.php');

/**
 * @package   mod_forumimproved
 * @copyright Copyright (c) 2013 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_service {
    /**
     * @var discussion_service
     */
    protected $discussionservice;

    /**
     * @var \moodle_database
     */
    protected $db;

    public function __construct(discussion_service $discussionservice = null, \moodle_database $db = null) {
        global $DB;

        if (is_null($discussionservice)) {
            $discussionservice = new discussion_service();
        }
        if (is_null($db)) {
            $db = $DB;
        }
        $this->discussionservice = $discussionservice;
        $this->db = $db;
    }

    /**
     * Toggle a vote
     *
     * @param object $forum
     * @param int    $postid
     * @param int    $userid
     * @return json_response
     */
    public function handle_vote($forum, $postid, $userid) {
        $response = array();

        try {
            forumimproved_toggle_vote($forum, $postid, $userid);
            $response['errorCode'] = 0;
        }
        catch (coding_exception $e) {
            $response['errorCode'] = $e->a;
            $response['errorMsg'] = get_string($e->a, 'forumimproved');
        }


        return new json_response((object) $response);
    }

    /**
     * Show how vote for a post, describe by his id, sort by a collomn
     *
     * @param int $postid
     * @param string $sqlsort
     * @param \context_module $context
     * @return json_response
     */
    public function handle_whovote($postid, $sqlsort, \context_module $context, $courseid) {
        global $OUTPUT;

        $response = array();

        try {
            $votes = forumimproved_get_all_post_votes($postid, $sqlsort);

            $response['votes'] = array();
            $response['errorCode'] = 0;
            if ($votes) {
                $canSeeDatetime = has_capability('mod/forumimproved:viewvotedatetime', $context);
                foreach ($votes as $vote) {
                    $vote->id = $vote->userid;
                    $result = array();

                    if ($courseid) {
                        $result['usrpicture'] = $OUTPUT->user_picture($vote, array('courseid' => $courseid));
                    } else {
                        $result['usrpicture'] = $OUTPUT->user_picture($vote);
                    }
                    $result['fullname'] = fullname($vote);

                    if ($canSeeDatetime) {
                        $result['datetime'] = userdate($vote->timestamp);
                        $result['timestamp'] = $vote->timestamp;
                    }

                    $response['votes'][] = $result;
                }
            }

        }
        catch (coding_exception $e) {
            $response['errorCode'] = $e->a;
            $response['errorMsg'] = get_string($e->a, 'forumimproved');
        }

        return new json_response((object) $response);
    }

    /**
     * Does all the grunt work for adding a reply to a discussion
     *
     * @param object $course
     * @param object $cm
     * @param object $forum
     * @param \context_module $context
     * @param object $discussion
     * @param object $parent The parent post
     * @param array $options These override default post values, EG: set the post message with this
     * @return json_response
     */
    public function handle_reply($course, $cm, $forum, $context, $discussion, $parent, array $options) {
        $uploader = new upload_file(
            new attachments($forum, $context), \mod_forumimproved_post_form::attachment_options($forum)
        );

        $post   = $this->create_post_object($discussion, $parent, $context, $options);
        $errors = $this->validate_post($course, $cm, $forum, $context, $discussion, $post, $uploader);

        if (!empty($errors)) {
            return $this->create_error_response($errors);
        }
        $this->save_post($discussion, $post, $uploader);
        $this->trigger_post_created($course, $context, $cm, $forum, $discussion, $post);

        return new json_response((object) array(
            'eventaction'  => 'postcreated',
            'discussionid' => (int) $discussion->id,
            'postid'       => (int) $post->id,
            'livelog'      => get_string('postcreated', 'forumimproved'),
            'html'         => $this->discussionservice->render_full_thread($discussion->id),
        ));
    }

    /**
     * Does all the grunt work for updating a post
     *
     * @param object $course
     * @param object $cm
     * @param object $forum
     * @param \context_module $context
     * @param object $discussion
     * @param object $post
     * @param array $deletefiles
     * @param array $options These override default post values, EG: set the post message with this
     * @return json_response
     */
    public function handle_update_post($course, $cm, $forum, $context, $discussion, $post, array $deletefiles = array(), array $options) {

        $this->require_can_edit_post($forum, $context, $discussion, $post);

        $uploader = new upload_file(
            new attachments($forum, $context, $deletefiles), \mod_forumimproved_post_form::attachment_options($forum)
        );

        // Apply updates to the post.
        foreach ($options as $name => $value) {
            if (property_exists($post, $name)) {
                $post->$name = $value;
            }
        }
        $post->itemid = empty($options['itemid']) ? 0 : $options['itemid'];

        if (!$post->parent) {
            $post->subject = $options['name'];
        }

        $errors = $this->validate_post($course, $cm, $forum, $context, $discussion, $post, $uploader);
        if (!empty($errors)) {
            return $this->create_error_response($errors);
        }
        $this->save_post($discussion, $post, $uploader);

        // If the user has access to all groups and they are changing the group, then update the post.
        if (empty($post->parent) && has_capability('mod/forumimproved:movediscussions', $context)) {
            $this->db->set_field('forumimproved_discussions', 'groupid', $options['groupid'], array('id' => $discussion->id));
        }

        $this->trigger_post_updated($context, $forum, $discussion, $post);

        return new json_response((object) array(
            'eventaction'  => 'postupdated',
            'discussionid' => (int) $discussion->id,
            'postid'       => (int) $post->id,
            'livelog'      => get_string('postwasupdated', 'forumimproved'),
            'html'         => $this->discussionservice->render_full_thread($discussion->id),
        ));
    }

    /**
     * Require that the current user can edit the post or
     * discussion
     *
     * @param object $forum
     * @param \context_module $context
     * @param object $discussion
     * @param object $post
     */
    public function require_can_edit_post($forum, \context_module $context, $discussion, $post) {
        global $CFG, $USER;

        if (!($forum->type == 'news' && !$post->parent && $discussion->timestart > time())) {
            if (((time() - $post->created) > $CFG->maxeditingtime) and
                !has_capability('mod/forumimproved:editanypost', $context)
            ) {
                print_error('maxtimehaspassed', 'forumimproved', '', format_time($CFG->maxeditingtime));
            }
        }
        if (($post->userid <> $USER->id) && !has_capability('mod/forumimproved:editanypost', $context)) {
            print_error('cannoteditposts', 'forumimproved');
        }
    }

    /**
     * Creates the post object to be saved.
     *
     * @param object $discussion
     * @param object $parent The parent post
     * @param \context_module $context
     * @param array $options These override default post values, EG: set the post message with this
     * @return \stdClass
     */
    public function create_post_object($discussion, $parent, $context, array $options = array()) {
        $post                = new \stdClass;
        $post->course        = $discussion->course;
        $post->forum         = $discussion->forum;
        $post->discussion    = $discussion->id;
        $post->parent        = $parent->id;
        $post->reveal        = 0;
        $post->privatereply  = 0;
        $post->mailnow       = 0;
        $post->attachment    = '';
        $post->message       = '';
        $post->messageformat = FORMAT_MOODLE;
        $post->messagetrust  = trusttext_trusted($context);
        $post->itemid        = 0; // For text editor stuffs.
        $post->groupid       = ($discussion->groupid == -1) ? 0 : $discussion->groupid;
        $post->flags         = null;

        $strre = get_string('re', 'forumimproved');
        foreach ($options as $name => $value) {
            if (property_exists($post, $name)) {
                $post->$name = $value;
            }
        }
        return $post;
    }

    /**
     * Validates the submitted post and any submitted files
     *
     * @param object $course
     * @param object $cm
     * @param object $forum
     * @param \context_module $context
     * @param object $discussion
     * @param object $post
     * @param upload_file $uploader
     * @return moodle_exception[]
     */
    public function validate_post($course, $cm, $forum, $context, $discussion, $post, upload_file $uploader) {
        global $USER;

        $errors = array();
        if (!forumimproved_user_can_post($forum, $discussion, null, $cm, $course, $context)) {
            $errors[] = new \moodle_exception('nopostforum', 'forumimproved');
        }
        if (!empty($post->id)) {
            if (!(($post->userid == $USER->id && (has_capability('mod/forumimproved:replypost', $context)
                        || has_capability('mod/forumimproved:startdiscussion', $context))) ||
                has_capability('mod/forumimproved:editanypost', $context))
            ) {
                $errors[] = new \moodle_exception('cannotupdatepost', 'forumimproved');
            }
        }
        if (empty($post->id)) {
            $thresholdwarning = forumimproved_check_throttling($forum, $cm);
            if ($thresholdwarning !== false && $thresholdwarning->canpost === false) {
                $errors[] = new \moodle_exception($thresholdwarning->errorcode, $thresholdwarning->module, $thresholdwarning->additional);
            }
        }
        if (!$post->parent && forumimproved_str_empty($post->subject)) {
            $errors[] = new \moodle_exception('discnameisrequired', 'forumimproved');
        }
        if (forumimproved_str_empty($post->message)) {
            $errors[] = new \moodle_exception('messageisrequired', 'forumimproved');
        }

        if ($post->privatereply) {
            if (!has_capability('mod/forumimproved:allowprivate', $context)
                || !$forum->allowprivatereplies
            ) {
                $errors[] = new \moodle_exception('cannotmakeprivatereplies', 'forumimproved');
            }
        }

        if ($uploader->was_file_uploaded()) {
            try {
                $uploader->validate_files(empty($post->id) ? 0 : $post->id);
            } catch (\Exception $e) {
                $errors[] = $e;
            }
        }
        return $errors;
    }

    /**
     * Save the post to the DB
     *
     * @param object $discussion
     * @param object $post
     * @param upload_file $uploader
     */
    public function save_post($discussion, $post, upload_file $uploader) {
        $message = '';

        // Because the following functions require these...
        $post->forum     = $discussion->forum;
        $post->course    = $discussion->course;
        $post->timestart = $discussion->timestart;
        $post->timeend   = $discussion->timeend;

        if (!empty($post->id)) {
            forumimproved_update_post($post, null, $message, $uploader);
        } else {
            forumimproved_add_new_post($post, null, $message, $uploader);
        }
    }

    /**
     * Update completion info and trigger event
     *
     * @param object $course
     * @param \context_module $context
     * @param object $cm
     * @param object $forum
     * @param object $discussion
     * @param object $post
     */
    public function trigger_post_created($course, \context_module $context, $cm, $forum, $discussion, $post) {
        global $CFG;

        require_once($CFG->libdir.'/completionlib.php');

        // Update completion state
        $completion = new \completion_info($course);
        if ($completion->is_enabled($cm) &&
            ($forum->completionreplies || $forum->completionposts)
        ) {
            $completion->update_state($cm, COMPLETION_COMPLETE);
        }

        $params = array(
            'context'  => $context,
            'objectid' => $post->id,
            'other'    => array(
                'discussionid' => $discussion->id,
                'forumid'      => $forum->id,
                'forumtype'    => $forum->type,
            )
        );
        $event = post_created::create($params);
        $event->add_record_snapshot('forumimproved_posts', $post);
        $event->add_record_snapshot('forumimproved_discussions', $discussion);
        $event->trigger();
    }

    /**
     * Trigger event
     *
     * @param \context_module $context
     * @param object $forum
     * @param object $discussion
     * @param object $post
     */
    public function trigger_post_updated(\context_module $context, $forum, $discussion, $post) {
        global $USER;

        $params = array(
            'context'  => $context,
            'objectid' => $post->id,
            'other'    => array(
                'discussionid' => $discussion->id,
                'forumid'      => $forum->id,
                'forumtype'    => $forum->type,
            )
        );

        if ($post->userid !== $USER->id) {
            $params['relateduserid'] = $post->userid;
        }

        $event = post_updated::create($params);
        $event->add_record_snapshot('forumimproved_discussions', $discussion);
        $event->trigger();
    }

    /**
     * @param array $errors
     * @return json_response
     */
    public function create_error_response(array $errors) {
        global $PAGE;

        /** @var \mod_forumimproved_renderer $renderer */
        $renderer = $PAGE->get_renderer('mod_forumimproved');

        return new json_response((object) array(
            'errors' => true,
            'html'   => $renderer->validation_errors($errors),
        ));
    }
}
