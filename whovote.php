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
 * Displays all voters for a post
 * More than inspired by ratings
 *
 * @package   mod_forumplusone
 * @copyright 2016 Descartes University
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2 or later
 * @copyright Copyright (c) 2016 Paris Descartes University (http://www.univ-paris5.fr/)
 */

require_once('../../config.php');
require_once("lib.php");

$contextid = required_param('contextid', PARAM_INT);
$postid    = required_param('postid', PARAM_INT);
$sort       = optional_param('sort', '', PARAM_ALPHA);
$popup      = optional_param('popup', 0, PARAM_INT); //==1 if in a popup window

list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);

$url = new moodle_url('/mod/forumplusone/whovote.php', array('contextid' => $contextid,
                                                              'postid' => $postid));

if (!empty($sort)) {
    $url->param('sort', $sort);
}
if (!empty($popup)) {
    $url->param('popup', $popup);
}
$PAGE->set_url($url);
$PAGE->set_context($context);

$config = get_config('forumplusone');
if (!empty($config->hideuserpicture) && $config->hideuserpicture) {
    $PAGE->add_body_class('forumplusone-nouserpicture');
}

if ($popup) {
    $PAGE->set_pagelayout('popup');
}

$params = array('contextid' => $contextid);

$forum   = $PAGE->activityrecord;

if ($forum->vote_display_name) {
    if (!has_capability('mod/forumplusone:viewwhovote', $context)) {
        print_error('vote_view_forbidden', 'forumplusone');
    }
}
else {
    if (!has_capability('mod/forumplusone:viewwhovote_annonymousvote', $context)) {
        print_error('vote_view_forbidden', 'forumplusone');
    }
}

if (!$forum->enable_vote) {
    print_error('vote_disabled_error', 'forumplusone');
}

switch ($sort) {
    case 'datetime': $sqlsort = "v.timestamp ASC"; break;
    default:         $sqlsort = "u.firstname ASC";
}

$strname    = get_string('username');
$strtime    = get_string('date');

$PAGE->set_title(get_string('allvoteforitem','forumplusone'));
echo $OUTPUT->header();

$votes = forumplusone_get_all_post_votes($postid, $sqlsort);
if (!$votes) {
    $msg = get_string('novotes','forumplusone');
    echo html_writer::tag('div', $msg, array('class'=>'mdl-align'));
} else {

    $canSeeDatetime = has_capability('mod/forumplusone:viewvotedatetime', $context);




    // To get the sort URL, copy the current URL and remove any previous sort
    $sorturl = new moodle_url($url);
    $sorturl->remove_params('sort');

    $table = new html_table;

    $table->attributes['class'] = 'generalbox table table-striped table-hover';

    $head = array(
        '',
        html_writer::link(new moodle_url($sorturl, array('sort' => 'firstname')), $strname)
    );
    if ($canSeeDatetime) {
        array_push($head, html_writer::link(new moodle_url($sorturl, array('sort' => 'datetime')), $strtime));
    }
    $table->head = $head;

    $table->colclasses = array('', 'firstname', 'time');
    $table->data = array();


    foreach ($votes as $vote) {
        //Undo the aliasing of the user id column from user_picture::fields()
        //we could clone the rating object or preserve the rating id if we needed it again
        //but we don't
        $vote->id = $vote->userid;

        $row = new html_table_row();
        $row->attributes['class'] = 'ratingitemheader';
        if ($course && $course->id) {
            $row->cells[] = $OUTPUT->user_picture($vote, array('courseid' => $course->id));
        } else {
            $row->cells[] = $OUTPUT->user_picture($vote);
        }
        $row->cells[] = fullname($vote);

        if ($canSeeDatetime)
            $row->cells[] = userdate($vote->timestamp);

        $table->data[] = $row;
    }
    echo html_writer::table($table);
}
if ($popup) {
    echo $OUTPUT->close_window_button();
}
echo $OUTPUT->footer();
