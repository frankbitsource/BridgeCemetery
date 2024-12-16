<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';

// Check if already logged in
if(isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT id, username FROM admins WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch();

        if ($user) {
            // Login successful
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password";
        }
    } catch(PDOException $e) {
        $error = "Connection failed: " . $e->getMessage();
    }
}

// Let's also verify the database content
try {
    $stmt = $conn->query("SELECT id, username FROM admins");
    echo "<div style='display:none'>Registered admins:<br>";
    while($row = $stmt->fetch()) {
        echo "ID: " . $row['id'] . ", Username: " . $row['username'] . "<br>";
    }
    echo "</div>";
} catch(PDOException $e) {
    echo "Error checking admins: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Cemetery Locator</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 2rem;
            background: rgba(26, 26, 26, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--accent-color);
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            color: var(--text-color);
            font-size: 0.9rem;
        }

        .form-group input {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-color);
            font-family: var(--body-font);
        }

        .form-group input:focus {
            outline: 2px solid var(--accent-color);
            background: rgba(255, 255, 255, 0.15);
        }

        .login-btn {
            background: var(--accent-color);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: var(--body-font);
            font-weight: 500;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .login-btn:hover {
            background: #876a31;
            transform: translateY(-2px);
        }

        .error-message {
            background: rgba(255, 0, 0, 0.1);
            border-left: 4px solid #ff0000;
            padding: 10px;
            margin-bottom: 1rem;
            color: #ff0000;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: var(--text-color);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: var(--accent-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <h1>Cemetery Locator</h1>
            <div class="nav-links">
                <a href="../index.php">Home</a>
            </div>
        </div>
    </nav>

    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form class="login-form" method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
        <a href="../index.php" class="back-link">Back to Home</a>
    </div>
</body>
</html>
