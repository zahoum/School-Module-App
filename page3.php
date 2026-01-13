<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: page2.php");
    exit();
}

$module_id = mysqli_real_escape_string($conn, $_GET['id']);

$user_id = $_SESSION['user_id'];
$check_sql = "SELECT * FROM user_modules WHERE user_id = $user_id AND module_id = $module_id";
$check_result = mysqli_query($conn, $check_sql);

$sql = "SELECT * FROM modules WHERE id = $module_id";
$result = mysqli_query($conn, $sql);
$module = mysqli_fetch_assoc($result);

// Check if user has access to this module
$has_access_sql = "SELECT * FROM user_modules WHERE user_id = $user_id AND module_id = $module_id";
$has_access_result = mysqli_query($conn, $has_access_sql);
$has_access = mysqli_num_rows($has_access_result) > 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $module['nom']; ?> - Module Details</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f0f4f8;
        color: #333;
        min-height: 100vh;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .header {
        background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .header h2 {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .back-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .btn {
        display: inline-block;
        padding: 12px 25px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        text-align: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
    }

    .btn-danger {
        background-color: #e74c3c;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
    }

    .module-details-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .module-header {
        background: linear-gradient(135deg, #27ae60 0%, #219653 100%);
        color: white;
        padding: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .module-title {
        font-size: 28px;
        font-weight: bold;
    }

    .module-status {
        background-color: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .module-content {
        padding: 30px;
    }

    .module-stats {
        display: flex;
        justify-content: space-around;
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .stat-item {
        text-align: center;
        flex: 1;
        min-width: 150px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #3498db;
        display: block;
    }

    .stat-label {
        color: #666;
        font-size: 14px;
        margin-top: 5px;
    }

    .description-section {
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 20px;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #eee;
    }

    .description-box {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        line-height: 1.8;
        color: #555;
        border-left: 4px solid #3498db;
    }

    .module-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-card {
        background-color: white;
        border: 1px solid #e9ecef;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }

    .info-card h4 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 18px;
    }

    .info-list {
        list-style-type: none;
    }

    .info-list li {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
        color: #666;
    }

    .info-list li:last-child {
        border-bottom: none;
    }

    .info-list strong {
        color: #2c3e50;
        min-width: 120px;
        display: inline-block;
    }

    .access-message {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        margin: 30px 0;
    }

    .footer-actions {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }
        
        .module-header {
            flex-direction: column;
            text-align: center;
        }
        
        .back-nav {
            flex-direction: column;
            align-items: stretch;
        }
        
        .btn {
            width: 100%;
        }
        
        .module-stats {
            flex-direction: column;
        }
        
        .footer-actions {
            flex-direction: column;
        }
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Module Details</h2>
            <p>Complete information about the selected module</p>
        </div>

        <div class="back-nav">
            <a href="page2.php" class="btn btn-primary">← Back to Modules</a>
            <div>
                <a href="modules.php" class="btn btn-secondary">📝 Manage Modules</a>
                <a href="logout.php" class="btn btn-danger">🚪 Log Out</a>
            </div>
        </div>

        <?php if (!$has_access): ?>
            <div class="access-message">
                <h3>⚠️ Access Restricted</h3>
                <p>You don't have access to this module. Please select it in the modules management page first.</p>
                <a href="modules.php" class="btn btn-primary" style="margin-top: 15px;">Go to Modules Management</a>
            </div>
        <?php else: ?>
            <div class="module-details-card">
                <div class="module-header">
                    <h2 class="module-title"><?php echo $module['nom']; ?></h2>
                    <span class="module-status">✅ Module Selected</span>
                </div>

                <div class="module-stats">
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $module['masse_horaire']; ?>h</span>
                        <span class="stat-label">Total Hours</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $module['nombre_cours']; ?></span>
                        <span class="stat-label">Number of Courses</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">
                            <?php 
                                if ($module['nombre_cours'] > 0) {
                                    echo round($module['masse_horaire'] / $module['nombre_cours'], 1);
                                } else {
                                    echo '0';
                                }
                            ?>h
                        </span>
                        <span class="stat-label">Avg. Hours per Course</span>
                    </div>
                </div>

                <div class="module-content">
                    <div class="description-section">
                        <h3 class="section-title">Module Description</h3>
                        <div class="description-box">
                            <?php echo nl2br($module['description']); ?>
                        </div>
                    </div>

                    <div class="module-info-grid">
                        <div class="info-card">
                            <h4>📊 Module Statistics</h4>
                            <ul class="info-list">
                                <li><strong>Module Name:</strong> <?php echo $module['nom']; ?></li>
                                <li><strong>Total Hours:</strong> <?php echo $module['masse_horaire']; ?> hours</li>
                                <li><strong>Number of Courses:</strong> <?php echo $module['nombre_cours']; ?> courses</li>
                                <li><strong>Average per Course:</strong> 
                                    <?php 
                                        if ($module['nombre_cours'] > 0) {
                                            echo round($module['masse_horaire'] / $module['nombre_cours'], 1) . ' hours';
                                        } else {
                                            echo 'N/A';
                                        }
                                    ?>
                                </li>
                            </ul>
                        </div>

                        <div class="info-card">
                            <h4>📚 Learning Details</h4>
                            <ul class="info-list">
                                <li><strong>Course Type:</strong> Technical Module</li>
                                <li><strong>Estimated Completion:</strong> 
                                    <?php 
                                        $weeks = ceil($module['masse_horaire'] / 15);
                                        echo $weeks . ' week' . ($weeks > 1 ? 's' : '');
                                    ?>
                                </li>
                                <li><strong>Intensity Level:</strong> 
                                    <?php 
                                        if ($module['masse_horaire'] >= 50) {
                                            echo 'High';
                                        } elseif ($module['masse_horaire'] >= 30) {
                                            echo 'Medium';
                                        } else {
                                            echo 'Low';
                                        }
                                    ?>
                                </li>
                                <li><strong>Recommended Pace:</strong> 
                                    <?php 
                                        $daily = ceil($module['masse_horaire'] / 30);
                                        echo $daily . ' hour' . ($daily > 1 ? 's' : '') . ' daily';
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="footer-actions">
            <a href="page2.php" class="btn btn-primary">← Back to My Modules</a>
            <a href="modules.php" class="btn btn-secondary">📝 Edit Module Selection</a>
            <a href="logout.php" class="btn btn-danger">🚪 Log Out</a>
        </div>
    </div>
</body>
</html>