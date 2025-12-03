<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

header('Content-Type: text/plain'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['cfName']));
    $email = htmlspecialchars(trim($_POST['cfEmail']));
    $phone = htmlspecialchars(trim($_POST['cfPhone']));
    $subjectOption = htmlspecialchars(trim($_POST['cfSubject']));
    $message = htmlspecialchars(trim($_POST['cfMessage']));

    if ($subjectOption == "0") {
        echo "Error: Please select a valid subject option.";
        exit;
    }

    $subjects = [
        "1" => "HRMS Solutions",
        "2" => "Implementation Support",
        "3" => "Technical Assistance",
        "4" => "Training Inquiry",
        "5" => "General Consultation"
    ];
    if (isset($subjects[$subjectOption])) {
        $subjectText = $subjects[$subjectOption];
    } else {
        $subjectText = "General Inquiry";
    }

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'echo'; 

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dini2001@graduate.utm.my'; 
        $mail->Password   = 'ylfyarmndmaktuqb'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; 

        // Recipients
        $mail->setFrom('dini2001@graduate.utm.my', 'Website Contact');
        $mail->addAddress('dini2001@graduate.utm.my'); 
        $mail->addReplyTo($email, $name);

        // Message Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission: $subjectText";
        $mail->Body    = "
            <strong>Name:</strong> $name<br>
            <strong>Email:</strong> $email<br>
            <strong>Phone:</strong> $phone<br>
            <strong>Subject:</strong> $subjectText<br><br>
            <strong>Message:</strong><br>" . nl2br($message);

        $mail->send();
        // Success message for AJAX
        echo "<span style='color:green;'>Thank you, **$name**! Your message has been sent successfully.</span>";
    } catch (Exception $e) {
        // Detailed error message for AJAX
        echo "<span style='color:red;'>Something went wrong. Please try again. Mailer Error: {$mail->ErrorInfo}</span>";
    }
} else {
    echo "<span style='color:red;'>Invalid request.</span>";
}
?>