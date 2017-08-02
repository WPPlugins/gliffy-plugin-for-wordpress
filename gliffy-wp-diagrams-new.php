<div class="wrap">
<div class="icon32">
<img src="<?php echo get_option('siteurl').'/wp-content/plugins/gliffy-plugin-for-wordpress/images/Logo_32x32.png'; ?>" alt="Gliffy Logo"/>
</div>
<h2>Add New Gliffy Diagram</h2>
<p>
Provide a name for your new diagram.  This will launch you into the Gliffy editor.  When you're done creating your diagram, just click
the link "Back to Wordpress".
</p>
<?php
if (!gliffy_isconfigured()) {
    echo "<div class='error' id='error'><p><strong>Gliffy for WordPress is not configured.  Please enter your <a href='options-general.php?page=gliffy-config'>API credentials</a>.</strong></p></div>";
}
?>

<form id="gliffy_new" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" name="post">
	<label for="document_name">Name</label>
	<input id="document_name" type="text" name="document_name"/>
    <input type="submit" class="button-primary" value="<?php _e('Create') ?>" />
</form>

<?php

if (!empty($_POST['document_name'])) {
    try {
        $gliffy = gliffy_getinstance();
        $diagramId = $gliffy->createDiagram($_POST['document_name']);
        $return_url = get_option('siteurl').'/wp-content/plugins/gliffy-plugin-for-wordpress/gliffy-wp-launch-close.php';
        $launch_link = $gliffy->getEditDiagramLink($diagramId, $return_url , 'Back to Wordpress');

?>

<script type="text/javascript">
//<![CDATA[
    function closeIframe() {
       var iframe = document.getElementById('gliffy_editor');
       iframe.parentNode.removeChild(iframe);

//        todo: get transparent background to work cross browser
//       var div_background = document.getElementById("TB_overlay");
//       div_background.style.display = "none";
    }
//]]>
</script>
        <iframe id="gliffy_editor"
                frameborder="0"
                style="
                    position: fixed;
                    z-index:100;
                    top: 0px;
                    left: 0px;
                    height:100%;
                    width:100%;
                "
                src="<?php echo $launch_link ?>"
                >
            <p>Your browser does not support iframes.</p>
        </iframe>
<?php
    
    }
    catch (GliffyException $e) {
        echo "<div class='error' id='error'><p><strong>",$e->getMessage(),"</strong></p></div>";
    }
}
?>

</div>
