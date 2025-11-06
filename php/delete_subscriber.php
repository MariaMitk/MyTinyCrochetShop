<?php
// delete_subscriber.php
header('Content-Type: text/html; charset=utf-8');

$message = "";
$status = "error";
$id = $_GET['id'] ?? null;

if ($id && is_numeric($id)) {
    // 1. Σύνδεση με τη βάση δεδομένων
    include 'db_connect.php';

    if ($conn->connect_error) {
        $message = "Σφάλμα σύνδεσης με τη βάση δεδομένων.";
    } else {
        // 2. Προετοιμασία δήλωσης SQL (DELETE)
        $stmt = $conn->prepare("DELETE FROM subscribers WHERE id = ?");
        $stmt->bind_param("i", $id);

        // 3. Εκτέλεση
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $message = "✅ Ο συνδρομητής με ID: $id διαγράφηκε επιτυχώς.";
                $status = "success";
            } else {
                $message = "❌ Δεν βρέθηκε συνδρομητής με ID: $id για διαγραφή.";
            }
        } else {
            $message = "❌ Σφάλμα κατά τη διαγραφή: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
} else {
    $message = "❌ Μη έγκυρο ID συνδρομητή.";
}

?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Διαγραφή Συνδρομητή</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { color: #4C4239; }
        .message { 
            margin-bottom: 15px; 
            font-weight: bold; 
            color: <?= ($status === 'success') ? 'green' : 'red' ?>; 
            padding: 10px;
            border: 1px solid currentColor;
        }
    </style>
</head>
<body>

    <h2>🗑️ Διαγραφή Συνδρομητή</h2>

    <p class="message"><?= $message ?></p>
    
    <br>
    <a href="search_subscribers.php">Επιστροφή στην Αναζήτηση</a>
    <br>
    <a href="index.html">Επιστροφή στην Αρχική</a>

</body>
</html>