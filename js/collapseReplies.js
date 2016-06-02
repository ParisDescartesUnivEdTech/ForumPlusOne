(function() {
    'use strict';

    function onload(){
        jQuery.fn.extend({
            collapseReplies: function() {
                console.log(this);
                this.each(function() {
                    var post = $(this);
                    $(this).find('.collapse-icon').on('click', function() {
                        if (isCollapse(post)) {
                            uncollapse(post);
                        }
                        else {
                            collapse(post);
                        }
                    });
                });

                return this;
            }
        });



        function isCollapse(post) {
            var svg = post.find('.collapse-icon');
            return svg.attr('data-original-title') ==  svg.attr('data-title-uncollapse');
        }


        function collapse(post) {
            if (post.hasClass('firstpost')) {
                // first post
                jQuery('.forumimproved-thread-body').hide(150);
            }
            else {
                post.next().hide(150);
            }


            var svg = post.find('.collapse-icon');
            var use = svg.find('use');
            svg.attr('data-original-title', svg.attr('data-title-uncollapse'));
            svg.tooltip('hide').tooltip('show');
            use.attr('xlink:href', '#uncollapse');
        }


        function uncollapse(post) {
            if (post.hasClass('firstpost')) {
                // first post
                jQuery('.forumimproved-thread-body').show(150);
            }
            else {
                post.next().show(150);
            }


            var svg = post.find('.collapse-icon');
            var use = svg.find('use');
            svg.attr('data-original-title', svg.attr('data-title-collapse'));
            svg.tooltip('hide').tooltip('show');
            use.attr('xlink:href', '#collapse');
        }


        jQuery('.forumimproved-post-wrapper').collapseReplies();

        jQuery(document.body).on('discussion:created', function() {
            jQuery('.forumimproved-post-wrapper').collapseReplies();
        });
        jQuery(document.body).on('discussion:deleted', function() {
            jQuery('.forumimproved-post-wrapper').collapseReplies();
        });
        jQuery(document.body).on('form:canceled', function() {
            jQuery('.forumimproved-post-wrapper').collapseReplies();
        });
        jQuery(document.body).on('post:created', function() {
            jQuery('.forumimproved-post-wrapper').collapseReplies();
        });
        jQuery(document.body).on('post:deleted', function() {
            jQuery('.forumimproved-post-wrapper').collapseReplies();
        });
        jQuery(document.body).on('post:updated', function() {
            jQuery('.forumimproved-post-wrapper').collapseReplies();
        });
    }




    window.onloadFnc = window.onloadFnc || [];
    window.onloadFnc.push(onload);
})(window, document);

