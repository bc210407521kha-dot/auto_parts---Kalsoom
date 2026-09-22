<?php
include "config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sort = isset($_GET['sort']) && $_GET['sort'] === 'desc' ? 'DESC' : 'ASC';
$result = $conn->query("SELECT full_name, username, city, email, created_at FROM users ORDER BY username $sort");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #5D5CDE;
            --primary-hover: #4A49C4;
            --secondary-color: #0275d8;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --bg-primary: #FFFFFF;
            --bg-secondary: #F9FAFB;
            --border-color: #E5E7EB;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --text-primary: #F9FAFB;
                --text-secondary: #D1D5DB;
                --bg-primary: #181818;
                --bg-secondary: #1F2937;
                --border-color: #374151;
            }
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: var(--bg-secondary);
            color: var(--text-primary);
            padding: 1rem;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: var(--bg-primary);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), #8B5CF6);
        }

        h2 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .controls {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
        }

        .btn-secondary:hover {
            background: #025aa5;
            transform: translateY(-1px);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-primary);
        }

        th {
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-weight: 600;
            padding: 1rem 0.75rem;
            text-align: left;
            font-size: 0.875rem;
            border-bottom: 2px solid var(--border-color);
        }

        td {
            padding: 0.875rem 0.75rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.875rem;
            color: var(--text-primary);
        }

        tr:hover {
            background: var(--bg-secondary);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .footer-links {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: var(--primary-hover);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
                margin: 0.5rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }

            .controls {
                flex-direction: column;
                align-items: center;
            }

            th, td {
                padding: 0.5rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome <?php echo htmlspecialchars($_SESSION['full_name']); ?></h2>
        
        <div class="controls">
            <a href="?sort=asc" class="btn btn-primary">Sort Ascending</a>
            <a href="?sort=desc" class="btn btn-secondary">Sort Descending</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>User Name</th>
                        <th>City</th>
                        <th>Email</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['city']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="footer-links">
            <a href="register.php">Register New User</a>
            |
            <a href="login.php">Logout</a>
        </div>
    </div>

    <script>
        // Dark mode support
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (event.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
</body>
</html>