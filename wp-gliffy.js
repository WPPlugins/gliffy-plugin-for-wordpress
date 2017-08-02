/* 
 * Gliffy javascript for wordpress plugin
 */

// grows the media iframe
function growIframe() {
    var iContainer =  jQuery("#TB_window");
    iContainer.addClass('gliffyEditorParent', 1000);
    
    var iframe = jQuery("#TB_iframeContent");
    iframe.addClass('gliffyEditoriFrame');
}


// shrinks the media iframe, for use after editor closes in media iframe
function ungrowIframe() {
    var iContainer =  jQuery("#TB_window");
    iContainer.removeClass('gliffyEditorParent', 1000);
    
    var iframe = jQuery("#TB_iframeContent");
    iframe.removeClass('gliffyEditoriFrame');
}
