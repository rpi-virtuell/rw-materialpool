(function(){

    // the minimum version of jQuery we want
    var v = "3.1.1";

    // check prior inclusion and version
    if (window.jQuery === undefined || window.jQuery.fn.jquery < v) {
        var done = false;
        var script = document.createElement("script");
        script.src = "//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js";
        script.onload = script.onreadystatechange = function(){
            if (!done && (!this.readyState || this.readyState == "loaded" || this.readyState == "complete")) {
                done = true;
                initMyBookmarklet();
            }
        };
        document.getElementsByTagName("head")[0].appendChild(script);
    } else {
        initMyBookmarklet();
    }

    function initMyBookmarklet() {
        (window.myBookmarklet = function() {
            var mText=document.getSelection();
            var url ="http://material.rpi-virtuell.de/wp-admin/post-new.php?post_type=material&url="
                +encodeURIComponent(escape(location.href))
                +"&text="+encodeURIComponent(escape(mText));
            window.open(url, "_blank");
        })();
    }

})();
