// @codekit-prepend "libs/jquery.lazyload.js";
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
  function cyslider(object) {
    setInterval(function(){
      $('ul li:nth-child(3)', object).addClass('active');
      $('ul li:nth-child(2)', object).removeClass('active');
      $('ul', object).animate( { top: - $('ul li', object).outerHeight() }, 500, function(){
        $('ul li:first-child', object).appendTo( $('ul', object) );
        $('ul', object).css({top: ''});
      });
    }, object.data('speed'));
  }

  $('.cyslider').each(function(){
    var sliderH = $('ul li', this).outerHeight();
    $(this).css({height: sliderH});
    $('ul', this).css({marginTop: -sliderH});
    $('ul li:last-child', this).prependTo($('ul', this));
    cyslider($(this));
  });

});
