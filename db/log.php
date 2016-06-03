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
 * @package    mod_forumplusone
 * @category   log
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @copyright Copyright (c) 2016 Paris Descartes University (http://www.univ-paris5.fr/)
 */

defined('MOODLE_INTERNAL') || die();

global $DB; // TODO: this is a hack, we should really do something with the SQL in SQL tables

$logs = array(
    array('module'=>'forumplusone', 'action'=>'add', 'mtable'=>'forumplusone', 'field'=>'name'),
    array('module'=>'forumplusone', 'action'=>'update', 'mtable'=>'forumplusone', 'field'=>'name'),
    array('module'=>'forumplusone', 'action'=>'add discussion', 'mtable'=>'forumplusone_discussions', 'field'=>'name'),
    array('module'=>'forumplusone', 'action'=>'add post', 'mtable'=>'forumplusone_posts', 'field'=>'subject'),
    array('module'=>'forumplusone', 'action'=>'update post', 'mtable'=>'forumplusone_posts', 'field'=>'subject'),
    array('module'=>'forumplusone', 'action'=>'user report', 'mtable'=>'user', 'field'=>$DB->sql_concat('firstname', "' '" , 'lastname')),
    array('module'=>'forumplusone', 'action'=>'move discussion', 'mtable'=>'forumplusone_discussions', 'field'=>'name'),
    array('module'=>'forumplusone', 'action'=>'view subscribers', 'mtable'=>'forumplusone', 'field'=>'name'),
    array('module'=>'forumplusone', 'action'=>'view discussion', 'mtable'=>'forumplusone_discussions', 'field'=>'name'),
    array('module'=>'forumplusone', 'action'=>'view forum', 'mtable'=>'forumplusone', 'field'=>'name'),
    array('module'=>'forumplusone', 'action'=>'subscribe', 'mtable'=>'forumplusone', 'field'=>'name'),
    array('module'=>'forumplusone', 'action'=>'unsubscribe', 'mtable'=>'forumplusone', 'field'=>'name'),
);
