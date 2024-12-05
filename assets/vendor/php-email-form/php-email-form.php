<?php
class PHP_Email_Form {

    // Adresse email du destinataire
    public $to;
    // Nom de l'expéditeur
    public $from_name;
    // Email de l'expéditeur
    public $from_email;
    // Sujet de l'email
    public $subject;
    // Utiliser AJAX ou non
    public $ajax = false;
    // Options SMTP (si nécessaires)
    public $smtp = null;
    // Contenu du message
    private $messages = [];

    /**
     * Ajouter un message au contenu de l'email.
     */
    public function add_message($message, $label = '', $priority = 0) {
        $this->messages[] = [
            'message' => $message,
            'label' => $label,
            'priority' => $priority
        ];
    }

    /**
     * Construire le contenu de l'email.
     */
    private function build_message() {
        $email_content = "";
        foreach ($this->messages as $msg) {
            $email_content .= (!empty($msg['label']) ? $msg['label'] . ": " : "") . htmlspecialchars($msg['message']) . "\n";
        }
        return $email_content;
    }

    /**
     * Envoyer l'email en utilisant mail() ou SMTP.
     */
    public function send() {
        // Vérification des champs essentiels
        if (empty($this->to) || empty($this->from_email) || empty($this->subject)) {
            return "Erreur : Informations d'email manquantes.";
        }

        // Construire le message
        $message = $this->build_message();
        $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";

        // Si SMTP est configuré
        if ($this->smtp) {
            return $this->send_via_smtp($message);
        }

        // Utiliser mail() si SMTP n'est pas configuré
        if (mail($this->to, $this->subject, $message, $headers)) {
            return $this->ajax ? json_encode(["success" => true, "message" => "Email envoyé avec succès."]) : "Email envoyé avec succès.";
        } else {
            return $this->ajax ? json_encode(["success" => false, "message" => "Erreur lors de l'envoi de l'email."]) : "Erreur lors de l'envoi de l'email.";
        }
    }

    /**
     * Envoyer l'email via SMTP.
     */
    private function send_via_smtp($message) {
        // Inclure PHPMailer (si disponible)
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return "Erreur : La bibliothèque PHPMailer est requise pour SMTP.";
        }

        // Configuration de PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->smtp['username'];
        $mail->Password = $this->smtp['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $this->smtp['port'];

        // Configurer l'email
        $mail->setFrom($this->from_email, $this->from_name);
        $mail->addAddress($this->to);
        $mail->Subject = $this->subject;
        $mail->Body = $message;

        // Envoyer l'email
        if ($mail->send()) {
            return $this->ajax ? json_encode(["success" => true, "message" => "Email envoyé avec succès via SMTP."]) : "Email envoyé avec succès via SMTP.";
        } else {
            return $this->ajax ? json_encode(["success" => false, "message" => "Erreur SMTP : " . $mail->ErrorInfo]) : "Erreur SMTP : " . $mail->ErrorInfo;
        }
    }
}