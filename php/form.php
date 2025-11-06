<?php
// form.php - Handles both displaying the form (GET) and processing the data (POST).

// --- 1. CONFIGURATION ---
$servername = "db";     
$username = "root"; 
$password = "secret";
$dbname = "crochet_shop"; 
$table = "subscribers";

$response = null;
$success = false;

// --- 2. HANDLE FORM SUBMISSION (POST Request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        $response = "Σφάλμα Σύνδεσης Βάσης Δεδομένων: " . $conn->connect_error;
        // Proceed to render page with error message
    } else {
        
        // Retrieve and Sanitize Data
        $first_name = $conn->real_escape_string($_POST['first_name'] ?? '');
        $last_name = $conn->real_escape_string($_POST['last_name'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $phone = $conn->real_escape_string($_POST['phone'] ?? '');
        $age = (int)($_POST['age'] ?? 0); 
        $terms_accepted = isset($_POST['terms']) ? 1 : 0; 

        // Character Operation
        $full_name = $first_name . " " . $last_name;
        
        // Numerical Operation
        $fixed_number = 50; 
        $numerical_result = ($age + $fixed_number) / 2;
        
        // Execute Secure Insert Query
        $sql = "INSERT INTO $table (first_name, last_name, email, phone, age, full_name, terms_accepted) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssisi", $first_name, $last_name, $email, $phone, $age, $full_name, $terms_accepted);

            if ($stmt->execute()) {
                $response = "✅ Επιτυχής Εγγραφή! Ευχαριστούμε, $full_name. Ο μέσος όρος ηλικίας (εσείς + $fixed_number) είναι: " . number_format($numerical_result, 1) . ".";
                $success = true;
            } else {
                $response = "❌ Σφάλμα κατά την εισαγωγή: " . $stmt->error . ". (Ελέγξτε τη δομή του πίνακα)";
            }
            $stmt->close();

        } else {
            $response = "❌ Σφάλμα προετοιμασίας SQL: " . $conn->error;
        }
        $conn->close();
    }
}
// --- END POST HANDLING ---

// The HTML below handles both displaying the form (if response is null) 
// and showing the results (if response is set)
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Φόρμα Εγγραφής</title>
  <style>
    /* Styling for the form and status messages */
    body { font-family: 'Inter', sans-serif; background-color: #f8f6f2; }
    .form-container {
      max-width: 500px; margin: 80px auto; padding: 2rem;
      background: #fff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .form-group { margin-bottom: 1rem; position: relative; }
    label { display: block; margin-bottom: 0.3rem; font-weight: 600; }
    input[type="text"], input[type="email"], input[type="number"] {
      width: 100%; padding: 0.6rem; border: 1px solid #ccc; border-radius: 6px;
    }
    .error { color: red; font-size: 0.85rem; display: none; margin-top: 0.3rem; }
    .status-box { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 600; }
    .status-success { background-color: #d1fae5; color: #065f46; border: 1px solid #34d399; }
    .status-error { background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
    button {
      background: #A67A6C; color: white; border: none; padding: 0.7rem 1.4rem;
      border-radius: 6px; cursor: pointer; font-weight: 600; width: 100%;
    }
    button:hover { opacity: 0.9; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Join Our Handmade World</h2>

    <?php if ($response): ?>
        <!-- Display status message if a submission attempt was made -->
        <div class="status-box <?php echo $success ? 'status-success' : 'status-error'; ?>">
            <?php echo htmlspecialchars($response); ?>
            <p><a href="index.html" style="text-decoration: underline;">Επιστροφή στην Αρχική</a></p>
        </div>
    <?php endif; ?>

    <!-- Form is displayed on initial GET request AND after submission -->
    <form id="subscribeForm" action="form.php" method="POST" novalidate>
      <div class="form-group">
        <label>First Name:</label>
        <input type="text" name="first_name" required minlength="3">
        <span class="error">Name must be at least 3 characters long</span>
      </div>
      <div class="form-group">
        <label>Last Name:</label>
        <input type="text" name="last_name" required minlength="3">
        <span class="error">Last name must be at least 3 characters long</span>
      </div>
      <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" required>
        <span class="error">Enter a valid email</span>
      </div>
      <div class="form-group">
        <label>Phone:</label>
        <input type="text" name="phone" required pattern="[0-9]{10}">
        <span class="error">Enter a valid 10-digit number</span>
      </div>
      <div class="form-group">
        <label>Age (18+):</label>
        <input type="number" name="age" required min="18" max="120">
        <span class="error">Enter a valid age between 18 and 120</span>
      </div>
      <div class="form-group">
        <input type="checkbox" id="terms" name="terms" required>
        <label for="terms">I accept the terms and conditions</label>
        <span class="error">You must accept the terms to continue</span>
      </div>
      <button type="submit">Subscribe</button>
    </form>
  </div>

  <script>
    // [Note: Your JS validation logic has been omitted for brevity 
    // but should be copied from your last successful subscribe.html file here.]
    // You MUST include your client-side validation logic here!
  </script>
</body>
</html>
```
eof

### 3. Final Test and Run Order

1.  **Stop Docker:** Run `docker-compose down`.
2.  **Clean Files:** Ensure `subscribe.html` and `process_form.php` are **deleted**. Only **`form.php`** should remain.
3.  **Start Docker:** Run `docker-compose up -d`.
4.  **Update `index.html` link:** In your `index.html` menu, change the link to the form to point to the new PHP file:
    ```html
    <a href="form.php" class="text-gray-600 hover:text-[#A67A6C] transition duration-150 font-bold">Εγγραφή (Φόρμα)</a>