<?php
echo "<h2>Diagnóstico p_edunote</h2>";
echo "<b>PHP version:</b> " . PHP_VERSION . "<br>";
echo "<b>Server:</b> " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "<br>";
echo "<b>Document root:</b> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "<br>";
echo "<b>Script filename:</b> " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "<br>";

echo "<h3>Extensiones</h3>";
$exts = ['pdo', 'pdo_mysql', 'mysqli', 'mbstring'];
foreach ($exts as $e) {
    echo "$e: " . (extension_loaded($e) ? '✅' : '❌') . "<br>";
}

echo "<h3>Funciones requeridas</h3>";
$funcs = [
    'match' => PHP_VERSION_ID >= 80000,
    'str_starts_with' => PHP_VERSION_ID >= 80000,
    'str_contains' => PHP_VERSION_ID >= 80000,
    'array_key_first' => PHP_VERSION_ID >= 70300,
    'password_hash' => function_exists('password_hash'),
];
foreach ($funcs as $f => $ok) {
    echo "$f: " . ($ok ? '✅' : '❌') . "<br>";
}

echo "<h3>Intento de conexión BD</h3>";
try {
    $config = require __DIR__ . '/../config/database.php';
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3,
    ]);
    echo "✅ Conexión exitosa<br>";
    $r = $pdo->query("SELECT COUNT(*) as cnt FROM estudiantes");
    echo "Estudiantes: " . $r->fetch()['cnt'] . "<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
