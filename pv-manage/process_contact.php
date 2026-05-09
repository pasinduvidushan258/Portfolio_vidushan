<?php
require_once 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // 1. Save to Database (For Admin Panel)
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);

    // 2. Send Email Notification
    $to = "pasinduvidushan258@gmail.com";
    $email_subject = "New Portfolio Message: $subject";
    $headers = "From: " . $email;
    $email_body = "You have received a new message.\n\n".
                  "Name: $name\n".
                  "Email: $email\n".
                  "Message:\n$message";
    
    mail($to, $email_subject, $email_body, $headers);

    // 3. WhatsApp Notification (Using CallMeBot - Free Service)
    // මේක කරගන්න ඔයා CallMeBot එකෙන් API Key එකක් ගන්න ඕනේ (විනාඩියක වැඩක්)
    $phone = "94766437197"; 
    $apikey = "YOUR_CALLMEBOT_API_KEY"; // CallMeBot එකෙන් දෙන කී එක
    $wa_message = urlencode("🚀 *New Message on Portfolio!*\n\n*From:* $name\n*Subject:* $subject\n*Message:* $message");
    
    // WhatsApp එකට මැසේජ් එක යවනවා
    file_get_contents("https://api.callmebot.com/whatsapp.php?phone=$phone&text=$wa_message&apikey=$apikey");

    echo "<script>alert('Message sent successfully!'); window.location.href='index.php';</script>";
}
?><?php
require_once 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // 1. Persist contact data to the database for administrative review
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);

    // 2. Dispatch email notification to the site administrator
    $to = "pasinduvidushan258@gmail.com";
    $email_subject = "New Portfolio Message: $subject";
    $headers = "From: " . $email;
    $email_body = "You have received a new message.\n\n".
                  "Name: $name\n".
                  "Email: $email\n".
                  "Message:\n$message";
    
    mail($to, $email_subject, $email_body, $headers);

    // 3. Trigger WhatsApp notification via CallMeBot third-party integration
    // Note: Integration requires a valid API Key obtained from the CallMeBot service.
    $phone = "94766437197"; 
    $apikey = "YOUR_CALLMEBOT_API_KEY"; // Authentication token provided by CallMeBot
    $wa_message = urlencode("🚀 *New Message on Portfolio!*\n\n*From:* $name\n*Subject:* $subject\n*Message:* $message");
    
    // Execute synchronous GET request to the WhatsApp API endpoint
    file_get_contents("https://api.callmebot.com/whatsapp.php?phone=$phone&text=$wa_message&apikey=$apikey");

    echo "<script>alert('Message sent successfully!'); window.location.href='index.php';</script>";
}
?>