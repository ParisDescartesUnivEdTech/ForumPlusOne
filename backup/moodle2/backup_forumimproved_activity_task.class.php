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
 * Defines backup_forumimproved_activity_task class
 *
 * @package   mod_forumimproved
 * @category  backup
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author Mark Nielsen
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/forumimproved/backup/moodle2/backup_forumimproved_stepslib.php');
require_once($CFG->dirroot.'/mod/forumimproved/backup/moodle2/backup_forumimproved_settingslib.php');

/**
 * Provides the steps to perform one complete backup of the Forum instance
 */
class backup_forumimproved_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Defines a backup step to store the instance data in the forumimproved.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_forumimproved_activity_structure_step('forumimproved_structure', 'forumimproved.xml'));
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
     * Encodes URLs to the index.php, view.php and discuss.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the list of forums
        $search="/(".$base."\/mod\/forumimproved\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@HSUFORUMINDEX*$2@$', $content);

        // Link to forum view by moduleid
        $search="/(".$base."\/mod\/forumimproved\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@HSUFORUMVIEWBYID*$2@$', $content);

        // Link to forum view by forumid
        $search="/(".$base."\/mod\/forumimproved\/view.php\?f\=)([0-9]+)/";
        $content= preg_replace($search, '$@HSUFORUMVIEWBYF*$2@$', $content);

        // Link to forum discussion with parent syntax
        $search="/(".$base."\/mod\/forumimproved\/discuss.php\?d\=)([0-9]+)\&parent\=([0-9]+)/";
        $content= preg_replace($search, '$@HSUFORUMDISCUSSIONVIEWPARENT*$2*$3@$', $content);

        // Link to forum discussion with relative syntax
        $search="/(".$base."\/mod\/forumimproved\/discuss.php\?d\=)([0-9]+)\#([0-9]+)/";
        $content= preg_replace($search, '$@HSUFORUMDISCUSSIONVIEWINSIDE*$2*$3@$', $content);

        // Link to forum discussion by discussionid
        $search="/(".$base."\/mod\/forumimproved\/discuss.php\?d\=)([0-9]+)/";
        $content= preg_replace($search, '$@HSUFORUMDISCUSSIONVIEW*$2@$', $content);

        return $content;
    }
}
