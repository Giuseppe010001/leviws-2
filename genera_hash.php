<!DOCTYPE html>
<html lang = "it">
<meta charset = "utf-8">
<head><title>Genera hash</title></head>
<body>
<?php
$password_hash = password_hash("admin_password", PASSWORD_BCRYPT);
echo "<p>admin password: ".$password_hash."</p>";

$password_hash = password_hash("user_password", PASSWORD_BCRYPT);
echo "<p>user password: ".$password_hash."</p>";
?>
</body>
</html>
