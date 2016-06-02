(function() {
    'use strict';

    var MARGIN_TOP = 100;

    function onload(){


        var dispatch = function () {
            var links = jQuery('.forumimproved-show-voters-link');


            links.on('click', function(event) {
                var indexEndingSlash = location.pathname.lastIndexOf('/'),
                    self = $(this),
                    pathname,
                    url;

                if (indexEndingSlash == -1 )
                    throw new Error('WTF (What Terible Failure) ! I don\'t reconize the URL');
                else
                    pathname = location.pathname.substring(0, indexEndingSlash + 1) + 'js/';

                url = location.origin + pathname + "jquery.min.js";

                jQuery.ajax({
                    url: M.cfg.wwwroot + '/mod/forumimproved/route.php',
                    data: {
                        action: 'whovote',
                        postid: self.parents('[data-postid]').attr('data-postid'),
                        contextid: links.attr('href').match(/contextid=(\d+)/)[1]
                    },
                    dataType: 'json',
                    success: function (datas, textStatus, jqXHR) {
                        // hide ohers panels
                        $('.votersPanel').hide();

                        if (datas.errorCode != 0) {
                            // There is an server error
                            console.error("I got an XHR error", datas);
                            jqXHR.fail()
                            return;
                        }

                        var post = self.parents('.forumimproved-post-target').first();

                        // Create panel
                        var panel = createVotersPanel(self, post);

                        // Fill panel
                        fillPanel(panel, datas);

                        // dispatch events
                        dispatchEvents(panel, datas);

                        // show(150)
                        panel.show(150);

                        if (panel.find('div').height() > panel.find('table').outerHeight(true))
                            panel.height(panel.height() - (panel.find('div').height() - panel.find('table').outerHeight(true) - 1)); // The "1" is to avoid o have a scroll bar due to round piwels number

                        // scroll to the element
                        $(self).scrollTo();
                    },
                    error: function (jXHR, textStatus, error) {
                        console.error("I got an XHR error", error);

                        var args = {
                            'url': self.attr('href'),
                            'name': 'showVoters',
                            'options': 'height=400,width=600,top=0,left=0,menubar=0,location=0,scrollbars,resizable,toolbar,status,directories=0,fullscreen=0,dependent'
                        };

                        if (args.url.indexOf('?') === -1) {
                            args.url += '?popup=1';
                        }
                        else {
                            args.url += '&popup=1';
                        }

                        if (openpopup.apply(self.get(), [event, args])) {
                            // window not open :3
                            location.href = link.getAttribute('href');
                        }
                    }
                });

                event.preventDefault();
            });
        }

        function createVotersPanel(launcher, post) {
            var htmlPanel = $.parseHTML(
                '<div class="votersPanel">' +
                    '<span aria-hidden="true" class="arrow"></span>' +
                    '<button class="closeBtn">&times;</button>' +
                    '<h4>' + window.jQueryStrings.votersPanelTitle + '</h4>' +
                    '<div>' +
                        '<table class="table table-hover">'+
                            '<thead>'+
                            '</thead>'+
                            '<tbody>'+
                            '</tbody>'+
                        '</table>'+
                    '</div>' +
                '</div>'
            );

            var elem = $(htmlPanel).insertAfter(post);

            elem.find('.arrow').offset({
                left: launcher.offset().left + (launcher.outerWidth()/2)
            });



            return elem;
        }

        function fillPanel(panel, datas) {
            var i,
                c,
                showDatetime,
                row,
                table = panel.find('table'),
                thead = table.find('thead'),
                tbody = table.find('tbody');

            thead.empty();
            tbody.empty();

            if (datas.votes.length < 1) {
                $('<tr><td style="text-align:center">' + window.jQueryStrings.thereNoVoteHere + '</td></tr>').appendTo(tbody);
                return;
            }

            if (datas.votes[0].datetime || false)
                showDatetime = true;


            // make header
            row = $('<tr>' +
                '<th></th>' +
                '<th><a>' + window.jQueryStrings.tableTitleName + '</a></th>' +
            '</tr>').appendTo(thead)

            if (showDatetime)
                row.append('<th><a>' + window.jQueryStrings.tableTitleDatetime + '</a></th>');


            // fill table
            for (i = 0, c = datas.votes.length; i < c ; ++i) {
                row = $('<tr>' +
                    '<td>' + datas.votes[i].usrpicture + '</td>' +
                    '<td>' + datas.votes[i].fullname + '</td>' +
                '</tr>').appendTo(tbody)

                if (showDatetime)
                    row.append('<td>' + datas.votes[i].datetime + '</td>');
            }
        }


        // dispatch events
        function dispatchEvents(panel, datas) {
            panel.find('.closeBtn').on('click', function() {
                panel.hide(150, function() {
                    $(this).remove();
                });
            });

            var th = panel.find('th')
            th.on('click', function() {
                var index = th.index(this);

                if (index == 0) {
                    return;
                }

                datas.votes = datas.votes.sort(function(a, b) {
                    switch (index) {
                        case 1:
                            return a.fullname.localeCompare(b.fullname);
                        case 2:
                            return a.timestamp - b.timestamp;
                    }
                });
                fillPanel(panel, datas);
                dispatchEvents(panel, datas);
            });
        }


        dispatch();



        jQuery.fn.extend({
            scrollTo: function () { // adapted from  (https://gitlab.com/ajabep/eweb-rock/blob/master/js/js.js#L324)
                var $viewport = $('html, body'); // body to webkit, html to others



                var optionAnimate = {};
                var options = {};

                optionAnimate.scrollTop = this.offset().top - $('.navbar-fixed-top').outerHeight() - MARGIN_TOP;
                optionAnimate.scrollLeft = this.offset().left;

                options.transitionDuration = options.transitionDuration || 150;
                options.transitionTimingFunction = options.transitionTimingFunction || 'swing';


                $viewport.animate(
                    optionAnimate,
                    options.transitionDuration,
                    options.transitionTimingFunction
                );

                // Stop the animation immediately, if a user manually scrolls during the animation.
                $viewport.on('scroll mousedown DOMMouseScroll mousewheel keyup', function (event) {
                    if (event.which > 0 || event.type === 'mousedown' || event.type === 'mousewheel') {
                        $viewport.stop().off('scroll mousedown DOMMouseScroll mousewheel keyup');
                    }
                });

                return this;
            }
        });






        jQuery(document.body).on('discussion:created', dispatch);
        jQuery(document.body).on('discussion:deleted', dispatch);
        jQuery(document.body).on('form:canceled', dispatch);
        jQuery(document.body).on('post:created', dispatch);
        jQuery(document.body).on('post:deleted', dispatch);
        jQuery(document.body).on('post:updated', dispatch);
    }




    window.onloadFnc = window.onloadFnc || [];
    window.onloadFnc.push(onload);
})(window, document);

