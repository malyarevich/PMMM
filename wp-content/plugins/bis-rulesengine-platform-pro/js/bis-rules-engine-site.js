function bis_re_showImageModalDialog(imagePath) {
    jQuery(".img-span").colorbox({open:true, href:imagePath});
}

function bis_re_showModalDialog(contentId, width) {
    if(width == null || width === '') {
        width = '50%';
    }
    jQuery(".img-span").colorbox({inline:true, width:width, open:true, href:'#'+contentId});
}

function bis_setCookie(key, value, expiredays) {
    var date = new Date();
    date.setTime(date.getTime() + (expiredays * 24 * 60 * 60 * 1000));
    jQuery.cookie(key, value, {expires: date, path: "/"});
}

function bis_re_set_path_cookie(key, value, expiredays) {
    var date = new Date();
    date.setTime(date.getTime() + (expiredays * 24 * 60 * 60 * 1000));
    jQuery.cookie(key, value, {expires: date});
}