<?php
echo "<h1>Website Name Check</h1>";
echo "Current folder: " . __DIR__ . "<br>";

// Check header.php
$header = file_get_contents('includes/header.php');
if (strpos($header, 'UbuntuBay') !== false) {
    echo "✅ header.php contains 'UbuntuBay'<br>";
} else {
    echo "❌ header.php still has old name<br>";
}

// Check index.php
$index = file_get_contents('index.php');
if (strpos($index, 'UbuntuBay') !== false) {
    echo "✅ index.php contains 'UbuntuBay'<br>";
} else {
    echo "❌ index.php still has old name<br>";
}

// Check footer.php
$footer = file_get_contents('includes/footer.php');
if (strpos($footer, 'UbuntuBay') !== false) {
    echo "✅ footer.php contains 'UbuntuBay'<br>";
} else {
    echo "❌ footer.php still has old name<br>";
}
?>