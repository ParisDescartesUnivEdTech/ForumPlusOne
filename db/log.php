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
 * Definition of log events
 *
 * @package    mod_forumimproved
 * @category   log
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author Mark Nielsen
 */

defined('MOODLE_INTERNAL') || die();

global $DB; // TODO: this is a hack, we should really do something with the SQL in SQL tables

$logs = array(
    array('module'=>'forumimproved', 'action'=>'add', 'mtable'=>'forumimproved', 'field'=>'name'),
    array('module'=>'forumimproved', 'action'=>'update', 'mtable'=>'forumimproved', 'field'=>'name'),
    array('module'=>'forumimproved', 'action'=>'add discussion', 'mtable'=>'forumimproved_discussions', 'field'=>'name'),
    array('module'=>'forumimproved', 'action'=>'add post', 'mtable'=>'forumimproved_posts', 'field'=>'subject'),
    array('module'=>'forumimproved', 'action'=>'update post', 'mtable'=>'forumimproved_posts', 'field'=>'subject'),
    array('module'=>'forumimproved', 'action'=>'user report', 'mtable'=>'user', 'field'=>$DB->sql_concat('firstname', "' '" , 'lastname')),
    array('module'=>'forumimproved', 'action'=>'move discussion', 'mtable'=>'forumimproved_discussions', 'field'=>'name'),
    array('module'=>'forumimproved', 'action'=>'view subscribers', 'mtable'=>'forumimproved', 'field'=>'name'),
    array('module'=>'forumimproved', 'action'=>'view discussion', 'mtable'=>'forumimproved_discussions', 'field'=>'name'),
    array('module'=>'forumimproved', 'action'=>'view forum', 'mtable'=>'forumimproved', 'field'=>'name'),
    array('module'=>'forumimproved', 'action'=>'subscribe', 'mtable'=>'forumimproved', 'field'=>'name'),
    array('module'=>'forumimproved', 'action'=>'unsubscribe', 'mtable'=>'forumimproved', 'field'=>'name'),
);
