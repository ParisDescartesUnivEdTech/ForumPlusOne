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
 * Discussion Sorting Management
 *
 * @package    mod
 * @subpackage forumplusone
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author     Mark Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class forumplusone_lib_discussion_sort implements Serializable {
    /**
     * @var string
     */
    protected $defaultkey = 'created';

    /**
     * @var string
     */
    protected $key;

    /* TODO - why would you sort by the number of unread replies??

    */

    /**
     * @var array
     */
    protected $keyopts = array(
        'created'    => 'p.created DESC',
        'lastreply'  => 'd.timemodified DESC',
        'unread'     => 'unread.unread DESC, p.created DESC',
        'popularity' => 'countVote DESC, p.created DESC',
        'closed'     => 'd.state DESC, p.created DESC',
        'open'       => 'd.state ASC, p.created DESC',
        // 'replies'    => 'extra.replies %dir%, p.created DESC',
        // 'firstname'  => 'u.firstname %dir%, p.created DESC',
        // 'lastname'   => 'u.lastname %dir%, p.created DESC',
        // 'subscribe'  => 'sd.id %dir%, p.created DESC',
    );

    /**
     * @var array
     */
    protected $disabled = array();

    public function __construct() {
        $this->key = $this->defaultkey;
    }

    /**
     * @static
     * @param stdClass $forum
     * @param context_module $context
     * @return forumplusone_lib_discussion_sort
     */
    public static function get_from_session($forum, context_module $context) {
        global $SESSION;

        require_once(__DIR__.'/subscribe.php');

        if (!empty($SESSION->forumplusone_lib_discussion_sort)) {
            /** @var $instance forumplusone_lib_discussion_sort */
            $instance = unserialize($SESSION->forumplusone_lib_discussion_sort);
        } else {
            $instance = new self();
        }
        $dsub = new forumplusone_lib_discussion_subscribe($forum, $context);
        if (!$dsub->can_subscribe()) {
            $instance->disable('subscribe');
        }
        return $instance;
    }

    /**
     * @static
     * @param forumplusone_lib_discussion_sort $sort
     */
    public static function set_to_session(forumplusone_lib_discussion_sort $sort) {
        global $SESSION;
        $SESSION->forumplusone_lib_discussion_sort = serialize($sort);
    }

    /**
     * @param array $disabled
     * @return forumplusone_lib_discussion_sort
     */
    public function set_disabled(array $disabled) {
        if (in_array($this->defaultkey, $disabled)) {
            throw new coding_exception('The ' . $this->defaultkey . ' key is the only key that cannot be disabled');
        }
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * @return array
     */
    public function get_disabled() {
        return $this->disabled;
    }

    /**
     * @return array
     */
    public function get_keyopts() {
        return $this->keyopts;
    }

    /**
     * @param string $key
     * @return forumplusone_lib_discussion_sort
     */
    public function set_key($key) {
        if (!array_key_exists($key, $this->get_keyopts())) {
            throw new coding_exception('Invalid sort key: '.$key);
        }
        if (in_array($key, $this->get_disabled())) {
            throw new coding_exception('Invalid sort key (it has been disabled): '.$key);
        }
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function get_key() {
        return $this->key;
    }

    /**
     * @return array
     */
    public function get_key_options_menu() {
        $menu = array();
        foreach ($this->get_keyopts() as $key => $sort) {
            if (!in_array($key, $this->get_disabled())) {
                $menu[$key] = get_string('discussionsortkey:'.$key, 'forumplusone');
            }
        }
        return $menu;
    }

    /**
     * @return string
     */
    public function get_sort_sql() {
        $sortopts = $this->get_keyopts();
        return $sortopts[$this->get_key()];
    }

    /**
     * @param $key
     * @return forumplusone_lib_discussion_sort
     */
    public function disable($key) {
        $disabled = $this->get_disabled();
        $disabled[$key] = $key;
        $this->set_disabled($disabled);

        if ($this->get_key() == $key) {
            $this->set_key($this->defaultkey);
        }
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or &null;
     */
    public function serialize() {
        return serialize(array('key' => $this->get_key()));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized) {
        $sortinfo = unserialize($serialized);

        try {
            $this->set_key($sortinfo['key']);
        } catch (Exception $e) {
            // Ignore...
        }
    }
}
