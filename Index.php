<?php
// Database configuration
$servername = "localhost";
$username = "your_username"; // Replace with your database username
$password = "your_password"; // Replace with your database password
$dbname = "shadrack_portfolio"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS contacts (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    $response = [
        'status' => 'error',
        'message' => 'Error creating table: ' . $conn->error
    ];
    echo json_encode($response);
    exit;
}

// Get form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate data
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    $response = [
        'status' => 'error',
        'message' => 'All fields are required.'
    ];
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = [
        'status' => 'error',
        'message' => 'Invalid email format.'
    ];
    echo json_encode($response);
    exit;
}

// Sanitize data
$name = $conn->real_escape_string($name);
$email = $conn->real_escape_string($email);
$subject = $conn->real_escape_string($subject);
$message = $conn->real_escape_string($message);

// Insert data into database
$sql = "INSERT INTO contacts (name, email, subject, message) 
        VALUES ('$name', '$email', '$subject', '$message')";

if ($conn->query($sql) === TRUE) {
    $response = [
        'status' => 'success',
        'message' => 'Thank you for your message! I will get back to you soon.'
    ];
    
    // Optional: Send email notification
    $to = "swriter@gmail.com";
    $email_subject = "New Contact Form Submission: $subject";
    $email_body = "You have received a new message from your website contact form.\n\n".
                  "Name: $name\n".
                  "Email: $email\n".
                  "Subject: $subject\n".
                  "Message:\n$message";
    $headers = "From: $email\n";
    $headers .= "Reply-To: $email";
    
    mail($to, $email_subject, $email_body, $headers);
} else {
    $response = [
        'status' => 'error',
        'message' => 'Error: ' . $sql . '<br>' . $conn->error
    ];
}

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
