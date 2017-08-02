<link rel="stylesheet" type="text/css" media="screen" href="/wp-content/plugins/gliffy-plugin-for-wordpress/style.css">
<div class="wrap">
<div class="icon32">
<img src="<?php echo get_option('siteurl').'/wp-content/plugins/gliffy-plugin-for-wordpress/images/Logo_32x32.png'; ?>" alt="Gliffy Logo"/>
</div>
<h2>Edit Gliffy Diagrams</h2>
<p>
All diagrams in your account will be displayed in the table below.  Only <strong>public</strong> diagrams will be displayed on your blog
if you choose to insert them in a post or page.<br />
<a href="/wp-admin/admin.php?page=gliffy-add">Add a new diagram</a>
</p>
<?php
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
?>
<form action="<?php gliffy_current_page() ?>" method="post">
    <div id="gliffydirectory">
<?php
    if (isset($diagrams) ) {
        foreach( $diagrams as $diagram ) {
            $launchLink = $gliffy->getEditDiagramLink($diagram->id, gliffy_current_page(), 'Back to Wordpress');

            echo "<div class=\"gliffydirbox\">
            <div class=\"gliffyimgbox\">
                <img class=\"gliffyimage\" src=\"".$gliffy->getDiagramAsURL($diagram->id, Gliffy::MIME_TYPE_PNG, 'M')."\">
            </div>
            <div class=\"gliffyinfobox\">
            <strong class=\"gliffytitle\">{$diagram->name}</strong><br/>
            <a href='".$launchLink."' title='Edit diagram'>Edit</a>&nbsp;&nbsp;
            <a href=\"".gliffy_diagram_view_link($diagram->id,"i")."\" target=\"_blank\" title=\"Opens diagram in new window\">View</a><br/>
            <div class=\"gliffyinfobottom\">
            Id: {$diagram->id}<br/>";
            echo ($diagram->is_public) ? 'diagram is public' : '<span style="color: red;">diagram is private - cannot be inserted</span>';
            echo "<br/>versions: {$diagram->num_versions}
                    </div>
                </div>
            </div>";
        }
    }
    error_log("**** END OF WP-DIAGRAMS ****");
?>
    </div>
</form>

</div>