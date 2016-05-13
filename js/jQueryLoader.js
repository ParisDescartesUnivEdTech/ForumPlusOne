
(function() {
    if (window.jQueryLoaderAlreadyLaunch || false)
        return;

    window.jQueryLoaderAlreadyLaunch = true;

    if (typeof jQuery === 'undefined') {
        // not using jQuery so we load our custom jQuery
        var j = document.createElement('script'),
            head = document.getElementsByTagName('head')[0],
            done = false,
            indexEndingSlash = location.pathname.lastIndexOf('/'),
            pathname;

        if (indexEndingSlash == -1 )
            throw new Error('WTF (What Terible Failure) ! I don\'t reconize the URL to load jQuery !');
        else
            pathname = location.pathname.substring(0, indexEndingSlash + 1) + 'js/';
        
        j.src = location.origin + pathname + "jquery.min.js";
        j.onload = j.onreadystatechange = function() {
            if (!done && !this.readyState || this.readyState == 'loaded' || this.readyState == 'complete') {
                done = true;

                j.onload = j.onreadystatechange = null;

                jQuery(function() {
                    onload();
                });
            }
        };

        head.appendChild(j);
    }
    else {
        // using jQuery, so we execute our onload function
        onload();
    }


    function onload() {
        window.onloadFnc = window.onloadFnc || [];
        var i, c;
        for (i = 0, c = window.onloadFnc.length ; i < c ; ++i) {
            window.onloadFnc[i].call();
        }
    }

})(window, document);


