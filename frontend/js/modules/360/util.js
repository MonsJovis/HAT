
var $ = jQuery;
window['DEBUG'] = window['DEBUG'] || false;
var $debug;
var errorLog = '';
var playLog = '';

function log(msg, unescaped) {
    if(DEBUG) {
        $debug = $debug || $('#debug');
        if(unescaped && 'string' == typeof(unescaped)) msg += unescaped;
        window.console && window.console.log && window.console.log(msg);
        println($debug, msg, unescaped);
    }
};

function errorlog(msg, unescaped) {
    errorLog += ' '+msg+' ;';
};

function playlog(msg, unescaped) {
    playLog += ' '+msg+' ;';
};

function println(target, line, unescaped) {
    var html = $(target).html();
    $(target)[unescaped ? 'html' : 'text'](line);	// escape text
    $(target).html($(target).html()+'<br/>'+html);
};

function printlnr(target, line, unescaped) {
    var html = $(target).html();
    $(target)[unescaped ? 'html' : 'text'](line);	// escape text
    $(target).html(html+$(target).html()+'<br/>');
};

function dump(obj, html) {
    return JSON.stringify(obj, null, html ? '<br/>' : 4);
};

window.Exception = window.Exception || function(msg) {
    this.name = 'User Exception',
    this.message = msg;
};

function dumpex(ex) {
    ex = ex || { message : 'NULL Exception!' };
    return ex.name+': '+ex.message /*e+' - at:<br/>+ '+(ex.stack || '').replace('\n','<br/>+ ')*/;
}

function formatTime(timestamp, t) {
    t = t || new Date(timestamp);
    return pad(t.getHours(),2)+':'+pad(t.getMinutes(),2)+':'+pad(t.getSeconds(),2)+','+pad(t.getMilliseconds(),3);
};

function pad(str, len) {
    str = '' + str;
    while(str.length < len)
        str = '0'+str;
    return str;
};

 function setCookie(name, value, maxage) {				//EXT_FUNC
    log("setCookie: "+name+" = "+value+" max-age: "+maxage);

    document.cookie = name + "=" + escape(value) + "; max-age="+(maxage | 0);
    log("cookie set to: "+getCookie(name));
}

function getCookie(cookieName) {
    var re = new RegExp('[; ]'+cookieName+'=([^\\s;]*)');
    var sMatch = (' '+document.cookie).match(re);
    return (cookieName && sMatch) ? unescape(sMatch[1]) : '';
}
	 		