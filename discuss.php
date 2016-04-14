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
 * Displays a post, and all the posts below it.
 * If no post is given, displays all posts in a discussion
 *
 * @package   mod_forumimproved
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author Mark Nielsen
 */

    require_once('../../config.php');
    require_once(__DIR__.'/lib/discussion/sort.php');

    $d      = required_param('d', PARAM_INT);                // Discussion ID
    $root = optional_param('root', 0, PARAM_INT);        // If set, then display this post and all children.
    $move   = optional_param('move', 0, PARAM_INT);          // If set, moves this discussion to another forum
    $mark   = optional_param('mark', '', PARAM_ALPHA);       // Used for tracking read posts if user initiated.
    $postid = optional_param('postid', 0, PARAM_INT);        // Used for tracking read posts if user initiated.
    $warned = optional_param('warned', 0, PARAM_INT);

    $config = get_config('forumimproved');

    $url = new moodle_url('/mod/forumimproved/discuss.php', array('d'=>$d));
    if ($root !== 0) {
        $url->param('root', $root);
    }
    $PAGE->set_url($url);

    $discussion = $DB->get_record('forumimproved_discussions', array('id' => $d), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $discussion->course), '*', MUST_EXIST);
    $forum = $DB->get_record('forumimproved', array('id' => $discussion->forum), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('forumimproved', $forum->id, $course->id, false, MUST_EXIST);

    require_course_login($course, true, $cm);

    // move this down fix for MDL-6926
    require_once($CFG->dirroot.'/mod/forumimproved/lib.php');

    $modcontext = context_module::instance($cm->id);
    require_capability('mod/forumimproved:viewdiscussion', $modcontext, NULL, true, 'noviewdiscussionspermission', 'forumimproved');

    if ($forum->type == 'single') {
        // If we are viewing a simple single forum then we need to log forum as viewed.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm);

        $params = array(
            'context' => $modcontext,
            'objectid' => $forum->id
        );
        $event = \mod_forumimproved\event\course_module_viewed::create($params);
        $event->add_record_snapshot('course_modules', $cm);
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('forumimproved', $forum);
        $event->trigger();
    }

    if (!empty($CFG->enablerssfeeds) && !empty($config->enablerssfeeds) && $forum->rsstype && $forum->rssarticles) {
        require_once("$CFG->libdir/rsslib.php");

        $rsstitle = format_string($course->shortname, true, array('context' => context_course::instance($course->id))) . ': ' . format_string($forum->name);
        rss_add_http_header($modcontext, 'mod_forumimproved', $forum, $rsstitle);
    }

/// move discussion if requested
    if ($move > 0 and confirm_sesskey()) {
        $return = $CFG->wwwroot.'/mod/forumimproved/discuss.php?d='.$discussion->id;

        require_capability('mod/forumimproved:movediscussions', $modcontext);

        if ($forum->type == 'single') {
            print_error('cannotmovefromsingleforum', 'forumimproved', $return);
        }

        if (!$forumto = $DB->get_record('forumimproved', array('id' => $move))) {
            print_error('cannotmovetonotexist', 'forumimproved', $return);
        }

        if ($forumto->type == 'single') {
            print_error('cannotmovetosingleforum', 'forumimproved', $return);
        }

        // Get target forum cm and check it is visible to current user.
        $modinfo = get_fast_modinfo($course);
        $forums = $modinfo->get_instances_of('forumimproved');
        if (!array_key_exists($forumto->id, $forums)) {
            print_error('cannotmovetonotfound', 'forumimproved', $return);
        }
        $cmto = $forums[$forumto->id];
        if (!$cmto->uservisible) {
            print_error('cannotmovenotvisible', 'forumimproved', $return);
        }

        $destinationctx = context_module::instance($cmto->id);
        require_capability('mod/forumimproved:startdiscussion', $destinationctx);

        if (!$forum->anonymous or $warned) {
            if (!forumimproved_move_attachments($discussion, $forum->id, $forumto->id)) {
                echo $OUTPUT->notification("Errors occurred while moving attachment directories - check your file permissions");
            }
            $DB->set_field('forumimproved_discussions', 'forum', $forumto->id, array('id' => $discussion->id));
            $DB->set_field('forumimproved_read', 'forumid', $forumto->id, array('discussionid' => $discussion->id));

            $params = array(
                'context'  => $destinationctx,
                'objectid' => $discussion->id,
                'other'    => array(
                    'fromforumid' => $forum->id,
                    'toforumid'   => $forumto->id,
                )
            );
            $event  = \mod_forumimproved\event\discussion_moved::create($params);
            $event->add_record_snapshot('forumimproved_discussions', $discussion);
            $event->add_record_snapshot('forumimproved', $forum);
            $event->add_record_snapshot('forumimproved', $forumto);
            $event->trigger();

            // Delete the RSS files for the 2 forums to force regeneration of the feeds
            require_once($CFG->dirroot.'/mod/forumimproved/rsslib.php');
            forumimproved_rss_delete_file($forum);
            forumimproved_rss_delete_file($forumto);

            redirect($return.'&moved=-1&sesskey='.sesskey());
        }
    }

    $params = array(
        'context' => $modcontext,
        'objectid' => $discussion->id,
    );
    $event = \mod_forumimproved\event\discussion_viewed::create($params);
    $event->add_record_snapshot('forumimproved_discussions', $discussion);
    $event->add_record_snapshot('forumimproved', $forum);
    $event->trigger();

    unset($SESSION->fromdiscussion);

    if (!$root) {
        $root = $discussion->firstpost;
    }

    if (! $post = forumimproved_get_post_full($root)) {
        print_error("notexists", 'forumimproved', "$CFG->wwwroot/mod/forumimproved/view.php?f=$forum->id");
    }

    if (!forumimproved_user_can_see_post($forum, $discussion, $post, null, $cm)) {
        print_error('noviewdiscussionspermission', 'forumimproved', "$CFG->wwwroot/mod/forumimproved/view.php?id=$forum->id");
    }

    if ($mark == 'read') {
        forumimproved_tp_add_read_record($USER->id, $postid);
    }


    $forumnode = $PAGE->navigation->find($cm->id, navigation_node::TYPE_ACTIVITY);
    if (empty($forumnode)) {
        $forumnode = $PAGE->navbar;
    } else {
        $forumnode->make_active();
    }
    $node = $forumnode->add(format_string($discussion->name), new moodle_url('/mod/forumimproved/discuss.php', array('d'=>$discussion->id)));
    $node->display = false;
    if ($node && $post->id != $discussion->firstpost) {
        $node->add(format_string($post->subject), $PAGE->url);
    }

    $dsort = forumimproved_lib_discussion_sort::get_from_session($forum, $modcontext);

    $renderer = $PAGE->get_renderer('mod_forumimproved');
    $PAGE->requires->js_init_call('M.mod_forumimproved.init', null, false, $renderer->get_js_module());

    $PAGE->set_title("$course->shortname: $discussion->name");
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    if ($forum->type != 'single') {
         echo "<h2><a href='$CFG->wwwroot/mod/forumimproved/view.php?f=$forum->id'>&#171; ".format_string($forum->name)."</a></h2>";
    }
     echo $renderer->svg_sprite();


/// Check to see if groups are being used in this forum
/// If so, make sure the current person is allowed to see this discussion
/// Also, if we know they should be able to reply, then explicitly set $canreply for performance reasons

    $canreply = forumimproved_user_can_post($forum, $discussion, $USER, $cm, $course, $modcontext);
    if (!$canreply and $forum->type !== 'news') {
        if (isguestuser() or !isloggedin()) {
            $canreply = true;
        }
        if (!is_enrolled($modcontext) and !is_viewing($modcontext)) {
            // allow guests and not-logged-in to see the link - they are prompted to log in after clicking the link
            // normal users with temporary guest access see this link too, they are asked to enrol instead
            $canreply = enrol_selfenrol_available($course->id);
        }
    }


    // Print Notice of Warning if Moving this Discussion
    if ($move > 0 and confirm_sesskey()) {
        echo $OUTPUT->confirm(
            get_string('anonymouswarning', 'forumimproved'),
            new moodle_url('/mod/forumimproved/discuss.php', array('d' => $discussion->id, 'move' => $move, 'warned' => 1)),
            new moodle_url('/mod/forumimproved/discuss.php', array('d' => $discussion->id))
        );
    }

    if (!empty($forum->blockafter) && !empty($forum->blockperiod)) {
        $a = new stdClass();
        $a->blockafter  = $forum->blockafter;
        $a->blockperiod = get_string('secondstotime'.$forum->blockperiod);
        echo $OUTPUT->notification(get_string('thisforumisthrottled','forumimproved',$a));
    }

    if ($forum->type == 'qanda' && !has_capability('mod/forumimproved:viewqandawithoutposting', $modcontext) &&
                !forumimproved_user_has_posted($forum->id,$discussion->id,$USER->id)) {
        echo $OUTPUT->notification(get_string('qandanotify','forumimproved'));
    }

    if ($move == -1 and confirm_sesskey()) {
        echo $OUTPUT->notification(get_string('discussionmoved', 'forumimproved', format_string($forum->name,true)));
    }

    $canrate = has_capability('mod/forumimproved:rate', $modcontext);
    forumimproved_print_discussion($course, $cm, $forum, $discussion, $post, $canreply, $canrate);

    echo '<div class="discussioncontrols">';

    if (!empty($CFG->enableportfolios) && has_capability('mod/forumimproved:exportdiscussion', $modcontext) && empty($forum->anonymous)) {
        require_once($CFG->libdir.'/portfoliolib.php');
        $button = new portfolio_add_button();
        $button->set_callback_options('forumimproved_portfolio_caller', array('discussionid' => $discussion->id), 'mod_forumimproved');
        $button = $button->to_html(PORTFOLIO_ADD_FULL_FORM, get_string('exportdiscussion', 'mod_forumimproved'));
        $buttonextraclass = '';
        if (empty($button)) {
            // no portfolio plugin available.
            $button = '&nbsp;';
            $buttonextraclass = ' noavailable';
        }
        echo html_writer::tag('div', $button, array('class' => 'discussioncontrol exporttoportfolio'.$buttonextraclass));
    }

    if ($course->format !='singleactivity' && $forum->type != 'single'
                && has_capability('mod/forumimproved:movediscussions', $modcontext)) {
        echo '<div class="discussioncontrol movediscussion">';
        // Popup menu to move discussions to other forums. The discussion in a
        // single discussion forum can't be moved.
        $modinfo = get_fast_modinfo($course);
        if (isset($modinfo->instances['forumimproved'])) {
            $forummenu = array();
            // Check forum types and eliminate simple discussions.
            $forumcheck = $DB->get_records('forumimproved', array('course' => $course->id),'', 'id, type');
            foreach ($modinfo->instances['forumimproved'] as $forumcm) {
                if (!$forumcm->uservisible || !has_capability('mod/forumimproved:startdiscussion',
                    context_module::instance($forumcm->id))) {
                    continue;
                }
                $section = $forumcm->sectionnum;
                $sectionname = get_section_name($course, $section);
                if (empty($forummenu[$section])) {
                    $forummenu[$section] = array($sectionname => array());
                }
                $forumidcompare = $forumcm->instance != $forum->id;
                $forumtypecheck = $forumcheck[$forumcm->instance]->type !== 'single';
                if ($forumidcompare and $forumtypecheck) {
                    $url = "/mod/forumimproved/discuss.php?d=$discussion->id&move=$forumcm->instance&sesskey=".sesskey();
                    $forummenu[$section][$sectionname][$url] = format_string($forumcm->name);
                }
            }
        }
        echo "</div>";
    }
    if (!empty($forummenu)) {
        echo '<div class="movediscussionoption">';
        $select = new url_select($forummenu, '',
            array(''=>get_string("movethisdiscussionto", "forumimproved")),
            'forummenu');
        echo $OUTPUT->render($select);
        echo "</div>";
    }
    if ($forum->type == 'single') {
        echo  forumimproved_search_form($course, $forum->id);
    }

    $neighbours = forumimproved_get_discussion_neighbours($cm, $discussion);
    echo $renderer->discussion_navigation($neighbours['prev'], $neighbours['next']);
    echo "</div>";

echo $renderer->advanced_editor();

echo $OUTPUT->footer();
