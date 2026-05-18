<?php
include 'auth.php';
ensureAdmin();
include '../includes/header.php';
include 'nav.php';
?>

<div class="container mt-5">
    <h1>Admin Dashboard</h1>
    <div class="list-group mt-4">
        <a href="hotels.php" class="list-group-item list-group-item-action">Manage Hotels</a>
        <a href="add_hotel.php" class="list-group-item list-group-item-action">Add Hotel</a>
        <a href="add_room.php" class="list-group-item list-group-item-action">Add Room</a>
        <a href="booking.php" class="list-group-item list-group-item-action">View Bookings</a>
        <a href="logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>