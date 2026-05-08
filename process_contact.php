<?php
require_once 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    try {
        // 1. Persist contact message data to the database
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, mobile, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $mobile, $subject, $message]);

        // 2. Send Email Notification (Native PHP implementation without external libraries)
        $to = "pasinduvidushan258@gmail.com";
        $email_subject = "Portfolio Contact: " . $subject;
        
        // Essential headers to ensure HTML compatibility and improve deliverability
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . $name . " <" . $email . ">" . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";

        // Define the email body structure using HTML for styled content
        $email_body = "
        <html>
        <head>
            <title>New Portfolio Message</title>
        </head>
        <body style='font-family: Arial, sans-serif; color: #333;'>
            <div style='background: #f4f4f4; padding: 20px; border-radius: 10px;'>
                <h2 style='color: #6366f1;'>New Contact Message 🚀</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Mobile:</strong> {$mobile}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <hr style='border: none; border-top: 1px solid #ddd;'/>
                <p><strong>Message:</strong><br/>" . nl2br(htmlspecialchars($message)) . "</p>
            </div>
        </body>
        </html>
        ";

        // Dispatch the email via standard PHP mail() function
        @mail($to, $email_subject, $email_body, $headers);

        // 3. Logic for WhatsApp redirection and message formatting
        $my_whatsapp_number = "94766437197"; 
        $wa_text = "Hi Pasindu! I contacted you via your portfolio. 🚀\n\n"
                 . "*Name:* $name\n"
                 . "*Email:* $email\n"
                 . "*Mobile:* $mobile\n"
                 . "*Subject:* $subject\n"
                 . "*Message:* $message";
        
        $wa_url = "https://wa.me/" . $my_whatsapp_number . "?text=" . urlencode($wa_text);

        echo "<script>
                alert('Message processing complete! Redirecting to WhatsApp...');
                window.location.href = '$wa_url';
              </script>";

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>