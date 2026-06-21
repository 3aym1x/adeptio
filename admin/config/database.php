<?php
/**
 * ADEPTIO – Admin DB config shim.
 *
 * The real connection now lives in /api/db.php so the public API and the
 * admin panel share one set of credentials. This file is kept only so the
 * existing admin pages (which require __DIR__ . '/../config/database.php')
 * keep working unchanged.
 */

require_once __DIR__ . '/../../api/db.php';
