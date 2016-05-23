/**
 * Created by msc on 27.04.2016.
 */
jQuery(document).ready(function($){
    //Content Boxes ------------------------------------------------------------------------------
    $generalNav = $('body');
    $startEl = $('.navigable').eq(0);

    $primary = $('.navigable.content_primary');
    $secondary = $('.navigable.content_secondary');
    $advertisement = $('.navigable.content_advertisement');

    $primary.navRight(function(){
        if ($secondary.length>0){
            $secondary.activate();
        }
    }).navDown(function(){
        if ($advertisement.length>0){
            $advertisement.activate();
        }
    });

    $secondary.navLeft(function(){
        if ($primary.length>0){
            $primary.activate();
        }
    });

    $advertisement.navUp(function(){
        if ($primary.length>0){
            $primary.activate();
        }
    }).navRight(function(){
        if ($secondary.length>0){
            $secondary.activate();
        }
    }).navEnter(function(){
        var link = $(this).find('a').attr('href') || false;
        if (link){
            window.location.href = link;
        }
    });
    //Text Box
    $('.navigable[data-type="text"]').navUp(function(){
        $(this).children()[1].scrollTop-=20;
    }).navDown(function(){
        $(this).children()[1].scrollTop+=20;
    });

    //Scribble Box
    $('.navigable[data-type="scribble"]').navUp(function(){
        scribble.scroll(-1);
    }).navDown(function(){
        scribble.scroll(1);
    }).navEnter(function(){
        scribble.onOk();
    });

    //Image Box
    var fullscreen = false;
    $('.navigable[data-type="image"]').activate(function(){
        $(this).append($('<div style="z-index: 1" class="fullscreen-banner">Press OK for Fullscreen</div>'));
        $(this).autoDestroy(false);
        $(this).find('.img-row').find('.img-wrap').activate();
        $(this).autoDestroy(true);
        var imgs = $(this).find('.img-wrap');
        imgs.first().addClass('first-img');
        imgs.last().addClass('last-img');
    }).destroy(function(){
        $(this).find('.fullscreen-banner, .img-select').remove();
    }).find('.img-wrap').activate(function(){
        if (fullscreen){
            $(this).addClass('fullscreen');
            if ($(this).hasClass('first-img')) {
                $('#arrows-h .arrowleft').hide();
            } else {
                $('#arrows-h .arrowleft').show();
            }
            if ($(this).hasClass('last-img')) {
                $('#arrows-h .arrowright').hide();
            } else {
                $('#arrows-h .arrowright').show();
            }
        } else {
            $(this).prepend('<div class="img-select"></div>');
            $(this).parent().parent()[0].scrollTop += $(this).parent().position().top;
        }
    }).destroy(function(){
        $(this).removeClass('fullscreen').find('.img-select').remove();
    }).navRight(function(){
        if (fullscreen) {
            var next = $(this).next();
            if (next.length) {
                next.activate();
            } else {
                next = $(this).parent().next();
                if (next.length){
                    next.children().first().activate();
                }
            }
        } else {
            var next = $(this).next();
            if (next.length){
                next.activate();
            } else {
                $(this).parent().parent().parent().destroy();
                $(this).destroy().delegateNavToParent();
            }
        }
    }).navLeft(function(){
        if (fullscreen) {
            var prev = $(this).prev();
            if (prev.length) {
                prev.activate();
            } else {
                prev = $(this).parent().prev();
                if (prev.length){
                    prev.children().last().activate();
                }
            }
        } else {
            var prev = $(this).prev();
            if (prev.length){
                prev.activate();
            } else {
                $(this).parent().parent().parent().destroy();
                $(this).destroy().delegateNavToParent();
            }
        }
    }).navUp(function(){
        if (!fullscreen){
            var prev = $(this).parent().prev().find('.img-wrap').eq($(this).attr('column'));
            if (prev.length){
                prev.activate();
            }
        }
    }).navDown(function(){
        if (!fullscreen){
            var next = $(this).parent().next().find('.img-wrap').eq($(this).attr('column'));
            if (next.length){
                next.activate();
            }
        }
    }).navEnter(function(){
        if ($(this).hasClass('fullscreen')){
            $('#arrows-h').remove();
            $(this).children('.arrows').remove();
            $(this).removeClass('fullscreen');
            $(this).prepend('<div class="img-select"></div>');
            $(this).parent().parent()[0].scrollTop +=  $(this).parent().position().top;
            fullscreen=false;
        } else {
            $('body').append('<div id="arrows-h" class="arrows-h"><div class="arrow arrowleft"></div><div class="arrow arrowright"></div></div>');
            if ($(this).hasClass('first-img')) {
                $('#arrows-h .arrowleft').hide();
            }
            if ($(this).hasClass('last-img')) {
                $('#arrows-h .arrowright').hide();
            }
            $(this).addClass('fullscreen');
            $(this).find('.img-select').remove();
            fullscreen=true;
        }
    });

    //Video Box
    $('.navigable[data-type="video"]').navEnter(function(){
        var content = $('#videoplayer');
        if(!content.hasClass('fullscreen')){
            content.addClass('fullscreen');
        } else {
            content.removeClass('fullscreen');
        }
    }).activate(function(){
        $(this).append($('<div class="fullscreen-banner" style="top:-51px!important;">Press OK for Fullscreen</div>'));
    }).destroy(function(){
        $(this).find('.fullscreen-banner').remove();
    });
    $(this).trigger('nav_init');

    // 360° Video
    videolength = 60*60*1000;
    curtime = 0;
    var min = Math.floor(videolength/1000/60);
    var sec = Math.floor((videolength/1000)%60);
    $('#player-time-full').html((min<10?"0"+min:min)+":"+(sec<10?"0"+sec:sec));
    setInterval(function(){
        curtime+=1000;
        var min = Math.floor(curtime/1000/60);
        var sec = Math.floor((curtime/1000)%60);
        $('#player-time-elapsed').html((min<10?"0"+min:min)+":"+(sec<10?"0"+sec:sec));
        $('#player-track-elapsed').css('width',Math.floor(curtime/videolength*100)+"%");
    },1000);
    setTimeout(function(){
        if (controller360){
            controller360.handleKey(KeyEvent.VK_5,function(json){
                videolength = json;
            });
        }
    },300);

    var closeTimeout;
    function refreshCloseTimeout(){
        if (closeTimeout) clearTimeout(closeTimeout);
        closeTimeout = setTimeout(function(){
            $('#controls-video-360').destroy();
            $("#container-video-360").activate();
        },40000);
    }
    $('.navigable[data-type="360"]').navEnter(function(){
        $('#controls-video-360').activate();
    });
    $("#container-video-360").navEnter(function(){
        $('#controls-video-360').activate();
        refreshCloseTimeout();
    }).navLeft(function(){
        $(this).parent().find('.arrow-left').fadeIn(300).delay(300).fadeOut(300);
        controller360.handleKey(VK_LEFT);
    }).navRight(function(){
        $(this).parent().find('.arrow-right').fadeIn(300).delay(300).fadeOut(300);
        controller360.handleKey(VK_RIGHT);
    }).navUp(function(){
        $(this).parent().find('.arrow-up').fadeIn(300).delay(300).fadeOut(300);
        controller360.handleKey(VK_UP);
    }).navDown(function(){
        $(this).parent().find('.arrow-down').fadeIn(300).delay(300).fadeOut(300);
        controller360.handleKey(VK_DOWN);
    }).nav("Back", function(){
        if ($(this).parent().parent().hasClass('fullscreen')){
            $(this).parent().parent().removeClass('fullscreen')
        } else {
            $(this).delegateNavToParent();
        }
    }).nav("5", function(){

    }).nav("7", function(){
        controller360.handleKey(VK_7);
    }).nav("8", function(){
        controller360.handleKey(VK_8);
    }).nav("9", function(){
        controller360.handleKey(VK_9);
    }).nav("0", function(){
        controller360.handleKey(VK_0);
    });

    $('#controls-video-360').activate(function(){
        refreshCloseTimeout();
        $(this).show();
        $(this).find('.control-button-360').eq(0).activate();
    }).destroy(function(){
        $(this).hide();
    }).autoDestroy(false);

    $('.control-button-360').navLeft(function(){
        refreshCloseTimeout();
        var prev = $(this).prev();
        if (prev.length){
            prev.activate();
        }
    }).navRight(function(){
        refreshCloseTimeout();
        var next = $(this).next();
        if (next.length){
            next.activate();
        }
    }).nav('Back',function(){
        $("#container-video-360").activate();
        $(this).parent().parent().destroy();
    });

    $('#button-360-toogleplay').navEnter(function(){
        refreshCloseTimeout();
        $(this).toggleClass('play');
        if ($(this).hasClass('play')){
            controller360.handleKey(VK_5);
        } else {
            controller360.handleKey(VK_8);
        }

    });

    $('#button-360-fullscreen').navEnter(function(){
        refreshCloseTimeout();
        var content = $(this).parent().parent().parent();
        if(!content.hasClass('fullscreen')){
            content.addClass('fullscreen');
        } else {
            content.removeClass('fullscreen');
        }
    });

    $('#button-360-zoomin').navEnter(function(){
        refreshCloseTimeout();
        controller360.handleKey(VK_9);
    });

    $('#button-360-zoomout').navEnter(function(){
        refreshCloseTimeout();
        controller360.handleKey(VK_7);
    });
    


});


