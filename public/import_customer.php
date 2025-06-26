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
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8" // Ensure proper encoding
);

// Verbindung herstellen
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn) {
    echo "✅ Verbindung erfolgreich hergestellt!";
} else {
    echo "❌ Verbindung fehlgeschlagen!";
    die(print_r(sqlsrv_errors(), true));
}


/*
// SQL-Abfrage zum Abrufen der Tabellenliste
$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'";
$stmt1 = sqlsrv_query($conn, $sql);
if ($stmt1 === false) {
    die("Fehler beim Abrufen der Tabellen: " . print_r(sqlsrv_errors(), true));
}
echo "📋 Tabellen in der Datenbank '$database':<br>";
while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
    echo "🔹 " . $row['TABLE_NAME'] . "<br>";
}
// Ressourcen freigeben
sqlsrv_free_stmt($stmt1);
*/

// SQL-Abfrage zur Auswahl aller Daten aus der ANSCHRIFT-Tabelle
$sql = "SELECT * FROM ANSCHRIFT";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die("Fehler beim Abrufen der Daten: " . print_r(sqlsrv_errors(), true));
}


$final_Arr = [];
//Tabelleninhalt anzeigen
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // echo "<pre>";
    // print_r($row);
    // exit;
    $tmp_arr = [];
    
    $tmp_arr['row_id'] = $row['ROWID'];
    $tmp_arr['company_name'] = $row['NAME1'];
    $tmp_arr['postal_code'] = $row['PLZ'];
    $tmp_arr['street'] = $row['STRASSEPOSTFACH'];
    $tmp_arr['city'] = $row['ORT'];

    $final_Arr[] = $tmp_arr;

}

// echo "<pre>";
// print_r($final_Arr);
// exit;
// ////////// LARAVEL /////////////
$servername = "localhost";
$username = "laravel_user"; // Change this if needed
$password = "jUJztUbM01rhKT"; // Change this if needed
$dbname = "laravel"; // Change this to your database name

// Create connection
$laravel_conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($laravel_conn->connect_error) {
    die("Laravel Connection failed: " . $laravel_conn->connect_error);
} 


foreach ($final_Arr as $final_data) {
    
    // Prepare an insert statement
    $row_id =  $final_data['row_id'];
    $company_name = $final_data['company_name'];
    $street = $final_data['street'];
    $city = $final_data['city'];
    $postal_code = $final_data['postal_code'];
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    $select_sql = "SELECT * FROM customers WHERE row_id='".$row_id."'  ";
    // echo $select_sql; exit;
    $result = $laravel_conn->query($select_sql);
    
    if ($result->num_rows > 0) { 
        
    }
    else {
        $sql = "INSERT INTO customers (row_id,company_name,street,email,city,postal_code,created_at,updated_at) VALUES ('$row_id','$company_name','$street',NULL,'$city','$postal_code','$created_at','$updated_at')";
        
        if ($laravel_conn->query($sql) === TRUE) {
            echo "<br>Record inserted successfully.";
        } else {
            echo "<br>Error: " . $laravel_conn->error;
        }
    }
}


sqlsrv_close($conn);
$laravel_conn->close();
?>