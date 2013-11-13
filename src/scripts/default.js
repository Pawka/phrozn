var thingy = {

    init: function(){
        setInterval( this.changeState, 2000 );
    },

    changeState: function() {
        if( $("#thingy").hasClass('breathing') ) {
            $("#thingy").removeClass('breathing');
        }
        else {
            if( Math.random() > 0.8 ) {
                $("#thingy").addClass('breathing');
            }
        }
    }
}

$(document).ready(function () {
    // cleanup TOC
    $(".toc-box").html('<div class="toc-header">Table Of Contents</div>');

    thingy.init();

    // add anchors to headers that have IDs set
    $("#content").children("h1[id], h2[id], h3[id]").each(function (key, el) {
        if ($(el).hasClass("in-toc")) {
            $(".toc-box").append(
                $(document.createElement("div"))
                    .addClass("toc-entry")
                    .html(
                        $(document.createElement("a"))
                            .attr({
                                title: $(el).html(),
                                href: "#" + $(el).attr("id"),
                            })
                            .html($(el).html())
                    ));
        }
        $(document.createElement("a"))
            .attr({
                title: "Permalink to this section",
                href: "#" + $(el).attr("id"),
            })
            .addClass("anchor")
            .html("&sect;")
            .appendTo($(el));
    });

    //$("pre").addClass("prettyprint"); // all pre elements are syntax colored
    //prettyPrint();
    $("pre").each(function(i, e) {hljs.highlightBlock(e, '    ')});
    $(".disable-highlight").each(function () {
        $(this).html($(this).text());
    });
});
