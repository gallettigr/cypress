// @codekit-prepend "libs/jquery.lazyload.js";
$(function() {
  // SHRINK NAV
  $(document).on('scroll resize touchmove', function(e) {
    var brand = $('#logo .lettering, #logo .brand');
    if ( $(document).scrollTop() > $('[id^="intro-"]').height() - 50 ) {
      $('#menu-bar nav').addClass('detached');
      brand.hide(10);
    } else {
      $('#menu-bar nav').removeClass('detached');
      brand.show(10);
    }
  });
  // LAZY LOADER INIT
  $('img.lazy').lazyload({
    effect: 'fadeIn',
    data_attribute: 'src'
  });
  // PARALLAX
  $(document).on('scroll resize touchmove', function(e) {
    $('.parallax').each(function(index, el) {
      var scroll = $(document).scrollTop(),
          speed = $(this).data('parallax');
      $(this).css('top', (0-(scroll*speed))+'px');
    });
  });
});
