<?php
// Set content type and enable session
header('Content-Type: text/html; charset=utf-8');
session_start();

// Ensure the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Retrieve and Sanitize Input
    $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''));
    $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone= htmlspecialchars(trim($_POST['phone'] ?? ''));
    $age = htmlspecialchars(trim($_POST['age'] ?? ''));
    
    // --- ΠΡΟΣΘΗΚΗ 1: Ένωση Ονόματος ---
    // Ένωση first_name + last_name για χρήση στο μήνυμα
    $full_name = trim($first_name . ' ' . $last_name);
    // ------------------------------------
    
    // 2. Connect to Database
    include 'db_connect.php';
    
    // Check if connection was successful
    if ($conn->connect_error) {
        $_SESSION['form_message'] = "❌ Database connection failed. Please try again later.";
        $_SESSION['form_status'] = 'error';
        header("Location: thank_you.php");
        exit();
    }
    
    // 3. Prepare SQL Statement (Using Prepared Statements for Security)
    $stmt = $conn->prepare("INSERT INTO subscribers (first_name, last_name, email, phone, age) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        $_SESSION['form_message'] = "❌ Error preparing database query.";
        $_SESSION['form_status'] = 'error';
        $conn->close(); // Κλείσιμο σύνδεσης σε περίπτωση σφάλματος προετοιμασίας
        header("Location: thank_you.php");
        exit();
    }
    
    // 4. Bind Parameters and Execute
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $age);
    
    if ($stmt->execute()) {
        // Success - Data saved to database
        
        // --- ΠΡΟΣΘΗΚΗ 2: Υπολογισμός Μέσου Όρου Ηλικίας ---
        $avg_result = $conn->query("SELECT AVG(age) AS average_age FROM subscribers");
        $average_age = "N/A";

        if ($avg_result && $avg_result->num_rows > 0) {
            $row = $avg_result->fetch_assoc();
            $average_age = round($row['average_age']); // Στρογγυλοποίηση
        }
        
        // Αποθήκευση μηνύματος επιτυχίας και μέσου όρου
        $_SESSION['form_message'] = "🎉 **Success!** Thanks for joining My Tiny Crochet Shop, **$full_name**! Your subscription has been confirmed. (Current average subscriber age: $average_age)";
        $_SESSION['form_status'] = 'success';
        
        // Log success
        $log_message = "SUCCESS: New subscription saved - Email: $email, Name: $full_name. Avg Age: $average_age\n";
        error_log($log_message, 3, "form_submissions.log");
        // ----------------------------------------------------

    } else {
        // Error - Failed to save
        $_SESSION['form_message'] = "❌ **Error!** Could not save your subscription. Please try again.";
        $_SESSION['form_status'] = 'error';
        
        // Log error
        $log_message = "ERROR: Failed to save subscription - " . $stmt->error . "\n";
        error_log($log_message, 3, "form_submissions.log");
    }
    
    // 5. Close Statement and Connection
    $stmt->close();
    $conn->close();
    
    // 6. Redirect to Thank You Page
    header("Location: thank_you.php");
    exit();
    
} else {
    // If accessed directly without POST
    http_response_code(405);
    die("❌ This page should only be accessed via form submission.");
}
?>