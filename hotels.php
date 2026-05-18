<?php
include 'includes/db.php';
include 'includes/header.php';

function getHotelImageSrc($image) {
    if (empty($image)) {
        return 'https://via.placeholder.com/400x250?text=No+Image';
    }

    $trimmed = trim($image);
    if (preg_match('#^(https?://|data:|/)#i', $trimmed)) {
        return $trimmed;
    }

    return 'images/' . ltrim($trimmed, '/\\');
}

$query = "SELECT * FROM hotels";
$result = mysqli_query($conn, $query);

if ($result === false) {
    $queryError = mysqli_error($conn);
}
?>

<div class="container mt-5">
    <h1 class="mb-4">Hotels</h1>

    <?php if (isset($queryError)): ?>
        <div class="alert alert-danger">Unable to load hotels: <?= htmlspecialchars($queryError) ?></div>
    <?php elseif ($result && mysqli_num_rows($result) > 0): ?>
        <div class="row">
            <?php while ($hotel = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars(getHotelImageSrc($hotel['image'])) ?>" class="card-img-top" alt="<?= htmlspecialchars($hotel['hotel_name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($hotel['hotel_name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($hotel['location']) ?></p>
                            <a href="hotel_details.php?id=<?= urlencode($hotel['id']) ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hotels found in the database yet.</div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php';
