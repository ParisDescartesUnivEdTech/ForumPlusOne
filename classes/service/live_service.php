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
 * @package   mod_forumplusone
 * @copyright Copyright (c) 2013 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2 or later
 * @copyright Copyright (c) 2016 Paris Descartes University (http://www.univ-paris5.fr/)
 */

namespace mod_forumplusone\service;

//use mod_forumplusone\attachments;
//use mod_forumplusone\event\post_created;
//use mod_forumplusone\event\post_updated;
//use mod_forumplusone\upload_file;
use moodle_exception;
use mod_forumplusone\response\json_response as json_response;

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__DIR__).'/response/json_response.php');
//require_once(dirname(__DIR__).'/upload_file.php');
require_once(dirname(dirname(dirname(dirname(__DIR__)))).'/config.php');
require_once(dirname(dirname(__DIR__)).'/lib.php');

/**
 * @package   mod_forumplusone
 * @copyright Copyright (c) 2013 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2 or later
 * @copyright Copyright (c) 2016 Paris Descartes University (http://www.univ-paris5.fr/)
 */
class live_service {
    /**
     * @var \moodle_database
     */
    protected $db;

    public function __construct(\moodle_database $db = null) {
        global $DB;

        if (is_null($db)) {
            $db = $DB;
        }

        $this->db = $db;
    }

    /**
     * Send informations to update the list of discussion
     *
     * @param object $forum
     * @param int    $userid
     * @return json_response
     */
    public function handle_live_list_disc($forum, $userid, $course, $cm, $context, $renderer) {
        $response = array();

        try {
            $response['new'] = $this->get_new_discussions($forum, $course, $cm, $context, $renderer);
            $response['update'] = $this->get_updated_discussions($forum, $course, $cm, $context, $renderer);
            $response['del'] = $this->get_deleted_discussions($forum);

            $response['errorCode'] = 0;
        }
        catch (coding_exception $e) {
            $response['errorCode'] = $e->a;
            $response['errorMsg'] = get_string($e->a, 'forumplusone');
        }


        return new json_response((object) $response);
    }

    private function get_new_discussions($forum, $course, $cm, $context, $renderer) {
        $config = get_config('forumplusone');

        if ($forum->count_vote_mode == FORUMPLUSONE_COUNT_MODE_RECURSIVE) {
            $countVote = '(
                    SELECT COUNT(v.id)
                    FROM {forumplusone_vote} v, {forumplusone_posts} p2
                    WHERE v.postid = p2.id AND p2.discussion = p.discussion
                    ) countvote';
        }
        else {
            $countVote = '(
                    SELECT COUNT(v.id)
                    FROM {forumplusone_vote} v
                    WHERE v.postid = p.id
                    ) countvote';
        }


        $allnames = get_all_user_name_fields(true, 'u');
        $newDiscSql = '
            SELECT d.id, d.state, d.name discname, d.firstpost, u.id userid, ' . $allnames . ', ' . $countVote . ', (
                SELECT COUNT(p.id)
                FROM {forumplusone_posts} p
                WHERE p.discussion = d.id
                AND p.parent <> 0
                ) replies, (
                    SELECT MAX(p.created)
                    FROM {forumplusone_posts} p
                    WHERE p.discussion = d.id
                    AND p.parent <> 0
                ) rawlastpost, (
                    SELECT p.flags
                    FROM {forumplusone_posts} p
                    WHERE p.discussion = d.id
                    AND p.parent = 0
                ) flags, (
                    SELECT p.created
                    FROM {forumplusone_posts} p
                    WHERE p.discussion = d.id
                    AND p.parent = 0
                ) rawcreated, (
                    SELECT MAX(p2.created)
                    FROM {forumplusone_posts} p2
                    WHERE p2.discussion = p.discussion
                ) lastpostcreationdate

            FROM {forumplusone_discussions} d, {forumplusone_posts} p, {user} u
            WHERE d.firstpost = p.id
            AND p.userid = u.id
            AND d.id IN (
                SELECT l.objectid
                FROM {logstore_standard_log} l
                WHERE l.component = :componentname
                AND l.timecreated > :timelastreload
                AND l.other = :datas
                AND l.crud = :crud
                AND l.target = "discussion"
            )
        ';
        $newDiscParams = array(
            'componentname' => 'mod_forumplusone',
            'timelastreload' => time() - $config->livereloadrate,
            'datas' => serialize(array(
                'forumid' => $forum->id
                )),
            'crud' => 'c'
        );

        $newDiscs = $this->db->get_records_sql($newDiscSql, $newDiscParams);

        $returnedValues = array();

        $canChangeState = has_capability('mod/forumplusone:change_state_discussion', $context);

        foreach ($newDiscs as $disc) {
            $groups = groups_get_all_groups($course->id, 0, $cm->groupingid);
            $group = '';
            if (groups_get_activity_groupmode($cm, $course) > 0 && isset($groups[$disc->groupid])) {
                $group = $groups[$disc->groupid];
                $group = format_string($group->name);
            }

            $disc->revealed = '';
            if ($forum->anonymous && $disc->userid === $USER->id && $disc->post_reveal) {
                $nonanonymous = get_string('nonanonymous', 'mod_forumplusone');
                $disc->revealed = '<span class="label label-danger">'.$nonanonymous.'</span>';
            }




            $disc->fullthread = false;
            $disc->fullname = fullname($disc, has_capability('moodle/site:viewfullnames', $context));
            $disc->unread = '';
            $disc->postid = $disc->firstpost;
            $disc->name = $disc->discname;
            $disc->viewurl = new \moodle_url('/mod/forumplusone/discuss.php', array('d' => $disc->id));
            $subscribe = new \forumplusone_lib_discussion_subscribe($forum, $context);
            $disc->subscribe = $renderer->discussion_subscribe_link($cm, $disc, $subscribe) ;
            $disc->postflags  = implode(' ', $renderer->post_get_flags($disc, $cm, $disc->id));


            $userurl = new \moodle_url('/user/profile.php', array('id' => $disc->userid));
            $byuser = \html_writer::link($userurl, $disc->fullname);


            $returnedValues[]  = array(
                    'id' => $disc->id,
                    'postid' => $disc->postid,
                    /*'name' => $disc->discname,
                      'state' => $disc->state,
                      'by' => $byuser . ' ' . $group . ' ' . $disc->revealed,
                      'nbReply' => $disc->replies,
                      'timeLastReply' => forumplusone_absolute_time($disc->lastpostcreationdate, array('class' => 'forumplusone-thread-pubdate')),
                      'nbVotes' => $disc->countvote,
                      'canChangeState' => $canChangeState,*/
                    'html' => $renderer->discussion_template($disc, $forum, $cm)
                    );
        }

        return $returnedValues;
    }

    private function get_updated_discussions($forum, $course, $cm, $context, $renderer) {
        $config = get_config('forumplusone');

        if ($forum->count_vote_mode == FORUMPLUSONE_COUNT_MODE_RECURSIVE) {
            $countVote = '(
                    SELECT COUNT(v.id)
                    FROM {forumplusone_vote} v, {forumplusone_posts} p2
                    WHERE v.postid = p2.id AND p2.discussion = p.discussion
                    ) countvote';
        }
        else {
            $countVote = '(
                    SELECT COUNT(v.id)
                    FROM {forumplusone_vote} v
                    WHERE v.postid = p.id
                    ) countvote';
        }



        $discIdDelVotesAndPost = $this->db->get_records_sql(
            '
            -- Event : delete vote
            SELECT l.other
            FROM {logstore_standard_log} l
            WHERE l.component = "mod_forumplusone"
              AND l.other LIKE :datas_del_vote
              AND l.crud = "d"
              AND l.target = "vote"
              AND l.timecreated > :timelastreload1
            GROUP BY l.other

            UNION

            -- Event : delete post
            SELECT l.other
            FROM {logstore_standard_log} l
            WHERE l.component = "mod_forumplusone"
              AND l.timecreated > :timelastreload2
              AND l.other LIKE :datas_del_post
              AND l.crud = "d"
              AND l.target = "post"
            ',
            array(
                'datas_del_vote' => 'a:3:{s:7:"forumid";' . serialize($forum->id) . 's:12:"discussionid";s:%";s:6:"postid";i:%;}',
                'datas_del_post' => 'a:3:{s:12:"discussionid";s:%";s:7:"forumid";' . serialize($forum->id) . 's:9:"forumtype";%;}',
                'timelastreload1' => time() - $config->livereloadrate,
                'timelastreload2' => time() - $config->livereloadrate,
            )
        );

        $discIdVoteAndPostDeleted = array();

        foreach ($discIdDelVotesAndPost as $objDatas) {
            $discIdVoteAndPostDeleted[] = unserialize($objDatas->other)['discussionid'];
        }

        if (empty($discIdVoteAndPostDeleted))
            $discIdVoteAndPostDeleted = '(0)';
        else
            $discIdVoteAndPostDeleted = '(' . implode(', ', $discIdVoteAndPostDeleted) . ')';







        $allnames = get_all_user_name_fields(true, 'u');
        $selectDisc = '
            SELECT d.id, d.state, d.name discname, d.firstpost, u.id userid, ' . $allnames . ', ' . $countVote . ', (
                SELECT COUNT(p.id)
                FROM {forumplusone_posts} p
                WHERE p.discussion = d.id
                AND p.parent <> 0
                ) replies, (
                    SELECT MAX(p.created)
                    FROM {forumplusone_posts} p
                    WHERE p.discussion = d.id
                    AND p.parent <> 0
                ) rawlastpost, (
                    SELECT p.flags
                    FROM {forumplusone_posts} p
                    WHERE p.discussion = d.id
                    AND p.parent = 0
                ) flags, (
                    SELECT p.created
                    FROM {forumplusone_posts} p
                    WHERE p.discussion = d.id
                    AND p.parent = 0
                ) rawcreated, (
                    SELECT MAX(p3.created)
                    FROM {forumplusone_posts} p3
                    WHERE p3.discussion = p.discussion
                ) lastpostcreationdate';
        $upDiscSql = '
            -- Event : update discussion
            ' . $selectDisc . '
            FROM {forumplusone_discussions} d, {forumplusone_posts} p, {user} u
            WHERE d.firstpost = p.id
              AND p.userid = u.id

              AND d.id IN (
                SELECT l.objectid
                FROM {logstore_standard_log} l
                WHERE l.component = :componentname1
                  AND l.timecreated > :timelastreload1
                  AND l.other = :datas_update_disc
                  AND l.crud = "u"
                  AND l.target = "discussion"
              )

            UNION

            -- Event : create post
            ' . $selectDisc . '
            FROM {forumplusone_discussions} d, {forumplusone_posts} p, {user} u, {forumplusone_posts} p4
            WHERE d.firstpost = p.id
              AND p.userid = u.id

              AND d.id = p4.discussion
              AND p4.id IN (
                SELECT l.objectid
                FROM {logstore_standard_log} l
                WHERE l.component = :componentname2
                  AND l.timecreated > :timelastreload2
                  AND l.other LIKE :datas_new_post
                  AND l.crud = "c"
                  AND l.target = "post"
              )
            GROUP BY d.id

            UNION

            -- Event : create vote
            ' . $selectDisc . '
            FROM {forumplusone_discussions} d, {forumplusone_posts} p, {user} u, {forumplusone_posts} p4, {forumplusone_vote} v2
            WHERE d.firstpost = p.id
              AND p.userid = u.id

              AND d.id = p4.discussion
              AND p4.id = v2.postid
              AND v2.id IN (
                SELECT l.objectid
                FROM {logstore_standard_log} l
                WHERE l.component = :componentname4
                  AND l.timecreated > :timelastreload4
                  AND l.other LIKE :datas_new_vote
                  AND l.crud = "c"
                  AND l.target = "vote"
              )
            GROUP BY d.id

            UNION

            -- Event : delete vote & post
            ' . $selectDisc . '
            FROM {forumplusone_discussions} d, {forumplusone_posts} p, {user} u
            WHERE d.firstpost = p.id
              AND p.userid = u.id

              AND d.id IN ' . $discIdVoteAndPostDeleted . '
            GROUP BY d.id
        ';
        $upDiscParams = array(
            'componentname1' => 'mod_forumplusone',
            'componentname2' => 'mod_forumplusone',
            'componentname3' => 'mod_forumplusone',
            'componentname4' => 'mod_forumplusone',
            'timelastreload1' => time() - $config->livereloadrate,
            'timelastreload2' => time() - $config->livereloadrate,
            'timelastreload3' => time() - $config->livereloadrate,
            'timelastreload4' => time() - $config->livereloadrate,
            'datas_update_disc' => serialize(array(
                'forumid' => $forum->id
            )),
            'datas_new_post' => 'a:3:{s:12:"discussionid";s:%";s:7:"forumid";' . serialize($forum->id) . 's:9:"forumtype";%;}',
            'datas_new_vote' => 'a:3:{s:7:"forumid";' . serialize($forum->id) . 's:12:"discussionid";s:%";s:6:"postid";i:%;}',
        );

        $upDiscs = $this->db->get_records_sql($upDiscSql, $upDiscParams);

        $returnedValues = array();

        $canChangeState = has_capability('mod/forumplusone:change_state_discussion', $context);

        foreach ($upDiscs as $disc) {
            $groups = groups_get_all_groups($course->id, 0, $cm->groupingid);
            $group = '';
            if (groups_get_activity_groupmode($cm, $course) > 0 && isset($groups[$disc->groupid])) {
                $group = $groups[$disc->groupid];
                $group = format_string($group->name);
            }

            $disc->revealed = '';
            if ($forum->anonymous && $disc->userid === $USER->id && $disc->post_reveal) {
                $nonanonymous = get_string('nonanonymous', 'mod_forumplusone');
                $disc->revealed = '<span class="label label-danger">'.$nonanonymous.'</span>';
            }




            $disc->fullthread = false;
            $disc->fullname = fullname($disc, has_capability('moodle/site:viewfullnames', $context));
            $disc->unread = '';
            $disc->postid = $disc->firstpost;
            $disc->name = $disc->discname;
            $disc->viewurl = new \moodle_url('/mod/forumplusone/discuss.php', array('d' => $disc->id));
            $subscribe = new \forumplusone_lib_discussion_subscribe($forum, $context);
            $disc->subscribe = $renderer->discussion_subscribe_link($cm, $disc, $subscribe) ;
            $disc->postflags  = implode(' ', $renderer->post_get_flags($disc, $cm, $disc->id));


            $userurl = new \moodle_url('/user/profile.php', array('id' => $disc->userid));
            $byuser = \html_writer::link($userurl, $disc->fullname);


            $returnedValues[]  = array(
                'id' => $disc->id,
                'postid' => $disc->postid,
                /*'name' => $disc->discname,
                  'state' => $disc->state,
                  'by' => $byuser . ' ' . $group . ' ' . $disc->revealed,
                  'nbReply' => $disc->replies,
                  'timeLastReply' => forumplusone_absolute_time($disc->lastpostcreationdate, array('class' => 'forumplusone-thread-pubdate')),
                  'nbVotes' => $disc->countvote,
                  'canChangeState' => $canChangeState,*/
                'html' => $renderer->discussion_template($disc, $forum, $cm)
            );
        }

        return $returnedValues;
    }

    private function get_deleted_discussions($forum) {
        $config = get_config('forumplusone');

        $delDiscSql = '
            SELECT l.objectid id
            FROM {logstore_standard_log} l
            WHERE l.component = :componentname
              AND l.timecreated > :timelastreload
              AND l.other = :datas
              AND l.crud = :crud
              AND l.target = "discussion"
        ';
        $delDiscParams = array(
            'componentname' => 'mod_forumplusone',
            'timelastreload' => time() - $config->livereloadrate,
            'datas' => serialize(array(
                'forumid' => $forum->id
            )),
            'crud' => 'd'
        );

        $delDiscs = $this->db->get_records_sql($delDiscSql, $delDiscParams);

        $returnedValues = array();

        foreach ($delDiscs as $disc) {
            $returnedValues[] = $disc;
        }

        return $returnedValues;
    }

    /**
     * Send informations to update a discussion page
     *
     * @param object $forum
     * @param int    $discid
     * @param int    $userid
     * @return json_response
     */
    public function handle_live_disc($discid, $forum, $userid, $course, $cm, $context, $renderer) {
        $response = array();

        try {
            $response['isDel'] = $this->is_disc_del($discid, $forum);
            $response['disc'] = $this->discChanging($discid, $forum);

            /*if ($this->is_disc_updated($discid)) {
                $response['disc'] = $this->get_disc_info($discid, $forum);
            }*/

            $response['new'] = $this->get_new_posts($discid, $forum, $course, $cm, $context, $renderer);
            $response['update'] = $this->get_updated_posts($discid, $forum, $course, $cm, $context, $renderer);
            $response['del'] = $this->get_deleted_posts($discid, $forum);

            $response['errorCode'] = 0;
        }
        catch (coding_exception $e) {
            $response['errorCode'] = $e->a;
            $response['errorMsg'] = get_string($e->a, 'forumplusone');
        }


        return new json_response((object) $response);
    }

    private function is_disc_del($discid, $forum) {
        $delDiscSql = '
            SELECT l.objectid id
            FROM {logstore_standard_log} l
            WHERE l.component = :componentname
              AND l.other = :datas
              AND l.crud = :crud
              AND l.target = "discussion"
              AND l.objectid = :id
        ';
        $delDiscParams = array(
            'componentname' => 'mod_forumplusone',
            'datas' => serialize(array(
                'forumid' => $forum->id
            )),
            'crud' => 'd',
            'id' => $discid
        );

        $delDisc = $this->db->get_records_sql($delDiscSql, $delDiscParams);

        return sizeof($delDisc);
    }

    private function discChanging($discid, $forum) {
        $config = get_config('forumplusone');

        $upDiscSql = '
            SELECT d.state, d.name
            FROM {forumplusone_discussions} d
            WHERE d.id IN (
                SELECT l.objectid id
                FROM {logstore_standard_log} l
                WHERE l.component = :componentname
                  AND l.other = :datas
                  AND l.crud = :crud
                  AND l.target = "discussion"
                  AND l.objectid = :id
                  AND l.timecreated > :timelastreload
                GROUP BY l.objectid
            )
        ';
        $upDiscParams = array(
            'componentname' => 'mod_forumplusone',
            'datas' => serialize(array(
                'forumid' => $forum->id
            )),
            'crud' => 'u',
            'id' => $discid,
            'timelastreload' => time() - $config->livereloadrate,
        );

        return $this->db->get_record_sql($upDiscSql, $upDiscParams);
    }

    private function get_new_posts($discid, $forum, $course, $cm, $context, $renderer) {
        global $PAGE, $USER;

        $config = get_config('forumplusone');

        $allnames = get_all_user_name_fields(true, 'u');
        $newPostSql = '
           SELECT p.id, p.discussion, p.message, p.created, p.modified, p.reveal, p.flags, p.userid, p.privatereply, p.parent, p.messagetrust, p.messageformat,
                  p.created rawcreated, p.discussion discussionid,
                  ' . $allnames . ', u.email, u.picture, u.imagealt,
                  d.timestart, d.groupid, (
                SELECT COUNT(p2.id)
                FROM {forumplusone_posts} p2
                WHERE p2.parent = p.id
            ) replycount, (
                SELECT COUNT(v.id)
                FROM {forumplusone_vote} v
                WHERE v.postid = p.id
            ) votecount
            FROM {forumplusone_posts} p, {forumplusone_discussions} d, {user} u
            WHERE p.userid = u.id
              AND d.id = p.discussion
              AND p.id IN (
                SELECT l.objectid
                FROM {logstore_standard_log} l
                WHERE l.component = :componentname
                  AND l.other = :datas
                  AND l.crud = :crud
                  AND l.target = "post"
                  AND l.timecreated > :timelastreload
            )
        ';
        $newPostParams = array(
            'componentname' => 'mod_forumplusone',
            'datas' => serialize(array(
                'discussionid' => (string)$discid,
                'forumid' => $forum->id,
                'forumtype' => $forum->type
            )),
            'crud' => 'c',
            'timelastreload' => time() - $config->livereloadrate,
        );


        $newPost = $this->db->get_records_sql($newPostSql, $newPostParams);


        $returnedValues = array();
        $canreply = null;

        foreach ($newPost as $post) {
            if ($canreply == null) {
                $canreply = forumplusone_user_can_post($forum, $post, $USER, $cm, $course, $context);
                if (!$canreply and $forum->type !== 'news') {
                    if (isguestuser() or !isloggedin()) {
                        $canreply = true;
                    }
                    if (!is_enrolled($context) and !is_viewing($context)) {
                        // allow guests and not-logged-in to see the link - they are prompted to log in after clicking the link
                        // normal users with temporary guest access see this link too, they are asked to enrol instead
                        $canreply = enrol_selfenrol_available($course->id);
                    }
                }
            }

/*
            $post->fullname = fullname($post);
            $post->unread = true;
            $post->revealed = $forum->anonymous && $postuser->id === $USER->id && $post->reveal;
            $post->tools = implode(' ', $renderer->post_get_commands($post, $post, $cm, $canreply, false));

            $postuser = forumplusone_extract_postuser($post, $forum, $context);
            $postuser->user_picture->size = 100;
            $post->imagesrc = $postuser->user_picture->get_url($PAGE)->out();
*/


            $count = 0;
            $returnedValues[] = array(
                "id" => $post->id,
                "parent" => $post->parent,
                "html" => $renderer->post_walker(
                    $cm,
                    (object) array(
                        'id' => $post->discussion,
                        'timestart' => $post->timestart,
                        'groupid' => $post->groupid
                    ),
                    array($post),
                    (object) array(
                        'id' => $post->parent
                    ),
                    $canreply,
                    $count
                )
            );
        }

        return $returnedValues;
    }

    private function get_updated_posts($discid, $forum, $course, $cm, $context, $renderer) {
        global $PAGE, $USER;

        $config = get_config('forumplusone');




        $postIdDelVotes = $this->db->get_records_sql(
            'SELECT l.other
             FROM {logstore_standard_log} l
             WHERE l.component = "mod_forumplusone"
               AND l.other LIKE :datas_del_vote
               AND l.crud = "d"
               AND l.target = "vote"
               AND l.timecreated > :timelastreload',
            array(
                'datas_del_vote' => 'a:3:{s:7:"forumid";' . serialize($forum->id) . 's:12:"discussionid";' . serialize((string) $discid) . 's:6:"postid";i:%;}',
                'timelastreload' => time() - $config->livereloadrate,
            )
        );

        $postIdVoteDeleted = array();

        foreach ($postIdDelVotes as $objDatas) {
            $postIdVoteDeleted[] = unserialize($objDatas->other)['postid'];
        }

        if (empty($postIdVoteDeleted))
            $postIdVoteDeleted = '(0)';
        else
            $postIdVoteDeleted = '(' . implode(', ', $postIdVoteDeleted) . ')';






        $allnames = get_all_user_name_fields(true, 'u');
        $selectSql = '
            SELECT p.id, p.discussion, p.message, p.created, p.modified, p.reveal, p.flags, p.userid, p.privatereply, p.parent, p.messagetrust, p.messageformat,
                   p.created rawcreated, p.discussion discussionid,
                   ' . $allnames . ', u.email, u.picture, u.imagealt,
                   d.timestart, d.groupid, (
                       SELECT COUNT(p2.id)
                       FROM {forumplusone_posts} p2
                       WHERE p2.parent = p.id
                   ) replycount, (
                       SELECT COUNT(v.id)
                       FROM {forumplusone_vote} v
                       WHERE v.postid = p.id
                   ) votecount
        ';

        $upPostSql = '
            -- Event : update post
            ' . $selectSql . '
            FROM {forumplusone_posts} p, {forumplusone_discussions} d, {user} u
            WHERE p.userid = u.id
              AND d.id = p.discussion
              AND p.id IN (
                SELECT l.objectid
                FROM {logstore_standard_log} l
                WHERE l.component = "mod_forumplusone"
                  AND l.other = :datas_update_post
                  AND l.crud = "u"
                  AND l.target = "post"
                  AND l.timecreated > :timelastreload1
            )

            UNION

            -- Event : create vote
            ' . $selectSql . '
            FROM {forumplusone_posts} p, {forumplusone_discussions} d, {user} u, {forumplusone_vote} v
            WHERE p.userid = u.id
              AND d.id = p.discussion
              AND p.id = v.postid
              AND v.id IN (
                SELECT l.objectid
                FROM {logstore_standard_log} l
                WHERE l.component = "mod_forumplusone"
                  AND l.other LIKE :datas_new_vote
                  AND l.crud = "c"
                  AND l.target = "vote"
                  AND l.timecreated > :timelastreload2
            )

            UNION

            -- Event : del vote
            ' . $selectSql . '
            FROM {forumplusone_posts} p, {forumplusone_discussions} d, {user} u
            WHERE p.userid = u.id
              AND d.id = p.discussion
              AND p.id IN ' . $postIdVoteDeleted . '
        ';
        $upPostParams = array(
            'datas_update_post' => serialize(array(
                'discussionid' => (string)$discid,
                'forumid' => $forum->id,
                'forumtype' => $forum->type
            )),

            'datas_new_vote' => 'a:3:{s:7:"forumid";' . serialize($forum->id) . 's:12:"discussionid";' . serialize((string) $discid) . 's:6:"postid";i:%;}',
            'timelastreload1' => time() - $config->livereloadrate,
            'timelastreload2' => time() - $config->livereloadrate,
        );

        $upPost = $this->db->get_records_sql($upPostSql, $upPostParams);


        $returnedValues = array();
        $canreply = null;

        foreach ($upPost as $post) {
            if ($canreply == null) {
                $canreply = forumplusone_user_can_post($forum, $post, $USER, $cm, $course, $context);
                if (!$canreply and $forum->type !== 'news') {
                    if (isguestuser() or !isloggedin()) {
                        $canreply = true;
                    }
                    if (!is_enrolled($context) and !is_viewing($context)) {
                        // allow guests and not-logged-in to see the link - they are prompted to log in after clicking the link
                        // normal users with temporary guest access see this link too, they are asked to enrol instead
                        $canreply = enrol_selfenrol_available($course->id);
                    }
                }
            }

            $discussion = (object) array(
                'id' => $post->discussion,
                'timestart' => $post->timestart,
                'groupid' => $post->groupid
            );

            if ($post->parent == 0) {
                $post->isFirstPost = true;
                $post->replies = 0; // it will be fill by JS
                $post->stateForm = '';
                $post->iconState = '';
                $subscribe = new \forumplusone_lib_discussion_subscribe($forum, \context_module::instance($cm->id));
                $post->subscribe = $renderer->discussion_subscribe_link($cm, $discussion, $subscribe) ;
                $post->threadtitle = '';
            }

            $count = 0;
            $returnedValues[] = array(
                "id" => $post->id,
                "parent" => $post->parent,
                "html" => $renderer->post_walker(
                    $cm,
                    $discussion,
                    array($post),
                    (object) array(
                        'id' => $post->parent
                    ),
                    $canreply,
                    $count,
                    false
                )
            );
        }

        return $returnedValues;
    }

    private function get_deleted_posts($discid, $forum) {
        $delPostSql = '
            SELECT l.objectid id
            FROM {logstore_standard_log} l
            WHERE l.component = :componentname
              AND l.other = :datas
              AND l.crud = :crud
              AND l.target = "post"
        ';
        $delPostParams = array(
            'componentname' => 'mod_forumplusone',
            'datas' => serialize(array(
                'discussionid' => $discid,
                'forumid' => $forum->id,
                'forumtype' => $forum->type
            )),
            'crud' => 'd',
        );

        $delPost = $this->db->get_records_sql($delPostSql, $delPostParams);


        $returnedValues = array();

        foreach ($delPost as $post) {
            $returnedValues[] = array(
                "id" => $post->id,
            );
        }

        return $returnedValues;
    }

    /*private function is_disc_updated($discid, $forum) {
        $discSql = '
            SELECT d.name, d.state
            FROM {forumplusone_discussions} d
            WHERE d.id IN ( -- IN is used to avoid get an error if, accidently, more than one record are return
                SELECT l.objectid id
                FROM {logstore_standard_log} l
                WHERE l.component = :componentname
                  AND l.other = :datas
                  AND l.crud = :crud
                  AND l.target = "discussion"
                  AND l.objectid = :id
            )
        ';

        $discParams = array(
            'componentname' => 'mod_forumplusone',
            'datas' => serialize(array(
                'forumid' => $forum->id
            )),
            'crud' => 'd',
            'id' => $discid
        );

        return $this->db->get_record_sql($discSql, $discParams);

    }

    private function get_disc_info($discid, $forum) {
        $discSql = '
            SELECT d.name, d.state
            FROM {forumplusone_discussions} d
            WHERE d.id IN ( -- IN is used to avoid get an error if, accidently, more than one record are return
                SELECT l.objectid id
                FROM {logstore_standard_log} l
                WHERE l.component = :componentname
                  AND l.other = :datas
                  AND l.crud = :crud
                  AND l.target = "discussion"
                  AND l.objectid = :id
            )
        ';

        $discParams = array(
            'componentname' => 'mod_forumplusone',
            'datas' => serialize(array(
                'forumid' => $forum->id
            )),
            'crud' => 'd',
            'id' => $discid
        );

        return $this->db->get_record_sql($discSql, $discParams);

    }*/
}
