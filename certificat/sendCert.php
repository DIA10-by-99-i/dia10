<?php
// Konfiguration: MySQL-Verbindung
$host = "localhost";
$db   = "dia10db";
$user = "dbuser";
$pass = "dbpass";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("DB-Verbindung fehlgeschlagen: " . $conn->connect_error);

$name = $_POST['name'] ?? '';
$amount = $_POST['amount'] ?? 0;
$certNumber = $_POST['certNumber'] ?? '';
$date = date("Y-m-d H:i:s");

if (!isset($_FILES['file'])) die("Keine Datei hochgeladen");

$tmpPath = $_FILES['file']['tmp_name'];
$filename = $_FILES['file']['name'];
$fileData = file_get_contents($tmpPath);

// Zertifikate in DB speichern
$stmt = $conn->prepare("INSERT INTO investments (cert_number, investor_name, amount, date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssis", $certNumber, $name, $amount, $date);
$stmt->execute();
$stmt->close();

// E-Mail senden
$to = "mgoffin@web.de";
$subject = "DIA‑10 Zertifikat von $name";
$message = "Ein neues Zertifikat wurde erstellt:\n\nZertifikats-Nr.: $certNumber\nName: $name\nBetrag: €$amount\nDatum: $date";
$fileContent = chunk_split(base64_encode($fileData));
$boundary = md5(time());

$headers  = "From: DIA-10 Plattform <no-reply@99-i.com>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";

$body  = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= $message."\r\n\r\n";

$body .= "--$boundary\r\n";
$body .= "Content-Type: application/pdf; name=\"$filename\"\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
$body .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
$body .= $fileContent."\r\n";
$body .= "--$boundary--";

if (mail($to, $subject, $body, $headers)) {
    echo "✅ Zertifikat gesendet!";
} else {
    echo "❌ Fehler beim Senden";
}

$conn->close();
?>
