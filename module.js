/**
 * @namespace M.mod_forumplusone
 * @author Mark Nielsen
 */
M.mod_forumplusone = M.mod_forumplusone || {};

/**
 * Set toggle link label and accessibility stuff on ajax reponse.
 *
 * @param link
 * @author Guy Thomas
 */
M.mod_forumplusone.onToggleResponse = function(link) {
    var active,
        status,
        title,
        svgTitle;

    link.toggleClass('forumplusone-toggled');

    if (link.getAttribute('aria-pressed') == 'true') {
        link.setAttribute('aria-pressed', false);
        active = false;
    } else {
        link.setAttribute('aria-pressed', true);
        active = true;
    }

    // Set new link title;
    status = active ? 'toggled' : 'toggle';
    title = M.util.get_string(status+':'+link.getData('toggletype'),'forumplusone');
    svgTitle = link.one('svg title');
    svgTitle.set('text', title);
}

M.mod_forumplusone.toggleStatesApplied = false;

/**
 * Initialise advanced forum javascript.
 * @param Y
 */
M.mod_forumplusone.init = function(Y) {
    M.mod_forumplusone.applyToggleState(Y);
}

/**
 * Apply toggle state
 * @param Y
 *
 * @author Mark Neilsen / Guy Thomas
 */
M.mod_forumplusone.applyToggleState = function(Y) {
    // @todo - Get rid of this check by making sure that lib.php and renderer.php only call this once.
    if (M.mod_forumplusone.toggleStatesApplied) {
        return;
    }
    M.mod_forumplusone.toggleStatesApplied = true;
    if (Y.all('.mod-forumplusone-posts-container').isEmpty()) {
        return;
    }
    // We bind to document otherwise screen readers read everything as clickable.
    Y.delegate('click', function(e) {
        var link = e.currentTarget;
        e.preventDefault();
        e.stopPropagation();

        M.mod_forumplusone.io(Y, link.get('href'), function() {
            M.mod_forumplusone.onToggleResponse(link);
        });
    }, document, 'a.forumplusone_flag, a.forumplusone_discussion_subscribe');

    // IE fix - When clicking on an SVG, the Y.delegate function above fails, the click function is never triggered
    // and the user ends up with a page refresh instead of an AJAX update. This code fixes the issue by making the svg
    // absolutely positioned and with a relatively positioned span taking its place.
    if (navigator.userAgent.match(/Trident|MSIE/)){
        Y.all('a.forumplusone_flag, a.forumplusone_discussion_subscribe').each(function (targNode) {
           var svgwidth = targNode.one('svg').getStyle('width');
           var item = Y.Node.create('<span style="display:inline-block;width:'+svgwidth+';min-width:'+svgwidth+';">&nbsp;</span>');
           targNode.append(item);
           item.setStyle('position', 'relative');
           targNode.all('svg').setStyle('position', 'absolute');
        });
    }
}

/**
 * @author Mark Nielsen
 */
M.mod_forumplusone.io = function(Y, url, successCallback, failureCallback) {
    Y.io(url, {
        on: {
            success: function(id, o) {
                M.mod_forumplusone.io_success_handler(Y, o, successCallback);
            },
            failure: function() {
                M.mod_forumplusone.io_failure_handler(Y, failureCallback);
            }
        }
    });
};

/**
 * @author Mark Nielsen
 */
M.mod_forumplusone.io_success_handler = function(Y, response, callback) {
    var data = {};
    if (response.responseText) {
        try {
            data = Y.JSON.parse(response.responseText);
            if (data.error) {
                alert(data.error);
                if (window.console !== undefined && console.error !== undefined) {
                    console.error(data.error);
                    console.error(data.stacktrace);
                    console.error(data.debuginfo);
                }
                return;
            }
        } catch (ex) {
            alert(M.str.forumplusone.jsondecodeerror);
            return;
        }
    }
    if (callback) {
        callback(data);
    }
};

/**
 * @author Mark Nielsen
 */
M.mod_forumplusone.io_failure_handler = function(Y, callback) {
    alert(M.str.forumplusone.ajaxrequesterror);

    if (callback) {
        callback();
    }
};

/**
 * @author Mark Nielsen
 */
M.mod_forumplusone.init_modform = function(Y, FORUMPLUSONE_GRADETYPE_MANUAL) {
    var gradetype = Y.one('.path-mod-forumplusone select[name="gradetype"]');

    if (gradetype) {
        var warning = Y.Node.create('<span id="gradetype_warning" class="hidden">' + M.str.forumplusone.manualwarning + '</span>');
        gradetype.get('parentNode').appendChild(warning);

        var updateMessage = function() {
            if (gradetype.get('value') == FORUMPLUSONE_GRADETYPE_MANUAL) {
                warning.removeClass('hidden');
            } else {
                warning.addClass('hidden');
            }
        };

        // Init the view
        updateMessage();

        // Update view on change
        gradetype.on('change', function() {
            updateMessage();
        });
    }
};
