<?php
require_once 'auth.php';
require_once '../includes/config.php';
require_once '../includes/db.php';
checkAdmin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cemetery Locator</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .welcome-section {
            background: rgba(26, 26, 26, 0.9);
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: rgba(26, 26, 26, 0.9);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }

        .action-button {
            display: inline-block;
            background: var(--accent-color);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .action-button:hover {
            background: #876a31;
            transform: translateY(-2px);
        }

        .graves-list {
            background: rgba(26, 26, 26, 0.9);
            padding: 1.5rem;
            border-radius: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(0, 0, 0, 0.3);
            color: var(--accent-color);
        }

        .action-links a {
            color: var(--accent-color);
            text-decoration: none;
            margin-right: 1rem;
        }

        .action-links a:hover {
            text-decoration: underline;
        }

        .logout-btn {
            color: #ff4444;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <h1>Admin Dashboard</h1>
            <div class="nav-links">
                <a href="../index.php">View Site</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <section class="welcome-section">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
            <p>Manage grave locations and information from this dashboard.</p>
        </section>

        <div class="admin-actions">
            <div class="action-card">
                <h3>Add New Grave</h3>
                <p>Record a new grave location</p>
                <a href="add-grave.php" class="action-button">Add Grave</a>
            </div>
            <div class="action-card">
                <h3>Manage Graves</h3>
                <p>Edit or remove existing graves</p>
                <a href="#graves-list" class="action-button">View All</a>
            </div>
        </div>

        <section class="graves-list" id="graves-list">
            <h3>Recent Graves</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $conn->query("SELECT * FROM graves ORDER BY created_at DESC LIMIT 10");
                        while ($row = $stmt->fetch()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>Lat: " . htmlspecialchars($row['latitude']) . 
                                 ", Lng: " . htmlspecialchars($row['longitude']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td class='action-links'>";
                            echo "<a href='edit-grave.php?id=" . $row['id'] . "'>Edit</a>";
                            echo "<a href='delete-grave.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } catch(PDOException $e) {
                        echo "<tr><td colspan='4'>Error loading graves: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>

</body>
</html>
