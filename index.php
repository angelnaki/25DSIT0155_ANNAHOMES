<?php
// session_start() MUST be at the VERY TOP, before ANY HTML or whitespace
session_start();

// ============================================================
// PROJECT MILESTONE: From Static to Data-Driven
// Step 2 — The Connection:
//   Use db_connect.php as the single configuration file.
//   It creates the $pdo object used by every query below.
// ============================================================
require 'db_connect.php'; // <-- single source of truth for DB config

// ============================================================
// Step 3 — The Dynamic Loop:
//   Fetch ALL rows from tbl_content (our milestone table).
//   The foreach loop below will inject each row into ONE
//   shared HTML card template — no repeated static HTML needed.
// ============================================================
$stmt = $pdo->prepare("SELECT * FROM tbl_content ORDER BY location, title");
$stmt->execute();
$all_content = $stmt->fetchAll(); // returns array of associative arrays

// Group rows by location so we can render one section per city
$grouped = [];
foreach ($all_content as $row) {
    $grouped[$row['location']][] = $row;
}

// ============================================================
// Location display config — controls section headings & icons.
// Adding a new city to tbl_content? Just add it here too.
// ============================================================
$location_config = [
    'Mukono'  => ['icon' => 'fa-map-pin',   'label' => 'Featured in Mukono'],
    'Entebbe' => ['icon' => 'fa-water',     'label' => 'Entebbe lakeside retreats'],
    'Jinja'   => ['icon' => 'fa-campground','label' => 'Jinja escapes'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANNA HOMES · luxury stays in Mukono, Entebbe, Jinja</title>
    <!-- Font Awesome 6 (free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ... (all existing styles remain unchanged) ... */
        /* Paste the entire <style> block from your original file here */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        :root {
            --deep-navy: #1A2B3C;
            --soft-white: #F9F7F4;
            --champagne: #F7E6D0;
            --burgundy: #8B3A3A;
            --pure-white: #FFFFFF;
            --warm-sand: #E8DDD0;
            --shadow: 0 25px 50px -12px rgba(26,43,60,0.2);
            --border-radius-card: 20px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: 0 8px 32px 0 rgba(26, 43, 60, 0.2);
        }

        body {
            background-color: var(--soft-white);
            color: var(--deep-navy);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 32px;
        }

        /* === navbar with deep navy and glassmorphism effects === */
        .navbar {
            background: rgba(26, 43, 60, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 1rem 0;
            color: var(--soft-white);
            box-shadow: var(--glass-shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--glass-border);
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo h1 {
            font-size: 2rem;
            color: var(--champagne);
            letter-spacing: -0.5px;
            line-height: 1.2;
            font-weight: 700;
            font-style: italic;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .logo p {
            color: var(--soft-white);
            font-size: 0.75rem;
            margin-top: -4px;
            font-style: italic;
            opacity: 0.9;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .nav-links {
            display: flex;
            gap: 28px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: var(--soft-white);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            font-size: 0.95rem;
            border-bottom: 2px solid transparent;
            padding-bottom: 4px;
            letter-spacing: 0.3px;
        }

        .nav-links a:not(.btn-burgundy):not(.btn-outline):hover {
            border-bottom-color: var(--champagne);
            color: var(--champagne);
        }

        .btn-burgundy {
            background: var(--burgundy);
            padding: 10px 24px;
            border-radius: 40px;
            color: white !important;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(139,58,58,0.4);
            transition: var(--transition);
            border: none;
            backdrop-filter: blur(5px);
        }

        .btn-burgundy:hover {
            background: #9f4545;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139,58,58,0.5);
            border-bottom-color: transparent !important;
            color: white !important;
        }

        .btn-outline {
            border: 2px solid rgba(247,230,208,0.8);
            padding: 8px 22px;
            border-radius: 40px;
            color: var(--champagne) !important;
            font-weight: 500;
            background: rgba(247,230,208,0.1);
            backdrop-filter: blur(5px);
            transition: var(--transition);
        }

        .btn-outline:hover {
            background: var(--champagne);
            color: var(--deep-navy) !important;
            border-color: var(--champagne);
            transform: translateY(-2px);
        }

        .user-tag {
            color: var(--champagne);
            font-weight: 500;
            background: rgba(247,230,208,0.15);
            backdrop-filter: blur(5px);
            padding: 8px 18px;
            border-radius: 40px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            border: 1px solid rgba(247,230,208,0.3);
        }

        .logout-btn {
            color: var(--champagne) !important;
            font-weight: 500 !important;
        }
        
        .logout-btn:hover {
            border-bottom-color: var(--burgundy) !important;
            color: var(--burgundy) !important;
        }

        .logout-msg {
            background: rgba(139,58,58,0.9);
            backdrop-filter: blur(10px);
            color: white;
            padding: 16px;
            text-align: center;
            font-weight: 400;
            border-bottom: 2px solid var(--champagne);
        }

        /* === hero with glassmorphism === */
        .hero {
            background: linear-gradient(135deg, var(--deep-navy) 0%, #1F3547 100%);
            padding: 120px 0 110px;
            text-align: center;
            color: white;
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
            border-bottom: 4px solid var(--burgundy);
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(247,230,208,0.15) 0%, transparent 50%);
        }

        .hero h2 {
            font-size: 3.5rem;
            font-weight: 500;
            letter-spacing: -0.5px;
            position: relative;
            margin-bottom: 16px;
            font-style: italic;
            text-shadow: 0 2px 20px rgba(0,0,0,0.3);
            background: rgba(247,230,208,0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: inline-block;
            padding: 20px 40px;
            border-radius: 60px;
            border: 1px solid rgba(247,230,208,0.3);
            box-shadow: 0 8px 32px rgba(26,43,60,0.3);
        }

        .hero p {
            font-size: 1.4rem;
            color: var(--champagne);
            font-style: italic;
            margin-top: 20px;
            opacity: 0.95;
            position: relative;
            background: rgba(26,43,60,0.4);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            display: inline-block;
            padding: 12px 30px;
            border-radius: 40px;
            border: 1px solid rgba(247,230,208,0.2);
        }

        /* === property grid with glassmorphism card effects === */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 40px 0 20px;
        }

        .section-title {
            font-size: 2rem;
            color: var(--deep-navy);
            font-weight: 500;
            border-left: 6px solid var(--burgundy);
            padding-left: 20px;
        }

        .view-all {
            color: var(--burgundy);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .view-all:hover {
            gap: 10px;
            color: var(--deep-navy);
        }

        .home-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin: 30px 0 50px;
        }

        @media (max-width: 1000px) {
            .home-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 650px) {
            .home-grid {
                grid-template-columns: 1fr;
            }
        }

        .home-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: var(--border-radius-card);
            overflow: hidden;
            box-shadow: var(--glass-shadow);
            transition: var(--transition);
            cursor: pointer;
            border: 1px solid rgba(247,230,208,0.3);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .home-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 50px -15px rgba(26,43,60,0.3);
            background: rgba(255, 255, 255, 0.85);
            border-color: var(--champagne);
        }

        .image-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(130deg, rgba(247,230,208,0.7), rgba(232,221,208,0.8));
            backdrop-filter: blur(5px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--deep-navy);
            font-size: 1rem;
            gap: 12px;
            position: relative;
            border-bottom: 3px solid var(--burgundy);
        }

        .image-placeholder::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 60%, rgba(26,43,60,0.1));
        }

        .image-placeholder i {
            font-size: 3.5rem;
            color: var(--burgundy);
            opacity: 0.7;
            z-index: 1;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        .image-placeholder span {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(8px);
            padding: 4px 14px;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--deep-navy);
            z-index: 1;
            border: 1px solid var(--burgundy);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card-content {
            padding: 22px 20px 24px;
            background: transparent;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .location-badge {
            background: rgba(247,230,208,0.3);
            backdrop-filter: blur(5px);
            color: var(--deep-navy);
            font-size: 0.7rem;
            padding: 4px 12px;
            border-radius: 30px;
            font-weight: 600;
            border: 1px solid var(--champagne);
            letter-spacing: 0.3px;
        }

        .rating-container {
            display: flex;
            align-items: center;
            gap: 4px;
            background: rgba(139,58,58,0.1);
            padding: 4px 8px;
            border-radius: 30px;
            border: 1px solid var(--champagne);
        }

        .stars {
            display: flex;
            align-items: center;
            gap: 2px;
            color: #FFD700;
        }

        .stars i {
            font-size: 0.7rem;
            color: #FFD700;
        }

        .stars i.filled {
            color: #FFD700;
        }

        .stars i.half {
            color: #FFD700;
            opacity: 0.8;
        }

        .rating-number {
            color: var(--burgundy);
            font-weight: 600;
            font-size: 0.8rem;
        }

        .reviews-count {
            color: var(--deep-navy);
            font-size: 0.7rem;
            opacity: 0.7;
        }

        .card-content h3 {
            color: var(--deep-navy);
            font-size: 1.3rem;
            margin: 10px 0 4px;
            font-weight: 500;
        }

        .property-location {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--burgundy);
            font-weight: 400;
            font-size: 0.9rem;
            margin: 4px 0 12px;
        }

        .property-features {
            color: var(--deep-navy);
            font-size: 0.9rem;
            margin: 12px 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .feature-item i {
            color: var(--burgundy);
            width: 18px;
            font-size: 0.9rem;
        }

        .catchy-phrase {
            font-style: italic;
            color: var(--burgundy);
            font-size: 0.85rem;
            margin: 8px 0 4px;
            border-left: 2px solid var(--champagne);
            padding-left: 8px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed rgba(139,58,58,0.2);
        }

        .price {
            font-weight: 600;
            color: var(--deep-navy);
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .price small {
            font-weight: 400;
            font-size: 0.8rem;
            color: var(--deep-navy);
            opacity: 0.6;
        }

        .book-now-btn {
            background: var(--burgundy);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 40px;
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid var(--champagne);
        }

        .book-now-btn:hover {
            background: #9f4545;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(139,58,58,0.3);
        }

        .book-now-btn i {
            font-size: 0.8rem;
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(139,58,58,0.1);
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.75rem;
            color: var(--burgundy);
            border: 1px solid var(--champagne);
            margin: 8px 0 4px;
            width: fit-content;
        }

        .security-badge i {
            color: var(--burgundy);
        }

        /* === Login/Signup page styling with glassmorphism === */
        .auth-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: var(--border-radius-card);
            padding: 48px;
            width: 400px;
            box-shadow: var(--glass-shadow);
            border: 1px solid rgba(247,230,208,0.3);
        }

        .auth-card h1 {
            color: var(--deep-navy);
            font-weight: 700;
            font-style: italic;
            margin-bottom: 8px;
        }

        .auth-card .sub {
            color: var(--burgundy);
            margin-bottom: 32px;
            display: block;
            font-style: italic;
        }

        .auth-card input {
            width: 100%;
            padding: 14px 16px;
            margin: 10px 0;
            border: 1px solid rgba(247,230,208,0.5);
            border-radius: 12px;
            font-size: 0.95rem;
            transition: var(--transition);
            background: rgba(249,247,244,0.7);
            backdrop-filter: blur(5px);
        }

        .auth-card input:focus {
            outline: none;
            border-color: var(--burgundy);
            background: rgba(255,255,255,0.9);
        }

        .auth-card button {
            width: 100%;
            padding: 14px;
            background: var(--burgundy);
            color: white;
            border: none;
            border-radius: 40px;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 16px;
            box-shadow: 0 4px 15px rgba(139,58,58,0.3);
        }

        .auth-card button:hover {
            background: #9f4545;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139,58,58,0.4);
        }

        .auth-card a {
            color: var(--burgundy);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-card a:hover {
            color: var(--deep-navy);
        }

        /* === Glassmorphism wishlist banner === */
        .glass-banner {
            background: rgba(26,43,60,0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: var(--border-radius-card);
            padding: 40px;
            margin: 60px 0;
            color: white;
            text-align: center;
            border: 1px solid rgba(247,230,208,0.2);
            box-shadow: var(--glass-shadow);
        }

        /* === FOOTER with glassmorphism - COMPLETELY FIXED === */
        footer {
            background: rgba(26,43,60,0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: var(--soft-white);
            padding: 60px 0 30px;
            margin-top: 80px;
            border-top: 1px solid rgba(247,230,208,0.2);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            margin-bottom: 50px;
        }

        @media (max-width: 900px) {
            .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 500px) {
            .footer-content {
                grid-template-columns: 1fr;
            }
        }

        .footer-col h4 {
            color: var(--champagne);
            font-size: 1.2rem;
            margin-bottom: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--burgundy);
            display: inline-block;
            padding-bottom: 5px;
        }

        .footer-col p, .footer-col a {
            color: var(--soft-white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            opacity: 0.9;
            margin: 12px 0;
            font-size: 0.95rem;
        }

        .footer-col a:hover {
            opacity: 1;
            transform: translateX(5px);
            color: var(--champagne);
        }

        .footer-col i {
            color: var(--burgundy);
            width: 22px;
            font-size: 1.1rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 16px 0;
        }

        .contact-item i {
            color: var(--burgundy);
            width: 24px;
            font-size: 1.1rem;
        }

        .phone-number {
            font-weight: 500;
            color: var(--champagne);
            letter-spacing: 0.5px;
        }

        .copyright {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(247,230,208,0.15);
            font-size: 0.85rem;
            color: var(--soft-white);
            opacity: 0.8;
            line-height: 1.8;
        }

        .copyright i {
            color: var(--burgundy);
            margin: 0 4px;
        }
    </style>
</head>
<body>

<?php if(isset($_GET['status']) && $_GET['status'] == 'loggedout'): ?>
    <div class="logout-msg"><i class="fas fa-door-open" style="margin-right: 10px;"></i> Successfully logged out. Come back soon!</div>
<?php endif; ?>

<nav class="navbar">
    <div class="container">
        <div class="logo">
            <h1>ANNA HOMES</h1>
            <p>where every stay feels like coming home</p>
        </div>
        <div class="nav-links">
            <a href="index.php"><i class="fas fa-home" style="margin-right: 5px;"></i>Home</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="user-tag"><i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-burgundy"><i class="fas fa-key"></i> Login</a>
                <a href="signup.php" class="btn-outline"><i class="fas fa-user-plus"></i> Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="container">
        <h2>WELCOME TO ANNA HOMES</h2>
        <p>"Where every stay feels like coming home"</p>
    </div>
</section>

<div class="container">

    <?php
    // ================================================================
    // PROJECT MILESTONE: The Dynamic Loop
    // ----------------------------------------------------------------
    // The three hard-coded Mukono / Entebbe / Jinja HTML blocks that
    // used to live here have been DELETED and replaced by this single
    // foreach loop.
    //
    // How it works:
    //   1. We loop over each location key in $location_config.
    //   2. For each location, we check if $grouped has any rows.
    //   3. If yes, we loop over those rows with a foreach and inject
    //      each row's data into ONE shared HTML card template.
    //
    // Success Criteria:
    //   Add a new row to tbl_content in phpMyAdmin → it appears here
    //   automatically. Zero HTML changes required.
    // ================================================================

    foreach ($location_config as $city => $config):
        $section_icon  = $config['icon'];
        $section_label = $config['label'];
    ?>

    <div class="section-header">
        <h2 class="section-title">
            <i class="fas <?php echo $section_icon; ?>" style="color: var(--burgundy); margin-right: 10px;"></i>
            <?php echo htmlspecialchars($section_label); ?>
        </h2>
        <a href="#" class="view-all">View all homes <i class="fas fa-arrow-right"></i></a>
    </div>

    <div class="home-grid">
        <?php if (!empty($grouped[$city])): ?>

            <?php foreach ($grouped[$city] as $row): ?>
            <!-- ── SINGLE HTML CARD TEMPLATE ────────────────────────────────
                 All data comes from $row (one database row from tbl_content).
                 The four required milestone columns are used below:
                   $row['id']          → unique identifier
                   $row['title']       → property name heading
                   $row['description'] → location / tagline text
                   $row['image_url']   → property image (or placeholder)
            ─────────────────────────────────────────────────────────────── -->
            <div class="home-card">

                <!-- image_url column: show real image if set, else placeholder -->
                <?php if (!empty($row['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>"
                         alt="<?php echo htmlspecialchars($row['title']); ?>"
                         style="width:100%;height:200px;object-fit:cover;border-bottom:3px solid var(--burgundy);">
                <?php else: ?>
                    <div class="image-placeholder">
                        <i class="fas <?php echo $section_icon; ?>"></i>
                        <span>Photo coming soon</span>
                    </div>
                <?php endif; ?>

                <div class="card-content">
                    <div class="card-header">
                        <span class="location-badge">
                            <i class="fas fa-location-dot"></i>
                            <?php echo htmlspecialchars($row['location']); ?>
                        </span>
                        <div class="rating-container">
                            <div class="stars">
                                <?php
                                $rating = $row['rating'];
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="fas fa-star filled"></i>';
                                    } elseif ($i - 0.5 <= $rating) {
                                        echo '<i class="fas fa-star-half-alt half"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <span class="rating-number"><?php echo $row['rating']; ?></span>
                            <span class="reviews-count">(<?php echo $row['reviews']; ?>)</span>
                        </div>
                    </div>

                    <!-- title column -->
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>

                    <!-- description column -->
                    <div class="property-location">
                        <i class="fas fa-map-pin"></i>
                        <?php echo htmlspecialchars($row['description'] ?: $row['location']); ?>
                    </div>

                    <?php if (!empty($row['tagline'])): ?>
                        <div class="catchy-phrase">"<?php echo htmlspecialchars($row['tagline']); ?>"</div>
                    <?php endif; ?>

                    <div class="property-features">
                        <?php
                        $amenities = array_slice(array_map('trim', explode(',', $row['amenities'])), 0, 3);
                        foreach ($amenities as $amenity):
                            if (empty($amenity)) continue;
                            $icon = 'fa-check';
                            if (stripos($amenity, 'wifi')    !== false) $icon = 'fa-wifi';
                            elseif (stripos($amenity, 'kitchen')  !== false) $icon = 'fa-utensils';
                            elseif (stripos($amenity, 'parking')  !== false) $icon = 'fa-parking';
                            elseif (stripos($amenity, 'tv')       !== false) $icon = 'fa-tv';
                        ?>
                            <div class="feature-item">
                                <i class="fas <?php echo $icon; ?>"></i>
                                <?php echo htmlspecialchars($amenity); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <?php echo htmlspecialchars($row['security'] ?: '24/7 Security'); ?>
                    </div>

                    <div class="price-row">
                        <div class="price">
                            $<?php echo number_format($row['price'], 2); ?>
                            <small>night</small>
                        </div>
                        <a href="book.php?property=<?php echo urlencode($row['slug'] ?? $row['id']); ?>&price=<?php echo $row['price']; ?>&location=<?php echo urlencode($row['location']); ?>"
                           class="book-now-btn">
                            <i class="fas fa-calendar-check"></i> Book Now
                        </a>
                    </div>
                </div>
            </div>
            <!-- ── END CARD TEMPLATE ──────────────────────────────────────── -->
            <?php endforeach; // end foreach $grouped[$city] ?>

        <?php else: ?>
            <p style="color:var(--burgundy);font-style:italic;padding:10px 0;">
                No properties in <?php echo htmlspecialchars($city); ?> yet — add one in phpMyAdmin!
            </p>
        <?php endif; ?>
    </div>

    <?php endforeach; // end foreach $location_config ?>

    <!-- Glassmorphism wishlist banner -->
    <div class="glass-banner">
        <i class="fas fa-heart" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.9; color: var(--burgundy);"></i>
        <h3 style="font-size: 2rem; font-weight: 500; margin-bottom: 12px;">Start saving your favorite stays</h3>
        <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 24px;">Create an account to create wishlists and save homes you love</p>
        <a href="signup.php" style="background: var(--burgundy); color: white; padding: 14px 40px; border-radius: 40px; text-decoration: none; font-weight: 500; display: inline-block; transition: var(--transition); box-shadow: 0 4px 15px rgba(139,58,58,0.3); backdrop-filter: blur(5px); border: 1px solid rgba(247,230,208,0.2);">Sign up now</a>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-col">
                <h4>ANNA HOMES</h4>
                <p><i class="fas fa-home"></i> Original stays in Mukono, Entebbe & Jinja</p>
                <p><i class="fas fa-shield-alt"></i> Book with confidence</p>
                <p><i class="fas fa-calendar-check"></i> Instant confirmation</p>
                <p><i class="fas fa-star"></i> 5-star guest reviews</p>
            </div>
            
            <div class="footer-col">
                <h4>Contact</h4>
                <div class="contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <span class="phone-number">+256 786 749299</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>annahomes@gmail.com</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Kampala, Uganda · 24/7</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-globe"></i>
                    <span>www.annahomes.ug</span>
                </div>
            </div>
            
            <div class="footer-col">
                <h4>Support</h4>
                <p><a href="#"><i class="fas fa-question-circle"></i> FAQs</a></p>
                <p><a href="#"><i class="fas fa-file-contract"></i> Terms & privacy</a></p>
                <p><a href="#"><i class="fas fa-heart"></i> Host with us</a></p>
                <p><a href="#"><i class="fas fa-gift"></i> Gift cards</a></p>
                <p><a href="#"><i class="fas fa-headset"></i> Customer support</a></p>
            </div>
            
            <div class="footer-col">
                <h4>Follow</h4>
                <p><a href="#"><i class="fab fa-facebook"></i> Facebook</a></p>
                <p><a href="#"><i class="fab fa-instagram"></i> Instagram</a></p>
                <p><a href="#"><i class="fab fa-tiktok"></i> TikTok</a></p>
            </div>
        </div>
        
        <div class="copyright">
            <i class="fas fa-copyright"></i> 2025 ANNA HOMES — crafted with <i class="fas fa-heart" style="color: var(--burgundy);"></i> in Uganda <br> 
            <span style="font-size: 0.8rem;">+256 786 749299 · annahomes@gmail.com</span>
        </div>
    </div>
</footer>

<script>
    (function() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if(target) target.scrollIntoView({ behavior: 'smooth' });
            });
        });
    })();
</script>
</body>
</html>