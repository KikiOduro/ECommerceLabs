<?php

// Always start the session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Check if a user is logged in
 * Returns true if a session variable user_id exists.
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Check if the logged-in user has administrative privileges
 * According to your schema: 2 = admin, 1 = customer.
 */
function isAdmin(): bool {
    return isLoggedIn() && (int)$_SESSION['user_role'] === 2;
}
