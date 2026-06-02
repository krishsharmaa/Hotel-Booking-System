<?php
include 'includes/db.php';

// Sample hotel data with image placeholders
$hotels = [
    [
        'hotel_name' => 'Maple Resort',
        'location' => 'Mountain Valley, Colorado',
        'image' => 'https://www.istockphoto.com/photos/luxury-hotel-exterior'
    ],
    [
        'hotel_name' => 'Ocean View Hotel',
        'location' => 'Malibu Beach, California',
        'image' => 'https://via.placeholder.com/400x250/1ABC9C/FFFFFF?text=Ocean+View+Hotel'
    ],
    [
        'hotel_name' => 'Mountain Peak Lodge',
        'location' => 'Aspen, Colorado',
        'image' => 'https://via.placeholder.com/400x250/E67E22/FFFFFF?text=Mountain+Peak+Lodge'
    ],
    [
        'hotel_name' => 'Sunset Paradise Hotel',
        'location' => 'Maui, Hawaii',
        'image' => 'https://via.placeholder.com/400x250/F39C12/FFFFFF?text=Sunset+Paradise'
    ]
];

// Insert sample hotels
$inserted = 0;
$failed = 0;

foreach ($hotels as $hotel) {
    $hotel_name = mysqli_real_escape_string($conn, $hotel['hotel_name']);
    $location = mysqli_real_escape_string($conn, $hotel['location']);
    $image = mysqli_real_escape_string($conn, $hotel['image']);
    
    $query = "INSERT INTO hotels (hotel_name, location, image) VALUES ('$hotel_name', '$location', '$image')";
    
    if (mysqli_query($conn, $query)) {
        $inserted++;
        echo "✓ Added: " . $hotel['hotel_name'] . "<br>";
    } else {
        $failed++;
        echo "✗ Failed to add: " . $hotel['hotel_name'] . " - " . mysqli_error($conn) . "<br>";
    }
}

echo "<br><strong>Summary: $inserted hotels added, $failed failed</strong><br>";
echo "<a href='hotels.php'>View Hotels</a>";
?>
