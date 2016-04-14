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
 * @package    mod_forumimproved
 * @subpackage backup-moodle2
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author Mark Nielsen
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/forumimproved/backup/moodle2/restore_forumimproved_stepslib.php'); // Because it exists (must)

/**
 * forum restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_forumimproved_activity_task extends restore_activity_task {

    /**
     * Given a comment area, return the itemname that contains the itemid mappings
     */
    public function get_comment_mapping_itemname($commentarea) {
        if ($commentarea == 'userposts_comments') {
            return 'user';
        }

        return $commentarea;
    }


    /**
     * @return stdClass
     */
    public function get_comment_file_annotation_info() {
        return (object) array(
            'component' => 'mod_forumimproved',
            'filearea' => 'comments',
        );
    }

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Choice only has one structure step
        $this->add_step(new restore_forumimproved_activity_structure_step('forumimproved_structure', 'forumimproved.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('forumimproved', array('intro'), 'forumimproved');
        $contents[] = new restore_decode_content('forumimproved_posts', array('message'), 'forumimproved_post');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        // List of forums in course
        $rules[] = new restore_decode_rule('HSUFORUMINDEX', '/mod/forumimproved/index.php?id=$1', 'course');
        // Forum by cm->id and forum->id
        $rules[] = new restore_decode_rule('HSUFORUMVIEWBYID', '/mod/forumimproved/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('HSUFORUMVIEWBYF', '/mod/forumimproved/view.php?f=$1', 'forumimproved');
        // Link to forum discussion
        $rules[] = new restore_decode_rule('HSUFORUMDISCUSSIONVIEW', '/mod/forumimproved/discuss.php?d=$1', 'forumimproved_discussion');
        // Link to discussion with parent and with anchor posts
        $rules[] = new restore_decode_rule('HSUFORUMDISCUSSIONVIEWPARENT', '/mod/forumimproved/discuss.php?d=$1&parent=$2',
                                           array('forumimproved_discussion', 'forumimproved_post'));
        $rules[] = new restore_decode_rule('HSUFORUMDISCUSSIONVIEWINSIDE', '/mod/forumimproved/discuss.php?d=$1#$2',
                                           array('forumimproved_discussion', 'forumimproved_post'));

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * forum logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('forumimproved', 'add', 'view.php?id={course_module}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'update', 'view.php?id={course_module}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'view', 'view.php?id={course_module}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'view forum', 'view.php?id={course_module}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'mark read', 'view.php?f={forumimproved}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'start tracking', 'view.php?f={forumimproved}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'stop tracking', 'view.php?f={forumimproved}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'subscribe', 'view.php?f={forumimproved}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'unsubscribe', 'view.php?f={forumimproved}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'subscriber', 'subscribers.php?id={forumimproved}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'subscribers', 'subscribers.php?id={forumimproved}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'view subscribers', 'subscribers.php?id={forumimproved}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'add discussion', 'discuss.php?d={forumimproved_discussion}', '{forumimproved_discussion}');
        $rules[] = new restore_log_rule('forumimproved', 'view discussion', 'discuss.php?d={forumimproved_discussion}', '{forumimproved_discussion}');
        $rules[] = new restore_log_rule('forumimproved', 'move discussion', 'discuss.php?d={forumimproved_discussion}', '{forumimproved_discussion}');
        $rules[] = new restore_log_rule('forumimproved', 'delete discussi', 'view.php?id={course_module}', '{forumimproved}',
                                        null, 'delete discussion');
        $rules[] = new restore_log_rule('forumimproved', 'delete discussion', 'view.php?id={course_module}', '{forumimproved}');
        $rules[] = new restore_log_rule('forumimproved', 'add post', 'discuss.php?d={forumimproved_discussion}&parent={forumimproved_post}', '{forumimproved_post}');
        $rules[] = new restore_log_rule('forumimproved', 'update post', 'discuss.php?d={forumimproved_discussion}#p{forumimproved_post}&parent={forumimproved_post}', '{forumimproved_post}');
        $rules[] = new restore_log_rule('forumimproved', 'update post', 'discuss.php?d={forumimproved_discussion}&parent={forumimproved_post}', '{forumimproved_post}');
        $rules[] = new restore_log_rule('forumimproved', 'prune post', 'discuss.php?d={forumimproved_discussion}', '{forumimproved_post}');
        $rules[] = new restore_log_rule('forumimproved', 'delete post', 'discuss.php?d={forumimproved_discussion}', '[post]');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('forumimproved', 'view forums', 'index.php?id={course}', null);
        $rules[] = new restore_log_rule('forumimproved', 'subscribeall', 'index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('forumimproved', 'unsubscribeall', 'index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('forumimproved', 'user report', 'user.php?course={course}&id={user}&mode=[mode]', '{user}');
        $rules[] = new restore_log_rule('forumimproved', 'search', 'search.php?id={course}&search=[searchenc]', '[search]');

        return $rules;
    }
}
