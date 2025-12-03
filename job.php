<?php
// This script is configured to handle AJAX/Fetch submissions (like your contact form)
// and echoes a message instead of performing a page redirect.

// PHP Error Reporting (Keep this for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

// The 'Content-Type: text/plain' header is intentionally omitted here, 
// as it was in the successful contact form submission, to allow the AJAX response to display HTML styling.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Collecting and sanitizing form data, using PHP 5.x compatible ternary operator
    $name = htmlspecialchars(trim(isset($_POST['cr_name']) ? $_POST['cr_name'] : 'N/A'));
    $applicant_email = htmlspecialchars(trim(isset($_POST['cr_email']) ? $_POST['cr_email'] : 'N/A'));
    $phone = htmlspecialchars(trim(isset($_POST['cr_phone']) ? $_POST['cr_phone'] : 'N/A'));
    $cover_letter = htmlspecialchars(trim(isset($_POST['cr_cover_letter']) ? $_POST['cr_cover_letter'] : 'No Cover Letter Provided'));

    if (empty($name) || empty($applicant_email) || empty($phone)) {
        echo "<span style='color:red;'>Error: Missing required form fields (Name, Email, Phone).</span>";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // --- SMTP Configuration (Same as contact.php) ---
        $mail->SMTPDebug = 0; // Set to 2 or 4 if debugging connection issues
        $mail->isSMTP();
        $mail->Host      = 'smtp.gmail.com';
        $mail->SMTPAuth  = true;
        $mail->Username  = 'dinifarizahyazid@gmail.com'; 
        $mail->Password  = 'twwd gmng mjww ofdt'; // Remember security advice!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port      = 587; 

        // --- Recipients and Addressing ---
        $mail->setFrom('dinifarizahyazid@gmail.com', 'Website Application Form');
        $mail->addAddress('dinifarizahyazid@gmail.com');
        $mail->addReplyTo($applicant_email, $name);
        
        // --- File handling ---
        $has_file = false;
        if (isset($_FILES['inputFile']) && $_FILES['inputFile']['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['inputFile']['tmp_name'];
            $file_name = basename($_FILES['inputFile']['name']);
            $mail->addAttachment($tmp_name, $file_name);
            $has_file = true;
        }

        // --- EMAIL CONTENT (This was the missing part causing the "Message body empty" error) ---
        $mail->isHTML(true);
        $mail->Subject = "NEW JOB APPLICATION: {$name}";
        
        // Build the HTML body
        $body_html = "
            <h2>New Job Application Received</h2>
            <table border='1' cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td style='font-weight: bold; background-color: #f7f7f7;'>Name:</td>
                    <td>{$name}</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; background-color: #f7f7f7;'>Email:</td>
                    <td>{$applicant_email}</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; background-color: #f7f7f7;'>Phone:</td>
                    <td>{$phone}</td>
                </tr>
            </table>
            
            <h3 style='margin-top: 20px;'>Cover Letter:</h3>
            <p style='white-space: pre-wrap; padding: 10px; border: 1px solid #ccc; background-color: #fff;'>" . nl2br($cover_letter) . "</p>
            
            <p style='font-style: italic; color: " . ($has_file ? 'green' : 'red') . ";'>
                " . ($has_file ? 'Resume attached successfully.' : 'WARNING: Resume file was NOT attached.') . "
            </p>
        ";

        // *** CRITICAL STEP: Set the body properties ***
        $mail->Body = $body_html;
        $mail->AltBody = "Name: {$name}\nEmail: {$applicant_email}\nPhone: {$phone}\n\nCover Letter:\n{$cover_letter}\n\nResume: " . ($has_file ? 'Attached' : 'Not Attached');
        // --------------------------------------------------------------------------------------
        
        $mail->send();
        
        // *** SUCCESS: ECHO A MESSAGE ***
        echo "<span style='color:green;'>Application submitted successfully!</span>";
        exit;
        
    } catch (Exception $e) {
        // *** FAILURE: ECHO A DETAILED ERROR MESSAGE ***
        echo "<span style='color:red;'>Submission Failed. Mailer Error: {$mail->ErrorInfo}</span>"; 
        exit;
    }
} else {
    // Not a POST request
    echo "Invalid request method.";
    exit;
}
?>