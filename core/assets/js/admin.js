var $ = jQuery;
$(function() {
    $(".wp-menu-image").remove(), $("#option-tree-version span").html("DOTSQRPress"), $("*").filter(function() {
        return $(this).css("text-decoration").toLowerCase().indexOf("overline") > -1
    }).addClass("dotsqrpress_bg"),
    $('<li class="dotsqrpress-menu-icon"><a href="http://www.melacommunication.com" rel="follow" title="Mela Communication" alt="Mela Communication"><img src="/core/assets/img/mela.png" alt="MelaPress - Developed by gallettigr and distributed by Mela Communication" title="MelaPress - Developed by gallettigr and distributed by Mela Communication" /></a><li>').prependTo("#wp-admin-bar-root-default")
});
