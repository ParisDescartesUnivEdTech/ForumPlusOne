(function() {
    'use strict';

    var pageType;
    var PAGE_TYPE_DISCUSSIONS_LIST = 0;
    var PAGE_TYPE_DISCUSSION_VIEW = 1;

    if (location.pathname.endsWith('view.php')) {
        pageType = PAGE_TYPE_DISCUSSIONS_LIST;
    }
    else if (location.pathname.endsWith('discuss.php')) {
        pageType = PAGE_TYPE_DISCUSSION_VIEW;
    }
    else {
        throw new Error('variables are not defined');
    }




    // Check if the vars are defined
    if (typeof window.liveReload === 'undefined') {
        throw new Error('variables are not defined');
    }



    var intervalReload = null;
    var discid = '';

    function onload() {
        if (pageType == PAGE_TYPE_DISCUSSION_VIEW) {
            discid = window.location
                           .search
                           .substring(1) // rm the first ?
                           .split('&') // 'a=b&d=1&c=d' -> ['a=b', 'd=1', 'c=d']
                           .map(function(a){return a.split('=')[0] == 'd' ? a.split('=')[1] : null;}) // ['a=b', 'd=1', 'c=d'] -> [null, '1', null]
                           .join(''); // [null, '1', null] -> '1'
        }
        intervalReload = setInterval(reload, window.liveReload.intervalReload * 1000);
    }

    function reload() {
        jQuery.get(
            window.liveReload.urlChange + '&discid=' + discid,
            function(datas) {
                if ((typeof datas.errorCode != "undefined" && datas.errorCode != 0) || typeof datas.error != "undefined") {
                    window.console.log('I got a XHR eror', datas);
                    return;
                }
                if (pageType == PAGE_TYPE_DISCUSSIONS_LIST) {
                    refreshDiscussionList(datas);
                }
                else {
                    refreshDiscussionView(datas);
                }
            },
            'json'
        );
    }


    function refreshDiscussionList(datas) {
        var i, c, elem, content, countNew, predecessor;

        if (datas['new'].length > 0) {
            countNew = 0;

            for (i = 0, c = datas['new'].length; i < c ; ++i) {
                elem = datas['new'][i];

                if (jQuery('#p' + elem.postid).length > 0)
                    continue;

                ++countNew;

                content = elem.html;

                content = jQuery(jQuery.parseHTML(content));

                content.addClass('newDiscussion');
                content.hide();
                content.insertAfter('.forumplusone-threads-wrapper .forumplusone-new-discussion-target');

                var bg = content.css('background-color');
                content.css('background-color', 'rgb(255, 221, 142)');
                content.show(150);
                content.animate({backgroundColor: 'rgba(255, 221, 142, 0)'}, 3000, function() {
                    content.css('background-color', bg);
                });
                // TODO improve the animation
            }

            var counter = jQuery('.forumplusone-discussion-count'),
                count = counter.attr('data-count');
            counter.text( counter.text().replace(count, parseInt(count) + countNew) );
            counter.attr('data-count', parseInt(count) + countNew);

            jQuery(document.body).trigger('discussion:created');
        }

        if (datas.update.length > 0) {
            for (i = 0, c = datas.update.length; i < c ; ++i) {
                elem = datas.update[i];
                predecessor = jQuery('#p' + elem.postid);

                if (predecessor.length == 0)
                    continue;

                content = elem.html;

                content = jQuery(jQuery.parseHTML(content));

                content.addClass('updatedDiscussion');
                content.hide();
                content.insertBefore(predecessor);

                var bg = content.css('background-color');
                content.css('background-color', 'rgb(255, 221, 142)');
                content.show();
                predecessor.remove();
                content.animate({backgroundColor: 'rgba(255, 221, 142, 0)'}, 3000, function() {
                    content.css('background-color', bg);
                });
            }

            jQuery(document.body).trigger('discussion:created');
        }

        if (datas.del.length > 0) {
            for (i = 0, c = datas.del.length; i < c ; ++i) {
                elem = datas.del[i];

                jQuery('[data-discussionid="' + elem.id + '"]').attr('data-delMsg', window.liveReload.msgDel);
                jQuery('[data-discussionid="' + elem.id + '"]').addClass('delDiscussion');
            }

            var counter = jQuery('.forumplusone-discussion-count'),
                count = counter.attr('data-count');
            counter.text( counter.text().replace(count, parseInt(count) - datas.del.length) );
            counter.attr('data-count', parseInt(count) - datas.del.length);

            jQuery(document.body).trigger('discussion:deleted');
        }


        // update : reload view
        // vote : change number
        // closed : ... close

    }

    function refreshDiscussionView(datas) {
        var i, c, elem, content, post, parent, countReplies, counterAdded = 0, predecessor;

        if (datas['new'].length > 0) {
            for (i = 0, c = datas['new'].length; i < c ; ++i) {
                elem = datas['new'][i];

                if (jQuery('#p' + elem.id).length > 0)
                    continue;

                ++counterAdded;

                content = elem.html;

                content = jQuery(jQuery.parseHTML(content));
                post = content.find('.forumplusone-post-wrapper');

                parent = $('#p' + elem.parent);

                post.addClass('newPost');
                post.hide();

                parent.find('.forumplusone-count-replies *').removeClass('hidden');

                if (parent.hasClass('forumplusone-thread')) {
                    console.log(parent, parent.find('.counterReplies'));
                    countReplies = parent.find('.counterReplies');
                    countReplies.removeAttr('hidden');
                    content.appendTo(parent.find('.forumplusone-thread-replies-list:first'));
                }
                else {
                    content.appendTo(parent.next('.forumplusone-thread-replies-list'));
                }



                var bg = post.css('background-color');
                post.css('background-color', 'rgb(255, 221, 142)');
                post.show(150);
                post.animate({backgroundColor: 'rgba(255, 221, 142, 0)'}, 3000, function() {
                    post.css('background-color', bg);
                });
                // TODO improve the animation
            }

            var counter = jQuery('.counterReplies'),
                count = parseInt(counter.text());
            counter.text( counter.text().replace(count, parseInt(count) + counterAdded) );

            jQuery(document.body).trigger('post:created');
        }

        if (datas.update.length > 0) {
            for (i = 0, c = datas.update.length; i < c ; ++i) {
                elem = datas.update[i];
                predecessor = jQuery('#p' + elem.id);

                if (predecessor.length == 0)
                    continue;

                ++counterAdded;

                content = jQuery(jQuery.parseHTML(elem.html));
                content.hide();

                if (elem.parent == 0) {
                    predecessor = predecessor.find('.forumplusone-post-wrapper:first');
                    content.find('header').remove();
                    content.find('.forumplusone-thread-flags').after(predecessor.find('header'))
                }

                content.insertBefore(predecessor);

                content.find('.forumplusone-count-replies').replaceWith(predecessor.find('.forumplusone-count-replies'))


                var bg = content.css('background-color');
                content.css('background-color', 'rgb(255, 221, 142)');
                content.show();
                predecessor.remove();
                content.animate({backgroundColor: 'rgba(255, 221, 142, 0)'}, 3000, function() {
                    content.css('background-color', bg);
                });
            }
            jQuery(document.body).trigger('post:created');
            jQuery('.votersPanel').prev('.forumplusone-post-wrapper').find('.forumplusone-show-voters-link').trigger('click');
        }

        if (datas.del.length > 0) {
            for (i = 0, c = datas.del.length; i < c ; ++i) {
                elem = datas.del[i];

                jQuery('[data-discussionid="' + elem.id + '"]').attr('data-delMsg', window.liveReload.msgDel);
                jQuery('[data-discussionid="' + elem.id + '"]').addClass('delDiscussion');
            }

            var counter = jQuery('.counterReplies'),
                count = parseInt(counter.text());
            counter.text( counter.text().replace(count, parseInt(count) - datas.del.length) );

            jQuery(document.body).trigger('post:deleted');
        }

        if (typeof datas.disc == "object") {
            jQuery('h3').text(datas.disc.name);
            jQuery('.stateChanger input[type="radio"][value="' + datas.disc.state + '"]').prop("checked", true);
            jQuery('.stateChanger').stateChange(datas.disc.state);
        }

        if (datas.isDel) {
            alert(window.liveReload.msgDel); // TODO Improve it !
        }
    }

// meme taitement pour les post
// + gestion du split (ajout d'une conv ou suppr de messages "déplacés")




























    window.onloadFnc = window.onloadFnc || [];
    window.onloadFnc.push(onload);

})(window, document);
