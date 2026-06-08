<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Eingaben bereinigen
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $bike = strip_tags(trim($_POST["bike"]));
    $date = strip_tags(trim($_POST["date"]));
    $message = strip_tags(trim($_POST["message"]));

    // --- KONFIGURATION ---
    $my_email = "jan.wersching75@gmail.com"; // DEINE E-Mail hier eintragen
    $site_name = "Bike Reloaded";
    // ---------------------

    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Bitte gültige Daten eingeben."]);
        exit;
    }

    // 1. E-Mail an DICH
    $subject_owner = "Neue Anfrage von $name - $site_name";
    $body_owner = "Du hast eine neue Terminanfrage erhalten:\n\n" .
                  "Name: $name\n" .
                  "E-Mail: $email\n" .
                  "Fahrrad: $bike\n" .
                  "Terminwunsch: $date\n\n" .
                  "Nachricht:\n$message";
    
    $headers_owner = "From: $site_name <noreply@deinedomain.de>\r\n";
    $headers_owner .= "Reply-To: $email";

    $mail_to_owner = mail($my_email, $subject_owner, $body_owner, $headers_owner);

    // 2. Bestätigungs-E-Mail an den KUNDEN
    $subject_customer = "Bestätigung: Deine Anfrage bei $site_name";
    $body_customer = "Hallo $name,\n\nvielen Dank für deine Nachricht! Wir haben deine Anfrage für dein $bike erhalten.\n\n" .
                     "Wir prüfen deinen Terminwunsch ($date) und melden uns schnellstmöglich bei dir.\n\n" .
                     "Bunte Grüße,\nDein Team von Bike Reloaded\nHornstein, Burgenland";
    
    $headers_customer = "From: $site_name <$my_email>\r\n";

    $mail_to_customer = mail($email, $subject_customer, $body_customer, $headers_customer);

    if ($mail_to_owner && $mail_to_customer) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Anfrage erfolgreich gesendet!"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Server-Fehler beim Mailversand."]);
    }

} else {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Methode nicht erlaubt."]);
}
?>