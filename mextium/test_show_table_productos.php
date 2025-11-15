<?php
require_once __DIR__ . '/model/database.php';

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SHOW CREATE TABLE productos");
    $row = $stmt->fetch();
    echo '<pre>' . htmlspecialchars($row['Create Table']) . '</pre>';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
