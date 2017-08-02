<script type="text/javascript">
    function showPopup(url) {
        newwindow = window.open(url,'name','height=650,width=985,top=200,left=300,resizable');
        if (window.focus) {
            newwindow.focus();
        }
    }
</script>
<div class="wrap">
<div class="icon32">
<img src="<?php echo get_option('siteurl').'/wp-content/plugins/gliffy-plugin-for-wordpress/images/Logo_32x32.png'; ?>" alt="Gliffy Logo"/>
</div>
<h2>Gliffy API Configuration</h2>
<p>
API access is a feature of Gliffy Premium Accounts.  Follow the links below to get your API credentials.
</p>

<?php
    $gliffy_root = get_option("gliffy_root");
    if (empty($gliffy_root)) {
        $gliffy_root = "http://www.gliffy.com";
    }

    $gliffy_username = get_option('gliffy_username');
    if (empty($gliffy_username)) {
        $gliffy_username = get_bloginfo('admin_email');
    }
?>

<form method="post" action="options.php">

<table class="form-table">
    <tr valign="top">
        <th scope="row">Gliffy Username (email)</th>
        <td><input type="text" size="35" name="gliffy_username" value="<?php echo $gliffy_username; ?>" /></td>
    </tr>

    <tr valign="top">
        <th scope="row">Account Id</th>
        <td><input type="text" size="35" name="gliffy_account_id" value="<?php echo get_option('gliffy_account_id'); ?>" /></td>
    </tr>

    <tr valign="top">
        <th scope="row">OAuth Consumer Key</th>
        <td><input type="text" size="35" name="gliffy_oauth_consumer_key" value="<?php echo get_option('gliffy_oauth_consumer_key'); ?>" /></td>
    </tr>

    <tr valign="top">
        <th scope="row">OAuth Consumer Secret</th>
        <td><input type="text" size="35" name="gliffy_oauth_consumer_secret" value="<?php echo get_option('gliffy_oauth_consumer_secret'); ?>" /></td>
    </tr>

    <tr valign="top">
        <th scope="row">Gliffy ROOT</th>
        <td>
            <input type="text" size="35" name="gliffy_root" value="<?php echo $gliffy_root; ?>" />
            <br/>
            <span style="padding: 0 0 0 5px;">(e.g. http://www.gliffy.com)</span>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </th>
        <td style="vertical-align:middle;">
        <a href="<?php echo $gliffy_root , '/gliffy/account/accountAPILogin.jsp?application=wordpress'; ?>" onClick="showPopup(this.href);return(false);" >Get API Credentials</a>
        <a style="padding: 25px;" href="<?php echo $gliffy_root , '/gliffy/commerce/signup.jsp'; ?>">Don't have a Gliffy Account?</a>
        </td>
    </tr>
</table>

<?php settings_fields( 'gliffy-option-group' ); ?>

</form>

<br/>
<h3>Test Configuration</h3>
<p>
A success or error message should appear below to indicate whether your credentials are working.
</p>
<?php
    if (isset($_POST["test"])) {
        try {
            $gliffy = gliffy_getinstance();
            $gliffy->getAccountInfo();
            echo "<div class='updated fade' id='message' style='background-color: rgb(255, 251, 204);'><p><strong>Successfully Connected!</strong></p></div>";
        }
        catch (GliffyException $e) {
            echo "<div class='error'><p><strong>",$e->getMessage(),"</strong></p></div>";
        }
    }
?>
<form method="post" action="">
<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <input name="test" type="submit" class="button-primary" value="<?php _e('Test Now') ?>" />
        </th>
    </tr>
</table>
</form>
</div>

