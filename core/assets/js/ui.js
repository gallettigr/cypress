function randomColor() {
  return Math.round(Math.random()) - .5
}
var $ = jQuery;
$(function() {
  $("#backtoblog").remove();
  $(".button, #nav a").addClass("btn btn-primary");
  $(".button, #nav a").attr("data-loading-text", "Loading...");
  $("#rememberme").prop("checked", !0);
  $(".login label").each(function() {
    $(this).contents().first().remove();
    $("br").remove();
  }),
  $("#user_login").attr("placeholder", "username");
  $("#user_pass").attr("placeholder", "password");
  $("#user_email").attr("placeholder", "email");
  $("#loginform").attr("action",$(location).attr("href"));
  $("#nav").html(function() {
    return $(this).html().replace("|", "");
  });
  $("#login").after('<a href="http://www.melacommunication.com" class="logo" title="Mela Communication" alt="Mela Communication"><img src="/core/assets/img/mela.png" /></a>');
  $(".button, #nav a").click(function() {
    var a = $(this);
    a.button("loading");
  });
  $("strong").hasClass("hello") && $("#login_error").addClass("hello");
  var a = ["orange", "blue", "green", "purple"];
  a.sort(randomColor);
  $("body.login").each(function(t) {
    $(this).addClass(a[t]);
    $("input[type=submit]").addClass(a[t]);
    $("#nav a, .nav button").addClass(a[t] + " button");
  })
});
function orient() {
  var height = $(window).height();
  var width = $(window).width();
  var orientation = "";
  if(width>height) {
    var orientation = 'landscape';
    return orientation;
  } else {
    var orientation = 'portrait';
    return orientation;
  }
}
$(window).load(function(){
  $('body').addClass(orient());
});
$(window).resize(function(){
  $('body').removeClass('portrait landscape').addClass(orient());
});
$(window).on("orientationchange",function(){
  $('body').removeClass('portrait landscape').addClass(orient());
});
