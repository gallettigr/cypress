// @codekit-prepend "libs/jquery.lazyload.js";
// @codekit-prepend "libs/cypress.js";
$(function() {
  // SHRINK NAV
  $(document).on('scroll resize touchmove', function() {
    var brand = $('#logo .lettering, #logo .brand'),
        menu = $('#menu-bar nav');
    if ( $(document).scrollTop() > $('[id^="intro-"]').height() - 50 ) {
      menu.addClass('detached');
      brand.hide(10);
    } else {
      menu.removeClass('detached');
      brand.show(10);
    }
  });

  // LAZY LOADER INIT
  $('img.lazy').lazyload({
    effect: 'fadeIn',
    data_attribute: 'src'
  });

  // PARALLAX
  $(document).on('scroll resize touchmove', function() {
    $('.parallax').each(function() {
      var scroll = $(document).scrollTop(),
          speed = $(this).data('parallax');
      $(this).css('top', (0-(scroll*speed))+'px');
    });
  });

  //CYSLIDER
  $('.cyslider').cyslider();

  //CYROUSEL
  $('.cyrousel').cyrousel();
});



