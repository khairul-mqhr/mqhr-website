<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//  Correct file paths
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['cfName']));
    $email = htmlspecialchars(trim($_POST['cfEmail']));
    $phone = htmlspecialchars(trim($_POST['cfPhone']));
    $subjectOption = htmlspecialchars(trim($_POST['cfSubject']));
    $message = htmlspecialchars(trim($_POST['cfMessage']));

    $subjects = [
        "1" => "HRMS Solutions",
        "2" => "Implementation Support",
        "3" => "Technical Assistance",
        "4" => "Training Inquiry",
        "5" => "General Consultation"
    ];
    $subjectText = $subjects[$subjectOption] ?? "General Inquiry";

    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dinifarizahyazid@gmail.com';
        $mail->Password   = 'ywauxxfstdwwfxmj'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('dinifarizahyazid@gmail.com', 'Website Contact');
        $mail->addAddress('dinifarizahyazid@gmail.com');
        $mail->addReplyTo($email, $name);

        // Message
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission: $subjectText";
        $mail->Body    = "
            <strong>Name:</strong> $name<br>
            <strong>Email:</strong> $email<br>
            <strong>Phone:</strong> $phone<br>
            <strong>Subject:</strong> $subjectText<br><br>
            <strong>Message:</strong><br>" . nl2br($message);

        $mail->send();
        echo "<div style='color:green;'>Thank you, $name! Your message has been sent successfully.</div>";
    } catch (Exception $e) {
        echo "<div style='color:red;'>Mailer Error: {$mail->ErrorInfo}</div>";
    }
} else {
    echo "<div style='color:red;'>Invalid request.</div>";
}
?>
