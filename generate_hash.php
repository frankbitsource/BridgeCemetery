<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: admin123<br>";
echo "Hash: " . $hash . "<br>";

// Verify the hash
if (password_verify('admin123', $hash)) {
    echo "Password verification successful!";
} else {
    echo "Password verification failed!";
}
?> 