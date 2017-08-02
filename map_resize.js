/** Checks to ensure diagrams with maps if resized then map areas are also resized. */
function upNumber(nummy, ratio) {
    return Math.round(parseInt(nummy) * ratio);
}

var ids = " "; // string to hold map ids, to ensure the resize doesn't run twice

function resizeMap(target) {
    var xratio = 1;
    var yratio = 1;
    var coords = new Array();
    var coord = '';
    
    xratio = parseInt(target.width)/parseInt(jQuery(target).attr("orgwidth"));
    yratio = parseInt(target.height)/parseInt(jQuery(target).attr("orgheight"));
    var map = jQuery(target).attr("usemap");

    if (((xratio != 1) || (yratio != 1)) && (ids.indexOf(map) == -1)) {
        ids += map;
        var areas = jQuery(map).children("area");
        jQuery(areas).each(function() {
            coords = jQuery(this).attr("coords").split(',');
            coord = upNumber(coords[0],xratio) +',' + upNumber(coords[1],xratio) + ',' + upNumber(coords[2],xratio) + ',' + upNumber(coords[3],xratio);
            jQuery(this).attr("coords", coord);
        })

    }
}