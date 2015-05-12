// @codekit-prepend "libs/jquery.lazyload.js";
$(function() {
    $("img[lazy]").lazyload({
      effect: 'fadeIn',
      data_attribute: 'src'
    });
});
$(window).scroll(function() {
  if ($(document).scrollTop() > 50) {
    $('nav').addClass('detached');
  } else {
    $('nav').removeClass('detached');
  }
});
