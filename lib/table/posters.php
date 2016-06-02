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
 * View Posters Table
 *
 * @package    mod
 * @subpackage forumplusone
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author     Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/tablelib.php');

class forumplusone_lib_table_posters extends table_sql {
    public function __construct($uniqueid) {
        global $PAGE, $USER;

        parent::__construct($uniqueid);

        $this->define_columns(array('userpic', 'fullname', 'total', 'posts', 'replies', 'substantive'));
        $this->define_headers(array(
            '',
            get_string('fullnameuser'),
            get_string('totalposts', 'forumplusone'),
            get_string('posts', 'forumplusone'),
            get_string('replies', 'forumplusone'),
            get_string('substantive', 'forumplusone'))
        );

        $fields = user_picture::fields('u', null, 'id');
        $params = array('forumid' => $PAGE->activityrecord->id);

        if (!has_capability('mod/forumplusone:viewposters', $PAGE->context)) {
            $params['userid'] = $USER->id;
            $usersql = ' AND u.id = :userid ';
        } else {
            $usersql = '';
        }
        $this->set_sql(
            "$fields,
             COUNT(*) AS total,
             SUM(CASE WHEN p.parent = 0 THEN 1 ELSE 0 END) AS posts,
             SUM(CASE WHEN p.parent != 0 THEN 1 ELSE 0 END) AS replies,
             SUM(CASE WHEN p.flags LIKE '%substantive%' THEN 1 ELSE 0 END) AS substantive",
            '{forumplusone_posts} p, {forumplusone_discussions} d, {forumplusone} f, {user} u',
            "u.id = p.userid AND p.discussion = d.id AND d.forum = f.id AND f.id = :forumid$usersql GROUP BY p.userid",
            $params
        );
        $this->set_count_sql("
            SELECT COUNT(DISTINCT p.userid)
              FROM {forumplusone_posts} p
              JOIN {user} u ON u.id = p.userid
              JOIN {forumplusone_discussions} d ON d.id = p.discussion
              JOIN {forumplusone} f ON f.id = d.forum
              WHERE f.id = :forumid$usersql
        ", $params);
    }

    public function col_userpic($row) {
        global $OUTPUT;
        return $OUTPUT->user_picture(user_picture::unalias($row, null, 'id'));
    }
}
