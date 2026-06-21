<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_layout.php';

require_admin();

// --- Totals -------------------------------------------------------------
$totalVisits      = (int) $pdo->query('SELECT COUNT(*) FROM page_visits')->fetchColumn();
$uniqueVisitors   = (int) $pdo->query('SELECT COUNT(DISTINCT session_id) FROM page_visits')->fetchColumn();
$totalSubmissions = (int) $pdo->query('SELECT COUNT(*) FROM submissions')->fetchColumn();
$visitsToday      = (int) $pdo->query('SELECT COUNT(*) FROM page_visits WHERE DATE(visited_at) = CURDATE()')->fetchColumn();

// --- Visits per day, last 30 days (zero-filled) -------------------------
$rows = $pdo->query("
    SELECT DATE(visited_at) AS d, COUNT(*) AS total
    FROM page_visits
    WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
    GROUP BY DATE(visited_at)
")->fetchAll();

$byDay = [];
foreach ($rows as $r) {
    $byDay[$r['d']] = (int) $r['total'];
}

$days = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i day"));
    $days[$date] = $byDay[$date] ?? 0;
}
$maxDay = max(1, max($days));

// --- Top pages (last 30 days) -------------------------------------------
$topPages = $pdo->query("
    SELECT page_url, COUNT(*) AS total
    FROM page_visits
    WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
    GROUP BY page_url
    ORDER BY total DESC
    LIMIT 10
")->fetchAll();

// --- Recent submissions -------------------------------------------------
$recentSubmissions = $pdo->query("
    SELECT id, name, email, phone, message, source_page, submitted_at
    FROM submissions
    ORDER BY submitted_at DESC
    LIMIT 15
")->fetchAll();

render_admin_header($pdo, 'Stats Site', 'stats');
?>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card-stat">
            <div class="stat-label">Visites totales</div>
            <div class="stat-value"><?= $totalVisits ?></div>
            <i class="fas fa-eye stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-stat">
            <div class="stat-label">Visiteurs uniques</div>
            <div class="stat-value"><?= $uniqueVisitors ?></div>
            <i class="fas fa-users stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-stat">
            <div class="stat-label">Visites aujourd'hui</div>
            <div class="stat-value"><?= $visitsToday ?></div>
            <i class="fas fa-calendar-day stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-stat">
            <div class="stat-label">Soumissions formulaire</div>
            <div class="stat-value"><?= $totalSubmissions ?></div>
            <i class="fas fa-inbox stat-icon"></i>
        </div>
    </div>
</div>

<!-- Visits per day chart -->
<div class="panel mb-4">
    <div class="panel-header"><h2>Visites des 30 derniers jours</h2></div>
    <div class="panel-body">
        <div style="display:flex;align-items:flex-end;gap:3px;height:180px;">
            <?php foreach ($days as $date => $count): ?>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;"
                     title="<?= e($date) ?> — <?= $count ?> visite(s)">
                    <div style="width:100%;background:var(--cyan,#00e5ff);border-radius:3px 3px 0 0;
                                height:<?= (int) round($count / $maxDay * 150) ?>px;min-height:<?= $count > 0 ? 3 : 0 ?>px;"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="d-flex justify-content-between text-muted small mt-2">
            <span><?= e(date('d/m', strtotime(array_key_first($days)))) ?></span>
            <span>Pic : <?= $maxDay ?> / jour</span>
            <span><?= e(date('d/m', strtotime(array_key_last($days)))) ?></span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent submissions -->
    <section class="col-xl-8">
        <div class="panel">
            <div class="panel-header"><h2>Dernieres soumissions</h2></div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Contact</th>
                            <th>Message</th>
                            <th>Page</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentSubmissions as $s): ?>
                        <tr>
                            <td><span style="color:var(--muted)">#<?= (int) $s['id'] ?></span></td>
                            <td class="fw-semibold"><?= e($s['name']) ?></td>
                            <td class="small">
                                <?php if ($s['email']): ?><div><?= e($s['email']) ?></div><?php endif; ?>
                                <?php if ($s['phone']): ?><div class="text-muted"><?= e($s['phone']) ?></div><?php endif; ?>
                            </td>
                            <td class="small" style="max-width:280px"><?= e(mb_strimwidth((string) $s['message'], 0, 90, '…')) ?></td>
                            <td class="text-muted small"><?= e($s['source_page'] ?? '-') ?></td>
                            <td class="text-muted small"><?= e(format_datetime($s['submitted_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recentSubmissions): ?>
                        <tr><td colspan="6" class="empty-state"><i class="fas fa-inbox"></i>Aucune soumission pour le moment.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Top pages -->
    <aside class="col-xl-4">
        <div class="panel">
            <div class="panel-header"><h2>Pages les plus visitees (30 j)</h2></div>
            <div class="panel-body">
                <?php foreach ($topPages as $p): ?>
                    <div class="status-row">
                        <span class="status-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px">
                            <?= e($p['page_url'] ?: '/') ?>
                        </span>
                        <span class="badge text-bg-primary"><?= (int) $p['total'] ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (!$topPages): ?>
                    <p class="text-muted mb-0" style="font-size:.85rem">Aucune donnee disponible.</p>
                <?php endif; ?>
            </div>
        </div>
    </aside>
</div>
<?php render_admin_footer(); ?>
