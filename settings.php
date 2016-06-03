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
 * @package   mod_forumplusone
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @copyright Copyright (c) 2016 Paris Descartes University (http://www.univ-paris5.fr/)
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/forumplusone/lib.php');

    $config = get_config('forumplusone');

    $settings->add(new admin_setting_configcheckbox('forumplusone/replytouser', get_string('replytouser', 'forumplusone'),
                       get_string('configreplytouser', 'forumplusone'), 1));

    // Less non-HTML characters than this is short
    $settings->add(new admin_setting_configtext('forumplusone/shortpost', get_string('shortpost', 'forumplusone'),
                       get_string('configshortpost', 'forumplusone'), 300, PARAM_INT));

    // More non-HTML characters than this is long
    $settings->add(new admin_setting_configtext('forumplusone/longpost', get_string('longpost', 'forumplusone'),
                       get_string('configlongpost', 'forumplusone'), 600, PARAM_INT));

    // Number of discussions on a page
    $settings->add(new admin_setting_configtext('forumplusone/manydiscussions', get_string('manydiscussions', 'forumplusone'),
                       get_string('configmanydiscussions', 'forumplusone'), 10, PARAM_INT));

    if (isset($CFG->maxbytes)) {
        $maxbytes = 0;
        if (isset($config->maxbytes)) {
            $maxbytes = $config->maxbytes;
        }
        $settings->add(new admin_setting_configselect('forumplusone/maxbytes', get_string('maxattachmentsize', 'forumplusone'),
                           get_string('configmaxbytes', 'forumplusone'), 512000, get_max_upload_sizes($CFG->maxbytes, 0, 0, $maxbytes)));
    }

    // Default number of attachments allowed per post in all forums
    $options = array(
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
        20 => 20,
        50 => 50,
        100 => 100
    );
    $settings->add(new admin_setting_configselect('forumplusone/maxattachments', get_string('maxattachments', 'forumplusone'),
                       get_string('configmaxattachments', 'forumplusone'), 9, $options));

    // Default number of days that a post is considered old
    $settings->add(new admin_setting_configtext('forumplusone/oldpostdays', get_string('oldpostdays', 'forumplusone'),
                       get_string('configoldpostdays', 'forumplusone'), 14, PARAM_INT));

    $options = array();
    for ($i = 0; $i < 24; $i++) {
        $options[$i] = sprintf("%02d",$i);
    }
    // Default time (hour) to execute 'clean_read_records' cron
    $settings->add(new admin_setting_configselect('forumplusone/cleanreadtime', get_string('cleanreadtime', 'forumplusone'),
                       get_string('configcleanreadtime', 'forumplusone'), 2, $options));

    // Default time (hour) to send digest email
    $settings->add(new admin_setting_configselect('forumplusone/digestmailtime', get_string('digestmailtime', 'forumplusone'),
                       get_string('configdigestmailtime', 'forumplusone'), 17, $options));

    if (empty($CFG->enablerssfeeds)) {
        $options = array(0 => get_string('rssglobaldisabled', 'admin'));
        $str = get_string('configenablerssfeeds', 'forumplusone').'<br />'.get_string('configenablerssfeedsdisabled2', 'admin');

    } else {
        $options = array(0=>get_string('no'), 1=>get_string('yes'));
        $str = get_string('configenablerssfeeds', 'forumplusone');
    }
    $settings->add(new admin_setting_configselect('forumplusone/enablerssfeeds', get_string('enablerssfeeds', 'admin'),
                       $str, 0, $options));

    $settings->add(new admin_setting_configcheckbox('forumplusone/enabletimedposts', get_string('timedposts', 'forumplusone'),
                       get_string('configenabletimedposts', 'forumplusone'), 0));

    $settings->add(new admin_setting_configcheckbox('forumplusone/hiderecentposts', get_string('hiderecentposts', 'forumplusone'),
                       get_string('confighiderecentposts', 'forumplusone'), 0));

    $settings->add(new admin_setting_configcheckbox('forumplusone/hideuserpicture', get_string('hideuserpicture', 'forumplusone'),
                       get_string('hideuserpicture', 'forumplusone'), 0));

    $settings->add(new admin_setting_configtext('forumplusone/votesColor', get_string('votecolor', 'forumplusone'),
                       get_string('configvotecolor', 'forumplusone'), '#da3d00', PARAM_NOTAGS));

    $settings->add(new admin_setting_configtext('forumplusone/livereloadrate', get_string('livereloadrate', 'forumplusone'),
                       get_string('configlivereloadrate', 'forumplusone'), 15, PARAM_INT));
}

