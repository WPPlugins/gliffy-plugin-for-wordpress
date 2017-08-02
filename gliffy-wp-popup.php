<?php 
define( 'IFRAME_REQUEST' , true );
require('../wp-blog-header.php');
require_once('./admin.php'); 
require_once('gliffy-plugin-for-wordpress.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?> >
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title>Gliffy Diagram Popup</title>
<?php

/** Load WordPress Administration Bootstrap */


wp_enqueue_script("jquery");
wp_enqueue_style( 'global' );
wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'colors' );
wp_enqueue_style( 'media' );
wp_enqueue_style( 'ie' );

?>
<?php
//wp_head();
do_action('admin_print_styles');
admin_css();


?>

<script type="text/javascript">
    // insertion from media frame
var GliffyMacro = {

	diagramId : '',
        size : 'i',

	insert : function() {
		var win = window.dialogArguments || opener || parent || top;
		var html = "#gliffydiagram(" + this.diagramId + "," + this.size + ")#";
		win.send_to_editor(html);
    },

    setDiagramId : function(diagram_id) {
        this.diagramId = diagram_id;
    },
    
    setSize : function(size_id) {
        this.size = size_id;
    }

};
parent.ungrowIframe(); 
</script>
</head>
<body>


<div class="wrap">
<!--
<h2>Create a new Diagram</h2>

<form>
    <label for="document_name">Document Name</label>
    <input id="document_name" name="document_name" type="text" />
    <input type="submit" value="Create"/>
</form>

<h2>Existing Diagrams</h2>
-->
<p style="padding: 5px; margin-left: 8px;">
Insert a Gliffy Diagram into your page or post. This will be rendered into an image when published or previewed.
<span style=" font-style: italic;">Remember, only <strong>public</strong> diagrams will be displayed.</span>
</p>
<?php
    if (gliffy_isconfigured()) {

        try {
            error_log("**** about to get instance");
            $gliffy = gliffy_getinstance();
            error_log("**** about to get diagrams");
            $diagrams = $gliffy->getDiagrams();
            error_log("**** back from get diagrams");
        }
        catch (GliffyException $e) {
            echo "<div class='error'><p><strong>",$e->getMessage(),"</strong></p></div>";
        }
    }
    else {
        echo "<div class='error' id='error'><p><strong>Gliffy for WordPress is not configured.  Please enter your API credentials in the Settings page.</strong></p></div>";
    }
?>

<form action="<?php gliffy_current_page() ?>" method="post">
<div id="gliffyinsertdir">
<?php
    if (isset($diagrams) ) {
        foreach( $diagrams as $diagram ) {
            $launch_link = $gliffy->getEditDiagramLink($diagram->id, gliffy_current_page(), 'Back to Wordpress');
            $returnURL = gliffy_current_page();

            echo "<div class=\"gliffydirbox\">
            <div class=\"gliffyimgbox\">
                <img class=\"gliffyimage\" src=\"".$gliffy->getDiagramAsURL($diagram->id, Gliffy::MIME_TYPE_PNG, 'M')."\">
            </div>
            <div class=\"gliffyinfobox\">\n
            <strong class=\"gliffytitle\">{$diagram->name}</strong><br/><br/>\n";
            
            if ($diagram->is_public) {
            echo "Choose insert type:<br /><br />
                <div style=\"margin-left: 10px;\">
            <label>
                
            <input id='$diagram->id' name='diagram' type='radio' onclick=\"GliffyMacro.setDiagramId($diagram->id);GliffyMacro.setSize('i');\" value='$diagram->id' />
            &nbsp;HTML [diagrams with links]
                    
            </label><br/>
            <label>
            <input id='$diagram->id' name='diagram' type='radio' onclick=\"GliffyMacro.setDiagramId($diagram->id);GliffyMacro.setSize('l');\" value='$diagram->id' />
            &nbsp;Large
            </label><br/>
            <label>
            <input id='$diagram->id' name='diagram' type='radio' onclick=\"GliffyMacro.setDiagramId($diagram->id);GliffyMacro.setSize('m');\" value='$diagram->id' />
            &nbsp;Medium
            </label><br/>
            <label>
            <input id='$diagram->id' name='diagram' type='radio' onclick=\"GliffyMacro.setDiagramId($diagram->id);GliffyMacro.setSize('s');\" value='$diagram->id' />
            &nbsp;Thumbnail
            </label><br/><br/>
            <label><input type=\"button\" name=\"sbmt_$diagram->id\" id=\"sbmt_$diagram->id\" value=\"Insert Diagram\" onclick=\"GliffyMacro.insert();\"></label><br/>
            <br></div>\n";           
            
            } else {
                echo "<span style=\"color: red;\">diagram is private - cannot be inserted</span>
                    <br/>To make this diagram public, open it in the Gliffy editor, select Share, publish to internet<br/>";
            }
            
            echo "<div class=\"gliffyinfobottom\">
                    Id: {$diagram->id} <br/>versions: {$diagram->num_versions}\n</div>
                <a href='".$launch_link."' title='Edit diagram' onclick=\"parent.growIframe()\">Edit</a>&nbsp;&nbsp;
                <a href=\"".gliffy_diagram_view_link($diagram->id,"i")."\" target=\"_blank\" title=\"Opens diagram in new window\">View</a>
                </div>
            </div>";

        }
    }
    error_log("**** END OF WP-DIAGRAMS ****");
?>

</div>
</form>
</div>

</body>
</html>
