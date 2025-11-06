<?php
// search_subscribers.php
header('Content-Type: text/html; charset=utf-8');

// 1. Ρύθμιση
$search_term = $_GET['search'] ?? ''; // Λήψη όρου αναζήτησης από το URL
$results = [];

if (empty($search_term)) {
    $message = "Παρακαλώ εισάγετε έναν όρο αναζήτησης (π.χ. Last Name).";
} else {
    // 2. Σύνδεση με τη βάση δεδομένων
    include 'db_connect.php';

    if ($conn->connect_error) {
        die("Σφάλμα σύνδεσης: " . $conn->connect_error);
    }

    // 3. Προετοιμασία δήλωσης SQL (SELECT με WHERE)
    // Χρησιμοποιούμε LIKE για ευέλικτη αναζήτηση
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, age FROM subscribers WHERE last_name LIKE ? OR first_name LIKE ?");

    // Προσθήκη μπαλαντέρ (%) στον όρο αναζήτησης
    $param_search = "%" . $search_term . "%";

    $stmt->bind_param("ss", $param_search, $param_search);

    // 4. Εκτέλεση και Λήψη Αποτελεσμάτων
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        $message = "Βρέθηκαν **" . count($results) . "** αποτελέσματα για τον όρο '" . htmlspecialchars($search_term) . "'.";
    } else {
        $message = "Δεν βρέθηκαν συνδρομητές με το όνομα '" . htmlspecialchars($search_term) . "'.";
    }

    $stmt->close();
    $conn->close();
}

// 5. Εμφάνιση αποτελεσμάτων (Απλή δομή HTML)
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Αναζήτηση Συνδρομητών</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h2 { color: #4C4239; }
        .results-table { border-collapse: collapse; width: 80%; margin-top: 20px; }
        .results-table th, .results-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .results-table th { background-color: #f2f2f2; }
        .message { margin-bottom: 15px; font-weight: bold; }
        .search-form { margin-bottom: 20px; }
    </style>
</head>
<body>

    <h2>🔍 Αναζήτηση Συνδρομητών</h2>

    <form class="search-form" action="search_subscribers.php" method="GET">
        <label for="search">Αναζήτηση (Όνομα/Επώνυμο):</label>
        <input type="text" id="search" name="search" value="<?= htmlspecialchars($search_term) ?>" required>
        <button type="submit">Αναζήτηση</button>
    </form>

    <p class="message"><?= $message ?? "" ?></p>

    <?php if (!empty($results)): ?>
        <table class="results-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Όνομα</th>
                    <th>Επώνυμο</th>
                    <th>Email</th>
                    <th>Ηλικία</th>
                    <th>Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $subscriber): ?>
                    <tr>
                        <td><?= htmlspecialchars($subscriber['id']) ?></td>
                        <td><?= htmlspecialchars($subscriber['first_name']) ?></td>
                        <td><?= htmlspecialchars($subscriber['last_name']) ?></td>
                        <td><?= htmlspecialchars($subscriber['email']) ?></td>
                        <td><?= htmlspecialchars($subscriber['age']) ?></td>
                        <td>
                            <a href="update_subscriber.php?id=<?= $subscriber['id'] ?>">Επεξεργασία</a> |
                            <a href="delete_subscriber.php?id=<?= $subscriber['id'] ?>" onclick="return confirm('Είστε σίγουροι ότι θέλετε να διαγράψετε αυτόν τον συνδρομητή;');">Διαγραφή</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <br>
    <a href="index.html">Επιστροφή στην Αρχική</a>

</body>
</html>