<?php
function log_activity($conn, $user_id, $activity_type, $description, $page)
{
    $query = "INSERT INTO activities (user_id, activity_type, description, page_visited) 
              VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $user_id, $activity_type, $description, $page);
    $stmt->execute();
}
?>
