(function() {
    'use strict';

    function onload(){
        var form = jQuery('.stateChanger');
        var radios = jQuery(this).find('input[type=radio]');



        function autoSubmitState(newChoise, form) {
            var self = this;
            jQuery.get(
                {
                    url: '/mod/forumimproved/route.php',
                    data: {
                        contextid: form.find('input[name="contextid"]').val(),
                        action: 'changestate',
                        state: newChoise,
                        discussionid: form.find('input[name="d"]').val(),
                    },
                    success: function (data) {
                        if (data.errorCode == 0) {
                            switch (newChoise) {
                                case "0": // open
                                    jQuery(self).parents('.forumimproved-thread').removeClass('topic-closed topic-hidden');
                                    break;
                                case "1": // close
                                    jQuery(self).parents('.forumimproved-thread').removeClass('topic-hidden').addClass('topic-closed');
                                    break;
                                case "2": // hide
                                    jQuery(self).parents('.forumimproved-thread').removeClass('topic-closed').addClass('topic-hidden');
                                    break;
                            }
                        }
                    }
                }
            );
        }




        jQuery.fn.extend({
            stateChangerDispach: function() {
                this.find('.select input[type="radio"]').on('change',function() {
                    autoSubmitState.apply(this, [jQuery(this).val(), jQuery(this).parents('form')]);
                });

                this.find('[type="submit"]').css('display', 'none');

                return this;
            }
        });

        form.stateChangerDispach();

        jQuery(document.body).on('discussion:created', function() {
            jQuery('.stateChanger').stateChangerDispach();
        });
        jQuery(document.body).on('discussion:deleted', function() {
            jQuery('.stateChanger').stateChangerDispach();
        });
        jQuery(document.body).on('form:canceled', function() {
            jQuery('.stateChanger').stateChangerDispach();
        });
        jQuery(document.body).on('post:created', function() {
            jQuery('.stateChanger').stateChangerDispach();
        });
        jQuery(document.body).on('post:deleted', function() {
            jQuery('.stateChanger').stateChangerDispach();
        });
        jQuery(document.body).on('post:updated', function() {
            jQuery('.stateChanger').stateChangerDispach();
        });
    }




    window.onloadFnc = window.onloadFnc || [];
    window.onloadFnc.push(onload);
})(window, document);
