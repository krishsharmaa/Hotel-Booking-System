<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'];
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/hotel_booking_system/index.php">Hotel Booking</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNav" aria-controls="userNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="userNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/hotel_booking_system/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/hotel_booking_system/hotels.php">Hotels</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/hotel_booking_system/user/bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/hotel_booking_system/user/logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/hotel_booking_system/user/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/hotel_booking_system/user/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
