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
 * Edit and save a new post to a discussion
 *
 * @package   mod_forumimproved
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author Mark Nielsen
 */

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/completionlib.php');

$reply   = optional_param('reply', 0, PARAM_INT);
$forum   = optional_param('forum', 0, PARAM_INT);
$edit    = optional_param('edit', 0, PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_INT);
$prune   = optional_param('prune', 0, PARAM_INT);
$name    = optional_param('name', '', PARAM_CLEAN);
$confirm = optional_param('confirm', 0, PARAM_INT);
$groupid = optional_param('groupid', null, PARAM_INT);

$PAGE->set_url('/mod/forumimproved/post.php', array(
        'reply' => $reply,
        'forum' => $forum,
        'edit'  => $edit,
        'delete'=> $delete,
        'prune' => $prune,
        'name'  => $name,
        'confirm'=>$confirm,
        'groupid'=>$groupid,
        ));
//these page_params will be passed as hidden variables later in the form.
$page_params = array('reply'=>$reply, 'forum'=>$forum, 'edit'=>$edit);

$sitecontext = context_system::instance();

if (!isloggedin() or isguestuser()) {

    if (!isloggedin() and !get_referer()) {
        // No referer+not logged in - probably coming in via email  See MDL-9052
        require_login();
    }

    if (!empty($forum)) {      // User is starting a new discussion in a forum
        if (! $forum = $DB->get_record('forumimproved', array('id' => $forum))) {
            print_error('invalidforumid', 'forumimproved');
        }
    } else if (!empty($reply)) {      // User is writing a new reply
        if (! $parent = forumimproved_get_post_full($reply)) {
            print_error('invalidparentpostid', 'forumimproved');
        }
        if (! $discussion = $DB->get_record('forumimproved_discussions', array('id' => $parent->discussion))) {
            print_error('notpartofdiscussion', 'forumimproved');
        }
        if (! $forum = $DB->get_record('forumimproved', array('id' => $discussion->forum))) {
            print_error('invalidforumid');
        }
    }
    if (! $course = $DB->get_record('course', array('id' => $forum->course))) {
        print_error('invalidcourseid');
    }

    if (!$cm = get_coursemodule_from_instance('forumimproved', $forum->id, $course->id)) { // For the logs
        print_error('invalidcoursemodule');
    } else {
        $modcontext = context_module::instance($cm->id);
    }

    $PAGE->set_cm($cm, $course, $forum);
    $PAGE->set_context($modcontext);
    $PAGE->set_title($course->shortname);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('noguestpost', 'forumimproved').'<br /><br />'.get_string('liketologin'), get_login_url(), get_referer(false));
    echo $OUTPUT->footer();
    exit;
}

require_login(0, false);   // Script is useless unless they're logged in

if (!empty($forum)) {      // User is starting a new discussion in a forum
    if (! $forum = $DB->get_record("forumimproved", array("id" => $forum))) {
        print_error('invalidforumid', 'forumimproved');
    }
    if (! $course = $DB->get_record("course", array("id" => $forum->course))) {
        print_error('invalidcourseid');
    }
    if (! $cm = get_coursemodule_from_instance("forumimproved", $forum->id, $course->id)) {
        print_error("invalidcoursemodule");
    }

    // Retrieve the contexts.
    $modcontext    = context_module::instance($cm->id);
    $coursecontext = context_course::instance($course->id);

    if (! forumimproved_user_can_post_discussion($forum, $groupid, -1, $cm)) {
        if (!is_enrolled($coursecontext)) {
            if (enrol_selfenrol_available($course->id)) {
                $SESSION->wantsurl = qualified_me();
                $SESSION->enrolcancel = get_referer(false);
                redirect($CFG->wwwroot.'/enrol/index.php?id='.$course->id, get_string('youneedtoenrol'));
            }
        }
        print_error('nopostforum', 'forumimproved');
    }

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $modcontext)) {
        print_error("activityiscurrentlyhidden");
    }

    $SESSION->fromurl = get_referer(false);

    // Load up the $post variable.

    $post = new stdClass();
    $post->course        = $course->id;
    $post->forum         = $forum->id;
    $post->discussion    = 0;           // ie discussion # not defined yet
    $post->parent        = 0;
    $post->subject       = '';
    $post->userid        = $USER->id;
    $post->reveal        = 0;
    $post->privatereply  = 0;
    $post->message       = '';
    $post->messageformat = editors_get_preferred_format();
    $post->messagetrust  = 0;

    if (isset($groupid)) {
        $post->groupid = $groupid;
    } else {
        $post->groupid = groups_get_activity_group($cm);
    }

    forumimproved_set_return();

} else if (!empty($reply)) {      // User is writing a new reply

    if (! $parent = forumimproved_get_post_full($reply)) {
        print_error('invalidparentpostid', 'forumimproved');
    }
    if (! $discussion = $DB->get_record("forumimproved_discussions", array("id" => $parent->discussion))) {
        print_error('notpartofdiscussion', 'forumimproved');
    }
    if (! $forum = $DB->get_record("forumimproved", array("id" => $discussion->forum))) {
        print_error('invalidforumid', 'forumimproved');
    }
    if (! $course = $DB->get_record("course", array("id" => $discussion->course))) {
        print_error('invalidcourseid');
    }
    if (! $cm = get_coursemodule_from_instance("forumimproved", $forum->id, $course->id)) {
        print_error('invalidcoursemodule');
    }

    // Ensure lang, theme, etc. is set up properly. MDL-6926
    $PAGE->set_cm($cm, $course, $forum);
    $renderer = $PAGE->get_renderer('mod_forumimproved');
    $PAGE->requires->js_init_call('M.mod_forumimproved.init', null, false, $renderer->get_js_module());

    // Retrieve the contexts.
    $modcontext    = context_module::instance($cm->id);
    $coursecontext = context_course::instance($course->id);

    if (! forumimproved_user_can_post($forum, $discussion, $USER, $cm, $course, $modcontext)) {
        if (!is_enrolled($coursecontext)) {  // User is a guest here!
            $SESSION->wantsurl = qualified_me();
            $SESSION->enrolcancel = get_referer(false);
            redirect($CFG->wwwroot.'/enrol/index.php?id='.$course->id, get_string('youneedtoenrol'));
        }
        print_error('nopostforum', 'forumimproved');
    }

    // Make sure user can post here
    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        $groupmode =  $cm->groupmode;
    } else {
        $groupmode = $course->groupmode;
    }
    if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $modcontext)) {
        if ($discussion->groupid == -1) {
            print_error('nopostforum', 'forumimproved');
        } else {
            if (!groups_is_member($discussion->groupid)) {
                print_error('nopostforum', 'forumimproved');
            }
        }
    }

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $modcontext)) {
        print_error("activityiscurrentlyhidden");
    }

    // Load up the $post variable.

    $post = new stdClass();
    $post->course      = $course->id;
    $post->forum       = $forum->id;
    $post->discussion  = $parent->discussion;
    $post->parent      = $parent->id;
    $post->reveal      = 0;
    $post->privatereply= 0;
    $post->subject     = $parent->subject;
    $post->userid      = $USER->id;
    $post->message     = '';

    $post->groupid = ($discussion->groupid == -1) ? 0 : $discussion->groupid;

    $strre = get_string('re', 'forumimproved');
    if (!(substr($post->subject, 0, strlen($strre)) == $strre)) {
        $post->subject = $strre.' '.$post->subject;
    }

    unset($SESSION->fromdiscussion);

} else if (!empty($edit)) {  // User is editing their own post

    if (! $post = forumimproved_get_post_full($edit)) {
        print_error('invalidpostid', 'forumimproved');
    }
    if ($post->parent) {
        if (! $parent = forumimproved_get_post_full($post->parent)) {
            print_error('invalidparentpostid', 'forumimproved');
        }
    }

    if (! $discussion = $DB->get_record("forumimproved_discussions", array("id" => $post->discussion))) {
        print_error('notpartofdiscussion', 'forumimproved');
    }
    if (! $forum = $DB->get_record("forumimproved", array("id" => $discussion->forum))) {
        print_error('invalidforumid', 'forumimproved');
    }
    if (! $course = $DB->get_record("course", array("id" => $discussion->course))) {
        print_error('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance("forumimproved", $forum->id, $course->id)) {
        print_error('invalidcoursemodule');
    } else {
        $modcontext = context_module::instance($cm->id);
    }

    $PAGE->set_cm($cm, $course, $forum);
    $renderer = $PAGE->get_renderer('mod_forumimproved');
    $PAGE->requires->js_init_call('M.mod_forumimproved.init', null, false, $renderer->get_js_module());

    if (!($forum->type == 'news' && !$post->parent && $discussion->timestart > time())) {
        if (((time() - $post->created) > $CFG->maxeditingtime) and
                    !has_capability('mod/forumimproved:editanypost', $modcontext)) {
            print_error('maxtimehaspassed', 'forumimproved', '', format_time($CFG->maxeditingtime));
        }
    }
    if (($post->userid <> $USER->id) and
                !has_capability('mod/forumimproved:editanypost', $modcontext)) {
        print_error('cannoteditposts', 'forumimproved');
    }


    // Load up the $post variable.
    $post->edit   = $edit;
    $post->course = $course->id;
    $post->forum  = $forum->id;
    $post->groupid = ($discussion->groupid == -1) ? 0 : $discussion->groupid;

    $post = trusttext_pre_edit($post, 'message', $modcontext);

    unset($SESSION->fromdiscussion);


}else if (!empty($delete)) {  // User is deleting a post

    if (! $post = forumimproved_get_post_full($delete)) {
        print_error('invalidpostid', 'forumimproved');
    }
    if (! $discussion = $DB->get_record("forumimproved_discussions", array("id" => $post->discussion))) {
        print_error('notpartofdiscussion', 'forumimproved');
    }
    if (! $forum = $DB->get_record("forumimproved", array("id" => $discussion->forum))) {
        print_error('invalidforumid', 'forumimproved');
    }
    if (!$cm = get_coursemodule_from_instance("forumimproved", $forum->id, $forum->course)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id' => $forum->course))) {
        print_error('invalidcourseid');
    }

    require_login($course, false, $cm);
    $modcontext = context_module::instance($cm->id);

    if ( !(($post->userid == $USER->id && has_capability('mod/forumimproved:deleteownpost', $modcontext))
                || has_capability('mod/forumimproved:deleteanypost', $modcontext)) ) {
        print_error('cannotdeletepost', 'forumimproved');
    }


    $replycount = forumimproved_count_replies($post);

    if (!empty($confirm) && confirm_sesskey()) {    // User has confirmed the delete
        redirect(
            forumimproved_verify_and_delete_post($course, $cm, $forum, $modcontext, $discussion, $post)
        );
    } else { // User just asked to delete something

        forumimproved_set_return();
        $PAGE->navbar->add(get_string('delete', 'forumimproved'));
        $PAGE->set_title($course->shortname);
        $PAGE->set_heading($course->fullname);
        $renderer = $PAGE->get_renderer('mod_forumimproved');
        $PAGE->requires->js_init_call('M.mod_forumimproved.init', null, false, $renderer->get_js_module());

        if ($replycount) {
            if (!has_capability('mod/forumimproved:deleteanypost', $modcontext)) {
                print_error("couldnotdeletereplies", "forumimproved",
                      forumimproved_go_back_to("discuss.php?d=$post->discussion"));
            }
            echo $OUTPUT->header();
            echo $OUTPUT->heading(format_string($forum->name), 2);
            echo $OUTPUT->confirm(get_string("deletesureplural", "forumimproved", $replycount+1),
                         "post.php?delete=$delete&confirm=$delete",
                         $CFG->wwwroot.'/mod/forumimproved/discuss.php?d='.$post->discussion.'#p'.$post->id);

            echo $renderer->post($cm, $discussion, $post, false, null, false);

            if (empty($post->edit)) {
                $posts = forumimproved_get_all_discussion_posts($discussion->id);
                forumimproved_print_posts_nested($course, $cm, $forum, $discussion, $post, false, false, $posts);
            }
        } else {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(format_string($forum->name), 2);
            echo $OUTPUT->confirm(get_string("deletesure", "forumimproved", $replycount),
                         "post.php?delete=$delete&confirm=$delete",
                         $CFG->wwwroot.'/mod/forumimproved/discuss.php?d='.$post->discussion.'#p'.$post->id);

            echo $renderer->post($cm, $discussion, $post, false, null, false);
        }

    }
    echo $OUTPUT->footer();
    die;


} else if (!empty($prune)) {  // Pruning

    if (!$post = forumimproved_get_post_full($prune)) {
        print_error('invalidpostid', 'forumimproved');
    }
    if (!$discussion = $DB->get_record("forumimproved_discussions", array("id" => $post->discussion))) {
        print_error('notpartofdiscussion', 'forumimproved');
    }
    if (!$forum = $DB->get_record("forumimproved", array("id" => $discussion->forum))) {
        print_error('invalidforumid', 'forumimproved');
    }
    if ($forum->type == 'single') {
        print_error('cannotsplit', 'forumimproved');
    }
    if (!$post->parent) {
        print_error('alreadyfirstpost', 'forumimproved');
    }
    if (!$cm = get_coursemodule_from_instance("forumimproved", $forum->id, $forum->course)) { // For the logs
        print_error('invalidcoursemodule');
    } else {
        $modcontext = context_module::instance($cm->id);
    }
    if (!has_capability('mod/forumimproved:splitdiscussions', $modcontext)) {
        print_error('cannotsplit', 'forumimproved');
    }

    if (!empty($name) && confirm_sesskey()) {    // User has confirmed the prune

        $newdiscussion = new stdClass();
        $newdiscussion->course       = $discussion->course;
        $newdiscussion->forum        = $discussion->forum;
        $newdiscussion->name         = $name;
        $newdiscussion->firstpost    = $post->id;
        $newdiscussion->userid       = $discussion->userid;
        $newdiscussion->groupid      = $discussion->groupid;
        $newdiscussion->assessed     = $discussion->assessed;
        $newdiscussion->usermodified = $post->userid;
        $newdiscussion->timestart    = $discussion->timestart;
        $newdiscussion->timeend      = $discussion->timeend;

        $newid = $DB->insert_record('forumimproved_discussions', $newdiscussion);

        $newpost = new stdClass();
        $newpost->id      = $post->id;
        $newpost->parent  = 0;
        $newpost->subject = $name;
        $newpost->privatereply = 0;

        $DB->update_record("forumimproved_posts", $newpost);

        forumimproved_change_discussionid($post->id, $newid);

        // update last post in each discussion
        forumimproved_discussion_update_last_post($discussion->id);
        forumimproved_discussion_update_last_post($newid);

        // Fire events to reflect the split..
        $params = array(
            'context' => $modcontext,
            'objectid' => $discussion->id,
            'other' => array(
                'forumid' => $forum->id,
            )
        );
        $event = \mod_forumimproved\event\discussion_updated::create($params);
        $event->trigger();

        $params = array(
            'context' => $modcontext,
            'objectid' => $newid,
            'other' => array(
                'forumid' => $forum->id,
            )
        );
        $event = \mod_forumimproved\event\discussion_created::create($params);
        $event->trigger();

        $params = array(
            'context' => $modcontext,
            'objectid' => $post->id,
            'other' => array(
                'discussionid' => $newid,
                'forumid' => $forum->id,
                'forumtype' => $forum->type,
            )
        );
        $event = \mod_forumimproved\event\post_updated::create($params);
        $event->add_record_snapshot('forumimproved_discussions', $discussion);
        $event->trigger();

        redirect(forumimproved_go_back_to("discuss.php?d=$newid"));

    } else { // User just asked to prune something

        $course = $DB->get_record('course', array('id' => $forum->course));

        $PAGE->set_cm($cm);
        $renderer = $PAGE->get_renderer('mod_forumimproved');
        $PAGE->requires->js_init_call('M.mod_forumimproved.init', null, false, $renderer->get_js_module());
        $PAGE->set_context($modcontext);
        $PAGE->navbar->add(format_string($post->subject, true), new moodle_url('/mod/forumimproved/discuss.php', array('d'=>$discussion->id)));
        $PAGE->navbar->add(get_string("prune", "forumimproved"));
        $PAGE->set_title("$discussion->name: $post->subject");
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
        echo $OUTPUT->heading(format_string($forum->name), 2);
        echo $OUTPUT->heading(get_string('pruneheading', 'forum'), 3);
        echo $renderer->svg_sprite();
        if (!empty($post->privatereply)) {
            echo $OUTPUT->notification(get_string('splitprivatewarning', 'forumimproved'));
        }
        echo '<center>';

        include('prune.html');

        echo '</center>';

        // We don't have the valid unread status. Set to read so we don't see
        // the unread tag.
        $post->postread = true;
        echo $renderer->post($cm, $discussion, $post);
    }
    echo $OUTPUT->footer();
    die;
} else {
    print_error('unknowaction');

}

if (!isset($coursecontext)) {
    // Has not yet been set by post.php.
    $coursecontext = context_course::instance($forum->course);
}


// from now on user must be logged on properly

if (!$cm = get_coursemodule_from_instance('forumimproved', $forum->id, $course->id)) { // For the logs
    print_error('invalidcoursemodule');
}
$modcontext = context_module::instance($cm->id);
require_login($course, false, $cm);

if (!isset($forum->maxattachments)) {  // TODO - delete this once we add a field to the forum table
    $forum->maxattachments = 3;
}

$thresholdwarning = forumimproved_check_throttling($forum, $cm);
$mform_post = new mod_forumimproved_post_form('post.php', array('course' => $course,
                                                        'cm' => $cm,
                                                        'coursecontext' => $coursecontext,
                                                        'modcontext' => $modcontext,
                                                        'forum' => $forum,
                                                        'post' => $post,
                                                        'thresholdwarning' => $thresholdwarning,
                                                        'edit' => $edit), 'post', '', array('id' => 'mformforumimproved'));

$draftitemid = file_get_submitted_draft_itemid('attachments');
file_prepare_draft_area($draftitemid, $modcontext->id, 'mod_forumimproved', 'attachment', empty($post->id)?null:$post->id, mod_forumimproved_post_form::attachment_options($forum));

//load data into form NOW!

if ($USER->id != $post->userid) {   // Not the original author, so add a message to the end
    $data = new stdClass();
    $data->date = userdate($post->modified);
    if ($post->messageformat == FORMAT_HTML) {
        $data->name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'&course='.$post->course.'">'.
                       fullname($USER).'</a>';
        $post->message .= '<p class="edited">('.get_string('editedby', 'forumimproved', $data).')</p>';
    } else {
        $data->name = fullname($USER);
        $post->message .= "\n\n(".get_string('editedby', 'forumimproved', $data).')';
    }
    unset($data);
}

$formheading = '';
if (!empty($parent)) {
    $formheading = get_string("yourreply", "forumimproved");
} else {
    if ($forum->type == 'qanda') {
        $formheading = get_string('yournewquestion', 'forumimproved');
    } else {
        $formheading = get_string('yournewtopic', 'forumimproved');
    }
}

if (forumimproved_is_subscribed($USER->id, $forum->id)) {
    $subscribe = true;

} else if (forumimproved_user_has_posted($forum->id, 0, $USER->id)) {
    $subscribe = false;

} else {
    // user not posted yet - use subscription default specified in profile
    $subscribe = !empty($USER->autosubscribe);
}

$postid = empty($post->id) ? null : $post->id;
$draftid_editor = file_get_submitted_draft_itemid('message');
$currenttext = file_prepare_draft_area($draftid_editor, $modcontext->id, 'mod_forumimproved', 'post', $postid, mod_forumimproved_post_form::editor_options($modcontext, $postid), $post->message);
$mform_post->set_data(array(        'attachments'=>$draftitemid,
                                    'subject'=>$post->subject,
                                    'message'=>array(
                                        'text'=>$currenttext,
                                        'format'=>empty($post->messageformat) ? editors_get_preferred_format() : $post->messageformat,
                                        'itemid'=>$draftid_editor
                                    ),
                                    'subscribe'=>$subscribe?1:0,
                                    'mailnow'=>!empty($post->mailnow),
                                    'userid'=>$post->userid,
                                    'parent'=>$post->parent,
                                    'reveal'=>$post->reveal,
                                    'privatereply'=>$post->privatereply,
                                    'discussion'=>$post->discussion,
                                    'course'=>$course->id) +
                                    $page_params +

                            (isset($post->format)?array(
                                    'format'=>$post->format):
                                array())+

                            (isset($discussion->timestart)?array(
                                    'timestart'=>$discussion->timestart):
                                array())+

                            (isset($discussion->timeend)?array(
                                    'timeend'=>$discussion->timeend):
                                array())+

                            (isset($post->groupid)?array(
                                    'groupid'=>$post->groupid):
                                array())+

                            (isset($discussion->id)?
                                    array('discussion'=>$discussion->id):
                                    array()));

if ($fromform = $mform_post->get_data()) {

    if (empty($SESSION->fromurl)) {
        $errordestination = "$CFG->wwwroot/mod/forumimproved/view.php?f=$forum->id";
    } else {
        $errordestination = $SESSION->fromurl;
    }

    $fromform->itemid        = $fromform->message['itemid'];
    $fromform->messageformat = $fromform->message['format'];
    $fromform->message       = $fromform->message['text'];
    // WARNING: the $fromform->message array has been overwritten, do not use it anymore!
    $fromform->messagetrust  = trusttext_trusted($modcontext);

    $contextcheck = isset($fromform->groupinfo) && has_capability('mod/forumimproved:movediscussions', $modcontext);

    if ($fromform->edit) {           // Updating a post
        unset($fromform->groupid);
        $fromform->id = $fromform->edit;
        $message = '';

        //fix for bug #4314
        if (!$realpost = $DB->get_record('forumimproved_posts', array('id' => $fromform->id))) {
            $realpost = new stdClass();
            $realpost->userid = -1;
        }


        // if user has edit any post capability
        // or has either startnewdiscussion or reply capability and is editting own post
        // then he can proceed
        // MDL-7066
        if ( !(($realpost->userid == $USER->id && (has_capability('mod/forumimproved:replypost', $modcontext)
                            || has_capability('mod/forumimproved:startdiscussion', $modcontext))) ||
                            has_capability('mod/forumimproved:editanypost', $modcontext)) ) {
            print_error('cannotupdatepost', 'forumimproved');
        }

        // If the user has access to all groups and they are changing the group, then update the post.
        if ($contextcheck) {
            if (empty($fromform->groupinfo)) {
                $fromform->groupinfo = -1;
            }
            $DB->set_field('forumimproved_discussions' ,'groupid' , $fromform->groupinfo, array('firstpost' => $fromform->id));
        }

        $updatepost = $fromform; //realpost
        $updatepost->forum = $forum->id;
        if (!forumimproved_update_post($updatepost, $mform_post, $message)) {
            print_error("couldnotupdate", "forumimproved", $errordestination);
        }

        // MDL-11818
        if (($forum->type == 'single') && ($updatepost->parent == '0')){ // updating first post of single discussion type -> updating forum intro
            $forum->intro = $updatepost->message;
            $forum->timemodified = time();
            $DB->update_record("forumimproved", $forum);
        }

        $timemessage = 2;
        if (!empty($message)) { // if we're printing stuff about the file upload
            $timemessage = 4;
        }

        if ($realpost->userid == $USER->id) {
            $message .= '<br />'.get_string("postupdated", "forumimproved");
        } else {
            $realuser = $DB->get_record('user', array('id' => $realpost->userid));
            $freshpost = $DB->get_record('forumimproved_posts', array('id' => $fromform->id));

            if ($realuser && $freshpost) {
                $postuser = forumimproved_get_postuser($realuser, $freshpost, $forum, $modcontext);
                $message .= '<br />'.get_string('editedpostupdated', 'forumimproved', $postuser->fullname);
            } else {
                $message .= '<br />'.get_string('postupdated', 'forumimproved');
            }
        }

        if ($subscribemessage = forumimproved_post_subscription($fromform, $forum)) {
            $timemessage = 4;
        }
        if ($forum->type == 'single') {
            // Single discussion forums are an exception. We show
            // the forum itself since it only has one discussion
            // thread.
            $discussionurl = "view.php?f=$forum->id";
        } else {
            $discussionurl = "discuss.php?d=$discussion->id#p$fromform->id";
        }

        $params = array(
            'context' => $modcontext,
            'objectid' => $fromform->id,
            'other' => array(
                'discussionid' => $discussion->id,
                'forumid' => $forum->id,
                'forumtype' => $forum->type,
            )
        );

        if ($realpost->userid !== $USER->id) {
            $params['relateduserid'] = $realpost->userid;
        }

        $event = \mod_forumimproved\event\post_updated::create($params);
        $event->add_record_snapshot('forumimproved_discussions', $discussion);
        $event->trigger();

        redirect(forumimproved_go_back_to("$discussionurl"), $message.$subscribemessage, $timemessage);

        exit;


    } else if ($fromform->discussion) { // Adding a new post to an existing discussion
        // Before we add this we must check that the user will not exceed the blocking threshold.
        forumimproved_check_blocking_threshold($thresholdwarning);

        unset($fromform->groupid);
        $message = '';
        $addpost = $fromform;
        $addpost->forum=$forum->id;
        if ($fromform->id = forumimproved_add_new_post($addpost, $mform_post, $message)) {

            $timemessage = 2;
            if (!empty($message)) { // if we're printing stuff about the file upload
                $timemessage = 4;
            }

            if ($subscribemessage = forumimproved_post_subscription($fromform, $forum)) {
                $timemessage = 4;
            }

            if (!empty($fromform->mailnow)) {
                $message .= get_string("postmailnow", "forumimproved");
                $timemessage = 4;
            } else {
                $message .= '<p>'.get_string("postaddedsuccess", "forumimproved") . '</p>';
                $message .= '<p>'.get_string("postaddedtimeleft", "forumimproved", format_time($CFG->maxeditingtime)) . '</p>';
            }

            if ($forum->type == 'single') {
                // Single discussion forums are an exception. We show
                // the forum itself since it only has one discussion
                // thread.
                $discussionurl = "view.php?f=$forum->id";
            } else {
                $discussionurl = "discuss.php?d=$discussion->id";
            }
            $post   = $DB->get_record('forumimproved_posts', array('id' => $fromform->id), '*', MUST_EXIST);
            $params = array(
                'context' => $modcontext,
                'objectid' => $fromform->id,
                'other' => array(
                    'discussionid' => $discussion->id,
                    'forumid' => $forum->id,
                    'forumtype' => $forum->type,
                )
            );
            $event = \mod_forumimproved\event\post_created::create($params);
            $event->add_record_snapshot('forumimproved_posts', $post);
            $event->add_record_snapshot('forumimproved_discussions', $discussion);
            $event->trigger();

            // Update completion state
            $completion=new completion_info($course);
            if($completion->is_enabled($cm) &&
                ($forum->completionreplies || $forum->completionposts)) {
                $completion->update_state($cm,COMPLETION_COMPLETE);
            }

            redirect(forumimproved_go_back_to("$discussionurl#p$fromform->id"), $message.$subscribemessage, $timemessage);

        } else {
            print_error("couldnotadd", "forumimproved", $errordestination);
        }
        exit;

    } else { // Adding a new discussion.
        // Before we add this we must check that the user will not exceed the blocking threshold.
        forumimproved_check_blocking_threshold($thresholdwarning);

        if (!forumimproved_user_can_post_discussion($forum, $fromform->groupid, -1, $cm, $modcontext)) {
            print_error('cannotcreatediscussion', 'forumimproved');
        }
        // If the user has access all groups capability let them choose the group.
        if ($contextcheck) {
            $fromform->groupid = $fromform->groupinfo;
        }
        if (empty($fromform->groupid)) {
            $fromform->groupid = -1;
        }

        $fromform->mailnow = empty($fromform->mailnow) ? 0 : 1;
        $fromform->reveal = empty($fromform->reveal) ? 0 : 1;

        $discussion = $fromform;
        $discussion->name    = $fromform->subject;

        $newstopic = false;
        if ($forum->type == 'news' && !$fromform->parent) {
            $newstopic = true;
        }
        $discussion->timestart = $fromform->timestart;
        $discussion->timeend = $fromform->timeend;

        $message = '';
        if ($discussion->id = forumimproved_add_discussion($discussion, $mform_post, $message)) {

            $params = array(
                'context' => $modcontext,
                'objectid' => $discussion->id,
                'other' => array(
                    'forumid' => $forum->id,
                )
            );
            $event = \mod_forumimproved\event\discussion_created::create($params);
            $event->add_record_snapshot('forumimproved_discussions', $discussion);
            $event->trigger();

            $timemessage = 2;
            if (!empty($message)) { // if we're printing stuff about the file upload
                $timemessage = 4;
            }

            if ($fromform->mailnow) {
                $message .= get_string("postmailnow", "forumimproved");
                $timemessage = 4;
            } else {
                $message .= '<p>'.get_string("postaddedsuccess", "forumimproved") . '</p>';
                $message .= '<p>'.get_string("postaddedtimeleft", "forumimproved", format_time($CFG->maxeditingtime)) . '</p>';
            }

            if ($subscribemessage = forumimproved_post_subscription($discussion, $forum)) {
                $timemessage = 6;
            }

            // Update completion status
            $completion=new completion_info($course);
            if($completion->is_enabled($cm) &&
                ($forum->completiondiscussions || $forum->completionposts)) {
                $completion->update_state($cm,COMPLETION_COMPLETE);
            }

            redirect(forumimproved_go_back_to("view.php?f=$fromform->forum"), $message.$subscribemessage, $timemessage);

        } else {
            print_error("couldnotadd", "forumimproved", $errordestination);
        }

        exit;
    }
}



// To get here they need to edit a post, and the $post
// variable will be loaded with all the particulars,
// so bring up the form.

// $course, $forum are defined.  $discussion is for edit and reply only.

if ($post->discussion) {
    if (! $toppost = $DB->get_record("forumimproved_posts", array("discussion" => $post->discussion, "parent" => 0))) {
        print_error('cannotfindparentpost', 'forumimproved', '', $post->id);
    }
} else {
    $toppost = new stdClass();
    $toppost->subject = get_string("addanewtopic", "forumimproved");
}

if (empty($post->edit)) {
    $post->edit = '';
}

if (empty($discussion->name)) {
    if (empty($discussion)) {
        $discussion = new stdClass();
    }
    $discussion->name = $forum->name;
}
if ($forum->type == 'single') {
    // There is only one discussion thread for this forum type. We should
    // not show the discussion name (same as forum name in this case) in
    // the breadcrumbs.
    $strdiscussionname = '';
} else {
    // Show the discussion name in the breadcrumbs.
    $strdiscussionname = $discussion->name.':';
}

$forcefocus = empty($reply) ? NULL : 'message';

if (!empty($discussion->id)) {
    $PAGE->navbar->add(format_string($toppost->subject, true), "discuss.php?d=$discussion->id");
}

if ($post->parent) {
    $PAGE->navbar->add(get_string('reply', 'forumimproved'));
}

if ($edit) {
    $PAGE->navbar->add(get_string('edit', 'forumimproved'));
}

$PAGE->set_title("$course->shortname: $strdiscussionname $toppost->subject");
$PAGE->set_heading($course->fullname);
$renderer = $PAGE->get_renderer('mod_forumimproved');
$PAGE->requires->js_init_call('M.mod_forumimproved.init', null, false, $renderer->get_js_module());

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($forum->name), 2);

// checkup
if (!empty($parent) && !forumimproved_user_can_see_post($forum, $discussion, $post, null, $cm)) {
    print_error('cannotreply', 'forumimproved');
}
if (empty($parent) && empty($edit) && !forumimproved_user_can_post_discussion($forum, $groupid, -1, $cm, $modcontext)) {
    print_error('cannotcreatediscussion', 'forumimproved');
}

if ($forum->type == 'qanda'
            && !has_capability('mod/forumimproved:viewqandawithoutposting', $modcontext)
            && !empty($discussion->id)
            && !forumimproved_user_has_posted($forum->id, $discussion->id, $USER->id)) {
    echo $OUTPUT->notification(get_string('qandanotify','forumimproved'));
}

// If there is a warning message and we are not editing a post we need to handle the warning.
if (!empty($thresholdwarning) && !$edit) {
    // Here we want to throw an exception if they are no longer allowed to post.
    forumimproved_check_blocking_threshold($thresholdwarning);
}

if (!empty($parent)) {
    if (!$discussion = $DB->get_record('forumimproved_discussions', array('id' => $parent->discussion))) {
        print_error('notpartofdiscussion', 'forumimproved');
    }

    echo $renderer->svg_sprite();
    // We don't have the valid unread status. Set to read so we don't see
    // the unread tag.
    $parent->postread = true;
    echo $renderer->post($cm, $discussion, $parent);
    if (empty($post->edit)) {
        if ($forum->type != 'qanda' || forumimproved_user_can_see_discussion($forum, $discussion, $modcontext)) {
            $posts = forumimproved_get_all_discussion_posts($discussion->id);
        }
    }
} else {
    if (!empty($forum->intro)) {
        echo $OUTPUT->box(format_module_intro('forumimproved', $forum, $cm->id), 'generalbox', 'intro');

        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir.'/plagiarismlib.php');
            echo plagiarism_print_disclosure($cm->id);
        }
    }
}
if (!empty($formheading)) {
    echo $OUTPUT->heading($formheading, 4);
}
$mform_post->display();

echo $OUTPUT->footer();
