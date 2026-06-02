<?php
include 'auth.php';
ensureAdmin();
include '../includes/db.php';
include '../includes/header.php';
include 'nav.php';

$query = "SELECT rooms.*, hotels.hotel_name FROM rooms LEFT JOIN hotels ON hotels.id = rooms.hotel_id ORDER BY hotels.hotel_name ASC, rooms.room_type ASC";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Rooms</h1>
        <a href="add_room.php" class="btn btn-success">Add New Room</a>
    </div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Hotel</th>
                        <th>Room Type</th>
                        <th>Price</th>
                        <th>Availability</th>
                        <th>Image URL</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($room = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($room['hotel_name'] ?: 'Unknown Hotel') ?></td>
                            <td><?= htmlspecialchars($room['room_type']) ?></td>
                            <td>₹<?= htmlspecialchars($room['price']) ?></td>
                            <td><?= htmlspecialchars($room['availability']) ?></td>
                            <td><?= htmlspecialchars(isset($room['image']) && $room['image'] ? $room['image'] : 'No image') ?></td>
                            <td>
                                <a href="edit_room.php?id=<?= urlencode($room['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No rooms have been added yet.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
