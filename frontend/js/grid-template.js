
jQuery(document).ready(function($){
    scroller = $('#app-list')[0];
    $('.app-item').activate(function(){
        scroller.scrollLeft = $(this).position().left-488;
    }).navEnter(function(){
        var link = $(this).find('a').attr('href') || false;
        if (link){
            window.location.href = link;
        }
    }).navLeft(function(){
        var prev = $(this).prev('.app-item');
        if (prev.length){
            prev.activate();
        }
    }).navRight(function(){
        var next = $(this).next('.app-item');
        if (next.length){
            next.activate();
        }
    });

    $('.app-item:first').activate(function(){
       $('#left-arrow').hide();
    }).destroy(function(){
        $('#left-arrow').show();
    });

    $('.app-item:last').activate(function(){
        $('#right-arrow').hide();
    }).destroy(function(){
        $('#right-arrow').show();
    });


    setTimeout(function(){
        var $navs = $('.app-item.navigable');
        $navs.eq(Math.floor($navs.length/2)).activate();
    },5);
});