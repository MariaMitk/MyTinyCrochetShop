<?php
// update_subscriber.php
header('Content-Type: text/html; charset=utf-8');

include 'db_connect.php';

$message = "";
$subscriber = null;
$id = $_REQUEST['id'] ?? null; // Λήψη ID από GET (για εμφάνιση) ή POST (για αποθήκευση)

if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

// Διαδικασία UPDATE (Εάν υποβλήθηκε η φόρμα)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $id) {
    $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''));
    $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $age = htmlspecialchars(trim($_POST['age'] ?? ''));

    $stmt = $conn->prepare("UPDATE subscribers SET first_name=?, last_name=?, email=?, phone=?, age=? WHERE id=?");
    $stmt->bind_param("ssssii", $first_name, $last_name, $email, $phone, $age, $id);

    if ($stmt->execute()) {
        $message = "✅ Η ενημέρωση του συνδρομητή με ID: $id ολοκληρώθηκε επιτυχώς!";
    } else {
        $message = "❌ Σφάλμα κατά την ενημέρωση: " . $stmt->error;
    }
    $stmt->close();
    // Μετά την ενημέρωση, πρέπει να ξαναφορτώσουμε τα δεδομένα για να εμφανιστούν τα νέα
}

// Φόρτωση τρέχοντων δεδομένων (για εμφάνιση στη φόρμα)
if ($id) {
    $stmt_select = $conn->prepare("SELECT first_name, last_name, email, phone, age FROM subscribers WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();

    if ($result_select->num_rows === 1) {
        $subscriber = $result_select->fetch_assoc();
    } else {
        $message = "Δεν βρέθηκε συνδρομητής με ID: " . htmlspecialchars($id);
    }
    $stmt_select->close();
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Επεξεργασία Συνδρομητή</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { color: #4C4239; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="number"] { width: 300px; padding: 8px; border: 1px solid #ccc; }
        .message { margin-bottom: 15px; font-weight: bold; color: green; }
    </style>
</head>
<body>

    <h2>✏️ Επεξεργασία Συνδρομητή (ID: <?= htmlspecialchars($id ?? 'N/A') ?>)</h2>

    <?php if ($message): ?>
        <p class="message" style="color: <?= (strpos($message, '✅') !== false) ? 'green' : 'red' ?>;"><?= $message ?></p>
    <?php endif; ?>

    <?php if ($subscriber): ?>
        <form action="update_subscriber.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

            <div class="form-group">
                <label for="first_name">Όνομα:</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($subscriber['first_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Επώνυμο:</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($subscriber['last_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($subscriber['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Τηλέφωνο:</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($subscriber['phone']) ?>">
            </div>
            <div class="form-group">
                <label for="age">Ηλικία:</label>
                <input type="number" id="age" name="age" value="<?= htmlspecialchars($subscriber['age']) ?>">
            </div>

            <button type="submit">Αποθήκευση Αλλαγών</button>
        </form>
    <?php elseif (!$message): ?>
        <p>Παρακαλώ καθορίστε έναν συνδρομητή για επεξεργασία μέσω του ID.</p>
    <?php endif; ?>
    
    <br>
    <a href="search_subscribers.php">Επιστροφή στην Αναζήτηση</a>
    <br>
    <a href="index.html">Επιστροφή στην Αρχική</a>

</body>
</html>