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
 * Strings for component 'forumplusone', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   mod_forumplusone
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @copyright Copyright (c) 2016 Paris Descartes University (http://www.univ-paris5.fr/)
 */

$string['activityoverview'] = 'There are new forum posts';
$string['addanewtopic'] = 'Add a new discussion';
$string['advancedsearch'] = 'Advanced search';
$string['allforums'] = 'All forums';
$string['allowdiscussions'] = 'Can a {$a} post to this forum?';
$string['allowsallsubscribe'] = 'This forum allows everyone to choose whether to subscribe or not';
$string['allowsdiscussions'] = 'This forum allows each person to start one discussion topic.';
$string['allsubscribe'] = 'Subscribe to all forums';
$string['allunsubscribe'] = 'Unsubscribe from all forums';
$string['alreadyfirstpost'] = 'This is already the first post in the discussion';
$string['anyfile'] = 'Any file';
$string['areaattachment'] = 'Attachments';
$string['areapost'] = 'Messages';
$string['attachment'] = 'Attachment';
$string['attachment_help'] = 'You can optionally attach one or more files to a forum post. If you attach an image, it will be displayed after the message.';
$string['attachmentnopost'] = 'You cannot export attachments without a post id';
$string['attachments'] = 'Attachments';
$string['blockafter'] = 'Post threshold for blocking';
$string['blockafter_help'] = 'This setting specifies the maximum number of posts that a user can post in the given time period. Users with the capability mod/forumplusone:postwithoutthrottling are exempt from post limits.';
$string['blockperiod'] = 'Time period for blocking';
$string['blockperiod_help'] = 'Students can be blocked from posting more than a given number of posts in a given time period. Users with the capability mod/forumplusone:postwithoutthrottling are exempt from post limits.';
$string['blockperioddisabled'] = 'Don\'t block';
$string['blogforum'] = 'Standard forum displayed in a blog-like format';
$string['bynameondate'] = 'by {$a->name} - {$a->date}';
$string['cannotadd'] = 'Could not add the discussion for this forum';
$string['cannotadddiscussion'] = 'Adding discussions to this forum requires group membership.';
$string['cannotadddiscussionall'] = 'You do not have permission to add a new discussion topic for all participants.';
$string['cannotaddsubscriber'] = 'Could not add subscriber with id {$a} to this forum!';
$string['cannotaddteacherforumto'] = 'Could not add converted teacher forum instance to section 0 in the course';
$string['cannotcreatediscussion'] = 'Could not create new discussion';
$string['cannotcreateinstanceforteacher'] = 'Could not create new course module instance for the teacher forum';
$string['cannotdeletepost'] = 'You can\'t delete this post!';
$string['cannoteditposts'] = 'You can\'t edit other people\'s posts!';
$string['cannotfinddiscussion'] = 'Could not find the discussion in this forum';
$string['cannotfindfirstpost'] = 'Could not find the first post in this forum';
$string['cannotfindorcreateforum'] = 'Could not find or create a main news forum for the site';
$string['cannotfindparentpost'] = 'Could not find top parent of post {$a}';
$string['cannotmovefromsingleforum'] = 'Cannot move discussion from a simple single discussion forum';
$string['cannotmovenotvisible'] = 'Forum not visible';
$string['cannotmovetonotexist'] = 'You can\'t move to that forum - it doesn\'t exist!';
$string['cannotmovetonotfound'] = 'Target forum not found in this course.';
$string['cannotmovetosingleforum'] = 'Cannot move discussion to a simple single discussion forum';
$string['cannotpurgecachedrss'] = 'Could not purge the cached RSS feeds for the source and/or destination forum(s) - check your file permissionsforums';
$string['cannotremovesubscriber'] = 'Could not remove subscriber with id {$a} from this forum!';
$string['cannotreply'] = 'You cannot reply to this post';
$string['cannotsplit'] = 'Discussions from this forum cannot be split';
$string['cannotsubscribe'] = 'Sorry, but you must be a group member to subscribe.';
$string['cannottrack'] = 'Could not stop tracking that forum';
$string['cannotunsubscribe'] = 'Could not unsubscribe you from that forum';
$string['cannotupdatepost'] = 'You can not update this post';
$string['cannotviewpostyet'] = 'You cannot read other students questions in this discussion yet because you haven\'t posted';
$string['cannotviewusersposts'] = 'There are no posts made by this user that you are able to view.';
$string['cleanreadtime'] = 'Mark old posts as read hour';
$string['completiondiscussions'] = 'Student must create discussions:';
$string['completiondiscussionsgroup'] = 'Require discussions';
$string['completiondiscussionshelp'] = 'requiring discussions to complete';
$string['completionposts'] = 'Student must post discussions or replies:';
$string['completionpostsgroup'] = 'Require posts';
$string['completionpostshelp'] = 'requiring discussions or replies to complete';
$string['completionreplies'] = 'Student must post replies:';
$string['completionrepliesgroup'] = 'Require replies';
$string['completionreplieshelp'] = 'requiring replies to complete';
$string['configcleanreadtime'] = 'The hour of the day to clean old posts from the \'read\' table.';
$string['configdigestmailtime'] = 'People who choose to have emails sent to them in digest form will be emailed the digest daily. This setting controls which time of day the daily mail will be sent (the next cron that runs after this hour will send it).';
$string['configenablerssfeeds'] = 'This switch will enable the possibility of RSS feeds for all forums.  You will still need to turn feeds on manually in the settings for each forum.';
$string['configenabletimedposts'] = 'Set to \'yes\' if you want to allow setting of display periods when posting a new forum discussion (Experimental as not yet fully tested)';
$string['configlongpost'] = 'Any post over this length (in characters not including HTML) is considered long. Posts displayed on the site front page, social format course pages, or user profiles are shortened to a natural break somewhere between the forumplusone_shortpost and forumplusone_longpost values.';
$string['configmanydiscussions'] = 'Maximum number of discussions shown in a forum per page';
$string['configmaxattachments'] = 'Default maximum number of attachments allowed per post.';
$string['configmaxbytes'] = 'Default maximum size for all forum attachments on the site (subject to course limits and other local settings)';
$string['configoldpostdays'] = 'Number of days old at which any post is considered read.';
$string['configreplytouser'] = 'When a forum post is mailed out, should it contain the user\'s email address so that recipients can reply personally rather than via the forum? Even if set to \'Yes\' users can choose in their profile to keep their email address secret.';
$string['configshortpost'] = 'Any post under this length (in characters not including HTML) is considered short (see below).';
$string['configtrackingtype'] = 'Default setting for read tracking.';
$string['configusermarksread'] = 'If \'yes\', the user must manually mark a post as read. If \'no\', when the post is viewed it is marked as read.';
$string['confirmsubscribe'] = 'Do you really want to subscribe to forum \'{$a}\'?';
$string['confirmunsubscribe'] = 'Do you really want to unsubscribe from forum \'{$a}\'?';
$string['couldnotadd'] = 'Could not add your post due to an unknown error';
$string['couldnotdeletereplies'] = 'Sorry, that cannot be deleted as people have already responded to it';
$string['couldnotupdate'] = 'Could not update your post due to an unknown error';
$string['crontask'] = 'Forum mailings and maintenance jobs';
$string['delete'] = 'Delete';
$string['deleteddiscussion'] = 'The discussion topic has been deleted';
$string['deletedpost'] = 'The post has been deleted';
$string['deletedposts'] = 'Those posts have been deleted';
$string['deletesure'] = 'Are you sure you want to delete this post?';
$string['deletesureplural'] = 'Are you sure you want to delete this post and all replies? ({$a} posts)';
$string['digestmailheader'] = 'This is your daily digest of new posts from the {$a->sitename} forums. To change your default forum email preferences, go to {$a->userprefs}.';
$string['digestmailpost'] = 'Change your forum digest preferences';
$string['digestmailprefs'] = 'your user profile';
$string['digestmailsubject'] = '{$a}: forum digest';
$string['digestmailtime'] = 'Hour to send digest emails';
$string['digestsentusers'] = 'Email digests successfully sent to {$a} users.';
$string['disallowsubscribe'] = 'Subscriptions not allowed';
$string['disallowsubscribeteacher'] = 'Subscriptions not allowed (except for teachers)';
$string['discussion'] = 'Discussion';
$string['discussionmoved'] = 'This discussion has been moved to \'{$a}\'.';
$string['discussionmovedpost'] = 'This discussion has been moved to <a href="{$a->discusshref}">here</a> in the forum <a href="{$a->forumhref}">{$a->forumname}</a>';
$string['discussionname'] = 'Discussion name';
$string['discussions'] = 'Discussions';
$string['discussionsstartedby'] = 'Discussions started by {$a}';
$string['discussionsstartedbyrecent'] = 'Discussions recently started by {$a}';
$string['discussionsstartedbyuserincourse'] = 'Discussions started by {$a->fullname} in {$a->coursename}';
$string['discussthistopic'] = 'Discuss this topic';
$string['displayend'] = 'Display end';
$string['displayend_help'] = 'This setting specifies whether a forum post should be hidden after a certain date. Note that administrators can always view forum posts.';
$string['displayperiod'] = 'Display period';
$string['displaystart'] = 'Display start';
$string['displaystart_help'] = 'This setting specifies whether a forum post should be displayed from a certain date. Note that administrators can always view forum posts.';
$string['displaywordcount'] = 'Display word count';
$string['displaywordcount_help'] = 'This setting specifies whether the word count of each post should be displayed or not.';
$string['eachuserforum'] = 'Each person posts one discussion';
$string['edit'] = 'Edit';
$string['editedby'] = 'Edited by {$a->name} - original submission {$a->date}';
$string['editedpostupdated'] = '{$a}\'s post was updated';
$string['editing'] = 'Editing';
$string['eventcoursesearched'] = 'Course searched';
$string['eventdiscussioncreated'] = 'Discussion created';
$string['eventdiscussionupdated'] = 'Discussion updated';
$string['eventdiscussiondeleted'] = 'Discussion deleted';
$string['eventdiscussionmoved'] = 'Discussion moved';
$string['eventdiscussionviewed'] = 'Discussion viewed';
$string['eventuserreportviewed'] = 'User report viewed';
$string['eventpostcreated'] = 'Post created';
$string['eventpostdeleted'] = 'Post deleted';
$string['eventpostupdated'] = 'Post updated';
$string['eventreadtrackingdisabled'] = 'Read tracking disabled';
$string['eventreadtrackingenabled'] = 'Read tracking enabled';
$string['eventsubscribersviewed'] = 'Subscribers viewed';
$string['eventsubscriptioncreated'] = 'Subscription created';
$string['eventsubscriptiondeleted'] = 'Subscription deleted';
$string['emaildigestcompleteshort'] = 'Complete posts';
$string['emaildigestdefault'] = 'Default ({$a})';
$string['emaildigestoffshort'] = 'No digest';
$string['emaildigestsubjectsshort'] = 'Subjects only';
$string['emaildigesttype'] = 'Email digest options';
$string['emaildigesttype_help'] = 'The type of notification that you will receive for each forum.

* Default - follow the digest setting found in your user profile. If you update your profile, then that change will be reflected here too;
* No digest - you will receive one e-mail per forum post;
* Digest - complete posts - you will receive one digest e-mail per day containing the complete contents of each forum post;
* Digest - subjects only - you will receive one digest e-mail per day containing just the subject of each forum post.
';
$string['emaildigestupdated'] = 'The e-mail digest option was changed to \'{$a->maildigesttitle}\' for the forum \'{$a->forum}\'. {$a->maildigestdescription}';
$string['emaildigestupdated_default'] = 'Your default profile setting of \'{$a->maildigesttitle}\' was used for the forum \'{$a->forum}\'. {$a->maildigestdescription}.';
$string['emaildigest_0'] = 'You will receive one e-mail per forum post.';
$string['emaildigest_1'] = 'You will receive one digest e-mail per day containing the complete contents of each forum post.';
$string['emaildigest_2'] = 'You will receive one digest e-mail per day containing the subject of each forum post.';
$string['emptymessage'] = 'Something was wrong with your post. Perhaps you left it blank or the attachment was too big. Your changes have NOT been saved.';
$string['erroremptymessage'] = 'Post message cannot be empty';
$string['erroremptysubject'] = 'Post subject cannot be empty.';
$string['errorenrolmentrequired'] = 'You must be enrolled in this course to access this content';
$string['errorwhiledelete'] = 'An error occurred while deleting record.';
$string['eventassessableuploaded'] = 'Some content has been posted.';
$string['everyonecanchoose'] = 'Everyone can choose to be subscribed';
$string['everyonecannowchoose'] = 'Everyone can now choose to be subscribed';
$string['everyoneisnowsubscribed'] = 'Everyone is now subscribed to this forum';
$string['everyoneissubscribed'] = 'Everyone is subscribed to this forum';
$string['existingsubscribers'] = 'Existing subscribers';
$string['exportdiscussion'] = 'Export whole discussion';
$string['forcessubscribe'] = 'This forum forces everyone to be subscribed';
$string['forum'] = 'Forum';
$string['forumplusone:addinstance'] = 'Add a new forum';
$string['forumplusone:allowforcesubscribe'] = 'Allow force subscribe';
$string['forumauthorhidden'] = 'Author (hidden)';
$string['forumblockingalmosttoomanyposts'] = 'You are approaching the posting threshold. You have posted {$a->numposts} times in the last {$a->blockperiod} and the limit is {$a->blockafter} posts.';
$string['forumbodyhidden'] = 'This post cannot be viewed by you, probably because you have not posted in the discussion, the maximum editing time hasn\'t passed yet, the discussion has not started or the discussion has expired.';
$string['forumplusone:createattachment'] = 'Create attachments';
$string['forumplusone:deleteanypost'] = 'Delete any posts (anytime)';
$string['forumplusone:deleteownpost'] = 'Delete own posts (within deadline)';
$string['forumplusone:editanypost'] = 'Edit any post';
$string['forumplusone:exportdiscussion'] = 'Export whole discussion';
$string['forumplusone:exportownpost'] = 'Export own post';
$string['forumplusone:exportpost'] = 'Export post';
$string['forumintro'] = 'Description';
$string['forumplusone:managesubscriptions'] = 'Manage subscriptions';
$string['forumplusone:movediscussions'] = 'Move discussions';
$string['forumplusone:postwithoutthrottling'] = 'Exempt from post threshold';
$string['forumname'] = 'Forum name';
$string['forumposts'] = 'Forum posts';
$string['forumplusone:addnews'] = 'Add news';
$string['forumplusone:addquestion'] = 'Add question';
$string['forumplusone:rate'] = 'Rate posts';
$string['forumplusone:replynews'] = 'Reply to news';
$string['forumplusone:replypost'] = 'Reply to posts';
$string['forums'] = 'Forums';
$string['forumplusone:splitdiscussions'] = 'Split discussions';
$string['forumplusone:startdiscussion'] = 'Start new discussions';
$string['forumsubjecthidden'] = 'Subject (hidden)';
$string['forumtracked'] = 'Unread posts are being tracked';
$string['forumtrackednot'] = 'Unread posts are not being tracked';
$string['forumtype'] = 'Forum type';
$string['forumtype_help'] = 'There are 5 forum types:

* A single simple discussion - A single discussion topic to which everyone can reply (cannot be used with separate groups)
* Each person posts one discussion - Each student can post exactly one new discussion topic, to which everyone can then reply
* Q and A forum - Students must first post their perspectives before viewing other students\' posts
* Standard forum displayed in a blog-like format - An open forum where anyone can start a new discussion at any time, and in which discussion topics are displayed on one page with "Discuss this topic" links
* Standard forum for general use - An open forum where anyone can start a new discussion at any time';
$string['forumplusone:viewallratings'] = 'View all raw ratings given by individuals';
$string['forumplusone:viewanyrating'] = 'View total ratings that anyone received';
$string['forumplusone:viewdiscussion'] = 'View discussions';
$string['forumplusone:viewhiddentimedposts'] = 'View hidden timed posts';
$string['forumplusone:viewqandawithoutposting'] = 'Always see Q and A posts';
$string['forumplusone:viewrating'] = 'View the total rating you received';
$string['forumplusone:viewsubscribers'] = 'View subscribers';
$string['forumplusone:viewposters'] = 'View forum posters';
$string['forumplusone:allowprivate'] = 'Allow user to respond privately';
$string['forumplusone:viewhiddendiscussion'] = 'View hidden discussions';
$string['generalforum'] = 'Standard forum for general use';
$string['generalforums'] = 'General forums';
$string['hiddenforumpost'] = 'Hidden forum post';
$string['inforum'] = 'in {$a}';
$string['introblog'] = 'The posts in this forum were copied here automatically from blogs of users in this course because those blog entries are no longer available';
$string['intronews'] = 'General news and announcements';
$string['introsocial'] = 'An open forum for chatting about anything you want';
$string['introteacher'] = 'A forum for teacher-only notes and discussion';
$string['invalidaccess'] = 'This page was not accessed correctly';
$string['invaliddiscussionid'] = 'Discussion ID was incorrect or no longer exists';
$string['invaliddigestsetting'] = 'An invalid mail digest setting was provided';
$string['invalidforcesubscribe'] = 'Invalid force subscription mode';
$string['invalidforumid'] = 'Forum ID was incorrect';
$string['invalidparentpostid'] = 'Parent post ID was incorrect';
$string['invalidpostid'] = 'Invalid post ID - {$a}';
$string['lastposttimeago'] = 'Last reply the {$a}';
$string['learningforums'] = 'Learning forums';
$string['longpost'] = 'Long post';
$string['mailnow'] = 'Mail now';
$string['manydiscussions'] = 'Discussions per page';
$string['markalldread'] = 'Mark all posts in this discussion read.';
$string['markallread'] = 'Mark all posts in this forum read.';
$string['markread'] = 'Mark read';
$string['markreadbutton'] = 'Mark<br />read';
$string['markunread'] = 'Mark unread';
$string['markunreadbutton'] = 'Mark<br />unread';
$string['maxattachments'] = 'Maximum number of attachments';
$string['maxattachments_help'] = 'This setting specifies the maximum number of files that can be attached to a forum post.';
$string['maxattachmentsize'] = 'Maximum attachment size';
$string['maxattachmentsize_help'] = 'This setting specifies the largest size of file that can be attached to a forum post.';
$string['maxtimehaspassed'] = 'Sorry, but the maximum time for editing this post ({$a}) has passed!';
$string['message'] = 'Message';
$string['messageprovider:digests'] = 'Subscribed forum plusone digests';
$string['messageprovider:posts'] = 'Subscribed forum plusone posts';
$string['missingsearchterms'] = 'The following search terms occur only in the HTML markup of this message:';
$string['modeflatnewestfirst'] = 'Display replies flat, with newest first';
$string['modeflatoldestfirst'] = 'Display replies flat, with oldest first';
$string['modenested'] = 'Display replies in nested form';
$string['modethreaded'] = 'Display replies in threaded form';
$string['modulename'] = 'Forum+1';
$string['modulename_help'] = 'The Forum+1 activity module enables participants to have asynchronous discussions,
i.e., discussions that take place over an extended period of time.

There are several forum types to choose from, such as a standard forum where anyone can start a new discussion at any time; a forum where each student can post exactly one discussion; or a question and answer forum where students must first post before being able to view other students\' posts. A teacher can allow files to be attached to forum posts. Attached images are displayed in the forum post.

Participants can subscribe to a forum to receive notifications of new forum posts. A teacher can set the subscription mode to optional, forced or auto, or prevent subscription completely. If required, students can be blocked from posting more than a given number of posts in a given time period; this can prevent individuals from dominating discussions.

Forum posts can be rated by teachers or students (peer evaluation). Ratings can be aggregated to form a final grade which is recorded in the gradebook.

Forums have many uses, such as:

* A social space for students to get to know each other
* For course announcements (using a news forum with forced subscription)
* For discussing course content or reading materials
* For continuing online an issue raised previously in a face-to-face session
* For teacher-only discussions (using a hidden forum)
* A help centre where tutors and students can give advice
* A one-on-one support area for private student-teacher communications (using a forum with separate groups and with one student per group)
* For extension activities, for example ‘brain teasers’ for students to ponder and suggest solutions';
$string['modulename_link'] = 'mod/forumplusone/view';
$string['modulenameplural'] = 'Forums+1';
$string['more'] = 'more';
$string['movedmarker'] = '(Moved)';
$string['movethisdiscussionto'] = 'Move this discussion to ...';
$string['mustprovidediscussionorpost'] = 'You must provide either a discussion id or post id to export';
$string['namenews'] = 'News forum';
$string['namenews_help'] = 'The news forum is a special forum for announcements that is automatically created when a course is created. A course can have only one news forum. Only teachers and administrators can post in the news forum. The "Latest news" block will display recent discussions from the news forum.';
$string['namesocial'] = 'Social forum';
$string['newforumposts'] = 'Recent forum posts';
$string['nextdiscussion'] = 'Newer discussion';
$string['noattachments'] = 'There are no attachments to this post';
$string['nodiscussionsstartedby'] = '{$a} has not started any discussions';
$string['nodiscussionsstartedbyyou'] = 'You haven\'t started any discussions yet';
$string['noguestpost'] = 'Sorry, guests are not allowed to post.';
$string['noguesttracking'] = 'Sorry, guests are not allowed to set tracking options.';
$string['nomorepostscontaining'] = 'No more posts containing \'{$a}\' were found';
$string['noonecansubscribenow'] = 'Subscriptions are now disallowed';
$string['nopermissiontosubscribe'] = 'You do not have the permission to view forum subscribers';
$string['nopermissiontoview'] = 'You do not have permissions to view this post';
$string['nopostforum'] = 'Sorry, you are not allowed to post to this forum';
$string['noposts'] = 'No posts';
$string['nopostsmadebyuser'] = '{$a} has made no posts';
$string['nopostsmadebyyou'] = 'You haven\'t made any posts';
$string['nosubscribers'] = 'There are no subscribers yet for this forum';
$string['notexists'] = 'Discussion no longer exists';
$string['nothingnew'] = 'Nothing new for {$a}';
$string['notingroup'] = 'Sorry, but you need to be part of a group to see this forum.';
$string['notinstalled'] = 'The forum module is not installed';
$string['notpartofdiscussion'] = 'This post is not part of a discussion!';
$string['notrackforum'] = 'Don\'t track unread posts';
$string['noviewdiscussionspermission'] = 'You do not have the permission to view discussions in this forum';
$string['nowallsubscribed'] = 'All forums in {$a} are subscribed.';
$string['nowallunsubscribed'] = 'All forums in {$a} are not subscribed.';
$string['nownotsubscribed'] = '{$a->name} will NOT be notified of new posts in \'{$a->forum}\'';
$string['nownottracking'] = '{$a->name} is no longer tracking \'{$a->forum}\'.';
$string['nowsubscribed'] = '{$a->name} will be notified of new posts in \'{$a->forum}\'';
$string['nowtracking'] = '{$a->name} is now tracking \'{$a->forum}\'.';
$string['numposts'] = '{$a} posts';
$string['olderdiscussions'] = 'Older discussions';
$string['oldertopics'] = 'Older topics';
$string['oldpostdays'] = 'Read after days';
$string['openmode0'] = 'No discussions, no replies';
$string['openmode1'] = 'No discussions, but replies are allowed';
$string['openmode2'] = 'Discussions and replies are allowed';
$string['overviewnumpostssince'] = '{$a} posts since last login';
$string['overviewnumunread'] = '{$a} total unread';
$string['page-mod-forumplusone-x'] = 'Any forum module page';
$string['page-mod-forumplusone-view'] = 'Forum module main page';
$string['page-mod-forumplusone-discuss'] = 'Forum module discussion thread page';
$string['parent'] = 'Show parent';
$string['parentofthispost'] = 'Parent of this post';
$string['pluginadministration'] = 'Forum administration';
$string['pluginname'] = 'Forum+1';
$string['postadded'] = '<p>Your post was successfully added.</p> <p>You have {$a} to edit it if you want to make any changes.</p>';
$string['postaddedsuccess'] = 'Your post was successfully added.';
$string['postaddedtimeleft'] = 'You have {$a} to edit it if you want to make any changes.';
$string['postbyuser'] = '{$a->post} by {$a->user}';
$string['postincontext'] = 'See this post in context';
$string['postmailinfo'] = 'This is a copy of a message posted on the {$a} website.

To reply click on this link:';
$string['postmailnow'] = '<p>This post will be mailed out immediately to all forum subscribers.</p>';
$string['postmailsubject'] = '{$a->courseshortname}: {$a->subject}';
$string['postrating1'] = 'Mostly separate knowing';
$string['postrating2'] = 'Separate and connected';
$string['postrating3'] = 'Mostly connected knowing';
$string['posts'] = 'Posts';
$string['postsmadebyuser'] = 'Posts made by {$a}';
$string['postsmadebyuserincourse'] = 'Posts made by {$a->fullname} in {$a->coursename}';
$string['postoptions'] = 'Post options';
$string['posttoforum'] = 'Post to forum';
$string['postupdated'] = 'Your post was updated';
$string['potentialsubscribers'] = 'Potential subscribers';
$string['previousdiscussion'] = 'Older discussion';
$string['processingdigest'] = 'Processing email digest for user {$a}';
$string['processingpost'] = 'Processing post {$a}';
$string['prune'] = 'Split';
$string['prunedpost'] = 'A new discussion has been created from that post';
$string['pruneheading'] = 'Split the discussion and move this post to a new discussion';
$string['qandaforum'] = 'Q and A forum';
$string['qandanotify'] = 'This is a question and answer forum. In order to see other responses to these questions, you must first post your answer';
$string['re'] = 'Re:';
$string['readtherest'] = 'Read the rest of this topic';
$string['replies'] = 'Replies';
$string['repliesmany'] = '{$a} replies so far';
$string['repliesone'] = '{$a} reply so far';
$string['reply'] = 'Reply';
$string['replyforum'] = 'Reply to forum';
$string['replytouser'] = 'Use email address in reply';
$string['resetforums'] = 'Delete posts from';
$string['resetforumsall'] = 'Delete all posts';
$string['resetdigests'] = 'Delete all per-user forum digest preferences';
$string['resetsubscriptions'] = 'Delete all forum subscriptions';
$string['resettrackprefs'] = 'Delete all forum tracking preferences';
$string['rsssubscriberssdiscussions'] = 'RSS feed of discussions';
$string['rsssubscriberssposts'] = 'RSS feed of posts';
$string['rssarticles'] = 'Number of RSS recent articles';
$string['rssarticles_help'] = 'This setting specifies the number of articles (either discussions or posts) to include in the RSS feed. Between 5 and 20 generally acceptable.';
$string['rsstype'] = 'RSS feed for this activity';
$string['rsstype_help'] = 'To enable the RSS feed for this activity, select either discussions or posts to be included in the feed.';
$string['search'] = 'Search';
$string['searchdatefrom'] = 'Posts must be newer than this';
$string['searchdateto'] = 'Posts must be older than this';
$string['searchforumintro'] = 'Please enter search terms into one or more of the following fields:';
$string['searchforums'] = 'Search';
$string['searchfullwords'] = 'These words should appear as whole words';
$string['searchnotwords'] = 'These words should NOT be included';
$string['searcholderposts'] = 'Search older posts...';
$string['searchphrase'] = 'This exact phrase must appear in the post';
$string['searchresults'] = 'Search results';
$string['searchuser'] = 'This name should match the author';
$string['searchuserid'] = 'The Moodle ID of the author';
$string['searchwhichforums'] = 'Choose which forums to search';
$string['searchwords'] = 'These words can appear anywhere in the post';
$string['seeallposts'] = 'See all posts made by this user';
$string['shortpost'] = 'Short post';
$string['showsubscribers'] = 'Show/edit forum subscribers';
$string['showrecent'] = 'Display recent posts on course page';
$string['showrecent_help'] = 'If enabled this will display recent posts on the course page.';
$string['showsubstantive'] = 'Allow marking as substantive';
$string['showsubstantive_help'] = 'If enabled this feature allows instructors to flag posts that have a substantive value.';
$string['showsubstantivedisabledglobally'] = 'Substantive flagging has been disabled globally at the plugin level.';
$string['showbookmark'] = 'Allow post bookmarking';
$string['showbookmark_help'] = 'If enabled, forum posts can be bookmarked.';
$string['showbookmarkdisabledglobally'] = 'Bookmarking has been disabled globally at the plugin level.';
$string['singleforum'] = 'A single simple discussion';
$string['smallmessage'] = '{$a->user} posted in {$a->forumname}';
$string['startedby'] = 'Started by';
$string['subject'] = 'Subject';
$string['subscribe'] = 'Subscribe to this forum';
$string['subscribeshort'] = 'Subscribe';
$string['subscribeall'] = 'Subscribe everyone to this forum';
$string['subscribeenrolledonly'] = 'Sorry, only enrolled users are allowed to subscribe to forum post notifications.';
$string['subscribed'] = 'Subscribed';
$string['subscribenone'] = 'Unsubscribe everyone from this forum';
$string['subscribers'] = 'Subscribers';
$string['subscribersto'] = 'Subscribers to \'{$a}\'';
$string['subscribestart'] = 'Send me notifications of new posts in this forum';
$string['subscribestop'] = 'I don\'t want to be notified of new posts in this forum';
$string['subscription'] = 'Subscription';
$string['subscription_help'] = 'If you are subscribed to a forum it means you will receive notification of new forum posts. Usually you can choose whether you wish to be subscribed, though sometimes subscription is forced so that everyone receives notifications.';
$string['subscriptionmode'] = 'Subscription mode';
$string['subscriptionmode_help'] = 'When a participant is subscribed to a forum it means they will receive forum post notifications. There are 4 subscription mode options:

* Optional subscription - Participants can choose whether to be subscribed
* Forced subscription - Everyone is subscribed and cannot unsubscribe
* Auto subscription - Everyone is subscribed initially but can choose to unsubscribe at any time
* Subscription disabled - Subscriptions are not allowed

Note: Any subscription mode changes will only affect users who enrol in the course in the future, and not existing users.';
$string['subscriptionoptional'] = 'Optional subscription';
$string['subscriptionforced'] = 'Forced subscription';
$string['subscriptionauto'] = 'Auto subscription';
$string['subscriptiondisabled'] = 'Subscription disabled';
$string['subscriptions'] = 'Subscriptions';
$string['thisforumisthrottled'] = 'This forum has a limit to the number of forum postings you can make in a given time period - this is currently set to {$a->blockafter} posting(s) in {$a->blockperiod}';
$string['timedposts'] = 'Timed posts';
$string['timestartenderror'] = 'Display end date cannot be earlier than the start date';
$string['trackforum'] = 'Track unread posts';
$string['tracking'] = 'Track';
$string['trackingoff'] = 'Off';
$string['trackingon'] = 'Forced';
$string['trackingoptional'] = 'Optional';
$string['trackingtype'] = 'Read tracking for this forum?';
$string['trackingtype_help'] = 'If enabled, participants can track read and unread posts in the forum and in discussions. There are three options:

* Optional - Participants can choose whether to turn tracking on or off via a link in the administration block. Forum tracking must also be enabled in the user\'s profile settings.
* Forced - Tracking is always on, regardless of user setting. Available depending on administrative setting.
* Off - Read and unread posts are not tracked.';
$string['unread'] = 'New';
$string['unreadposts'] = 'Unread posts';
$string['unreadpostsnumber'] = '{$a} unread posts';
$string['unreadpostsone'] = '1 unread post';
$string['unsubscribe'] = 'Unsubscribe from this forum';
$string['unsubscribeall'] = 'Unsubscribe from all forums';
$string['unsubscribeallconfirm'] = 'You are subscribed to {$a} forums now. Do you really want to unsubscribe from all forums and disable forum auto-subscribe?';
$string['unsubscribealldone'] = 'All optional forum subscriptions were removed. You will still receive notifications from forums with forced subscription. To manage forum notifications go to Messaging in My Profile Settings.';
$string['unsubscribeallempty'] = 'You are not subscribed to any forums. To disable all notifications from this server go to Messaging in My Profile Settings.';
$string['unsubscribed'] = 'Unsubscribed';
$string['unsubscribeshort'] = 'Unsubscribe';
$string['usermarksread'] = 'Manual message read marking';
$string['viewalldiscussions'] = 'View all discussions';
$string['warnafter'] = 'Post threshold for warning';
$string['warnafter_help'] = 'Students can be warned as they approach the maximum number of posts allowed in a given period. This setting specifies after how many posts they are warned. Users with the capability mod/forumplusone:postwithoutthrottling are exempt from post limits.';
$string['warnformorepost'] = 'Warning! There is more than one discussion in this forum - using the most recent';
$string['yournewquestion'] = 'Your new question';
$string['yournewtopic'] = 'Your new discussion topic';
$string['yourreply'] = 'Your reply';

$string['allowanonymous'] = 'Allow anonymous posting';
$string['allowanonymous_help'] = 'If checked, then the author\'s name for each post will be suppressed when viewing the forum.';
$string['anonymousfirstname'] = 'Anonymous';
$string['anonymouslastname'] = 'User';
$string['anonymousfirstnamephonetic'] = 'Anonymous';
$string['anonymouslastnamephonetic'] = 'User';
$string['anonymousmiddlename'] = '';
$string['anonymousalternatename'] = 'Anonymous';
$string['reveal'] = 'Reveal yourself in this post';
$string['reveal_help'] = 'If checked, then your name will be shown in the post and you will no longer be anonymous.';
$string['forumplusone:revealpost'] = 'Reveal yourself in an anonymous forum';
$string['forumplusone:viewflags'] = 'View post flags';
$string['viewposters'] = 'View posters';
$string['substantive'] = 'Substantive';
$string['toggle:bookmark'] = 'Bookmark';
$string['toggle:subscribe'] = 'Subscribe';
$string['toggle:substantive'] = 'Substantive';
$string['toggled:bookmark'] = 'Bookmarked';
$string['toggled:subscribe'] = 'Subscribed';
$string['toggled:substantive'] = 'Marked Substantive';
$string['jsondecodeerror'] = 'Failed to decode response, please try again.';
$string['ajaxrequesterror'] = 'Failed to complete request, please try again.';
$string['default'] = 'Default';
$string['tree'] = 'Tree';
$string['discussiondisplay'] = 'Discussion display';
$string['javascriptdisableddisplayformat'] = 'Javascript has been disabled in your browser.  Please enable Javascript and reload the page or select a different discussion display.';
$string['cansubscribediscerror'] = 'You are not allowed to subscribe to this discussion.';
$string['unsubscribedisc'] = 'Unsubscribe to this discussion';
$string['subscribedisc'] = 'Subscribe to this discussion';
$string['showdiscussionsubscribers'] = 'Show/edit discussion subscribers';
$string['discussionsubscribers'] = 'Discussion subscribers';
$string['privatereply'] = 'Private reply';
$string['privatereply_help'] = 'If checked, then this post will only be visible to the user that you are responding to.  Also, no one will be able to reply to this post.';
$string['privatereplies'] = 'Allow private replies';
$string['privatereplies_help'] = 'With this feature, instructors can send a private reply to a forum post. This reply is only viewable by the student that made the original post or reply and invisible to the rest of the students.
';
$string['privaterepliesdisabledglobally'] = 'Private replies have been disabled globally.';
$string['discussionsortkey:lastreply'] = 'Last publication date';
$string['discussionsortkey:created'] = 'Creation date';
$string['discussionsortkey:replies'] = 'Most active';
$string['discussionsortkey:subscribe'] = 'Subscribed';
$string['discussionsortkey:unread'] = 'Unread';
$string['discussionsortkey:popularity'] = 'Popularity';
$string['discussionsortkey:closed'] = 'Closed discussion';
$string['discussionsortkey:open'] = 'Open discussion';

$string['nextdiscussionx'] = '({$a}) Next >';
$string['prevdiscussionx'] = '< Previous ({$a})';
$string['modeflatfirstname'] = 'Display replies flat, by user first name';
$string['modeflatlastname'] = 'Display replies flat, by user last name';
$string['nested'] = 'Nested';
$string['startedbyx'] = 'Started by {$a}';
$string['startedbyxgroupx'] = 'Started by {$a->name} for group {$a->group}';
$string['lastpostbyx'] = 'Last post by {$a->name} on {$a->time}';
$string['repliesx'] = 'Replies: {$a}';
$string['unreadx'] = 'Unread: {$a}';
$string['clicktoexpand'] = 'Click to show post message and any replies';
$string['clicktocollapse'] = 'Click to hide post message and any replies';
$string['createdbynameondate'] = 'Created by {$a->name} on {$a->date}';
$string['expandall'] = 'Expand all';
$string['collapseall'] = 'Collapse all';
$string['gradetypenone'] = 'None';
$string['gradetypemanual'] = 'Manual';
$string['gradetyperating'] = 'Rating';
$string['gradetype'] = 'Grade Type';
$string['gradetype_help'] = 'The grade type is used to determine the method of grading.

* None: the forum is not graded.
* Manual: the forum has to be manually graded by the teacher via the gradebook.
* Rating: use ratings for generating a grade.';
$string['totalsubstantive'] = 'Substantive Posts: {$a}';
$string['totalreplies'] = 'Replies: {$a}';
$string['totaldiscussions'] = 'Posts: {$a}';
$string['totalpostsanddiscussions'] = 'Total posts: {$a}';
$string['totalrating'] = 'Rating: {$a}';
$string['grade'] = 'Grade';
$string['thisisanonymous'] = 'This forum is anonymous.';
$string['anonymouswarning'] = 'By moving this discussion topic you might reveal anonymous information. Are you sure you want to do that?';
$string['totalposts'] = 'Total posts';
$string['completionusegradeerror'] = 'Cannot require grade because this forum is not graded.  Either remove this completion requirement or make this forum graded.';
$string['splitprivatewarning'] = 'You are splitting a private reply. After splitting, this post will no longer be private.';
$string['manualwarning'] = 'Activity grading is not yet supported. Grading is only available through the Course Gradebook.';
$string['sortdiscussions'] = 'Sort discussions';
$string['sortdiscussionsby'] = 'Sort';
$string['orderdiscussionsby'] = 'Order by';
$string['displaydiscussionreplies'] = 'Display discussion replies';
$string['discussionsummary'] = 'A table of all forum discussions for {$a}. The header First name last name is a merge of the user\'s firstname, lastname and picture.';
$string['export'] = 'Export';
$string['discussion:x'] = 'Discussion: {$a}';
$string['subjectbyuserondate'] = '{$a->subject} by {$a->author} on {$a->date}';
$string['subjectbyprivateuserondate'] = '{$a->subject} (private) by {$a->author} on {$a->date}';
$string['attachments:x'] = 'Attachments: {$a}';
$string['general'] = 'General';
$string['postsfor'] = 'Posts for';
$string['exportformat'] = 'Export format';
$string['csv'] = 'CSV';
$string['print'] = 'Print';
$string['plaintext'] = 'Plain text';
$string['exportattachments'] = 'Export attachments';
$string['all'] = 'All';
$string['participants'] = 'Participants';
$string['author'] = 'Author';
$string['date'] = 'Date';
$string['byx'] = 'by {$a}';
$string['postbyx'] = 'Post by {$a}';
$string['xreplies'] = '{$a} replies';
$string['onereply'] = '1 reply';
$string['xdiscussions'] = '{$a} discussions';
$string['loadmorediscussions'] = 'Load more discussions';
$string['validationerrorx'] = 'There was an error with your submission: {$a}';
$string['validationerrorsx'] = 'There were {$a->count} errors with your submission: {$a->errors}';
$string['messageisrequired'] = 'The message is required';
$string['discnameisrequired'] = 'The subject is required';
$string['replytox'] = 'Reply to {$a}';
$string['addareply'] = 'Add your reply';
$string['submit'] = 'Submit';
$string['useadvancededitor'] = 'Use advanced editor';
$string['hideadvancededitor'] = 'Hide advanced editor';
$string['loadingeditor'] = 'Loading editor...';
$string['accessible'] = 'Accessible';
$string['addyourdiscussion'] = 'Add your discussion';
$string['subjectplaceholder'] = 'Your subject';
$string['messageplaceholder'] = 'Type your post';
$string['notuploadedfile'] = 'There was a problem with uploading your file, please try again';
$string['trackingoptions'] = 'Tracking options';
$string['xunread'] = '{$a} new';
$string['replybuttontitle'] = 'Reply to {$a}';
$string['replybyx'] = 'Reply by {$a}';
$string['postbyxinreplytox'] = ' Reply to {$a->parent} from {$a->author} {$a->parentpost}';
$string['privatereplybyx'] = 'Private reply by {$a}';
$string['postbyxinprivatereplytox'] = 'Private reply to {$a->parent} from {$a->author} ';
$string['inreplyto'] = 'in reply to';
$string['inprivatereplyto'] = 'in private reply to';
$string['options'] = 'Options';
$string['articledateformat'] = '%l:%M%P %b %e, %Y';
$string['postdeleted'] = 'Post deleted';
$string['postcreated'] = 'Post created';
$string['cannnotdeletesinglediscussion'] = 'Sorry, but you are not allowed to delete that discussion!';
$string['cannotmakeprivatereplies'] = 'Sorry, but you are not allowed to make private replies to this forum';
$string['editingpost'] = 'Editing post';
$string['deleteattachmentx'] = 'Delete {$a}';
$string['deleteattachments'] = 'Delete attachments';
$string['postwasupdated'] = 'The post was updated';
$string['id'] = 'id';
$string['switchtoaccessible'] = 'Switch to the accessible view';
$string['anonymousrecentactivity'] = 'There may have been recent activity in this forum, but the details cannot be displayed because the forum is anonymous.';
$string['manageforumsubscriptions'] = 'Manage forum subscriptions';
$string['nonanonymous'] = 'Non anonymously';
$string['hiderecentposts'] = 'Hide recent Posts';
$string['confighiderecentposts'] = 'Set to yes to stop the display of recent forum posts on the course page.';
$string['hideuserpicture'] = 'Hide user pictures';
$string['confighideuserpicture'] = 'Set to yes to stop the display of user pictures.';


// Rights labels
$string['forumplusone:viewvotedatetime'] = 'View the date of a vote';
$string['forumplusone:viewwhovote'] = 'View voters';
$string['forumplusone:viewwhovote_annonymousvote'] = 'View voters with an annonmous vote';
$string['forumplusone:close_discussion'] = 'Close or open a discussion';



// Vote
$string['votes_section_header'] = 'Votes';
$string['enable_vote'] = 'Enable votes';
$string['enable_vote_help'] = 'Allow students to vote on posts.';
$string['vote_display_name'] = 'Display voters\' name to students';
$string['vote_display_name_help'] = 'Allow students to access to the list of voters.';
$string['votetime'] = 'Restrict votes with dates in this range:';
$string['count_vote_mode'] = 'Popularity mode';
$string['count_vote_mode_recursive'] = 'Recursive';
$string['count_vote_mode_first_post'] = 'First post';
$string['count_vote_mode_help'] = 'The popularity mode is the mode to evaluate the populaity of a discussion. <br>
The mode "Recursive" will count all votes of the discussion. <br>
The mode "First post" will count only the vote on the first post of he discussion.';
$string['votebuttontitle'] = 'Vote to this message';
$string['hasVotebuttontitle'] = 'Undo your vote to this message';
$string['vote'] = '<span aria-hidden="true">+1</span><span class="accesshide">Vote for this message</span>';
$string['countvote'] = 'Number of votes : {$a}';
$string['show-voters-link-title'] = 'Show voters';
$string['novotes'] = 'No votes submitted';
$string['vote_disabled_error'] = 'The vote is disabled';
$string['to_early_to_vote_error'] = 'You can\'t vote : the vote is not yet enabled.';
$string['to_late_to_vote_error'] = 'You can\'t vote : it\'s too late to vote.';
$string['own_vote_error'] = 'You can\'t vote for your own vote.';
$string['allvoteforitem'] = 'Voters list';
$string['votecolor'] = 'Vote color';
$string['configvotecolor'] = 'This parameter point out the color of the vote link when the user had vote. I recommand to point out a  hexadecimal color value.<br>
Try <a href="http://rgb.to/">rgb.to</a> to know your hexadecimal value.<br>
Try to avoid the same color than the links.<br>
Avoid the colors too close to the background : the color blinds can have difficulties to read that. A ratio of 4.5 on <a href="http://leaverou.github.io/contrast-ratio/#%23da3d00-on-white">the Lea Verou\'s contrast ratio</a> is satisfactory.';



// State discussion
$string['enable_states_disc'] = 'Allow to close or hide of discussions';
$string['enable_states_disc_help'] = 'When a discussion is closed, nobody can replay of vote.<br/>
When a discussion is hidden, nobody can reply ou vote and no students can view see the discussion.';
$string['discussion_closed'] = 'The discussion is closed';
$string['discussion_hidden'] = 'The discussion is hidden';
$string['popularity_text'] = '{$a} votes';

$string['title_open_discussion'] = 'Open this discussion';
$string['title_close_discussion'] = 'Close this discussion';
$string['title_hide_discussion'] = 'Hide this discussion to students';
$string['title_is_closed_discussion'] = 'Discussion closed';
$string['title_is_hidden_discussion'] = 'Discussion hidden to students';



// Collapse replies
$string['title-replies-collapse'] = 'Collapse replies';
$string['title-replies-uncollapse'] = 'Uncollapse replies';



// LiveReload
$string['livereloadrate'] = 'Refresh rate (in seconds)';
$string['configlivereloadrate'] = 'This rate will determine the interval between refresh of informations<br>
Type 0 to disable the refresh<br>
<strong>YOU NEED TO CUSTOM THIS PARAMETER TAKING INTO ACCOUNT YOUR SERVERS</strong><br>
<strong style="color:red">IF THIS RATE IS TOO LOW, YOU HAVE RISK OF LOAD INCREASE FOLLOWED BY <a href="https://en.wikipedia.org/w/index.php?title=Denial_of_service">DOS</a>.</strong>';
$string['enable_refresh'] = 'Live session';
$string['enable_refresh_help'] = 'This option will enable the automatic refresh of infomations. For technical reasons, enable this opion ONLY if you need it.';





