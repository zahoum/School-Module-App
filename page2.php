<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// user Date
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

//trie
$user_modules_sql = "SELECT modules.*, user_modules.est_coche 
                    FROM modules 
                    JOIN user_modules ON modules.id = user_modules.module_id 
                    WHERE user_modules.user_id = $user_id
                    ORDER BY modules.masse_horaire DESC, modules.nom";
$user_modules_result = mysqli_query($conn, $user_modules_sql);

//user state count
$stats_sql = "SELECT 
    COUNT(DISTINCT m.id) as total_modules,
    SUM(m.nombre_cours) as total_cours,
    SUM(m.masse_horaire) as total_heures
    FROM modules m
    JOIN user_modules um ON m.id = um.module_id
    WHERE um.user_id = $user_id";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage State</title>
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
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .welcome-header {
        background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .welcome-header h2 {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .user-stats {
        display: flex;
        justify-content: space-around;
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        flex-wrap: wrap;
        gap: 15px;
    }

    .stat-card {
        text-align: center;
        flex: 1;
        min-width: 150px;
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
    }

    .stat-number {
        font-size: 36px;
        font-weight: bold;
        color: #3498db;
        display: block;
    }

    .stat-label {
        color: #666;
        font-size: 14px;
        margin-top: 5px;
    }

    .actions-bar {
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
    }

    .btn-manage {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
    }

    .btn-manage:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
    }

    .btn-logout {
        background-color: #e74c3c;
        color: white;
    }

    .btn-logout:hover {
        background-color: #c0392b;
        transform: translateY(-2px);
    }

    .section-title {
        font-size: 24px;
        color: #2c3e50;
        margin: 30px 0 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #3498db;
    }

    .module-container {
        background: linear-gradient(to right, #ffffff 0%, #f8fdf9 100%);
        padding: 25px;
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        border-left: 5px solid #27ae60;
        transition: all 0.3s ease;
    }

    .module-container:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
    }

    .module-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .module-name {
        font-size: 24px;
        font-weight: bold;
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.3s;
    }

    .module-name:hover {
        color: #3498db;
    }

    .module-meta {
        display: flex;
        gap: 20px;
        margin-top: 10px;
    }

    .meta-badge {
        background-color: #e8f4fd;
        color: #166088;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .coche-indicator {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .module-desc {
        color: #666;
        line-height: 1.7;
        margin: 15px 0;
        font-size: 16px;
    }

    .module-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
        flex-wrap: wrap;
        gap: 10px;
    }

    .action-link {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
        padding: 8px 16px;
        border: 2px solid #3498db;
        border-radius: 6px;
        transition: all 0.3s;
    }

    .action-link:hover {
        background-color: #3498db;
        color: white;
    }

    .empty-state {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
        padding: 40px;
        border-radius: 10px;
        text-align: center;
        margin: 30px 0;
    }

    .empty-state a {
        color: #3498db;
        font-weight: 600;
        text-decoration: none;
        margin-top: 15px;
        display: inline-block;
    }

    .empty-state a:hover {
        text-decoration: underline;
    }

    .sort-info {
        background-color: #e8f4fd;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        color: #166088;
        border-left: 4px solid #3498db;
    }

    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }
        
        .user-stats {
            flex-direction: column;
        }
        
        .stat-card {
            min-width: 100%;
        }
        
        .module-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .module-footer {
            flex-direction: column;
            align-items: stretch;
        }
        
        .action-link {
            text-align: center;
        }
    }
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f0f4f8;
    color: #333;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.welcome-header {
    background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.welcome-header h2 {
    font-size: 32px;
    margin-bottom: 10px;
}

.actions-bar {
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
}

.btn-manage {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
}

.btn-manage:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
}

.btn-details {
    background-color: #27ae60;
    color: white;
    padding: 8px 15px;
    font-size: 14px;
}

.btn-details:hover {
    background-color: #219653;
    transform: translateY(-2px);
}

.btn-logout {
    background-color: #e74c3c;
    color: white;
}

.btn-logout:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
}

.section-title {
    font-size: 24px;
    color: #2c3e50;
    margin: 30px 0 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #3498db;
}

.no-modules-message {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    text-align: center;
}

.no-modules-message a {
    color: #3498db;
    font-weight: 600;
    text-decoration: none;
}

.no-modules-message a:hover {
    text-decoration: underline;
}

.selected-module {
    background: linear-gradient(to right, #ffffff 0%, #f8fdf9 100%);
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    border-left: 5px solid #27ae60;
    transition: all 0.3s ease;
}

.selected-module:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 10px;
}

.module-name {
    font-size: 22px;
    font-weight: bold;
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s;
}

.module-name:hover {
    color: #3498db;
}

.coche-indicator {
    background-color: #d4edda;
    color: #155724;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.module-desc {
    color: #666;
    line-height: 1.7;
    margin-bottom: 15px;
}

.module-actions {
    display: flex;
    gap: 15px;
    margin-top: 15px;
}

.action-link {
    color: #3498db;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: color 0.3s;
}

.action-link:hover {
    color: #2980b9;
}

.footer {
    text-align: center;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
    color: #666;
}

@media (max-width: 768px) {
    .container {
        padding: 15px;
    }
    
    .welcome-header {
        padding: 20px 15px;
    }
    
    .welcome-header h2 {
        font-size: 26px;
    }
    
    .actions-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .module-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .module-actions {
        flex-direction: column;
    }
    .manage-btn{
        
    }
}
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-header">
            <h2>Hello Agin <?php echo $username; ?></h2>
            <p>Etudiant Space</p>
        </div>

            <!-- User States -->
        <div class="user-stats">
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['total_modules'] ?? 0; ?></span>
                <span class="stat-label">Module Count</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['total_cours'] ?? 0; ?></span>
                <span class="stat-label">Total Cours</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['total_heures'] ?? 0; ?>h</span>
                <span class="stat-label">Houre Total</span>
            </div>
        </div>

        <div class="actions-bar">
            <a href="modules.php" class="btn btn-manage">📝 Manage Courses</a>
            <a href="logout.php" class="btn btn-logout">🚪 Log Out</a>
        </div>

        <div class="sort-info">
            <strong>ℹ️</strong> Modules Ordres Desc
        </div>

        <h3 class="section-title">Course Selected</h3>

        <?php
        if (mysqli_num_rows($user_modules_result) > 0) {
            while ($module = mysqli_fetch_assoc($user_modules_result)) {
                echo '<div class="module-container">';
                echo '<div class="module-header">';
                echo '<a href="page3.php?id=' . $module['id'] . '" class="module-name">' . $module['nom'] . '</a>';
                
                if ($module['est_coche'] == 1) {
                    echo '<span class="coche-indicator">✓ Selected as Favorite</span>';
                }
                echo '</div>';
                
                echo '<div class="module-meta">';
                echo '<span class="meta-badge">⏱️ ' . $module['masse_horaire'] . ' Houre</span>';
                echo '<span class="meta-badge">📚 ' . $module['nombre_cours'] . ' Lesance</span>';
                echo '</div>';
                
                echo '<p class="module-desc">' . $module['description'] . '</p>';
                
                echo '<div class="module-footer">';
                echo '<a href="page3.php?id=' . $module['id'] . '" class="action-link">🔍 Show All Detailles</a>';
                echo '<span>Ordred By Masse Horaire: ' . $module['masse_horaire'] . ' Houres</span>';
                echo '</div>';
                
                echo '</div>';
            }
        } else {
            echo '<div class="empty-state">';
            echo '<h3>There is no Lesance</h3>';
            echo '<p>you can add a lecance from the manage Lecance</p>';
            echo '<a href="modules.php">➡️ Go to lecance managment</a>';
            echo '</div>';
        }
        ?>
        
        
    </div>
</body>
</html>