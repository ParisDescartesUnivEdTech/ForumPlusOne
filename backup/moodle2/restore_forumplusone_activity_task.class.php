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
 * @package    mod_forumplusone
 * @subpackage backup-moodle2
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author Mark Nielsen
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/forumplusone/backup/moodle2/restore_forumplusone_stepslib.php'); // Because it exists (must)

/**
 * forum restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_forumplusone_activity_task extends restore_activity_task {

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
            'component' => 'mod_forumplusone',
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
        $this->add_step(new restore_forumplusone_activity_structure_step('forumplusone_structure', 'forumplusone.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('forumplusone', array('intro'), 'forumplusone');
        $contents[] = new restore_decode_content('forumplusone_posts', array('message'), 'forumplusone_post');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        // List of forums in course
        $rules[] = new restore_decode_rule('FORUMPLUSONEINDEX', '/mod/forumplusone/index.php?id=$1', 'course');
        // Forum by cm->id and forum->id
        $rules[] = new restore_decode_rule('FORUMPLUSONEVIEWBYID', '/mod/forumplusone/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('FORUMPLUSONEVIEWBYF', '/mod/forumplusone/view.php?f=$1', 'forumplusone');
        // Link to forum discussion
        $rules[] = new restore_decode_rule('FORUMPLUSONEDISCUSSIONVIEW', '/mod/forumplusone/discuss.php?d=$1', 'forumplusone_discussion');
        // Link to discussion with parent and with anchor posts
        $rules[] = new restore_decode_rule('FORUMPLUSONEDISCUSSIONVIEWPARENT', '/mod/forumplusone/discuss.php?d=$1&parent=$2',
                                           array('forumplusone_discussion', 'forumplusone_post'));
        $rules[] = new restore_decode_rule('FORUMPLUSONEDISCUSSIONVIEWINSIDE', '/mod/forumplusone/discuss.php?d=$1#$2',
                                           array('forumplusone_discussion', 'forumplusone_post'));

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

        $rules[] = new restore_log_rule('forumplusone', 'add', 'view.php?id={course_module}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'update', 'view.php?id={course_module}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'view', 'view.php?id={course_module}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'view forum', 'view.php?id={course_module}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'mark read', 'view.php?f={forumplusone}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'start tracking', 'view.php?f={forumplusone}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'stop tracking', 'view.php?f={forumplusone}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'subscribe', 'view.php?f={forumplusone}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'unsubscribe', 'view.php?f={forumplusone}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'subscriber', 'subscribers.php?id={forumplusone}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'subscribers', 'subscribers.php?id={forumplusone}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'view subscribers', 'subscribers.php?id={forumplusone}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'add discussion', 'discuss.php?d={forumplusone_discussion}', '{forumplusone_discussion}');
        $rules[] = new restore_log_rule('forumplusone', 'view discussion', 'discuss.php?d={forumplusone_discussion}', '{forumplusone_discussion}');
        $rules[] = new restore_log_rule('forumplusone', 'move discussion', 'discuss.php?d={forumplusone_discussion}', '{forumplusone_discussion}');
        $rules[] = new restore_log_rule('forumplusone', 'delete discussi', 'view.php?id={course_module}', '{forumplusone}',
                                        null, 'delete discussion');
        $rules[] = new restore_log_rule('forumplusone', 'delete discussion', 'view.php?id={course_module}', '{forumplusone}');
        $rules[] = new restore_log_rule('forumplusone', 'add post', 'discuss.php?d={forumplusone_discussion}&parent={forumplusone_post}', '{forumplusone_post}');
        $rules[] = new restore_log_rule('forumplusone', 'update post', 'discuss.php?d={forumplusone_discussion}#p{forumplusone_post}&parent={forumplusone_post}', '{forumplusone_post}');
        $rules[] = new restore_log_rule('forumplusone', 'update post', 'discuss.php?d={forumplusone_discussion}&parent={forumplusone_post}', '{forumplusone_post}');
        $rules[] = new restore_log_rule('forumplusone', 'prune post', 'discuss.php?d={forumplusone_discussion}', '{forumplusone_post}');
        $rules[] = new restore_log_rule('forumplusone', 'delete post', 'discuss.php?d={forumplusone_discussion}', '[post]');

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

        $rules[] = new restore_log_rule('forumplusone', 'view forums', 'index.php?id={course}', null);
        $rules[] = new restore_log_rule('forumplusone', 'subscribeall', 'index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('forumplusone', 'unsubscribeall', 'index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('forumplusone', 'user report', 'user.php?course={course}&id={user}&mode=[mode]', '{user}');
        $rules[] = new restore_log_rule('forumplusone', 'search', 'search.php?id={course}&search=[searchenc]', '[search]');

        return $rules;
    }
}
