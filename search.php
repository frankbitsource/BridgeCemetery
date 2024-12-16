<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

header('Content-Type: application/json');

// Debug: Log that the file is being accessed
error_log("Search.php accessed");

if (isset($_GET['term'])) {
    try {
        $search = $_GET['term'];
        error_log("Search term received: " . $search); // Debug: Log search term

        // Test database connection
        error_log("Testing database connection...");
        $test = $conn->query("SELECT 1");
        error_log("Database connection successful");

        // Debug: Show the SQL query
        $stmt = $conn->prepare("SELECT id, name, latitude, longitude, birth_date, death_date FROM graves WHERE name = ?");
        error_log("SQL Query: SELECT id, name, latitude, longitude, birth_date, death_date FROM graves WHERE name = " . $search);

        $stmt->execute([$search]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debug: Log the results
        error_log("Number of results found: " . count($results));
        error_log("Results: " . json_encode($results));

        echo json_encode($results);
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    error_log("No search term provided");
    echo json_encode(['error' => 'No search term provided']);
}
?> 