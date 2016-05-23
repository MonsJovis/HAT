
jQuery(document).ready(function($){

    $('#redButtonIMG').activate(function(){
    
    }).destroy(function(){

    }).nav("Red", function(){
    	console.log("red");
        var link = $(this).data('frontpage-url') || false;
        if (link){
            window.location.href = link;
        }
    });
    $(this).trigger('nav_init');


    setTimeout(function() {
    	$("#redButtonIMG").css("display", "block");
    	setTimeout(function() {
    		$("#redButtonIMG").css("display", "none");
    	}, ($("#redButtonIMG").data('display-duration')*1000));
    }, ($("#redButtonIMG").data('fadein-time')*1000));
});