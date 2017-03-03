jQuery.fn.extend({
  cyslider: function(options) {
    var container = $(this);
        slideheight = $('ul li', this).outerHeight(),
        defaults = { speed: 5000, easing: 'swing' },
        slide = $('ul li', this),
        slider = $('ul', this);
    var settings = $.extend(true, {}, defaults, options);
    $(window).resize(function(){
      container.css({height: $('ul li', container).outerHeight()});
      slider.css({marginTop: -$('ul li', container).outerHeight()});
    });
    this.each(function() {
      $(this).css({height: slideheight});
      slider.css({marginTop: -slideheight});
      slider.children(':last-child').prependTo($('ul', this));
      setInterval(function(){
        slider.children(':nth-child(3)').addClass('active');
        slider.children(':nth-child(2)').removeClass('active');
        slider.animate({ top: -slide.outerHeight() }, settings.speed / 4, settings.easing, function(){
          slider.children(':first-child').appendTo(slider);
          slider.css({ top: '' });
        });
      }, settings.speed);
    });
  },
  cyrousel: function(options) {
    var defaults = { speed: 700, easing: 'linear', interval: 10000 },
        slides = $('.item', this),
        count = slides.length,
        progress = $('<span class="progress"></span>').appendTo($(this)),
        current = 0,
        settings = $.extend(true, {}, defaults, options);

    slides.eq(current).addClass('active');

    setTimeout(function(){
      slides.css({transition: 'opacity ' + settings.speed + 'ms ' + settings.easing });
    }, 25);

    startRotator();

    function startRotator() {
      startProgress();
      setTimeout(function(){
        resetProgress();
        nextSlide();
        startRotator();
      }, settings.interval );
    }

    function nextSlide() {
      slides.eq(current).removeClass('active');
      current = current < count -1 ? current + 1 : 0;
      slides.eq(current).addClass('active');
    }

    function startProgress() {
      setTimeout(function(){
        progress.css({ transition: 'width ' + settings.interval + 'ms linear', width: '100%' });
      }, 25);
    }

    function resetProgress() {
      progress.css({transition: 'none', width: '0%'});
    }
  },
  svgifer: function() {
    $(this).each(function(){
      var $img = { 'sel': $(this), 'id': $(this).attr('id'), 'class': $(this).attr('class'), 'src': $(this).attr('src') };
      $.get($img.src, function(data){
        var $svg = $(data).find('svg');
        if(typeof $img.id !== 'undefined') {
          $svg = $svg.attr('id', $img.id);
        }
        if(typeof $img.class !== 'undefined') {
          $svg = $svg.attr('class', $img.class + ' svgifer');
        }
        $svg = $svg.removeAttr('xmlns:a');
        $img.sel.replaceWith($svg);
      }, 'xml');
    });
  }
});
