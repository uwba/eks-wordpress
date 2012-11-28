/*
Copyright 2011 : Simone Gianni <simoneg@apache.org>

Released under The Apache License 2.0
http://www.apache.org/licenses/LICENSE-2.0

*/

(function($) {
    function createPlayer(jqe, video, options) {
        var ifr = $('iframe', jqe);
        if (ifr.length === 0) {
            ifr = $('<iframe scrolling="no">');
            ifr.addClass('player');
        }
        var src = 'http://www.youtube.com/embed/' + video;
        if (options.playopts) {
            src += '?';
            for (var k in options.playopts) {
                src+= k + '=' + options.playopts[k] + '&';
            }
        }
        ifr.attr('src', src);
        console.log(ifr);
        jqe.append(ifr);  
    }
    
    var defoptions = {
        autoplay: false,
        user: null,
        player: createPlayer,
        loaded: function() {},
        playopts: {
            autoplay: 0,
            egm: 1,
            autohide: 1,
            fs: 1,
            showinfo: 1
        }
    };
    
    $.fn.extend({
        youTubeChannel: function(options) {
            var md = $(this);
            var allopts = $.extend(true, {}, defoptions, options);
              
            $.getJSON('http://gdata.youtube.com/feeds/api/users/' + allopts.user + '/uploads?alt=jsonc&v=2', null, function(data) {
                var videos = [];
                var playlist = '';
                $.each(data.data.items, function(i, item) {
                    videos.push(item.id);
                    playlist += item.id + ',';
                });
                //console.log(videos[0]);
                allopts.playopts.playlist = playlist;
                allopts.player(md, videos[0], allopts);
            });
        }
    });
    
})(jQuery);
        
jQuery(document).ready(function($) {
    $('#player').youTubeChannel({user:'kasparsdambis'});
});
