<?php
$serverName = "HOST"; // MSSQL-Server mit Port
$database = "DATABASE"; // Ersetze mit deinem Datenbanknamen
$username = "USER"; // MSSQL-Benutzername
$password = "PASSWORD"; // MSSQL-Passwort

// Verbindungsoptionen
$connectionOptions = array(
    "Database" => $database,
    "Uid" => $username,
    "PWD" => $password,
    "TrustServerCertificate" => true
);

// Verbindung herstellen
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn) {
    echo "✅ Verbindung erfolgreich hergestellt!";
} else {
    echo "❌ Verbindung fehlgeschlagen!";
    die(print_r(sqlsrv_errors(), true));
}

// Verbindung schließen (optional)
sqlsrv_close($conn);

?>