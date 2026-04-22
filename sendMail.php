<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // --- Sanitize & Validate ---
    $name    = htmlspecialchars(strip_tags($name));
    $subject = htmlspecialchars(strip_tags($subject));
    $message = htmlspecialchars(strip_tags($message));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.html?contact=error&reason=invalid_email");
        exit();
    }

    // Prevent header injection
    function preventHeaderInjection($str)
    {
        return preg_replace("/[\r\n]+/", "", $str);
    }

    $name    = preventHeaderInjection($name);
    $email   = preventHeaderInjection($email);
    $subject = preventHeaderInjection($subject);

    // --- Send Mail ---
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nishmalamichhane2005@gmail.com';
        $mail->Password   = 'vwzq pnja benp jjll';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('nishmalamichhane2005@gmail.com', 'Portfolio Contact');
        $mail->addAddress('nishmalamichhane2005@gmail.com');
        $mail->addReplyTo($email, $name);

      // ... (Keep your SMTP settings above) ...

        $mail->isHTML(true); // Enable HTML
        $mail->Subject = " New Portfolio Inquiry: $subject";

        // HTML Email Template
        $mail->Body = "
        <div style='background-color: #06060f; padding: 40px; font-family: sans-serif; color: #eeeef8;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #0f0f24; border: 1px solid #e91e63; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(233, 30, 99, 0.2);'>
                
                <h2 style='color: #e91e63; border-bottom: 2px solid #e91e63; padding-bottom: 10px; margin-top: 0;'>New Contact Message</h2>
                
                <p style='font-size: 14px; color: #8e8eb5;'>You have a new lead from your portfolio website.</p>
                
                <div style='margin: 25px 0;'>
                    <div style='margin-bottom: 15px;'>
                        <strong style='color: #e91e63; display: block; font-size: 12px; text-transform: uppercase;'>Sender Name</strong>
                        <span style='font-size: 18px;'>".htmlspecialchars($name)."</span>
                    </div>
                    
                    <div style='margin-bottom: 15px;'>
                        <strong style='color: #e91e63; display: block; font-size: 12px; text-transform: uppercase;'>Email Address</strong>
                        <a href='mailto:".htmlspecialchars($email)."' style='color: #ff6b6b; text-decoration: none; font-size: 16px;'>".htmlspecialchars($email)."</a>
                    </div>
                    
                    <div style='margin-bottom: 15px;'>
                        <strong style='color: #e91e63; display: block; font-size: 12px; text-transform: uppercase;'>Subject</strong>
                        <span style='font-size: 16px;'>".htmlspecialchars($subject)."</span>
                    </div>
                    
                    <div style='background: #06060f; padding: 20px; border-radius: 12px; border-left: 4px solid #e91e63;'>
                        <strong style='color: #e91e63; display: block; font-size: 12px; text-transform: uppercase; margin-bottom: 10px;'>Message Content</strong>
                        <p style='line-height: 1.6; margin: 0;'>".nl2br(htmlspecialchars($message))."</p>
                    </div>
                </div>
                
                <div style='text-align: center; margin-top: 30px; border-top: 1px solid rgba(233, 30, 99, 0.2); padding-top: 20px;'>
                    <p style='font-size: 12px; color: #8e8eb5;'>Sent from <strong>Nishma.portfolio</strong> System</p>
                </div>
            </div>
        </div>
        ";

        // Plain text version for non-HTML email clients
        $mail->AltBody = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";

        $mail->send();
        $mail->send();

        // Return JSON instead of a header redirect
        echo json_encode(["status" => "success"]);
        exit();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $mail->ErrorInfo]);
        exit();
    }
}
