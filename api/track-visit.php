<?php
/**
 * ADEPTIO – Page-visit tracker.
 *
 * Called on page load by every static page. Assigns each visitor a persistent
 * `visitor_sid` cookie and records one row in `page_visits` per (session, page)
 * combination, so reloading the same page in the same session isn't counted
 * twice. Same-origin only.
 */

require_once __DIR__ . '/db.php';

function no_content(): never
{
    http_response_code(204);
    exit;
}

// --- Resolve / assign the session id ------------------------------------
$cookieName = 'visitor_sid';
$sessionId  = $_COOKIE[$cookieName] ?? '';

// Basic sanity: must look like our 32-byte hex token, otherwise reissue.
if (!preg_match('/^[a-f0-9]{64}$/', $sessionId)) {
    $sessionId = bin2hex(random_bytes(32));
    setcookie($cookieName, $sessionId, [
        'expires'  => time() + 60 * 60 * 24 * 365, // 1 year
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

// --- Read the visited page / referrer -----------------------------------
$page     = substr($_POST['page']     ?? ($_SERVER['HTTP_REFERER'] ?? '/'), 0, 500);
$referrer = substr($_POST['referrer'] ?? '', 0, 500);

// --- Dedupe per (session, page) -----------------------------------------
try {
    $check = $pdo->prepare(
        'SELECT id FROM page_visits WHERE session_id = ? AND page_url = ? LIMIT 1'
    );
    $check->execute([$sessionId, $page]);

    if (!$check->fetch()) {
        $stmt = $pdo->prepare(
            'INSERT INTO page_visits (page_url, referrer, session_id) VALUES (?, ?, ?)'
        );
        $stmt->execute([$page, $referrer !== '' ? $referrer : null, $sessionId]);
    }
} catch (PDOException $e) {
    // Tracking must never break the page — fail silently.
}

no_content();
