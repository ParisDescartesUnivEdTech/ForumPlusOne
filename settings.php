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
 * @package   mod_forumimproved
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author Mark Nielsen
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/forumimproved/lib.php');

    $config = get_config('forumimproved');

    $settings->add(new admin_setting_configcheckbox('forumimproved/replytouser', get_string('replytouser', 'forumimproved'),
                       get_string('configreplytouser', 'forumimproved'), 1));

    // Less non-HTML characters than this is short
    $settings->add(new admin_setting_configtext('forumimproved/shortpost', get_string('shortpost', 'forumimproved'),
                       get_string('configshortpost', 'forumimproved'), 300, PARAM_INT));

    // More non-HTML characters than this is long
    $settings->add(new admin_setting_configtext('forumimproved/longpost', get_string('longpost', 'forumimproved'),
                       get_string('configlongpost', 'forumimproved'), 600, PARAM_INT));

    // Number of discussions on a page
    $settings->add(new admin_setting_configtext('forumimproved/manydiscussions', get_string('manydiscussions', 'forumimproved'),
                       get_string('configmanydiscussions', 'forumimproved'), 100, PARAM_INT));

    if (isset($CFG->maxbytes)) {
        $maxbytes = 0;
        if (isset($config->maxbytes)) {
            $maxbytes = $config->maxbytes;
        }
        $settings->add(new admin_setting_configselect('forumimproved/maxbytes', get_string('maxattachmentsize', 'forumimproved'),
                           get_string('configmaxbytes', 'forumimproved'), 512000, get_max_upload_sizes($CFG->maxbytes, 0, 0, $maxbytes)));
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
    $settings->add(new admin_setting_configselect('forumimproved/maxattachments', get_string('maxattachments', 'forumimproved'),
                       get_string('configmaxattachments', 'forumimproved'), 9, $options));

    // Default number of days that a post is considered old
    $settings->add(new admin_setting_configtext('forumimproved/oldpostdays', get_string('oldpostdays', 'forumimproved'),
                       get_string('configoldpostdays', 'forumimproved'), 14, PARAM_INT));

    $options = array();
    for ($i = 0; $i < 24; $i++) {
        $options[$i] = sprintf("%02d",$i);
    }
    // Default time (hour) to execute 'clean_read_records' cron
    $settings->add(new admin_setting_configselect('forumimproved/cleanreadtime', get_string('cleanreadtime', 'forumimproved'),
                       get_string('configcleanreadtime', 'forumimproved'), 2, $options));

    // Default time (hour) to send digest email
    $settings->add(new admin_setting_configselect('forumimproved/digestmailtime', get_string('digestmailtime', 'forumimproved'),
                       get_string('configdigestmailtime', 'forumimproved'), 17, $options));

    if (empty($CFG->enablerssfeeds)) {
        $options = array(0 => get_string('rssglobaldisabled', 'admin'));
        $str = get_string('configenablerssfeeds', 'forumimproved').'<br />'.get_string('configenablerssfeedsdisabled2', 'admin');

    } else {
        $options = array(0=>get_string('no'), 1=>get_string('yes'));
        $str = get_string('configenablerssfeeds', 'forumimproved');
    }
    $settings->add(new admin_setting_configselect('forumimproved/enablerssfeeds', get_string('enablerssfeeds', 'admin'),
                       $str, 0, $options));

    $settings->add(new admin_setting_configcheckbox('forumimproved/enabletimedposts', get_string('timedposts', 'forumimproved'),
                       get_string('configenabletimedposts', 'forumimproved'), 0));

    $settings->add(new admin_setting_configcheckbox('forumimproved/hiderecentposts', get_string('hiderecentposts', 'forumimproved'),
                       get_string('confighiderecentposts', 'forumimproved'), 0));
}

