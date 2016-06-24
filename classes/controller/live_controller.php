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
 * View Posters Controller
 *
 * @package    mod
 * @subpackage forumplusone
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @copyright Copyright (c) 2016 Paris Descartes University (http://www.univ-paris5.fr/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forumplusone\controller;


use mod_forumplusone\response\json_response as json_response;


defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/controller_abstract.php');

class live_controller extends controller_abstract {
    /**
     * @var post_service
     */
    protected $liveservice;

    public function init($action) {
        parent::init($action);

        require_once(dirname(__DIR__).'/response/json_response.php');
        require_once(dirname(__DIR__).'/service/live_service.php');
        require_once(dirname(dirname(__DIR__)).'/lib.php');

        $this->liveservice = new \mod_forumplusone\service\live_service();
    }

    /**
     * Do any security checks needed for the passed action
     *
     * @param string $action
     */
    public function require_capability($action) {
        global $PAGE;

        require_capability('mod/forumplusone:live_reload', $PAGE->context);
    }

    /**
     * View Posters
     */
    public function reload_action() {
        global $PAGE, $USER, $DB;


        $discid = optional_param('discid', 0, PARAM_INT);


        $forum   = $PAGE->activityrecord;
        $cm      = $PAGE->cm;
        $course  = $PAGE->course;
        $context = $PAGE->context;



        try {
            if ($discid) {
                return $this->liveservice->handle_live_disc($discid, $forum, $USER->id, $course, $cm, $context, $this->renderer);
            }
            else {
                return $this->liveservice->handle_live_list_disc($forum, $USER->id, $course, $cm, $context, $this->renderer);
            }
        } catch (\Exception $e) {
            return new json_response($e);
        }
    }
}
