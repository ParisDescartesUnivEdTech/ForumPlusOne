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
 * Export Adapter
 *
 * @package   mod_forumplusone
 * @copyright Copyright (c) 2013 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2 or later
 * @copyright Copyright (c) 2016 Paris Descartes University (http://www.univ-paris5.fr/)
 */

namespace mod_forumplusone\export;

defined('MOODLE_INTERNAL') || die();

/**
 * @package   mod_forumplusone
 * @copyright Copyright (c) 2013 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2 or later
 * @copyright Copyright (c) 2016 Paris Descartes University (http://www.univ-paris5.fr/)
 */
interface adapter_interface {
    /**
     * Initialization routine
     *
     * @param null|\stdClass $discussion Only passed if exporting a single discussion
     * @return void
     */
    public function initialization($discussion = null);

    /**
     * Send a discussion and its posts to the export
     *
     * @param \stdClass $discussion
     * @param \stdClass[] $posts
     * @return void
     */
    public function send_discussion($discussion, $posts);

    /**
     * Exporting is done, wrap things up.
     *
     * @return void
     */
    public function finish();
}
