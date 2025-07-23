<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Get JSON data from frontend
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (
    !isset($data['name']) || empty(trim($data['name'])) ||
    !isset($data['email']) || empty(trim($data['email'])) ||
    !isset($data['phone']) || empty(trim($data['phone'])) ||
    !isset($data['location']) || empty(trim($data['location']))
) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Please provide name, email, phone, and location"]);
    exit;
}

$name = htmlspecialchars(trim($data['name']));
$email = filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL);
$phone = htmlspecialchars(trim($data['phone']));
$location = htmlspecialchars(trim($data['location']));
$message = isset($data['message']) ? htmlspecialchars(trim($data['message'])) : '';

if (!$email) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid email address"]);
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'abc@gmail.com';
    $mail->Password = '1234 5678 4321'; // Your app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('abc@gmail.com', 'Chatbot Enquiry');
    $mail->addAddress('abc@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Chatbot Enquiry';

    // Construct email body with all fields
    $mailBody = "
        <h2>Chatbot Enquiry</h2>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Phone:</strong> {$phone}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Location:</strong> {$location}</p>
        <p><strong>Details:</strong><br>" . nl2br($message) . "</p>
    ";

    $mail->Body = $mailBody;

    $mail->send();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false]);
}

