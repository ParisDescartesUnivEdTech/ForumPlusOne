YUI.add("moodle-mod_forumimproved-article",function(e,t){function s(){s.superclass.constructor.apply(this,arguments)}function u(){u.superclass.constructor.apply(this,arguments)}function a(){a.superclass.constructor.apply(this,arguments)}var n={DISCUSSION_EDIT:"forumimproved-thread-edit",DISCUSSION_EXPANDED:"forumimproved-thread-article-expanded",POST_EDIT:"forumimproved-post-edit"},r={ADD_DISCUSSION:"#newdiscussionform",ADD_DISCUSSION_TARGET:".forumimproved-add-discussion-target",ALL_FORMS:".forumimproved-reply-wrapper form",CONTAINER:".mod-forumimproved-posts-container",CONTAINER_LINKS:".mod-forumimproved-posts-container a",DISCUSSION:".forumimproved-thread",DISCUSSIONS:".forumimproved-threads-wrapper",DISCUSSION_EDIT:"."+n.DISCUSSION_EDIT,DISCUSSION_BY_ID:'.forumimproved-thread[data-discussionid="%d"]',DISCUSSION_COUNT:".forumimproved-discussion-count",DISCUSSION_STATE_BTN_TOGGLE_BY_DISCUSSION_ID:'article[data-discussionid="%d"] .forumimproved-thread-title .forumimproved-toggle-state-link',DISCUSSION_STATE_LABEL_BY_DISCUSSION_ID:'article[data-discussionid="%d"] .forumimproved-thread-title h4 span.label',DISCUSSION_TARGET:".forumimproved-new-discussion-target",DISCUSSION_TEMPLATE:"#forumimproved-discussion-template",DISCUSSION_VIEW:".forumimproved-thread-view",EDITABLE_MESSAGE:"[contenteditable]",FORM:".forumimproved-form",FORM_ADVANCED:".forumimproved-use-advanced",FORM_REPLY_WRAPPER:".forumimproved-reply-wrapper",INPUT_FORUM:'input[name="forum"]',INPUT_MESSAGE:'textarea[name="message"]',INPUT_REPLY:'input[name="reply"]',INPUT_SUBJECT:'input[name="subject"]',LINK_CANCEL:".forumimproved-cancel",NO_DISCUSSIONS:".forumnodiscuss",NOTIFICATION:".forumimproved-notification",OPTIONS_TO_PROCESS:".forumimproved-options-menu.unprocessed",PLACEHOLDER:".thread-replies-placeholder",POSTS:".forumimproved-thread-replies",POST_BY_ID:'.forumimproved-post-target[data-postid="%d"]',POST_EDIT:"."+n.POST_EDIT,POST_TARGET:".forumimproved-post-target",RATE:".forum-post-rating",RATE_POPUP:".forum-post-rating a",REPLY_TEMPLATE:"#forumimproved-reply-template",SEARCH_PAGE:"#page-mod-forumimproved-search",VALIDATION_ERRORS:".forumimproved-validation-errors",VIEW_POSTS:".forumimproved-view-posts",VOTERS_LINK_BY_POST_ID:'.forumimproved-post-target[data-postid="%d"] .forumimproved-show-voters-link',VOTE_BTN_BY_POST_ID:'.forumimproved-post-target[data-postid="%d"] .forumimproved-vote-link',VOTES_COUNTER_BY_POST_ID:'.forumimproved-post-target[data-postid="%d"] .forumimproved-votes-counter'},i={DISCUSSION_CREATED:"discussion:created",DISCUSSION_DELETED:"discussion:deleted",FORM_CANCELED:"form:canceled",POST_CREATED:"post:created",POST_DELETED:"post:deleted",POST_UPDATED:"post:updated"};M.mod_forumimproved=M.mod_forumimproved||{},s.NAME="moodle-mod_forumimproved-dom",s.ATTRS={io:{value:null}},e.extend(s,e.Base,{initializer:function(){e.all(r.RATE).addClass("processed"),this.initOptionMenus()},initFeatures:function(){this.initOptionMenus(),this.initRatings()},initRatings:function(){e.all(r.RATE).each(function(t){if(t.hasClass("processed"))return;M.core_rating.Y=e,t.all("select.postratingmenu").each(M.core_rating.attach_rating_events,M.core_rating),t.all("input.postratingmenusubmit").setStyle("display","none"),t.addClass("processed")})},initOptionMenus:function(){e.all(r.OPTIONS_TO_PROCESS).each(function(t){t.removeClass("unprocessed");var n=new e.YUI2.widget.Menu(t.generateID(),{lazyLoad:!0});n.render(e.one(r.CONTAINER).generateID()),e.one("#"+t.getData("controller")).on("click",function(e){e.preventDefault(),n.cfg.setProperty("y",e.currentTarget.getY()+e.currentTarget.get("offsetHeight")),n.cfg.setProperty("x",e.currentTarget.getX()),n.show()})})},handleViewRating:function(e){if(e.currentTarget.ancestor(".helplink")!==null)return;e.preventDefault(),openpopup(e,{url:e.currentTarget.get("href")+"&popup=1",name:"ratings",options:"height=400,width=600,top=0,left=0,menubar=0,location=0,scrollbars,resizable,toolbar,status,directories=0,fullscreen=0,dependent"})},markPostAsRead:function(e,t,n){this.get("io").send({postid:e,action:"markread"},t,n)},incrementDiscussionCount:function(t){var n=e.one(r.DISCUSSION_COUNT);n!==null&&(n.setData("count",parseInt(n.getData("count"),10)+t),n.setHTML(M.util.get_string("xdiscussions","mod_forumimproved",n.getData("count"))))},displayNotification:function(t){var n=e.Node.create(t);e.one(r.NOTIFICATION).append(n),setTimeout(function(){n.remove(!0)},1e4)},handleNotification:function(t){e.Lang.isString(t.notificationhtml)&&t.notificationhtml.trim().length>0&&this.displayNotification(t.notificationhtml)},handleUpdateDiscussion:function(t){var n=e.one("#discussionsview");n?n.setHTML(t.html):(n=e.one(r.DISCUSSION_BY_ID.replace("%d",t.discussionid)),n?n.replace(t.html):e.one(r.DISCUSSION_TARGET).insert(t.html,"after"))},handleDiscussionCreated:function(){e.one(r.NO_DISCUSSIONS)&&e.one(r.NO_DISCUSSIONS).remove()},handleDiscussionDeleted:function(t){var n=e.one(r.POST_BY_ID.replace("%d",t.postid));if(n===null||!n.hasAttribute("data-isdiscussion"))return;e.one(r.DISCUSSIONS)?(n.remove(!0),this.incrementDiscussionCount(-1),e.one(r.DISCUSSION_COUNT).focus()):window.location.href=t.redirecturl}}),M.mod_forumimproved.Dom=s;var o=e.Base.create("forumimprovedRouter",e.Router,[],{initializer:function(){},discussion:function(e){this.get("article").viewDiscussion(e.query.d,e.query.postid)},post:function(t){e.Lang.isUndefined(t.query.reply)?e.Lang.isUndefined(t.query.forum)?e.Lang.isUndefined(t.query.vote)?e.Lang.isUndefined(t.query.close)?e.Lang.isUndefined(t.query["delete"])?e.Lang.isUndefined(t.query.edit)?e.Lang.isUndefined(t.query.prune)||(window.location.href=t.url):this.get("article").get("form").showEditForm(t.query.edit):this.get("article").confirmDeletePost(t.query["delete"]):this.get("article").toggleDiscussionState(t.query.close):this.get("article").toggleVote(t.query.vote):this.get("article").get("form").showAddDiscussionForm(t.query.forum):this.get("article").get("form").showReplyToForm(t.query.reply
)},whovote:function(e){this.get("article").showVoters(e.query.postid)},focusHash:function(t){var n=t.get("href").split("#");setTimeout(function(){e.one("#"+n[1]).ancestor("li").focus()},300)},handleRoute:function(e){if(e.button!==1||e.ctrlKey||e.metaKey||e.currentTarget.hasClass("disable-router")){e.currentTarget.get("href").indexOf("#")>-1&&this.focusHash(e.currentTarget);return}M.mod_forumimproved.restoreEditor(),this.routeUrl(e.currentTarget.get("href"))&&e.preventDefault()},routeUrl:function(e){return this.hasRoute(e)?(this.save(this.removeRoot(e)),!0):!1},handleAddDiscussionRoute:function(e){e.preventDefault(),M.mod_forumimproved.restoreEditor();var t=e.currentTarget,n=t.one(r.INPUT_FORUM).get("value");this.save(t.get("action")+"?forum="+n)},handleViewDiscussion:function(t){var n="/discuss.php?d="+t.discussionid;e.Lang.isUndefined(t.postid)||(n=n+"&postid="+t.postid),this.save(n)},hideForms:function(e,t,n){this.get("article").get("form").removeAllForms(),n()}},{ATTRS:{article:{value:null},root:{value:"/mod/forumimproved"},routes:{value:[{path:"/view.php",callbacks:["hideForms"]},{path:"/discuss.php",callbacks:["hideForms","discussion"]},{path:"/post.php",callbacks:["hideForms","post"]},{path:"/whovote.php",callbacks:["whovote"]}]}}});M.mod_forumimproved.Router=o,u.NAME="moodle-mod_forumimproved-form",u.ATTRS={io:{value:null}},e.extend(u,e.Base,{handleFormPaste:function(e){var t="",n=window.getSelection(),r=function(e){var t=document.createElement("div");t.innerHTML=e,tags=t.getElementsByTagName("*");for(var n=0,r=tags.length;n<r;n++)tags[n].removeAttribute("id"),tags[n].removeAttribute("style"),tags[n].removeAttribute("size"),tags[n].removeAttribute("color"),tags[n].removeAttribute("bgcolor"),tags[n].removeAttribute("face"),tags[n].removeAttribute("align");return t.innerHTML},i=!1;e._event&&e._event.clipboardData&&e._event.clipboardData.getData?i=e._event.clipboardData:window.clipboardData&&window.clipboardData.getData&&(i=window.clipboardData);if(i){if(i.types){if(/text\/html/.test(i.types)||i.types.contains("text/html"))t=i.getData("text/html");else if(/text\/plain/.test(i.types)||i.types.contains("text/plain"))t=i.getData("text/plain")}else t=i.getData("Text");if(t!==""){if(n.getRangeAt&&n.rangeCount){var s=n.getRangeAt(0),o=document.createElement("p");o.innerHTML=r(t),o.childNodes[0].tagName==="META"&&o.removeChild(o.childNodes[0]);var u=o.childNodes[o.childNodes.length-1];for(var a=0;a<=o.childNodes.length;a++){var f=o.childNodes[o.childNodes.length-1];s.insertNode(f)}s.setStartAfter(u),s.setEndAfter(u),n.removeAllRanges(),n.addRange(s)}return e._event.preventDefault&&(e._event.stopPropagation(),e._event.preventDefault()),!1}}setTimeout(function(){var t=r(e.currentTarget.get("innerHTML"));e.currentTarget.setContent(t);var n=document.createRange(),i=window.getSelection(),s=function(e){var t=e.childNodes;if(!t)return!1;var n=t[t.length-1];if(!n||typeof n=="undefined")return e;var r=s(n);return r&&typeof r!="undefined"?r:n&&typeof n!="undefined"?n:e},o=s(e.currentTarget._node),u=1;typeof o.innerHTML!="undefined"?u=o.innerHTML.length:u=o.length,n.setStart(o,u),n.collapse(!0),i.removeAllRanges(),i.addRange(n)},100)},_displayReplyForm:function(t){var n=e.one(r.REPLY_TEMPLATE).getHTML(),i=t.one(r.FORM_REPLY_WRAPPER);i instanceof e.Node?i.replace(n):t.append(n),i=t.one(r.FORM_REPLY_WRAPPER),this.attachFormWarnings(),i.one(r.INPUT_REPLY).setAttribute("value",t.getData("postid"));var s=i.one(r.FORM_ADVANCED);s.setAttribute("href",s.getAttribute("href").replace(/reply=\d+/,"reply="+t.getData("postid"))),t.hasAttribute("data-ispost")&&i.one("legend").setHTML(M.util.get_string("replytox","mod_forumimproved",t.getData("author")))},_copyMessage:function(e){var t=e.one(r.EDITABLE_MESSAGE).get("innerHTML");e.one(r.INPUT_MESSAGE).set("value",t)},_submitReplyForm:function(e,t){e.all("button").setAttribute("disabled","disabled"),this._copyMessage(e);var n=e.all("form input[type=file]");this.get("io").submitForm(e.one("form"),function(n){n.yuiformsubmit=1,n.errors===!0?(e.one(r.VALIDATION_ERRORS).setHTML(n.html).addClass("notifyproblem"),e.all("button").removeAttribute("disabled")):t.call(this,n)},this,n._nodes.length>0)},attachFormWarnings:function(){e.all(r.ALL_FORMS).each(function(e){if(!e.hasClass("form-checker-added")){var t=M.core_formchangechecker.init({formid:e.generateID()});e.addClass("form-checker-added"),e.one(r.EDITABLE_MESSAGE).on("keypress",M.core_formchangechecker.set_form_changed,t)}})},removeAllForms:function(){e.all(r.POSTS+" "+r.FORM_REPLY_WRAPPER).each(function(e){!e.ancestor(r.DISCUSSION_EDIT)&&!e.ancestor(r.POST_EDIT)&&e.remove(!0)});var t=e.one(r.ADD_DISCUSSION_TARGET);t!==null&&t.empty()},handleCancelForm:function(e){e.preventDefault(),M.mod_forumimproved.restoreEditor();var t=e.target.ancestor(r.POST_TARGET);t&&t.removeClass(n.POST_EDIT).removeClass(n.DISCUSSION_EDIT),e.target.ancestor(r.FORM_REPLY_WRAPPER).remove(!0),this.fire(i.FORM_CANCELED,{discussionid:t.getData("discussionid"),postid:t.getData("postid")})},handleFormSubmit:function(e){e.preventDefault(),M.mod_forumimproved.restoreEditor();var t=e.currentTarget.ancestor(r.FORM_REPLY_WRAPPER);this._submitReplyForm(t,function(e){switch(e.eventaction){case"postupdated":this.fire(i.POST_UPDATED,e);break;case"postcreated":this.fire(i.POST_UPDATED,e);break;case"discussioncreated":this.fire(i.DISCUSSION_CREATED,e)}})},showReplyToForm:function(t){var n=e.one(r.POST_BY_ID.replace("%d",t));n.hasAttribute("data-ispost")&&this._displayReplyForm(n),n.one(r.EDITABLE_MESSAGE).focus()},showAddDiscussionForm:function(){e.one(r.ADD_DISCUSSION_TARGET).setHTML(e.one(r.DISCUSSION_TEMPLATE).getHTML()).one(r.INPUT_SUBJECT).focus(),this.attachFormWarnings()},showEditForm:function(t){var i=e.one(r.POST_BY_ID.replace("%d",t));if(i.hasClass(n.DISCUSSION_EDIT)||i.hasClass(n.POST_EDIT)){i.one(r.EDITABLE_MESSAGE).focus();return}this.get("io").send({discussionid:i.getData("discussionid"),postid:i.getData("postid"),action:"edit_post_form"},function(e
){i.prepend(e.html),i.hasAttribute("data-isdiscussion")?i.addClass(n.DISCUSSION_EDIT):i.addClass(n.POST_EDIT),i.one(r.EDITABLE_MESSAGE).focus(),this.attachFormWarnings()},this)}}),M.mod_forumimproved.Form=u,a.NAME=t,a.ATTRS={contextId:{value:undefined},io:{readOnly:!0},dom:{readOnly:!0},router:{readOnly:!0},form:{readOnly:!0},liveLog:{readOnly:!0},editorMutateObserver:null,currentEditLink:null};var f="",l="";e.extend(a,e.Base,{initializer:function(){this._set("router",new M.mod_forumimproved.Router({article:this,html5:!1})),this._set("io",new M.mod_forumimproved.Io({contextId:this.get("contextId")})),this._set("dom",new M.mod_forumimproved.Dom({io:this.get("io")})),this._set("form",new M.mod_forumimproved.Form({io:this.get("io")})),this._set("liveLog",M.mod_forumimproved.init_livelog()),this.bind()},bind:function(){var t=document.getElementsByClassName("forumimproved-post-unread")[0];if(t&&location.hash==="#unread"){var n=document.getElementById(t.id).parentNode;(M.cfg.theme!=="express"||!navigator.userAgent.match(/Trident|MSIE/))&&n.scrollIntoView(),n.focus()}if(e.one(r.SEARCH_PAGE)!==null)return;var s=this.get("dom"),o=this.get("form"),u=this.get("router");e.delegate("paste",o.handleFormPaste,document,".forumimproved-textarea",o),e.delegate("click",o.handleCancelForm,document,r.LINK_CANCEL,o),e.delegate("click",u.handleRoute,document,r.CONTAINER_LINKS,u),e.delegate("click",s.handleViewRating,document,r.RATE_POPUP,s),e.delegate("click",function(t){var n=e.one("#hiddenadvancededitorcont"),r,i,s=this,o;if(!n)return;t.preventDefault(),i=e.one("#hiddenadvancededitoreditable"),r=i.ancestor(".editor_atto"),r?M.mod_forumimproved.toggleAdvancedEditor(s):(s.setContent(M.util.get_string("loadingeditor","forumimproved")),o=setInterval(function(){r=i.ancestor(".editor_atto"),r&&(clearInterval(o),M.mod_forumimproved.toggleAdvancedEditor(s))},500))},document,".forumimproved-use-advanced"),e.delegate("submit",o.handleFormSubmit,document,r.FORM,o),e.delegate("click",u.handleAddDiscussionRoute,document,r.ADD_DISCUSSION,u),o.on(i.POST_CREATED,s.handleUpdateDiscussion,s),o.on(i.POST_CREATED,s.handleNotification,s),o.on(i.POST_CREATED,u.handleViewDiscussion,u),o.on(i.POST_CREATED,this.handleLiveLog,this),o.on(i.POST_UPDATED,s.handleUpdateDiscussion,s),o.on(i.POST_UPDATED,u.handleViewDiscussion,u),o.on(i.POST_UPDATED,s.handleNotification,s),o.on(i.POST_UPDATED,this.handleLiveLog,this),o.on(i.DISCUSSION_CREATED,s.handleUpdateDiscussion,s),o.on(i.DISCUSSION_CREATED,s.handleDiscussionCreated,s),o.on(i.DISCUSSION_CREATED,s.handleNotification,s),o.on(i.DISCUSSION_CREATED,u.handleViewDiscussion,u),o.on(i.DISCUSSION_CREATED,this.handleLiveLog,this),this.on(i.DISCUSSION_DELETED,s.handleDiscussionDeleted,s),this.on(i.DISCUSSION_DELETED,s.handleNotification,s),this.on(i.DISCUSSION_DELETED,this.handleLiveLog,this),this.on(i.POST_DELETED,s.handleUpdateDiscussion,s),this.on(i.POST_DELETED,u.handleViewDiscussion,u),this.on(i.POST_DELETED,s.handleNotification,s),this.on(i.POST_DELETED,this.handleLiveLog,this),o.on(i.FORM_CANCELED,u.handleViewDiscussion,u)},handleLiveLog:function(t){e.Lang.isString(t.livelog)&&this.get("liveLog").logText(t.livelog)},viewDiscussion:function(t,n){var i=e.one(r.DISCUSSION_BY_ID.replace("%d",t));if(!(i instanceof e.Node))return;if(!e.Lang.isUndefined(n)){var s=e.one(r.POST_BY_ID.replace("%d",n));s===null||s.hasAttribute("data-isdiscussion")?i.focus():s.get("parentNode").focus()}else i.focus()},confirmDeletePost:function(t){var n=e.one(r.POST_BY_ID.replace("%d",t));if(n===null)return;window.confirm(M.str.mod_forumimproved.deletesure)===!0&&this.deletePost(t)},deletePost:function(t){var n=e.one(r.POST_BY_ID.replace("%d",t));if(n===null)return;this.get("io").send({postid:t,sesskey:M.cfg.sesskey,action:"delete_post"},function(e){n.hasAttribute("data-isdiscussion")?this.fire(i.DISCUSSION_DELETED,e):this.fire(i.POST_DELETED,e)},this)},toggleVote:function(t){var n=e.one(r.VOTE_BTN_BY_POST_ID.replace("%d",t));if(n===null)return;var i=e.one(r.VOTES_COUNTER_BY_POST_ID.replace("%d",t));if(i===null)return;this.get("io").send({postid:t,action:"vote"},function(t){if(typeof t.errorCode=="undefined"||t.errorCode=="0"){var r="active",s=0;n.toggleClass(r),s=n.hasClass(r)?1:-1,i.set("innerHTML",parseInt(i.get("innerHTML"),10)+s)}else e.Lang.isUndefined(t.errorMsg)&&alert(t.errorMsg)},this)},showVoters:function(t){var n=e.one(r.VOTERS_LINK_BY_POST_ID.replace("%d",t));if(n===null)return;var i={url:n.getAttribute("href"),name:"showVoters",options:"height=400,width=600,top=0,left=0,menubar=0,location=0,scrollbars,resizable,toolbar,status,directories=0,fullscreen=0,dependent"};i.url.indexOf("?")===-1?i.url+="?popup=1":i.url+="&popup=1",openpopup.apply(n,[null,i])&&(location.href=n.getAttribute("href"))},toggleDiscussionState:function(t){var n=e.one(r.DISCUSSION_STATE_BTN_TOGGLE_BY_DISCUSSION_ID.replace("%d",t));if(n===null)return;f==""&&(f=n.getData("closed-text")),l==""&&(l=n.getData("open-text"));var i=e.one(r.DISCUSSION_STATE_LABEL_BY_DISCUSSION_ID.replace("%d",t));if(i===null)return;this.get("io").send({discussionid:t,action:"togglestate"},function(t){typeof t.errorCode=="undefined"||t.errorCode=="0"?t.state=="o"?(i.addClass("hidden"),n.set("textContent",l)):(i.removeClass("hidden"),n.set("textContent",f)):e.Lang.isUndefined(t.errorMsg)&&alert(t.errorMsg)},this)}}),M.mod_forumimproved.Article=a,M.mod_forumimproved.init_article=function(e){new a(e)},M.mod_forumimproved.dispatchClick=function(e){if(document.createEvent){var t=new MouseEvent("click",{view:window,bubbles:!0,cancelable:!0});e.dispatchEvent(t)}else e.fireEvent&&e.fireEvent("onclick")},M.mod_forumimproved.restoreEditor=function(){var t=e.one("#hiddenadvancededitorcont");if(t){var n=e.one("#hiddenadvancededitoreditable"),r=n.ancestor(".editor_atto"),i=M.mod_forumimproved.Article.currentEditLink,s=!1;i&&(s=i.previous(".forumimproved-textarea"));var o=!r||r.getComputedStyle("display")==="none";o||(r.one(".atto_html_button.highlight")&&M.mod_forumimproved.dispatchClick
(r.one(".atto_html_button.highlight")._node),s&&s.setContent(n.getContent())),M.mod_forumimproved.toggleAdvancedEditor(!1,!0),e.one("#hiddenadvancededitorcont").show(),e.one("#hiddenadvancededitorcont")._node.style.display="block",t.appendChild(r),t.appendChild(e.one("#hiddenadvancededitor"))}},M.mod_forumimproved.toggleAdvancedEditor=function(t,n,r){var i=!1;n||(i=t&&t.getAttribute("aria-pressed")==="false"),t&&(M.mod_forumimproved.Article.currentEditLink=t,i?t.removeClass("hideadvancededitor"):t.addClass("hideadvancededitor"));if(n){if(!t){var s=e.all(".forumimproved-use-advanced");for(var o=0;o<s.size();o++){var u=s.item(o);if(r&&r===u)continue;M.mod_forumimproved.toggleAdvancedEditor(u,!0)}return}}else M.mod_forumimproved.toggleAdvancedEditor(!1,!0,t);var a=e.one("#hiddenadvancededitorcont"),f,l=t.previous(".forumimproved-textarea"),c;if(!a)throw"Failed to get editor";f=e.one("#hiddenadvancededitoreditable"),c=f.ancestor(".editor_atto"),l&&f.setStyle("height",l.getDOMNode().offsetHeight+"px");var h=!1;if(!c||c.getComputedStyle("display")==="none")h=!0;if(i){t.setAttribute("aria-pressed","true"),t.setContent(M.util.get_string("hideadvancededitor","forumimproved")),l.hide(),c.one(".atto_html_button.highlight")&&e.one("#hiddenadvancededitor").show(),c.show(),l.insert(c,"before"),l.insert(e.one("#hiddenadvancededitor"),"before"),f.setContent(l.getContent()),f.focus();var p=function(){l.setContent(f.getContent())};window.MutationObserver?(M.mod_forumimproved.Article.editorMutateObserver=new MutationObserver(p),M.mod_forumimproved.Article.editorMutateObserver.observe(f.getDOMNode(),{childList:!0,characterData:!0,subtree:!0})):f.getDOMNode().addEventListener("DOMCharacterDataModified",editAreachanged,!1)}else t.setAttribute("aria-pressed","false"),M.mod_forumimproved.Article.editorMutateObserver&&M.mod_forumimproved.Article.editorMutateObserver.disconnect(),t.setContent(M.util.get_string("useadvancededitor","forumimproved")),l.show(),h||(c.one(".atto_html_button.highlight")&&M.mod_forumimproved.dispatchClick(c.one(".atto_html_button.highlight")._node),l.setContent(f.getContent())),e.one("#hiddenadvancededitor").hide(),c.hide()}},"@VERSION@",{requires:["base","node","event","router","core_rating","querystring","moodle-mod_forumimproved-io","moodle-mod_forumimproved-livelog","moodle-core-formchangechecker"]});
