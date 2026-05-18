<?php
include 'auth.php';
ensureAdmin();
include '../includes/db.php';
include '../includes/header.php';
include 'nav.php';

$query = "SELECT * FROM hotels ORDER BY hotel_name ASC";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Hotels</h1>
        <a href="add_hotel.php" class="btn btn-success">Add New Hotel</a>
    </div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Image URL</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($hotel = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($hotel['hotel_name']) ?></td>
                            <td><?= htmlspecialchars($hotel['location']) ?></td>
                            <td><a href="<?= htmlspecialchars($hotel['image']) ?>" target="_blank"><?= htmlspecialchars($hotel['image'] ?: 'No image') ?></a></td>
                            <td>
                                <a href="edit_hotel.php?id=<?= urlencode($hotel['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hotels have been added yet.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>