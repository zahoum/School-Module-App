<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$all_modules_sql = "SELECT * FROM modules ORDER BY masse_horaire DESC, nom";
$all_modules_result = mysqli_query($conn, $all_modules_sql);

$user_modules_sql = "SELECT module_id, est_coche FROM user_modules WHERE user_id = $user_id";
$user_modules_result = mysqli_query($conn, $user_modules_sql);

$selected_modules = array();
$selected_modules_coche = array();
while ($row = mysqli_fetch_assoc($user_modules_result)) {
    $selected_modules[] = $row['module_id'];
    if ($row['est_coche'] == 1) {
        $selected_modules_coche[] = $row['module_id'];
    }
}

$stats_sql = "SELECT 
    COUNT(DISTINCT m.id) as total_modules,
    SUM(m.nombre_cours) as total_cours,
    SUM(m.masse_horaire) as total_heures
    FROM modules m
    JOIN user_modules um ON m.id = um.module_id
    WHERE um.user_id = $user_id";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);

if (isset($_POST['update_modules'])) {
    $delete_sql = "DELETE FROM user_modules WHERE user_id = $user_id";
    mysqli_query($conn, $delete_sql);
    
    if (isset($_POST['modules']) && is_array($_POST['modules'])) {
        foreach ($_POST['modules'] as $module_id) {
            $module_id = intval($module_id);
            


            $est_coche = isset($_POST['coche_' . $module_id]) ? 1 : 0;
            
            $insert_sql = "INSERT INTO user_modules (user_id, module_id, est_coche) 
                          VALUES ($user_id, $module_id, $est_coche)";
            mysqli_query($conn, $insert_sql);
        }
    }
    
    header("Location: page2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>State Note</title>
    <style>

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f5f7fa;
        color: #333;
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .header {
        background: linear-gradient(135deg, #4a6fa5 0%, #166088 100%);
        color: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .stats-container {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-around;
        text-align: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .stat-item {
        flex: 1;
        min-width: 150px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: bold;
        display: block;
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .module-meta {
        display: flex;
        gap: 20px;
        margin-top: 10px;
        color: #555;
        font-size: 14px;
    }

    .meta-item {
        background-color: #f0f8ff;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .meta-icon {
        font-weight: bold;
        color: #4a6fa5;
    }
           
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f5f7fa;
    color: #333;
    line-height: 1.6;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    background: linear-gradient(135deg, #4a6fa5 0%, #166088 100%);
    color: white;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.header h2 {
    font-size: 28px;
    margin-bottom: 10px;
}

.header h3 {
    font-size: 22px;
    font-weight: 400;
    opacity: 0.9;
}

.controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
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
    background: linear-gradient(135deg, #4a6fa5 0%, #166088 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(74, 111, 165, 0.4);
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
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}

.form-container {
    background-color: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.form-container p {
    font-size: 18px;
    margin-bottom: 20px;
    color: #555;
}

.info-box {
    background-color: #e8f4fd;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #4a6fa5;
    margin-bottom: 25px;
}

.info-box strong {
    color: #166088;
    display: block;
    margin-bottom: 10px;
    font-size: 18px;
}

.info-box ul {
    padding-right: 20px;
}

.info-box li {
    margin-bottom: 8px;
    color: #555;
}

.module-container {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.module-container:hover {
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.module-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
}

.module-checkbox {
    transform: scale(1.3);
    accent-color: #4a6fa5;
    cursor: pointer;
}

.module-title {
    font-weight: bold;
    color: #2c3e50;
    font-size: 20px;
    flex-grow: 1;
}

.module-desc {
    color: #666;
    margin: 15px 0;
    padding-right: 40px;
    line-height: 1.8;
}

.coche-section {
    display: flex;
    align-items: center;
    gap: 10px;
    background-color: #f0f8ff;
    padding: 10px 15px;
    border-radius: 6px;
}

.coche-checkbox {
    transform: scale(1.2);
    accent-color: #28a745;
    cursor: pointer;
}

.coche-checkbox:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.coche-label {
    color: #2d5016;
    font-weight: bold;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.no-modules {
    text-align: center;
    padding: 40px;
    color: #666;
    font-size: 18px;
}

.logout-link {
    display: block;
    text-align: center;
    margin-top: 30px;
    color: #dc3545;
    text-decoration: none;
    font-weight: 600;
}

.logout-link:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .container {
        padding: 15px;
    }
    
    .header {
        padding: 20px 15px;
    }
    
    .module-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .module-desc {
        padding-right: 10px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
 
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Hello Agine <?php echo $username; ?></h2>
            <h3>Manage State </h3>
        </div>

        <!-- States -->
        <?php if (!empty($selected_modules)): ?>
        <div class="stats-container">
            <div class="stat-item">
                <span class="stat-value"><?php echo count($selected_modules); ?></span>
                <span class="stat-label">state Count</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo $stats['total_cours'] ?? 0; ?></span>
                <span class="stat-label">Coure State</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo $stats['total_heures'] ?? 0; ?>h</span>
                <span class="stat-label"> Masse horaire</span>
            </div>
        </div>
        <?php endif; ?>

        <div class="controls">
            <a href="page2.php" class="btn btn-primary">back to module</a>
        </div>

        
            
            <form method="POST">
                <?php
                if (mysqli_num_rows($all_modules_result) > 0) {
                    while ($module = mysqli_fetch_assoc($all_modules_result)) {
                        $is_selected = in_array($module['id'], $selected_modules);
                        $is_coche = in_array($module['id'], $selected_modules_coche);
                        
                        echo '<div class="module-container">';
                        echo '<div class="module-header">';
                        
                        echo '<input type="checkbox" class="module-checkbox" 
                               name="modules[]" value="' . $module['id'] . '"' 
                               . ($is_selected ? ' checked' : '') . '>';
                        
                        echo '<span class="module-title">' . $module['nom'] . '</span>';
                        

                        echo '<div class="module-meta">';
                        echo '<span class="meta-item"><span class="meta-icon">⏱️</span> ' . $module['masse_horaire'] . ' houre</span>';
                        echo '<span class="meta-item"><span class="meta-icon">📚</span> ' . $module['nombre_cours'] . ' coure</span>';
                        echo '</div>';
                        
                        echo '<div style="display: flex; align-items: center; gap: 5px;">';
                        
                        echo '</div>';
                        
                        echo '</div>';
                        
                        echo '<p class="module-desc">' . $module['description'] . '</p>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>لا توجد وحدات دراسية في النظام</p>";
                }
                ?>
                
                <div class="form-actions">
                    <button type="submit" name="update_modules" class="btn btn-primary">Save Changes</button>
                    <a href="page2.php" class="btn btn-secondary">Cancel</a>
                    <a href="logout.php" class="btn btn-danger">Log Out</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
            
            moduleCheckboxes.forEach(checkbox => {
                const moduleId = checkbox.value;
                const cocheCheckbox = document.querySelector('input[name="coche_' + moduleId + '"]');
                
                if (cocheCheckbox && !checkbox.checked) {
                    cocheCheckbox.disabled = true;
                }
                
                checkbox.addEventListener('change', function() {
                    if (cocheCheckbox) {
                        if (this.checked) {
                            cocheCheckbox.disabled = false;
                        } else {
                            cocheCheckbox.disabled = true;
                            cocheCheckbox.checked = false;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>