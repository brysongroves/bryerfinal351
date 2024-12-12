<?php
$host = 'localhost'; 
$dbname = 'final'; 
$user = 'root'; 
$pass = 'mysql';
$charset = 'utf8mb4';

// comment for new repository commit

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT * FROM coffeeshops WHERE ShopName LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        isset($_POST['ShopName']) && isset($_POST['Address']) && isset($_POST['City']) && 
        isset($_POST['State']) && isset($_POST['ZipCode']) && isset($_POST['ContactInfo']) && 
        isset($_POST['WebsiteURL']) && isset($_POST['AverageRating']) && isset($_POST['RoasteryID'])
    ) {
        // Insert new entry
        $shop_name = htmlspecialchars($_POST['ShopName']);
        $address = htmlspecialchars($_POST['Address']);
        $city = htmlspecialchars($_POST['City']);
        $state = htmlspecialchars($_POST['State']);
        $zip_code = htmlspecialchars($_POST['ZipCode']);
        $contact_info = htmlspecialchars($_POST['ContactInfo']);
        $website_url = htmlspecialchars($_POST['WebsiteURL']);
        $average_rating = (float) $_POST['AverageRating'];
        $roastery_id = (int) $_POST['RoasteryID'];
        
        $insert_sql = 'INSERT INTO coffeeshops (ShopName, Address, City, State, ZipCode, ContactInfo, WebsiteURL, AverageRating, RoasteryID) 
        VALUES (:ShopName, :Address, :City, :State, :ZipCode, :ContactInfo, :WebsiteURL, :AverageRating, :RoasteryID)';
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute([
        'ShopName' => $shop_name,
        'Address' => $address,
        'City' => $city,
        'State' => $state,
        'ZipCode' => $zip_code,
        'ContactInfo' => $contact_info,
        'WebsiteURL' => $website_url,
        'AverageRating' => $average_rating,
        'RoasteryID' => $roastery_id]);
    } elseif (isset($_POST['delete_id'])) {
        // Delete an entry
        $delete_id = (int) $_POST['delete_id'];
        
        $delete_sql = 'DELETE FROM coffeeshops WHERE ShopID = :id';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['id' => $delete_id]);
    }
}

$sql = 'SELECT * FROM coffeeshops';
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coffee shops</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-title">Find Coffee Shops in Your Area</h1>
        <p class="hero-subtitle">One may become your new favorite spot!</p>
        
        <!-- Search moved to hero section -->
        <div class="hero-search">
            <h2>Search for a shop</h2>
            <form action="shops.php" method="GET" style="display:inline;">
                <label for="search">Search by Title:</label>
                <input type="text" id="search" name="search" required>
                <input type="submit" value="Search">
            </form>
            
            <?php if (isset($_GET['search'])): ?>
                <div class="search-results">
                    <h3>Search Results</h3>
                    <?php if ($search_results && count($search_results) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    
                                    <th>ShopName</th>
                                    <th>Address</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>ZipCode</th>
                                    <th>ContactInfo</th>
                                    <th>WebsiteURL</th>
                                    <th>AverageRating</th>
                                    <th>RoasteryID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $row): ?>
                                <tr>
                                    
                                    <td><?php echo htmlspecialchars($row['ShopName']); ?></td>
                                    <td><?php echo htmlspecialchars($row['Address']); ?></td>
                                    <td><?php echo htmlspecialchars($row['City']); ?></td>
                                    <td><?php echo htmlspecialchars($row['State']); ?></td>
                                    <td><?php echo htmlspecialchars($row['ZipCode']); ?></td>
                                    <td><?php echo htmlspecialchars($row['ContactInfo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['WebsiteURL']); ?></td>
                                    <td><?php echo htmlspecialchars($row['AverageRating']); ?></td>
                                    <td><?php echo htmlspecialchars($row['RoasteryID']); ?></td>
               
                                        <form action="shops.php" method="post" style="display:inline;">

                                            <input type="hidden" name="delete_id" value="<?php echo $row['ShopID']; ?>">
                                            <input type="submit" value="Delete">
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No shops found matching your search.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- back to it i suppose -->
    <!-- Table section with container -->
    <div class="shoplist">
        <h2>All Coffee Shops</h2>
        <table class="half-width-left-align">
            <thead>
                <tr>
                    <th>ShopName</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>ZipCode</th>
                    <th>ContactInfo</th>
                    <th>WebsiteURL</th>
                    <th>AverageRating</th>
                    <th>RoasteryID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>

                <tr>
                
                        <td><?php echo htmlspecialchars($row['ShopName']); ?></td>
                        <td><?php echo htmlspecialchars($row['Address']); ?></td>
                        <td><?php echo htmlspecialchars($row['City']); ?></td>
                        <td><?php echo htmlspecialchars($row['State']); ?></td>
                        <td><?php echo htmlspecialchars($row['ZipCode']); ?></td>
                        <td><?php echo htmlspecialchars($row['ContactInfo']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($row['WebsiteURL']); ?>" target="_blank">Visit</a></td>
                        <td><?php echo htmlspecialchars($row['AverageRating']); ?></td>
                        <td><?php echo htmlspecialchars($row['RoasteryID']); ?></td>
                    <td>
                        <form action="shops.php" method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['ShopID']; ?>">
                            <input type="submit" value="Delete">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Form section with container -->
    <div class="form-container">
    <h2>Add a Coffee Shop</h2>
        <form action="" method="POST">
            <label for="ShopName">Shop Name:</label>
            <input type="text" id="ShopName" name="ShopName" required>
            <br><br>
            <label for="Address">Address:</label>
            <input type="text" id="Address" name="Address" required>
            <br><br>
            <label for="City">City:</label>
            <input type="text" id="City" name="City" required>
            <br><br>
            <label for="State">State:</label>
            <input type="text" id="State" name="State" required>
            <br><br>
            <label for="ZipCode">Zip Code:</label>
            <input type="text" id="ZipCode" name="ZipCode" required>
            <br><br>
            <label for="ContactInfo">Contact Info:</label>
            <input type="text" id="ContactInfo" name="ContactInfo" required>
            <br><br>
            <label for="WebsiteURL">Website URL:</label>
            <input type="url" id="WebsiteURL" name="WebsiteURL" required>
            <br><br>
            <label for="RoasteryID">Roastery ID:</label>
            <input type="number" id="RoasteryID" name="RoasteryID" required>
            <br><br>
            <input type="submit" value="Add Coffee Shop">
        </form>
    </div>
</body>
</html>