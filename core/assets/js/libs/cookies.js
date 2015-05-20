jQuery.cookies = function(options) {
  var defaults = { warning: 'This website uses cookies. By continuing we assume your permission to deploy cookies.', policy: false, confirm: false, close: 'Close' },
      settings = $.extend(true, {}, defaults, options),
      cookie = 'cookies',
      banner = $('#cookies'),
      close = $('#cookies .close');

  $(function() {
    if( check(cookie) !== 'accepted' ) {
      notice();
    }
    $('body').on('click', '#cookies .close', function(){
      $('body').children('#cookies').remove();
      create(cookie,'accepted', 14);
    });
  });

  function notice(){
      $('body').prepend('<div id="cookies"></div>').addClass(' notice');
      $('#cookies').append('<p>' + settings.warning + '</p>');
      if( settings.confirm !== false ) {
        if(settings.policy == false) settings.policy = '#';
        $('#cookies').append('<a href="' + settings.policy + '" title="Cookies Policy">' + settings.confirm + '</a>');
      }
      $('#cookies').append('<button type="button" class="btn close" ><span>' + settings.close +'</span></button>');
  }


  function create(name,value,days) {
      if (days) {
          var date = new Date();
          date.setTime(date.getTime()+(days*24*60*60*1000));
          var expires = "; expires="+date.toGMTString();
      }
      else var expires = "";
      document.cookie = name+"="+value+expires+"; path=/";
  }

  function check(name) {
      var nameEQ = name + "=";
      var ca = document.cookie.split(';');
      for(var i=0;i < ca.length;i++) {
          var c = ca[i];
          while (c.charAt(0)==' ') c = c.substring(1,c.length);
          if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
      }
      return null;
  }

  function erase(name) {
      create(name,"",-1);
  }

  function removeNotice(){
    var element = document.getElementById('cookies');
    element.parentNode.removeChild(element);
  }
}
