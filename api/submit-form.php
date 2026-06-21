<?php
/**
 * ADEPTIO – Contact form endpoint.
 *
 * Accepts a POST from the static site's contact forms, validates the input,
 * stores it in `submissions`, and replies with JSON. Same-origin only, so no
 * CORS headers are needed.
 */

header('Content-Type: application/json; charset=utf-8');

function respond(int $status, array $payload): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'Méthode non autorisée.']);
}

require_once __DIR__ . '/db.php';

// --- Collect & trim -----------------------------------------------------
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$phone   = trim($_POST['phone']   ?? '');
$message = trim($_POST['message'] ?? '');
$source  = trim($_POST['source_page'] ?? '');

// Extra fields used to build a `demandes` row (workflow lead).
$formType    = ($_POST['form_type'] ?? '') === 'partenaire' ? 'partenaire' : 'etudiant';
$destination = trim($_POST['destination'] ?? '');
$niveau      = trim($_POST['niveau'] ?? '');
$organization = trim($_POST['organization'] ?? '');
$partnerType  = trim($_POST['partnerType'] ?? '');

if ($formType === 'partenaire') {
    $sujet = trim(
        ($organization !== '' ? "Etablissement: $organization" : '') .
        ($partnerType  !== '' ? " — Besoin: $partnerType"       : '')
    );
} else {
    $sujet = trim(
        ($destination !== '' ? "Destination: $destination" : '') .
        ($niveau      !== '' ? " — Niveau: $niveau"        : '')
    );
}
$sujet = $sujet !== '' ? mb_substr($sujet, 0, 255) : null;

// --- Validate -----------------------------------------------------------
$errors = [];

if ($name === '' || mb_strlen($name) > 150) {
    $errors[] = 'Le nom est requis.';
}

if ($message === '') {
    $errors[] = 'Le message est requis.';
}

// Email is required so the admin can reply by email from the panel.
if ($email === '') {
    $errors[] = "L'adresse email est requise.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'adresse email n'est pas valide.";
}

if ($errors) {
    respond(422, ['ok' => false, 'error' => implode(' ', $errors)]);
}

// --- Insert -------------------------------------------------------------
// Written to two tables in one transaction:
//   submissions -> raw capture (Stats Site)
//   demandes    -> manageable lead in the existing admin workflow
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'INSERT INTO submissions (name, email, phone, message, source_page)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        mb_substr($name, 0, 150),
        $email !== '' ? mb_substr($email, 0, 150) : null,
        $phone !== '' ? mb_substr($phone, 0, 40)  : null,
        $message,
        $source !== '' ? mb_substr($source, 0, 255) : null,
    ]);

    $demande = $pdo->prepare(
        'INSERT INTO demandes (type_demande, nom, email, telephone, sujet, message, statut)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $demande->execute([
        $formType,
        mb_substr($name, 0, 100),
        $email !== '' ? mb_substr($email, 0, 100) : null,
        $phone !== '' ? mb_substr($phone, 0, 20)  : null,
        $sujet,
        $message,
        'en_attente',
    ]);

    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    respond(500, ['ok' => false, 'error' => "Une erreur est survenue. Réessayez plus tard."]);
}

respond(201, ['ok' => true, 'message' => 'Message envoyé ! Nous vous recontacterons très bientôt.']);
