<?php
/**
 * InterGlobe.cloud — Single-file PHP application
 * Shipping & Tracking Platform (EN/FR)
 *
 * Deployment: Upload this single index.php to your web root.
 * Optional: place the included .htaccess alongside for pretty URLs.
 * Without .htaccess, use ?r=/book, ?r=/track, etc.
 * Requires: PHP 7.4+  |  Writes to ./data/shipments.json (auto-created)
 */

session_start();
date_default_timezone_set('UTC');

// ---------- Config ----------
const APP_NAME = 'InterGlobe.cloud';
const DATA_DIR = __DIR__ . '/data';
const DATA_FILE = DATA_DIR . '/shipments.json';

if (!is_dir(DATA_DIR)) @mkdir(DATA_DIR, 0775, true);
if (!file_exists(DATA_FILE)) {
    file_put_contents(DATA_FILE, json_encode([
        'IG-2026-004821' => [
            'id' => 'IG-2026-004821', 'from' => 'Shanghai, CN', 'to' => 'Rotterdam, NL',
            'service' => 'Sea Freight', 'status' => 'In Transit', 'created' => '2026-06-18',
            'eta' => '2026-07-11',
            'events' => [
                ['t' => '2026-06-18 09:12', 'city' => 'Shanghai Port', 'label' => 'Container loaded on vessel MV Aurora'],
                ['t' => '2026-06-22 14:40', 'city' => 'Singapore', 'label' => 'Transit stop cleared customs'],
                ['t' => '2026-06-30 03:10', 'city' => 'Suez Canal', 'label' => 'Passage confirmed'],
            ],
        ],
        'IG-2026-004822' => [
            'id' => 'IG-2026-004822', 'from' => 'Frankfurt, DE', 'to' => 'Lagos, NG',
            'service' => 'Air Express', 'status' => 'Out for Delivery', 'created' => '2026-07-01',
            'eta' => '2026-07-03',
            'events' => [
                ['t' => '2026-07-01 06:00', 'city' => 'Frankfurt FRA', 'label' => 'Cargo loaded on flight IG-441'],
                ['t' => '2026-07-02 18:20', 'city' => 'Lagos LOS', 'label' => 'Arrived at destination hub'],
                ['t' => '2026-07-03 07:55', 'city' => 'Lagos', 'label' => 'Out for delivery with courier'],
            ],
        ],
    ], JSON_PRETTY_PRINT));
}

function load_shipments(): array { $j = @file_get_contents(DATA_FILE); return $j ? (json_decode($j, true) ?: []) : []; }
function save_shipments(array $a): void { file_put_contents(DATA_FILE, json_encode($a, JSON_PRETTY_PRINT)); }

// ---------- i18n ----------
$LANG = $_SESSION['lang'] ?? 'en';
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'fr'], true)) {
    $_SESSION['lang'] = $_GET['lang']; $LANG = $_SESSION['lang'];
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit;
}
$DICT = [
    'en' => [
        'nav.home' => 'Home', 'nav.book' => 'Book Shipment', 'nav.track' => 'Track', 'nav.about' => 'About',
        'nav.reviews' => 'Reviews', 'nav.contact' => 'Contact', 'nav.terms' => 'Terms',
        'hero.eyebrow' => 'Global Logistics · Air · Sea · Ground',
        'hero.title' => 'Move the world with precision.',
        'hero.sub' => 'InterGlobe.cloud is the modern freight and courier network engineered for speed, transparency, and unmatched reliability across 220+ countries.',
        'hero.cta1' => 'Book a Shipment', 'hero.cta2' => 'Track a Parcel',
        'track.title' => 'Track your shipment', 'track.ph' => 'Enter tracking number (e.g. IG-2026-004821)',
        'track.btn' => 'Track', 'track.notfound' => 'No shipment found for that ID.',
        'book.title' => 'Create a Waybill', 'book.sender' => 'Sender', 'book.recipient' => 'Recipient',
        'book.parcel' => 'Parcel', 'book.service' => 'Service', 'book.review' => 'Review',
        'book.name' => 'Full name', 'book.email' => 'Email', 'book.phone' => 'Phone',
        'book.address' => 'Address', 'book.city' => 'City', 'book.country' => 'Country',
        'book.weight' => 'Weight (kg)', 'book.desc' => 'Description', 'book.submit' => 'Generate Waybill',
        'services.title' => 'Every mode. One network.',
        'services.air' => 'Air Express', 'services.airD' => 'Next-flight-out couriering with priority tarmac handling.',
        'services.sea' => 'Sea Freight', 'services.seaD' => 'FCL & LCL container solutions across 400+ ports.',
        'services.road' => 'Ground', 'services.roadD' => 'Cross-border trucking with live geo-fenced telemetry.',
        'services.ware' => 'Warehousing', 'services.wareD' => 'Bonded fulfilment centres in 38 strategic hubs.',
        'about.title' => 'Built for the next century of trade.',
        'about.body' => 'InterGlobe.cloud unifies fleet operations, customs intelligence, and last-mile courier networks into a single API-driven platform. We move freight for Fortune 500 manufacturers, e-commerce leaders, and independent senders — with the same obsessive precision.',
        'reviews.title' => 'Trusted by shippers worldwide',
        'contact.title' => 'Talk to logistics engineers',
        'terms.title' => 'Terms & Policy',
        'voice.line' => 'Welcome to InterGlobe dot cloud. Your shipments move with certainty across two hundred and twenty countries, backed by real time tracking and guaranteed reliability.',
        'footer.rights' => 'All rights reserved.',
    ],
    'fr' => [
        'nav.home' => 'Accueil', 'nav.book' => 'Expédier', 'nav.track' => 'Suivi', 'nav.about' => 'À propos',
        'nav.reviews' => 'Avis', 'nav.contact' => 'Contact', 'nav.terms' => 'Conditions',
        'hero.eyebrow' => 'Logistique mondiale · Air · Mer · Terre',
        'hero.title' => 'Déplacez le monde avec précision.',
        'hero.sub' => "InterGlobe.cloud est le réseau moderne de fret et de messagerie conçu pour la rapidité, la transparence et une fiabilité inégalée dans plus de 220 pays.",
        'hero.cta1' => 'Créer une expédition', 'hero.cta2' => 'Suivre un colis',
        'track.title' => 'Suivez votre envoi', 'track.ph' => 'Entrez le numéro (ex. IG-2026-004821)',
        'track.btn' => 'Suivre', 'track.notfound' => 'Aucun envoi trouvé pour cet identifiant.',
        'book.title' => 'Créer un bordereau', 'book.sender' => 'Expéditeur', 'book.recipient' => 'Destinataire',
        'book.parcel' => 'Colis', 'book.service' => 'Service', 'book.review' => 'Vérifier',
        'book.name' => 'Nom complet', 'book.email' => 'E-mail', 'book.phone' => 'Téléphone',
        'book.address' => 'Adresse', 'book.city' => 'Ville', 'book.country' => 'Pays',
        'book.weight' => 'Poids (kg)', 'book.desc' => 'Description', 'book.submit' => 'Générer le bordereau',
        'services.title' => 'Tous les modes. Un seul réseau.',
        'services.air' => 'Express aérien', 'services.airD' => 'Messagerie prochain vol avec priorité tarmac.',
        'services.sea' => 'Fret maritime', 'services.seaD' => 'Solutions FCL & LCL dans plus de 400 ports.',
        'services.road' => 'Terrestre', 'services.roadD' => 'Camionnage transfrontalier avec télémétrie géolocalisée.',
        'services.ware' => 'Entreposage', 'services.wareD' => 'Centres sous douane dans 38 hubs stratégiques.',
        'about.title' => 'Conçu pour le prochain siècle du commerce.',
        'about.body' => "InterGlobe.cloud unifie les opérations de flotte, l'intelligence douanière et les réseaux de livraison en une seule plateforme API. Nous transportons pour les industriels du Fortune 500, les leaders du e-commerce et les expéditeurs indépendants — avec la même précision obsessionnelle.",
        'reviews.title' => 'La confiance des expéditeurs du monde entier',
        'contact.title' => 'Parlez à nos ingénieurs logistiques',
        'terms.title' => 'Conditions & Politique',
        'voice.line' => "Bienvenue sur InterGlobe point cloud. Vos envois se déplacent avec certitude dans plus de deux cent vingt pays, avec un suivi en temps réel et une fiabilité garantie.",
        'footer.rights' => 'Tous droits réservés.',
    ],
];
function t(string $k): string { global $DICT, $LANG; return $DICT[$LANG][$k] ?? $DICT['en'][$k] ?? $k; }
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// ---------- Router ----------
$route = $_GET['r'] ?? ($_SERVER['PATH_INFO'] ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if ($scriptDir && strpos($route, $scriptDir) === 0) $route = substr($route, strlen($scriptDir));
$route = '/' . trim(preg_replace('#/index\.php$#', '', $route ?? ''), '/');

// ---------- Handle POST: booking ----------
$flash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $route === '/book') {
    $id = 'IG-' . date('Y') . '-' . str_pad((string)random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    $data = load_shipments();
    $data[$id] = [
        'id' => $id,
        'from' => trim(($_POST['sender_city'] ?? '') . ', ' . ($_POST['sender_country'] ?? '')),
        'to'   => trim(($_POST['recipient_city'] ?? '') . ', ' . ($_POST['recipient_country'] ?? '')),
        'service' => $_POST['service'] ?? 'Air Express',
        'status' => 'Booked', 'created' => date('Y-m-d'), 'eta' => date('Y-m-d', strtotime('+5 days')),
        'sender' => ['name' => $_POST['sender_name'] ?? '', 'email' => $_POST['sender_email'] ?? '', 'phone' => $_POST['sender_phone'] ?? ''],
        'recipient' => ['name' => $_POST['recipient_name'] ?? '', 'email' => $_POST['recipient_email'] ?? '', 'phone' => $_POST['recipient_phone'] ?? ''],
        'parcel' => ['weight' => $_POST['weight'] ?? '', 'desc' => $_POST['description'] ?? ''],
        'events' => [['t' => date('Y-m-d H:i'), 'city' => $_POST['sender_city'] ?? '', 'label' => 'Waybill created — awaiting pickup']],
    ];
    save_shipments($data);
    header('Location: ?r=/track&id=' . urlencode($id)); exit;
}

// ---------- Special: sitemap & robots ----------
if ($route === '/robots.txt') { header('Content-Type: text/plain'); echo "User-agent: *\nAllow: /\nSitemap: " . (($_SERVER['REQUEST_SCHEME'] ?? 'https') . '://' . $_SERVER['HTTP_HOST']) . "/sitemap.xml\n"; exit; }
if ($route === '/sitemap.xml') {
    header('Content-Type: application/xml');
    $base = ($_SERVER['REQUEST_SCHEME'] ?? 'https') . '://' . $_SERVER['HTTP_HOST'];
    echo '<?xml version="1.0"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach (['/', '/book', '/about', '/reviews', '/contact', '/terms'] as $p) echo "<url><loc>{$base}{$p}</loc></url>";
    echo '</urlset>'; exit;
}

// ---------- Layout helpers ----------
function svg_logo(): string {
    return '<svg viewBox="0 0 40 40" width="34" height="34" aria-hidden="true">
      <defs><linearGradient id="lg" x1="0" x2="1" y1="0" y2="1"><stop offset="0" stop-color="#F5B301"/><stop offset="1" stop-color="#FF7A00"/></linearGradient></defs>
      <circle cx="20" cy="20" r="18" fill="#0A1B3D"/>
      <path d="M8 22 Q 20 6, 32 22" stroke="url(#lg)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
      <path d="M8 26 Q 20 12, 32 26" stroke="url(#lg)" stroke-width="1.5" fill="none" opacity=".55"/>
      <circle cx="30" cy="14" r="2.2" fill="#F5B301"/>
    </svg>';
}
function svg_hero(): string {
    return '<svg viewBox="0 0 800 500" class="hero-art" preserveAspectRatio="xMidYMid slice">
      <defs>
        <linearGradient id="sky" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#0A1B3D"/><stop offset="1" stop-color="#0F2E66"/></linearGradient>
        <radialGradient id="glow" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#F5B301" stop-opacity=".55"/><stop offset="1" stop-color="#F5B301" stop-opacity="0"/></radialGradient>
        <linearGradient id="amber" x1="0" x2="1"><stop offset="0" stop-color="#F5B301"/><stop offset="1" stop-color="#FF7A00"/></linearGradient>
      </defs>
      <rect width="800" height="500" fill="url(#sky)"/>
      <circle cx="620" cy="180" r="220" fill="url(#glow)"/>
      <!-- globe grid -->
      <g stroke="#3A55A0" stroke-opacity=".35" fill="none">
        <ellipse cx="400" cy="250" rx="220" ry="220"/>
        <ellipse cx="400" cy="250" rx="220" ry="90"/>
        <ellipse cx="400" cy="250" rx="220" ry="150"/>
        <ellipse cx="400" cy="250" rx="90" ry="220"/>
        <ellipse cx="400" cy="250" rx="150" ry="220"/>
        <line x1="180" y1="250" x2="620" y2="250"/>
        <line x1="400" y1="30" x2="400" y2="470"/>
      </g>
      <!-- route arcs -->
      <path d="M240 340 Q 400 60, 560 200" stroke="url(#amber)" stroke-width="3" fill="none" stroke-dasharray="6 6"/>
      <path d="M300 400 Q 500 200, 640 320" stroke="#F5B301" stroke-opacity=".6" stroke-width="2" fill="none" stroke-dasharray="4 8"/>
      <!-- nodes -->
      <g fill="#F5B301"><circle cx="240" cy="340" r="6"/><circle cx="560" cy="200" r="6"/><circle cx="300" cy="400" r="5"/><circle cx="640" cy="320" r="5"/></g>
      <!-- plane -->
      <g transform="translate(500,140) rotate(20)"><path d="M0 0 L40 -6 L60 0 L40 6 Z M20 -3 L20 -18 L28 -18 L36 -3 M20 3 L20 18 L28 18 L36 3" fill="#fff"/></g>
      <!-- ship -->
      <g transform="translate(130,380)"><rect width="120" height="18" fill="#F5B301"/><rect y="-20" width="30" height="20" fill="#fff"/><rect x="40" y="-14" width="18" height="14" fill="#fff" opacity=".8"/><rect x="65" y="-14" width="18" height="14" fill="#fff" opacity=".8"/></g>
    </svg>';
}
function svg_icon(string $k): string {
    $paths = [
      'air' => '<path d="M3 12l18-7-4 18-5-6-6 4-3-9z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
      'sea' => '<path d="M3 17l9-4 9 4M5 20h14M6 13V7l6-2 6 2v6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
      'road'=> '<rect x="3" y="8" width="12" height="8" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M15 11h4l2 3v2h-6z" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="7" cy="17" r="1.6" fill="currentColor"/><circle cx="17" cy="17" r="1.6" fill="currentColor"/>',
      'ware'=> '<path d="M3 10l9-6 9 6v10H3z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><rect x="8" y="13" width="8" height="7" fill="none" stroke="currentColor" stroke-width="1.6"/>',
      'user'=> '<circle cx="12" cy="8" r="4" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M4 20c1.5-4 5-6 8-6s6.5 2 8 6" fill="none" stroke="currentColor" stroke-width="1.6"/>',
      'box' => '<path d="M3 7l9-4 9 4-9 4-9-4zm0 0v10l9 4 9-4V7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
      'pin' => '<path d="M12 22s7-7 7-13a7 7 0 10-14 0c0 6 7 13 7 13z" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="9" r="2.5" fill="currentColor"/>',
    ];
    return '<svg viewBox="0 0 24 24" width="22" height="22">' . ($paths[$k] ?? '') . '</svg>';
}

// ---------- Render header ----------
function head_html(string $title, string $desc): void { ?>
<!DOCTYPE html><html lang="<?= e($GLOBALS['LANG']) ?>"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> — InterGlobe.cloud</title>
<meta name="description" content="<?= e($desc) ?>">
<meta property="og:title" content="<?= e($title) ?>"><meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:type" content="website"><meta name="twitter:card" content="summary_large_image">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
:root{--navy:#0A1B3D;--navy2:#0F2E66;--amber:#F5B301;--orange:#FF7A00;--ink:#0B1220;--muted:#5B6A85;--line:#E7ECF3;--bg:#F7F8FB;--white:#fff}
*{box-sizing:border-box}html,body{margin:0;padding:0}
body{font-family:'Inter',system-ui,sans-serif;color:var(--ink);background:var(--bg);-webkit-font-smoothing:antialiased}
h1,h2,h3,h4{font-family:'Outfit',sans-serif;letter-spacing:-.02em;margin:0}
.mono{font-family:'JetBrains Mono',monospace}
a{color:inherit;text-decoration:none}
.container{max-width:1200px;margin:0 auto;padding:0 24px}
/* header */
.top{position:sticky;top:0;z-index:50;background:rgba(10,27,61,.92);backdrop-filter:blur(14px);color:#fff;border-bottom:1px solid rgba(255,255,255,.08)}
.top .row{display:flex;align-items:center;justify-content:space-between;padding:14px 0}
.brand{display:flex;align-items:center;gap:10px;font-family:'Outfit';font-weight:800;font-size:20px}
.brand small{font-weight:500;color:var(--amber);font-size:11px;letter-spacing:.18em}
.nav{display:flex;gap:26px;align-items:center}
.nav a{font-size:14px;color:#D8DEEB;font-weight:500}
.nav a:hover{color:var(--amber)}
.pill{padding:8px 14px;border:1px solid rgba(255,255,255,.2);border-radius:999px;font-size:12px;color:#fff;background:transparent;cursor:pointer}
.pill.amber{background:linear-gradient(90deg,var(--amber),var(--orange));border:0;color:#111;font-weight:700}
.menu-toggle{display:none;background:transparent;border:0;color:#fff;font-size:22px}
@media(max-width:820px){.nav{display:none}.nav.open{display:flex;position:absolute;top:60px;right:16px;left:16px;background:#0A1B3D;padding:16px;border-radius:14px;flex-direction:column;gap:14px}.menu-toggle{display:block}}
/* hero */
.hero{position:relative;background:linear-gradient(180deg,#0A1B3D 0%,#0F2E66 100%);color:#fff;overflow:hidden}
.hero .container{display:grid;grid-template-columns:1.05fr .95fr;gap:40px;align-items:center;padding:70px 24px 110px}
.eyebrow{display:inline-flex;align-items:center;gap:8px;color:var(--amber);font-size:12px;letter-spacing:.22em;text-transform:uppercase;font-weight:600;margin-bottom:18px}
.eyebrow::before{content:'';width:28px;height:2px;background:var(--amber)}
.hero h1{font-size:clamp(36px,5vw,64px);line-height:1.02;font-weight:800}
.hero p.sub{max-width:520px;color:#C6D0E4;font-size:17px;margin:22px 0 30px;line-height:1.55}
.cta{display:flex;gap:12px;flex-wrap:wrap}
.btn{display:inline-flex;align-items:center;gap:8px;padding:14px 22px;border-radius:12px;font-weight:700;cursor:pointer;border:0;font-size:15px}
.btn-amber{background:linear-gradient(90deg,var(--amber),var(--orange));color:#111}
.btn-ghost{background:transparent;color:#fff;border:1px solid rgba(255,255,255,.25)}
.btn:hover{transform:translateY(-1px)}
.hero-art{width:100%;height:auto;border-radius:22px;box-shadow:0 30px 80px rgba(0,0,0,.35)}
@media(max-width:900px){.hero .container{grid-template-columns:1fr;padding:50px 24px 80px}}
/* floating dock */
.dock{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:60;background:rgba(10,27,61,.95);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.1);padding:10px 14px;border-radius:999px;display:flex;gap:6px;box-shadow:0 20px 50px rgba(0,0,0,.35)}
.dock a{color:#D8DEEB;padding:8px 14px;border-radius:999px;font-size:13px;font-weight:600}
.dock a.active,.dock a:hover{background:linear-gradient(90deg,var(--amber),var(--orange));color:#111}
/* tracking bar (near top after dock) */
.trackbar{max-width:960px;margin:-70px auto 40px;position:relative;z-index:5}
.trackbar .card{background:#fff;border-radius:22px;padding:26px;box-shadow:0 30px 80px rgba(10,27,61,.18);border:1px solid var(--line)}
.trackbar h3{font-size:22px;margin-bottom:14px}
.trackbar form{display:flex;gap:10px}
.trackbar input{flex:1;padding:16px 18px;border-radius:12px;border:1px solid var(--line);font-size:15px;font-family:'JetBrains Mono';background:#F7F8FB}
.trackbar input:focus{outline:2px solid var(--amber);background:#fff}
@media(max-width:640px){.trackbar form{flex-direction:column}.trackbar{margin-top:-40px;padding:0 16px}}
/* sections */
section.block{padding:80px 0}
.section-head{text-align:center;margin-bottom:50px}
.section-head h2{font-size:clamp(28px,3.5vw,44px)}
.section-head p{color:var(--muted);max-width:620px;margin:14px auto 0}
/* services bento */
.bento{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.tile{background:#fff;border:1px solid var(--line);border-radius:20px;padding:26px;transition:.2s;position:relative;overflow:hidden}
.tile:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(10,27,61,.1);border-color:var(--amber)}
.tile .icn{width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,var(--navy),var(--navy2));color:var(--amber);display:flex;align-items:center;justify-content:center;margin-bottom:16px}
.tile h4{font-size:18px;margin-bottom:8px}
.tile p{color:var(--muted);font-size:14px;line-height:1.5;margin:0}
.tile::after{content:'';position:absolute;right:-40px;bottom:-40px;width:120px;height:120px;background:radial-gradient(circle,rgba(245,179,1,.15),transparent 70%)}
@media(max-width:900px){.bento{grid-template-columns:repeat(2,1fr)}}
@media(max-width:520px){.bento{grid-template-columns:1fr}}
/* stats strip */
.stats{background:linear-gradient(135deg,var(--navy),var(--navy2));color:#fff;border-radius:28px;padding:44px;display:grid;grid-template-columns:repeat(4,1fr);gap:24px;position:relative;overflow:hidden}
.stats::before{content:'';position:absolute;inset:0;background:radial-gradient(600px 200px at 90% 0%,rgba(245,179,1,.25),transparent)}
.stat{position:relative}
.stat .n{font-family:'Outfit';font-size:38px;font-weight:800;color:var(--amber)}
.stat .l{font-size:13px;color:#B7C2DA;margin-top:4px}
@media(max-width:720px){.stats{grid-template-columns:repeat(2,1fr);padding:28px}}
/* marquee */
.marquee{background:#0A1B3D;color:#fff;padding:18px 0;overflow:hidden;border-top:1px solid rgba(255,255,255,.06);border-bottom:1px solid rgba(255,255,255,.06)}
.marquee .track{display:flex;gap:60px;animation:scroll 40s linear infinite;white-space:nowrap;font-family:'JetBrains Mono';font-size:13px;color:#C6D0E4}
.marquee .track span{display:inline-flex;align-items:center;gap:10px}
.marquee .track b{color:var(--amber)}
@keyframes scroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
/* footer */
footer{background:#050D22;color:#B7C2DA;padding:60px 0 30px;margin-top:60px}
footer .grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:40px;margin-bottom:40px}
footer h5{color:#fff;font-family:'Outfit';font-size:14px;letter-spacing:.14em;text-transform:uppercase;margin-bottom:16px}
footer a{display:block;padding:6px 0;font-size:14px}
footer a:hover{color:var(--amber)}
footer .base{border-top:1px solid rgba(255,255,255,.08);padding-top:20px;font-size:12px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px}
@media(max-width:720px){footer .grid{grid-template-columns:1fr 1fr}}
/* page */
.page{padding:80px 0;min-height:60vh}
.page h1{font-size:clamp(32px,4vw,52px);margin-bottom:20px}
.page .lead{color:var(--muted);font-size:18px;max-width:720px;margin-bottom:40px;line-height:1.6}
.card{background:#fff;border:1px solid var(--line);border-radius:20px;padding:30px}
/* booking form */
.wizard{background:#fff;border:1px solid var(--line);border-radius:24px;padding:36px;box-shadow:0 20px 60px rgba(10,27,61,.08)}
.steps{display:flex;gap:8px;margin-bottom:30px}
.step{flex:1;padding:12px;border-radius:12px;background:#F7F8FB;border:1px solid var(--line);font-size:13px;text-align:center;color:var(--muted);display:flex;align-items:center;justify-content:center;gap:8px}
.step.active{background:linear-gradient(90deg,var(--amber),var(--orange));color:#111;border-color:transparent;font-weight:700}
.fset{display:grid;grid-template-columns:1fr 1fr;gap:26px;margin-bottom:24px}
.fset .box{border:1px solid var(--line);border-radius:16px;padding:22px;background:#FBFCFE}
.fset h4{display:flex;align-items:center;gap:10px;margin-bottom:16px;font-size:16px}
.fset h4 .ic{width:34px;height:34px;border-radius:10px;background:var(--navy);color:var(--amber);display:flex;align-items:center;justify-content:center}
label{display:block;font-size:12px;color:var(--muted);margin:10px 0 6px;font-weight:600;text-transform:uppercase;letter-spacing:.08em}
input,select,textarea{width:100%;padding:12px 14px;border:1px solid var(--line);border-radius:10px;font-size:14px;font-family:inherit;background:#fff}
input:focus,select:focus,textarea:focus{outline:2px solid var(--amber);border-color:transparent}
.services-choice{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin:20px 0}
.sc{padding:18px;border:2px solid var(--line);border-radius:14px;cursor:pointer;text-align:center}
.sc input{display:none}
.sc.selected,.sc:hover{border-color:var(--amber);background:#FFF9E8}
.sc b{display:block;font-family:'Outfit';font-size:16px;margin-bottom:4px}
.sc small{color:var(--muted)}
@media(max-width:720px){.fset{grid-template-columns:1fr}.services-choice{grid-template-columns:1fr}.steps{flex-wrap:wrap}}
/* tracking result */
.track-result{display:grid;grid-template-columns:1fr 1.4fr;gap:30px}
.timeline{position:relative;padding-left:30px}
.timeline::before{content:'';position:absolute;left:8px;top:8px;bottom:8px;width:2px;background:linear-gradient(var(--amber),var(--orange))}
.tl-item{position:relative;padding:0 0 26px}
.tl-item::before{content:'';position:absolute;left:-26px;top:6px;width:14px;height:14px;border-radius:50%;background:var(--amber);box-shadow:0 0 0 4px #FFF9E8}
.tl-item .t{font-family:'JetBrains Mono';font-size:12px;color:var(--muted)}
.tl-item .c{font-family:'Outfit';font-weight:700;font-size:16px;margin:2px 0}
.tl-item .l{color:var(--muted);font-size:14px}
.badge{display:inline-block;padding:6px 12px;background:linear-gradient(90deg,var(--amber),var(--orange));color:#111;border-radius:999px;font-size:12px;font-weight:700}
@media(max-width:820px){.track-result{grid-template-columns:1fr}}
.notice{padding:14px 18px;border-radius:12px;background:#FFF3D6;border:1px solid #F5B301;color:#5A4200;font-size:14px}
.voice-btn{position:fixed;right:20px;bottom:88px;z-index:60;background:linear-gradient(135deg,var(--amber),var(--orange));color:#111;border:0;padding:14px 18px;border-radius:999px;font-weight:700;box-shadow:0 15px 40px rgba(0,0,0,.25);cursor:pointer;display:flex;align-items:center;gap:8px}
</style>
</head><body>
<header class="top"><div class="container row">
  <a href="?r=/" class="brand"><?= svg_logo() ?><span>InterGlobe<small>.CLOUD</small></span></a>
  <nav class="nav" id="nav">
    <a href="?r=/"><?= t('nav.home') ?></a>
    <a href="?r=/book"><?= t('nav.book') ?></a>
    <a href="?r=/about"><?= t('nav.about') ?></a>
    <a href="?r=/reviews"><?= t('nav.reviews') ?></a>
    <a href="?r=/contact"><?= t('nav.contact') ?></a>
    <a href="?lang=<?= $GLOBALS['LANG']==='en'?'fr':'en' ?>" class="pill"><?= strtoupper($GLOBALS['LANG']==='en'?'FR':'EN') ?></a>
    <a href="?r=/book" class="pill amber"><?= t('nav.book') ?></a>
  </nav>
  <button class="menu-toggle" onclick="document.getElementById('nav').classList.toggle('open')">☰</button>
</div></header>
<button class="voice-btn" onclick="speakVoice()" id="vb">🔊 <?= $GLOBALS['LANG']==='en'?'Play voice':'Écouter' ?></button>
<?php }

function foot_html(): void { global $route; ?>
<nav class="dock">
  <a href="?r=/" class="<?= $route==='/'?'active':'' ?>"><?= t('nav.home') ?></a>
  <a href="?r=/book" class="<?= $route==='/book'?'active':'' ?>"><?= t('nav.book') ?></a>
  <a href="?r=/about" class="<?= $route==='/about'?'active':'' ?>"><?= t('nav.about') ?></a>
  <a href="?r=/contact" class="<?= $route==='/contact'?'active':'' ?>"><?= t('nav.contact') ?></a>
</nav>
<footer><div class="container">
  <div class="grid">
    <div>
      <div class="brand" style="color:#fff;margin-bottom:14px"><?= svg_logo() ?><span>InterGlobe<small style="color:var(--amber)">.CLOUD</small></span></div>
      <p style="font-size:14px;line-height:1.6">The modern freight and courier network engineered for speed, transparency, and reliability.</p>
    </div>
    <div><h5>Company</h5><a href="?r=/about"><?= t('nav.about') ?></a><a href="?r=/reviews"><?= t('nav.reviews') ?></a><a href="?r=/contact"><?= t('nav.contact') ?></a></div>
    <div><h5>Services</h5><a href="?r=/book"><?= t('services.air') ?></a><a href="?r=/book"><?= t('services.sea') ?></a><a href="?r=/book"><?= t('services.road') ?></a></div>
    <div><h5>Legal</h5><a href="?r=/terms"><?= t('nav.terms') ?></a><a href="?r=/terms">Privacy</a></div>
  </div>
  <div class="base"><span>© <?= date('Y') ?> InterGlobe.cloud — <?= t('footer.rights') ?></span><span class="mono">v1.0 · PHP</span></div>
</div></footer>
<script>
function speakVoice(){
  if(!('speechSynthesis' in window)){alert('Voice not supported');return;}
  window.speechSynthesis.cancel();
  var u=new SpeechSynthesisUtterance(<?= json_encode(t('voice.line')) ?>);
  u.lang='<?= $GLOBALS['LANG']==='fr'?'fr-FR':'en-US' ?>';u.rate=.95;u.pitch=1;
  window.speechSynthesis.speak(u);
}
// Auto-play voice on home (muted until user interacts, browsers require gesture)
document.addEventListener('click',function once(){speakVoice();document.removeEventListener('click',once)},{once:true});
// service tier
document.addEventListener('click',function(e){
  var sc=e.target.closest('.sc');if(!sc)return;
  document.querySelectorAll('.sc').forEach(x=>x.classList.remove('selected'));
  sc.classList.add('selected');sc.querySelector('input').checked=true;
});
</script>
</body></html>
<?php }

// ---------- Pages ----------
switch ($route) {
case '/':
    head_html('Global Shipping & Tracking', 'InterGlobe.cloud — modern freight and courier network across 220+ countries.');
    ?>
    <section class="hero"><div class="container">
      <div>
        <span class="eyebrow"><?= t('hero.eyebrow') ?></span>
        <h1><?= t('hero.title') ?></h1>
        <p class="sub"><?= t('hero.sub') ?></p>
        <div class="cta">
          <a href="?r=/book" class="btn btn-amber"><?= t('hero.cta1') ?> →</a>
          <a href="#track" class="btn btn-ghost"><?= t('hero.cta2') ?></a>
        </div>
      </div>
      <div><?= svg_hero() ?></div>
    </div></section>

    <div id="track" class="trackbar"><div class="card">
      <h3><?= t('track.title') ?></h3>
      <form method="get" action="">
        <input type="hidden" name="r" value="/track">
        <input type="text" name="id" placeholder="<?= e(t('track.ph')) ?>" required>
        <button class="btn btn-amber" type="submit"><?= t('track.btn') ?></button>
      </form>
      <p style="color:var(--muted);font-size:13px;margin:12px 0 0">Try: <span class="mono">IG-2026-004821</span> · <span class="mono">IG-2026-004822</span></p>
    </div></div>

    <div class="marquee"><div class="track">
      <?php for($i=0;$i<2;$i++): ?>
      <span><b>SHA→RTM</b> IG-2026-004821 · In Transit · ETA 07-11</span>
      <span><b>FRA→LOS</b> IG-2026-004822 · Out for Delivery</span>
      <span><b>NYC→DXB</b> IG-2026-004901 · Cleared Customs</span>
      <span><b>HKG→LAX</b> IG-2026-004955 · Departed Origin</span>
      <span><b>MEX→MAD</b> IG-2026-005012 · Booked</span>
      <?php endfor; ?>
    </div></div>

    <section class="block"><div class="container">
      <div class="section-head"><h2><?= t('services.title') ?></h2><p>Air, sea, ground and warehousing — orchestrated on one command surface.</p></div>
      <div class="bento">
        <div class="tile"><div class="icn"><?= svg_icon('air') ?></div><h4><?= t('services.air') ?></h4><p><?= t('services.airD') ?></p></div>
        <div class="tile"><div class="icn"><?= svg_icon('sea') ?></div><h4><?= t('services.sea') ?></h4><p><?= t('services.seaD') ?></p></div>
        <div class="tile"><div class="icn"><?= svg_icon('road') ?></div><h4><?= t('services.road') ?></h4><p><?= t('services.roadD') ?></p></div>
        <div class="tile"><div class="icn"><?= svg_icon('ware') ?></div><h4><?= t('services.ware') ?></h4><p><?= t('services.wareD') ?></p></div>
      </div>
    </div></section>

    <section class="block" style="padding-top:0"><div class="container">
      <div class="stats">
        <div class="stat"><div class="n">220+</div><div class="l">Countries served</div></div>
        <div class="stat"><div class="n">38</div><div class="l">Bonded hubs</div></div>
        <div class="stat"><div class="n">99.6%</div><div class="l">On-time reliability</div></div>
        <div class="stat"><div class="n">2.4M</div><div class="l">Monthly shipments</div></div>
      </div>
    </div></section>
    <?php
    foot_html(); break;

case '/book':
    head_html(t('book.title'), 'Create a waybill and dispatch a shipment worldwide.');
    ?>
    <section class="page"><div class="container">
      <h1><?= t('book.title') ?></h1>
      <p class="lead">Complete the details below. A tracking code is generated instantly.</p>
      <form method="post" action="?r=/book" class="wizard">
        <div class="steps">
          <div class="step active">1 · <?= t('book.sender') ?></div>
          <div class="step active">2 · <?= t('book.recipient') ?></div>
          <div class="step active">3 · <?= t('book.parcel') ?></div>
          <div class="step active">4 · <?= t('book.service') ?></div>
          <div class="step active">5 · <?= t('book.review') ?></div>
        </div>
        <div class="fset">
          <div class="box"><h4><span class="ic"><?= svg_icon('user') ?></span><?= t('book.sender') ?></h4>
            <label><?= t('book.name') ?></label><input name="sender_name" required>
            <label><?= t('book.email') ?></label><input name="sender_email" type="email" required>
            <label><?= t('book.phone') ?></label><input name="sender_phone" required>
            <label><?= t('book.address') ?></label><input name="sender_address" required>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
              <div><label><?= t('book.city') ?></label><input name="sender_city" required></div>
              <div><label><?= t('book.country') ?></label><input name="sender_country" required></div>
            </div>
          </div>
          <div class="box"><h4><span class="ic"><?= svg_icon('pin') ?></span><?= t('book.recipient') ?></h4>
            <label><?= t('book.name') ?></label><input name="recipient_name" required>
            <label><?= t('book.email') ?></label><input name="recipient_email" type="email" required>
            <label><?= t('book.phone') ?></label><input name="recipient_phone" required>
            <label><?= t('book.address') ?></label><input name="recipient_address" required>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
              <div><label><?= t('book.city') ?></label><input name="recipient_city" required></div>
              <div><label><?= t('book.country') ?></label><input name="recipient_country" required></div>
            </div>
          </div>
        </div>
        <div class="box" style="border:1px solid var(--line);border-radius:16px;padding:22px;background:#FBFCFE;margin-bottom:24px">
          <h4><span class="ic" style="background:var(--navy);color:var(--amber);width:34px;height:34px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;margin-right:10px"><?= svg_icon('box') ?></span><?= t('book.parcel') ?></h4>
          <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px">
            <div><label><?= t('book.weight') ?></label><input name="weight" type="number" step="0.1" required></div>
            <div><label><?= t('book.desc') ?></label><input name="description" required></div>
          </div>
        </div>
        <h4 style="margin:10px 0 6px"><?= t('book.service') ?></h4>
        <div class="services-choice">
          <label class="sc selected"><input type="radio" name="service" value="Air Express" checked><b><?= t('services.air') ?></b><small>1–3 days</small></label>
          <label class="sc"><input type="radio" name="service" value="Sea Freight"><b><?= t('services.sea') ?></b><small>15–35 days</small></label>
          <label class="sc"><input type="radio" name="service" value="Ground"><b><?= t('services.road') ?></b><small>3–7 days</small></label>
        </div>
        <button class="btn btn-amber" type="submit" style="width:100%;justify-content:center;padding:18px"><?= t('book.submit') ?> →</button>
      </form>
    </div></section>
    <?php
    foot_html(); break;

case '/track':
    $id = trim($_GET['id'] ?? '');
    $data = load_shipments();
    $s = $data[$id] ?? null;
    head_html('Track ' . ($id ?: 'shipment'), 'Live tracking for shipment ' . e($id));
    ?>
    <section class="page"><div class="container">
      <h1><?= e($id ?: t('track.title')) ?></h1>
      <?php if(!$s): ?>
        <div class="notice"><?= t('track.notfound') ?></div>
        <form method="get" action="" style="margin-top:20px;display:flex;gap:10px;max-width:520px">
          <input type="hidden" name="r" value="/track">
          <input name="id" placeholder="<?= e(t('track.ph')) ?>" required>
          <button class="btn btn-amber"><?= t('track.btn') ?></button>
        </form>
      <?php else: ?>
        <div class="track-result">
          <div class="card">
            <span class="badge"><?= e($s['status']) ?></span>
            <h3 style="margin:16px 0 6px;font-family:'Outfit';font-size:22px"><?= e($s['from']) ?> → <?= e($s['to']) ?></h3>
            <p style="color:var(--muted);font-size:14px"><?= e($s['service']) ?></p>
            <hr style="border:0;border-top:1px solid var(--line);margin:20px 0">
            <div style="display:grid;gap:10px;font-size:14px">
              <div><b>Tracking</b><br><span class="mono"><?= e($s['id']) ?></span></div>
              <div><b>Created</b><br><?= e($s['created']) ?></div>
              <div><b>ETA</b><br><?= e($s['eta']) ?></div>
            </div>
          </div>
          <div class="card">
            <h4 style="font-family:'Outfit';margin-bottom:20px">Shipment Timeline</h4>
            <div class="timeline">
              <?php foreach(array_reverse($s['events']) as $ev): ?>
                <div class="tl-item"><div class="t"><?= e($ev['t']) ?></div><div class="c"><?= e($ev['city']) ?></div><div class="l"><?= e($ev['label']) ?></div></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div></section>
    <?php
    foot_html(); break;

case '/about':
    head_html(t('nav.about'), 'About InterGlobe.cloud — a modern global logistics network.');
    ?><section class="page"><div class="container" style="max-width:900px">
      <h1><?= t('about.title') ?></h1>
      <p class="lead"><?= t('about.body') ?></p>
      <div class="bento" style="grid-template-columns:1fr 1fr">
        <div class="tile"><h4>Our mission</h4><p>Bring institutional-grade logistics to every sender — from Fortune 500 shippers to independent merchants — with pricing and reliability that scales.</p></div>
        <div class="tile"><h4>What sets us apart</h4><p>A single API and command surface for air, sea, ground and warehousing. Live geofenced telemetry. Zero-touch customs clearance.</p></div>
      </div>
    </div></section><?php foot_html(); break;

case '/reviews':
    head_html(t('nav.reviews'), 'Metrics and network performance for InterGlobe.cloud.');
    ?><section class="page"><div class="container">
      <h1><?= t('reviews.title') ?></h1>
      <p class="lead">Verified network performance — measured, not marketed.</p>
      <div class="stats" style="margin-top:20px">
        <div class="stat"><div class="n">99.6%</div><div class="l">On-time reliability</div></div>
        <div class="stat"><div class="n">4.9/5</div><div class="l">Enterprise NPS</div></div>
        <div class="stat"><div class="n">28 min</div><div class="l">Avg. customs clearance</div></div>
        <div class="stat"><div class="n">0.02%</div><div class="l">Claim rate</div></div>
      </div>
    </div></section><?php foot_html(); break;

case '/contact':
    head_html(t('nav.contact'), 'Contact InterGlobe.cloud logistics engineers.');
    ?><section class="page"><div class="container" style="max-width:820px">
      <h1><?= t('contact.title') ?></h1>
      <p class="lead">Reach the operations desk 24/7. We respond within 15 minutes on enterprise plans.</p>
      <div class="card">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          <div><label>Name</label><input></div>
          <div><label>Email</label><input type="email"></div>
        </div>
        <label>Message</label><textarea rows="5"></textarea>
        <button class="btn btn-amber" style="margin-top:16px">Send message</button>
      </div>
    </div></section><?php foot_html(); break;

case '/terms':
    head_html(t('nav.terms'), 'Terms of service and privacy policy for InterGlobe.cloud.');
    ?><section class="page"><div class="container" style="max-width:820px">
      <h1><?= t('terms.title') ?></h1>
      <p class="lead">By using InterGlobe.cloud you agree to our carriage, liability, and data-handling terms. Full legal text available on request.</p>
      <div class="card">
        <h3>1. Carriage</h3><p>All shipments are carried under the InterGlobe Master Carriage Contract v3.0.</p>
        <h3 style="margin-top:20px">2. Liability</h3><p>Standard liability is USD 100 per shipment; declared value insurance is optional at booking.</p>
        <h3 style="margin-top:20px">3. Privacy</h3><p>Sender and recipient data are used solely to fulfil the shipment and comply with customs regulations.</p>
      </div>
    </div></section><?php foot_html(); break;

default:
    http_response_code(404);
    head_html('Not found', 'Page not found');
    echo '<section class="page"><div class="container"><h1>404</h1><p class="lead">The page you requested does not exist.</p><a href="?r=/" class="btn btn-amber">Return home</a></div></section>';
    foot_html();
}
