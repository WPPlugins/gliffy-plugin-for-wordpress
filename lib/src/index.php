<?php
/**
 * This is a quick form to submit to {@link test_configuration.php} to validate your configuration.
 * @package Gliffy
 */
?>
<html><head><title>Test Configuration</title><head><body>
<h2>Test your Gliffy PHP Client Configuration</h2>
<form method="GET" action="test_configuration.php">
Enter your Gliffy username: <input type="text" name="username" value="" /><br />
<font size="-2">
<i>(Your Gliffy username is the username of the admin user of your account.  By default, it would be the user part of your email address)</i>
</font><br />
<input type="Submit" value="Check Configuration" />
</form>
</body>
</html>
