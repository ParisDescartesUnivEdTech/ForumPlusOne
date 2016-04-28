/**
 * Forum Router
 *
 * @module moodle-mod_forumimproved-router
 */

/**
 * Handles URL routing
 *
 * @constructor
 * @namespace M.mod_forumimproved
 * @class Router
 * @extends Y.Router
 */
var ROUTER = Y.Base.create('forumimprovedRouter', Y.Router, [], {
    /**
     *
     * @method initializer
     */
    initializer: function() {
    },

    /**
     * View a discussion
     *
     * @method discussion
     * @param {Object} req
     */
    discussion: function(req) {
        this.get('article').viewDiscussion(req.query.d, req.query.postid);
    },

    /**
     * Post editing
     *
     * @method post
     * @param {Object} req
     */
    post: function(req) {
        if (!Y.Lang.isUndefined(req.query.reply)) {
            this.get('article').get('form').showReplyToForm(req.query.reply);
        } else if (!Y.Lang.isUndefined(req.query.forum)) {
            this.get('article').get('form').showAddDiscussionForm(req.query.forum);
        } else if (!Y.Lang.isUndefined(req.query.vote)) {
            this.get('article').toggleVote(req.query.vote);
        } else if (!Y.Lang.isUndefined(req.query.close)) {
            this.get('article').toggleDiscussionState(req.query.close);
        } else if (!Y.Lang.isUndefined(req.query['delete'])) {
            this.get('article').confirmDeletePost(req.query['delete']);
        } else if (!Y.Lang.isUndefined(req.query.edit)) {
            this.get('article').get('form').showEditForm(req.query.edit);
        } else if (!Y.Lang.isUndefined(req.query.prune)) {
            window.location.href = req.url;
        }
    },

    /**
     * View voter for a post
     *
     * @method whovote
     * @param {Object} req
     */
    whovote: function(req) {
        this.get('article').showVoters(req.query.postid);
    },

    /**
     * Focus hashed element.
     *
     * @param el
     */
    focusHash: function(el) {
        var ta = el.get('href').split('#');
        // Without this timeout it doesn't always focus on the desired element.
        setTimeout(function(){
            Y.one('#'+ta[1]).ancestor('li').focus();
        },300);
    },


    /**
     * Handles routing of link clicks
     *
     * @param e
     */
    handleRoute: function(e) {
        // Allow the native behavior on middle/right-click, or when Ctrl or Command are pressed.
        if (e.button !== 1 || e.ctrlKey || e.metaKey || e.currentTarget.hasClass('disable-router')) {
            if (e.currentTarget.get('href').indexOf('#') >-1){
                this.focusHash(e.currentTarget);
            }
            return;
        }
        // Whenever a route takes us somewhere else we need to move the editor back to its original container.
        M.mod_forumimproved.restoreEditor();

        if (this.routeUrl(e.currentTarget.get('href'))) {
            e.preventDefault();
        }
    },

    /**
     * Route a URL if possible
     *
     * @method routeUrl
     * @param {String} url
     * @returns {boolean}
     */
    routeUrl: function(url) {
        if (this.hasRoute(url)) {
            this.save(this.removeRoot(url));
            return true;
        }
        return false;
    },

    /**
     * Add discussion button handler
     *
     * @method handleAddDiscussionRoute
     * @param e
     */
    handleAddDiscussionRoute: function(e) {
        e.preventDefault();

        // Put editor back to its original place in DOM.
        M.mod_forumimproved.restoreEditor();

        var formNode = e.currentTarget,
            forumId  = formNode.one(SELECTORS.INPUT_FORUM).get('value');

        this.save(formNode.get('action') + '?forum=' + forumId);
    },

    /**
     * Update route to view the discussion
     *
     * Usually done after the discussion was added
     * or updated.
     *
     * @method handleViewDiscussion
     * @param e
     */
    handleViewDiscussion: function(e) {
        var path = '/discuss.php?d=' + e.discussionid;
        if (!Y.Lang.isUndefined(e.postid)) {
            path = path + '&postid=' + e.postid;
        }
        this.save(path);
    },

    /**
     * Middleware: before executing a route, hide
     * all of the open forms.
     *
     * @method hideForms
     * @param req
     * @param res
     * @param next
     */
    hideForms: function(req, res, next) {
        this.get('article').get('form').removeAllForms();
        next();
    }
}, {
    ATTRS: {
        /**
         * Used for responding to routing actions
         *
         * @attribute article
         * @type M.mod_forumimproved.Article
         * @required
         */
        article: { value: null },

        /**
         * Root URL
         *
         * @attribute root
         * @type String
         * @default '/mod/forumimproved'
         * @required
         */
        root: {
            value: '/mod/forumimproved'
        },

        /**
         * Default routes
         *
         * @attribute routes
         * @type Array
         * @required
         */
        routes: {
            value: [
                { path: '/view.php', callbacks: ['hideForms'] },
                { path: '/discuss.php', callbacks: ['hideForms', 'discussion'] },
                { path: '/post.php', callbacks: ['hideForms', 'post'] },
                { path: '/whovote.php', callbacks: ['whovote'] }
            ]
        }
    }
});

M.mod_forumimproved.Router = ROUTER;
