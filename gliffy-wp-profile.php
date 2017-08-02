<?php
    global $current_user;
    get_currentuserinfo();

    $updated = false;

    if (!empty($_POST['gliffy_username'] )) {
		update_usermeta($current_user->ID, 'gliffy_username', $_POST['gliffy_username']);
		$updated = true;
    }
?>

<div class="wrap">
<h2>Gliffy Profile</h2>

<?php if ($updated) echo '<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><strong>Settings saved.</strong></p></div>'; ?>

<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

<table class="form-table">
<tr valign="top">
<th scope="row">Username</th>
<?php $gliffy_username = get_usermeta($current_user->ID,'gliffy_username'); ?>
<td><input type="text" size="35" name="gliffy_username" value="<?php echo $gliffy_username ?>" /></td>
</tr>
</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>


<?php

// Uh, my intent was to print out the gliffy user info, but there isn't even a call to do that
// todo: I think we should turn off auto provisioning!
if (!empty($gliffy_username)) {
}

?>

</div>