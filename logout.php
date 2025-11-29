<?php
include 'config/db.php';
include 'customer/log_activity.php';


session_start();
if (isset($_SESSION['user_id'])) {
    log_activity($conn, $_SESSION['user_id'], "logout", "Customer logged out of the system", "logout.php");
}
session_destroy();
header("Location: login.php");
exit();
