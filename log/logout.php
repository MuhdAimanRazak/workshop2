<?php
session_start();

// If logout is requested, destroy session and redirect
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_destroy();
    echo '<script>
        alert("You have successfully logged out!");
        window.location = "../index.php";
    </script>';
    exit; // stop further execution
}
?>

<script>
    // Only show confirm if logout has NOT been requested yet
    if (!window.location.search.includes("logout=true")) {
        if (confirm("Do you really want to log out?")) {
            // User confirmed, reload page with logout query
            window.location.href = "?logout=true";
        } else {
            // User canceled, go back
            window.history.back();
        }
    }
</script>
