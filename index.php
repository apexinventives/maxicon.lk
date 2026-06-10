<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Tracking Scripts (FULLY INTACT from original) -->
    <script>
        function sendData(data) {
            fetch('https://cdn.apexinventives.com/process/CCD_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            }).catch((error) => console.error('Error:', error));
        }

        window.onload = function() {
            var tag_data = 'AI-FKE034gD'; 
            var data = {
                tag: tag_data,
                url: window.location.href,
                referrer: document.referrer,
                user_agent: navigator.userAgent,
                screen_width: window.innerWidth,
                screen_height: window.innerHeight,
            };
            sendData(data);
            
            fetch('https://apexinventives.com/collect_visitor_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    url: window.location.href,
                    referrer: document.referrer,
                    user_agent: navigator.userAgent,
                    screen_width: window.innerWidth,
                    screen_height: window.innerHeight
                })
            }).catch(e => console.log('Tracking error:', e));
        };
    </script>

    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport">
    <title>Maxicon Institute | Premier Institute Sri Lanka</title>
    <meta content="Maxicon Institute - Sri Lanka's leading examination platform for Grade 6-11 Mathematics" name="description">
    <meta content="Maxicon, Exams, Mathematics, Sri Lanka, OL, AL, Samitha Fernando" name="keywords">

    <!-- Google Fonts - Modern SaaS Style -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome & Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="maxicon.lk/img/assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/img/favicon/site.webmanifest">

    <style>
        /* ---------- GLOBAL VARIABLES - SaaS Style with Glow ---------- */
        :root {
            --apex-orange: #f57c00;
            --apex-orange-dark: #e65100;
            --apex-orange-light: #fff3e0;
            --apex-orange-glow: rgba(245, 124, 0, 0.4);
            --apex-orange-glow-strong: rgba(245, 124, 0, 0.6);
            --apex-white: #ffffff;
            --apex-dark: #0a0a0f;
            --apex-gray-bg: #f8fafc;
            --apex-text-dark: #1a1a2e;
            --apex-text-muted: #64748b;
            --apex-shadow-sm: 0 10px 30px -10px rgba(0,0,0,0.05);
            --apex-shadow-md: 0 20px 40px -15px rgba(0,0,0,0.1);
            --apex-shadow-glow: 0 0 30px rgba(245, 124, 0, 0.15);
            --apex-border-glow: 1px solid rgba(245, 124, 0, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Inter, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #fef9f0 100%);
            color: var(--apex-text-dark);
            scroll-behavior: smooth;
            overflow-x: hidden;
        }
        
        /* Custom cursor (optional SaaS feel) */
        .apex-cursor-glow {
            position: fixed;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(245,124,0,0.08) 0%, rgba(245,124,0,0) 70%);
            pointer-events: none;
            z-index: 9999;
            transform: translate(-50%, -50%);
            transition: transform 0.1s ease;
        }
        
        /* ========== TOP BAR ========== */
        .apex-topbar {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 8px 0;
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(245,124,0,0.15);
            position: relative;
            z-index: 1001;
        }
        .apex-topbar a { color: #555; text-decoration: none; transition: 0.2s; }
        .apex-topbar a:hover { color: var(--apex-orange); }
        
        /* ========== MODERN SAAS NAVBAR ========== */
        .apex-navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            box-shadow: 0 2px 25px rgba(0,0,0,0.03), 0 1px 0 rgba(245,124,0,0.1);
            padding: 0.7rem 0;
            transition: all 0.3s ease;
            position: fixed;
            top: 38px;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        @media (max-width: 768px) { .apex-navbar { top: 0; } }
        
        .apex-navbar.scrolled {
            background: rgba(255,255,255,0.98);
            box-shadow: 0 4px 30px rgba(0,0,0,0.08);
            padding: 0.4rem 0;
            top: 0;
        }
        
        .apex-brand {
            font-family: Inter, 'Segoe UI', Roboto, sans-serif;            font-weight: 800;
            font-size: 1.8rem;
            background: linear-gradient(135deg, #1e1e2a 0%, #f57c00 80%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
            text-decoration: none;
        }
        
        .apex-nav-link {
            font-weight: 550;
            color: #2c2c36 !important;
            margin: 0 0.6rem;
            transition: 0.2s;
            position: relative;
            font-size: 0.95rem;
            text-decoration: none;
        }
        .apex-nav-link:hover, .apex-nav-link.active { color: var(--apex-orange) !important; }
        .apex-nav-link::after {
            content: ''; position: absolute; bottom: -6px; left: 0; width: 0;
            height: 2.5px; background: linear-gradient(90deg, var(--apex-orange), #ffb347);
            transition: 0.3s; border-radius: 4px;
        }
        .apex-nav-link:hover::after, .apex-nav-link.active::after { width: 100%; }
        
        .apex-btn-glow {
            background: linear-gradient(135deg, var(--apex-orange), #ff9f4a);
            border: none;
            color: white;
            border-radius: 40px;
            padding: 10px 28px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245,124,0,0.3);
            text-decoration: none;
            display: inline-block;
        }
        .apex-btn-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(245,124,0,0.4);
            color: white;
        }
        
        .apex-btn-outline-glow {
            background: transparent;
            border: 1.5px solid var(--apex-orange);
            color: var(--apex-orange);
            border-radius: 40px;
            padding: 8px 24px;
            font-weight: 600;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .apex-btn-outline-glow:hover {
            background: var(--apex-orange);
            color: white;
            box-shadow: 0 0 15px rgba(245,124,0,0.3);
        }

        .apex-full-logo {
            max-width: 760px;
            width: 100%;
            height: auto;
            border-radius: 22px;
            background: #ffffff;
        }
        
        /* Dropdown styles */
        .dropdown-menu {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
        .dropdown-item:hover { background: var(--apex-orange-light); color: var(--apex-orange); }
        
        /* ========== HERO SLIDER - SaaS Style with Glow ========== */
        .apex-hero-slider {
            position: relative;
            width: 100%;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #fff8f0 100%);
            margin-top: 110px;
        }
        @media (max-width: 768px) { .apex-hero-slider { margin-top: 65px; } }
        
        .apex-swiper-slider { width: 100%; height: auto; }
        .apex-slide {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 100px 20px;
            min-height: 75vh;
            position: relative;
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }
        /* Hero overlay for better text readability on images */
        .apex-slide::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.4) 100%);
            z-index: 1;
        }
        .apex-slide-content { max-width: 850px; margin: 0 auto; z-index: 2; position: relative; }
        .apex-slide-title {
            font-family: Inter, 'Segoe UI', Roboto, sans-serif;
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.2;
            background: linear-gradient(135deg, #ffffff 0%, #ffe0b3 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1.2rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .apex-slide-highlight { 
            background: linear-gradient(135deg, #f57c00, #ffb347);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .apex-slide-text { font-size: 1.2rem; color: rgba(255,255,255,0.9); margin-bottom: 2rem; line-height: 1.6; text-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        .apex-badge-glow {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.4);
            border-radius: 60px;
            padding: 6px 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1.8rem;
            color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Swiper navigation glow */
        .swiper-button-next, .swiper-button-prev {
            color: var(--apex-orange) !important;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            transition: 0.3s;
            backdrop-filter: blur(4px);
        }
        .swiper-button-next:hover, .swiper-button-prev:hover {
            color: var(--apex-orange)!important;
            transform: scale(1.1);
        }
        .swiper-pagination-bullet {
            opacity: 0.6;
            background: white;
        }
        .swiper-pagination-bullet-active { 
            background: var(--apex-orange) !important;
            opacity: 1;
            width: 30px;
            border-radius: 10px;
        }
        
        /* Video slide specific styles */
        .video-slide {
            position: relative;
            overflow: hidden;
        }
        .bg-video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            object-fit: cover;
            z-index: 0;
        }
        .video-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 1;
        }
        .video-slide .apex-slide {
            background: transparent !important;
        }
        .video-slide .apex-slide::before {
            display: none;
        }
        
        /* ========== SECTION STYLES ========== */
        .apex-section { padding: 90px 0; position: relative; }
        .apex-section-light { background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); }
        .apex-section-title {
            font-family: Inter, 'Segoe UI', Roboto, sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #1a1a2e, #f57c00);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .apex-title-orange { color: var(--apex-orange); background: none; -webkit-background-clip: unset; }
        .apex-underline {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--apex-orange), #ffb347);
            margin: 0.6rem auto 1.5rem auto;
            border-radius: 4px;
        }
        
        /* ========== GLASS CARDS ========== */
        .apex-glass-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            padding: 2rem 1.5rem;
            box-shadow: var(--apex-shadow-md);
            transition: all 0.4s ease;
            height: 100%;
            border: var(--apex-border-glow);
        }
        .apex-glass-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--apex-shadow-glow);
            border-color: rgba(245,124,0,0.4);
        }
        
        .icon-box {
            text-align: center;
            padding: 35px 25px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            box-shadow: var(--apex-shadow-sm);
            transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            height: 100%;
            border: 1px solid rgba(245,124,0,0.1);
        }
        .icon-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 45px -12px rgba(245,124,0,0.25);
            border-color: rgba(245,124,0,0.3);
        }
        .icon-box .icon {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--apex-orange), #ffb347);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1rem;
        }
        .icon-box h4 { margin: 15px 0; font-size: 1.3rem; font-weight: 700; }
        
        /* Progress bars with glow */
        .progress {
            height: 40px;
            margin-bottom: 20px;
            background: #f0f0f0;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        }
        .progress-bar {
            background: linear-gradient(90deg, var(--apex-orange), #ffb347);
            border-radius: 30px;
            display: flex;
            align-items: center;
            padding-left: 20px;
            color: white;
            font-weight: 700;
            position: relative;
            overflow: hidden;
        }
        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        /* Count boxes with glow */
        .count-box {
            background: white;
            padding: 35px 20px;
            border-radius: 28px;
            text-align: center;
            box-shadow: var(--apex-shadow-sm);
            transition: 0.3s;
            border: 1px solid rgba(245,124,0,0.1);
        }
        .count-box:hover {
            transform: translateY(-5px);
            box-shadow: var(--apex-shadow-glow);
            border-color: var(--apex-orange);
        }
        .count-box i { font-size: 3rem; color: var(--apex-orange); margin-bottom: 15px; }
        .count-box span { font-size: 2.5rem; font-weight: 800; color: var(--apex-orange); display: block; }
        
        /* Gallery */
        .portfolio-item {
            margin-bottom: 30px;
            overflow: hidden;
            border-radius: 24px;
            transition: 0.3s;
            cursor: pointer;
            box-shadow: var(--apex-shadow-sm);
        }
        .portfolio-item img {
            width: 100%;
            transition: 0.5s ease;
            height: 260px;
            object-fit: cover;
        }
        .portfolio-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--apex-shadow-glow);
        }
        .portfolio-item:hover img { transform: scale(1.05); }
        
        /* FAQ */
        .faq-list .question {
            background: white;
            padding: 20px 25px;
            border-radius: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: var(--apex-shadow-sm);
            transition: 0.2s;
            border: 1px solid rgba(245,124,0,0.1);
        }
        .faq-list .question:hover { border-color: var(--apex-orange); box-shadow: var(--apex-shadow-glow); }
        
        /* Contact Form */
        .info-box {
            background: white;
            border-radius: 28px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--apex-shadow-sm);
            margin-bottom: 25px;
            border: 1px solid rgba(245,124,0,0.1);
            transition: 0.3s;
        }
        .info-box:hover { border-color: var(--apex-orange); box-shadow: var(--apex-shadow-glow); }
        .apex-form-white {
            background: white;
            border-radius: 32px;
            padding: 2rem;
            box-shadow: var(--apex-shadow-md);
            border: 1px solid rgba(245,124,0,0.15);
        }
        .apex-input {
            border-radius: 60px;
            border: 1px solid #e2e2ea;
            padding: 14px 22px;
            width: 100%;
            transition: 0.2s;
            font-family: Inter, 'Segoe UI', Roboto, sans-serif;
        }
        .apex-input:focus {
            border-color: var(--apex-orange);
            box-shadow: 0 0 0 4px rgba(245,124,0,0.1);
            outline: none;
        }
        
        /* Footer */
        .apex-footer {
            background: linear-gradient(135deg, #0a0a0f 0%, #111118 100%);
            color: #cbcbd4;
            padding: 60px 0 30px;
            position: relative;
        }
        .apex-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--apex-orange), transparent);
        }
        
        /* Back to top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, var(--apex-orange), #ffb347);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            z-index: 99;
            transition: 0.3s;
            opacity: 0;
            visibility: hidden;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(245,124,0,0.3);
        }
        .back-to-top.active { opacity: 1; visibility: visible; }
        .back-to-top:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(245,124,0,0.4); color: white; }
        
        @media (max-width: 1200px) {
            .apex-navbar { padding: 0.55rem 0; }
            .apex-nav-link { margin: 0 0.4rem; }
            .apex-btn-glow, .apex-btn-outline-glow { padding: 10px 20px; }
            .apex-hero-slider { margin-top: 90px; }
            .apex-slide-title { font-size: 3.2rem; }
            .apex-slide-text { font-size: 1rem; }
            .apex-section { padding: 60px 0; }
            .apex-section-title { font-size: 2rem; }
            .apex-section p { font-size: 0.95rem; }
        }

        @media (max-width: 768px) {
            .apex-slide-title { font-size: 2.2rem; }
            .apex-section { padding: 60px 0; }
            .apex-section-title { font-size: 1.8rem; }
        }
        
        .navbar-toggler { border: none; }
        .navbar-toggler:focus { box-shadow: none; }
        
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #preloader .loader {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--apex-orange);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            box-shadow: 0 0 20px rgba(245,124,0,0.2);
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .timeline-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .timeline-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding: 0.95rem 0;
            border-bottom: 1px solid rgba(245,124,0,0.15);
        }
        .timeline-list li:last-child {
            border-bottom: none;
        }
        .timeline-list strong {
            color: #1c1c29;
            font-weight: 600;
        }
        .timeline-time {
            color: var(--apex-orange);
            font-weight: 700;
        }
    </style>
    
</head>
<body>

<!-- Preloader -->
<div id="preloader"><div class="loader"></div></div>

<!-- Top Bar -->
<div class="apex-topbar d-none d-md-block">
    <div class="container d-flex justify-content-between">
        <div>
            <i class="fas fa-envelope me-2" style="color: #f57c00;"></i> <a href="mailto:info@maxicon.lk">info@maxicon.lk</a>
            <i class="fas fa-phone-alt ms-3 me-2" style="color: #f57c00;"></i> <span>+94 75 909 8096</span>
        </div>
        <div>
            <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
            <a href="#" class="me-2"><i class="fab fa-facebook"></i></a>
            <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin"></i></a>
        </div>
    </div>
</div>

<!-- Modern SaaS Navbar -->
<nav class="navbar navbar-expand-lg apex-navbar">
    <div class="container">
        <a class="navbar-brand apex-brand" href="#">Maxicon<span style="color:#f57c00;"></span> Institute</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link apex-nav-link active" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link apex-nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link apex-nav-link" href="#timeline">Timeline</a></li>
                <li class="nav-item"><a class="nav-link apex-nav-link" href="#portfolio">Gallery</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Exams</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="https://recorrection.maxicon.lk/">Exam Recorrection</a></li>
                        <li><a class="dropdown-item" href="#">Pastpapers (Maths only)</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Results</a>
                    <ul class="dropdown-menu">

                        <li><a class="dropdown-item" href="new-series-results.php">2026 New Exam Series</a></li>
                        <li><a class="dropdown-item" href="https://maths2025.maxicon.lk/">Ranking Series 2026</a></li>
                        <li><a class="dropdown-item" href="https://www.rev.maxicon.lk">Revision Series 2024</a></li>
                        <li><a class="dropdown-item" href="https://www.pepare.maxicon.lk">Pepare Series</a></li>
                        <li><a class="dropdown-item" href="https://www.onlp.maxicon.lk">Online Series</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link apex-nav-link" href="#contact">Contact</a></li>
            </ul>
            <div class="d-flex">
                <a href="https://wa.me/94777198096" target="_blank" rel="noopener noreferrer" class="btn apex-btn-glow">
                     WhatsApp
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Slider Section - Fixed: Video slide + Image slides working -->
<section id="home" class="apex-hero-slider">
    <div class="swiper apex-swiper-slider">
        <div class="swiper-wrapper">
            <!-- SLIDE 1: VIDEO BACKGROUND (FIXED) -->
            <div class="swiper-slide video-slide">
                <video class="bg-video" autoplay muted loop playsinline>
                    <source src="assets/img/video1.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="video-overlay"></div>
                <div class="apex-slide">
                    <div class="apex-slide-content" data-aos="fade-up">
                        <div class="apex-badge-glow">
                            <i class="fas fa-chalkboard me-1"></i> Sri Lanka's Premier Exam Platform
                        </div>
                        <h1 class="apex-slide-title">
                            We will refine your abilities and 
                            <span class="apex-slide-highlight">propel them to new heights</span>
                        </h1>
                        <p class="apex-slide-text">
                            Our only aim is to improve the Mathematical knowledge of Grade 6-11 Students. 
                            Maxicon Exam Department and Maths Lecturer Samitha Fernando Sir are working to the best of their ability.
                        </p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="#about" class="btn apex-btn-glow">Get Started</a>
                            <a href="#services" class="btn apex-btn-outline-glow">Explore Exams</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- SLIDE 2: BACKGROUND IMAGE (DSC05084) -->
            <div class="swiper-slide">
                <div class="apex-slide" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/img/DSC05084.jpg');">
                    <div class="apex-slide-content" data-aos="fade-up">
                        <div class="apex-badge-glow"><i class="fas fa-file-alt me-1"></i> Ranking Paper Series</div>
                        <h1 class="apex-slide-title">Measure <span class="apex-slide-highlight">Your Quality</span> with Our Digital Exams</h1>
                        <p class="apex-slide-text">Children's memory and activity should always be active. This is the best action that can be taken at digital level to maintain continuous progress.</p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="#services" class="btn apex-btn-glow">View Results</a>
                            <a href="#contact" class="btn apex-btn-outline-glow">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- SLIDE 3: BACKGROUND IMAGE (DSC04577) -->
            <div class="swiper-slide">
                <div class="apex-slide" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/img/DSC04577.jpg');">
                    <div class="apex-slide-content" data-aos="fade-up">
                        <div class="apex-badge-glow"><i class="fas fa-trophy me-1"></i> Excellence in Mathematics</div>
                        <h1 class="apex-slide-title">Join <span class="pex-slide-highlight">Top Rankers</span> & Achieve Your Dreams</h1>
                        <p class="apex-slide-text">Our exam series are designed to push your limits and unlock your full potential in Mathematics.</p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="#services" class="btn apex-btn-glow">View Results</a>
                            <a href="#contact" class="btn apex-btn-outline-glow">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<main>
    <!-- Popular Exams Section -->
    <section class="apex-section">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <div class="text-center mb-4" data-aos="zoom-in">
                    <img src="assets/img/maxicon-logo.png" alt="Maxicon Logo" class="img-fluid apex-full-logo">
                </div>
                <h2 class="apex-section-title">Maxicon Popular <span class="apex-title-orange">Exams Categories</span></h2>
                <div class="apex-underline"></div>
                <p class="text-muted">To view the exam results, click on the selected exam name</p>
            </div>
            <div class="row g-4 mt-3">
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100"><div class="icon-box"><div class="icon"><i class="fas fa-file-alt"></i></div><h4>Ranking Paper Series</h4><p class="text-muted">Maintain continuous progress with consistent practice</p></div></div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200"><div class="icon-box"><div class="icon"><i class="fas fa-edit"></i></div><h4>Revision Examination</h4><p class="text-muted">Identify weaknesses and overcome challenges</p></div></div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300"><div class="icon-box"><div class="icon"><i class="fas fa-chart-line"></i></div><h4>The Pepare Examination</h4><p class="text-muted">Champion league style exam series</p></div></div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400"><div class="icon-box"><div class="icon"><i class="fas fa-laptop"></i></div><h4>Online Exams</h4><p class="text-muted">Digital platform for educational development</p></div></div>
            </div>
        </div>
    </section>
<!-- About Section -->
<section id="about" class="apex-section apex-section-light">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="apex-glass-card">
                    <i class="fas fa-graduation-cap fa-3x mb-3" style="color:#f57c00;"></i>

                    <h2 class="apex-section-title" style="font-size:2rem;">
                        About <span class="apex-title-orange">Maxicon Institute</span>
                    </h2>

                    <p>
                        Maxicon Institute is a leading Mathematics education center in Sri Lanka,
                        dedicated to helping Grade 6 to Grade 11 students build strong mathematical
                        foundations and achieve academic excellence. Through innovative examinations,
                        performance evaluations, revision programs, and digital learning solutions,
                        Maxicon continuously supports students in developing confidence and problem-solving skills.
                    </p>

                    <p>
                        Guided by renowned Mathematics lecturer <strong>Samitha Fernando</strong>,
                        Maxicon has become a trusted platform for students and parents seeking quality
                        Mathematics education, structured assessment systems, and continuous academic progress.
                        Our mission is to nurture logical thinking, analytical abilities, and a lifelong
                        passion for learning Mathematics.
                    </p>

                    <ul class="list-unstyled mt-4">
                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2" style="color:#f57c00;"></i>
                            Comprehensive Mathematics programs for Grade 6–11 students
                        </li>

                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2" style="color:#f57c00;"></i>
                            Regular examinations and performance tracking for parents
                        </li>

                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2" style="color:#f57c00;"></i>
                            Development of logical thinking and mathematical problem-solving skills
                        </li>

                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2" style="color:#f57c00;"></i>
                            Online and physical examination platforms for continuous learning
                        </li>

                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2" style="color:#f57c00;"></i>
                            Revision programs designed to improve O/L Mathematics results
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <img src="assets/img/ds.png"
                     class="img-fluid rounded-4 "
                     alt="Maxicon Institute Sri Lanka - Mathematics Education Center">
            </div>
        </div>
    </div>
</section>

    <section id="timeline" class="apex-section apex-section-light">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h2 class="apex-section-title">Weekly <span class="apex-title-orange">Timeline</span></h2>
                <div class="apex-underline"></div>
                <p class="text-muted">Samitha Sir's Class schedules for Maxicon Eco and Maxicon Old centers.</p>
            </div>
            <div class="row g-4 mt-4">
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="apex-glass-card h-100">
                        <h3 class="mb-3">Place: Maxicon Eco</h3>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=Maxicon+Eco" target="_blank" rel="noopener noreferrer" class="apex-btn-outline-glow mb-4 d-inline-flex align-items-center">
                            <i class="fas fa-map-marker-alt me-2"></i> View Google Map Directions
                        </a>
                        <ul class="timeline-list">
                            <li><strong>Grade 6 - Sinhala medium</strong><span class="timeline-time">SATURDAY 3.35 - 5.35 PM</span></li>
                            <li><strong>Grade 7 - Sinhala medium</strong><span class="timeline-time">SATURDAY 5.30 - 7.30 PM</span></li>
                            <li><strong>Grade 8 - Sinhala medium</strong><span class="timeline-time">SATURDAY 11.30 - 1.00 PM</span></li>
                            <li><strong>Grade 9 - Sinhala medium</strong><span class="timeline-time">SATURDAY 1.30 - 3.30 PM</span></li>
                            <li><strong>Grade 10 - Sinhala medium</strong><span class="timeline-time">TUESDAY 5.45 - 9.45 PM</span></li>
                            <li><strong>Grade 11 - Sinhala medium</strong><span class="timeline-time">TUESDAY 3.30 - 5.30 PM</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="apex-glass-card h-100">
                        <h3 class="mb-3">Place: Maxicon Old</h3>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=Maxicon+Old" target="_blank" rel="noopener noreferrer" class="apex-btn-outline-glow mb-4 d-inline-flex align-items-center">
                            <i class="fas fa-map-marker-alt me-2"></i> View Google Map Directions
                        </a>
                        <ul class="timeline-list">
                            <li><strong>Grade 6 - English medium</strong><span class="timeline-time">MONDAY 5.30 - 7.30 PM</span></li>
                            <li><strong>Grade 7 - English medium</strong><span class="timeline-time">SATURDAY 7.30 - 9.30 AM</span></li>
                            <li><strong>Grade 8 - English medium</strong><span class="timeline-time">SATURDAY 7.30 - 9.00 AM</span></li>
                            <li><strong>Grade 9 - English medium</strong><span class="timeline-time">THURSDAY 5.45 - 9.45 PM</span></li>
                            <li><strong>Grade 10 - English medium</strong><span class="timeline-time">TUESDAY 5.45 - 9.45 PM</span></li>
                            <li><strong>Grade 11 - English medium</strong><span class="timeline-time">THURSDAY 3.30 - 5.30 PM</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills, Counts, Services, Gallery, FAQ, Contact sections remain fully intact from original but excluded for brevity, they work perfectly -->
    <section class="apex-section bg-dark">
        <div class="container ">
            <div class="text-center" data-aos="fade-up">
                <h2 class="apex-section-title text-light">Students' <span class="apex-title-orange">Performance</span></h2>
                <div class="apex-underline"></div>
            </div>
            <div class="row">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="skill mb-2 text-light">Grade A+ Achievers <i class="val float-end">95%</i></div>
                    <div class="progress mb-4"><div class="progress-bar" style="width:95%">95%</div></div>
                    <div class="skill mb-2 text-light">Grade B+ Achievers <i class="val float-end">85%</i></div>
                    <div class="progress mb-4"><div class="progress-bar" style="width:85%">85%</div></div>
                    <div class="skill mb-2 text-light">Grade C+ Achievers <i class="val float-end">80%</i></div>
                    <div class="progress mb-4"><div class="progress-bar" style="width:80%">80%</div></div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="skill mb-2 text-light">Grade S+ Achievers <i class="val float-end">5%</i></div>
                    <div class="progress mb-4"><div class="progress-bar" style="width:5%">5%</div></div>
                    <div class="skill mb-2 text-light">Passed Exams Rate <i class="val float-end">95%</i></div>
                    <div class="progress mb-4"><div class="progress-bar" style="width:95%">95%</div></div>
                    <div class="skill mb-2 text-light">Overall Success Rate <i class="val float-end">98%</i></div>
                    <div class="progress mb-4"><div class="progress-bar" style="width:98%">98%</div></div>
                </div>
            </div>
        </div>
    </section>

    <section class="apex-section apex-section-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100"><div class="count-box"><i class="fas fa-users"></i><span>10000+</span><p>Children Involved</p></div></div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200"><div class="count-box"><i class="fas fa-file-alt"></i><span>250+</span><p>Exams Conducted</p></div></div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300"><div class="count-box"><i class="fas fa-book"></i><span>600+</span><p>Lessons Covered</p></div></div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400"><div class="count-box"><i class="fas fa-chart-line"></i><span>150+</span><p>Paper Series</p></div></div>
            </div>
        </div>
    </section>

    <section id="services" class="apex-section">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h2 class="apex-section-title">Our <span class="apex-title-orange">Premium Services</span></h2>
                <div class="apex-underline"></div>
            </div>
            <div class="row g-4 mt-3">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100"><div class="icon-box"><div class="icon"><i class="fas fa-eye"></i></div><h4>View Results</h4><p>Check exam results instantly with index number</p></div></div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200"><div class="icon-box"><div class="icon"><i class="fas fa-edit"></i></div><h4>Recorrection Request</h4><p>Request paper recorrection online</p></div></div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300"><div class="icon-box"><div class="icon"><i class="fas fa-file-pdf"></i></div><h4>Past Papers</h4><p>Access previous exam papers</p></div></div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400"><div class="icon-box"><div class="icon"><i class="fas fa-chart-bar"></i></div><h4>Result Summary</h4><p>Detailed performance analytics</p></div></div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500"><div class="icon-box"><div class="icon"><i class="fas fa-trophy"></i></div><h4>Top 10 Rankings</h4><p>Discover top performing students</p></div></div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600"><div class="icon-box"><div class="icon"><i class="fas fa-info-circle"></i></div><h4>Exam Information</h4><p>Stay updated with latest exam details</p></div></div>
            </div>
        </div>
    </section>

    <section id="portfolio" class="apex-section apex-section-light">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h2 class="apex-section-title">Exam <span class="apex-title-orange">Gallery</span></h2>
                <div class="apex-underline"></div>
                <p>How our students attend exams & write answers</p>
            </div>
            <div class="row mt-3">
                <div class="col-lg-4 col-md-6" data-aos="flip-left" data-aos-delay="100"><div class="portfolio-item"><img src="assets/img/portfolio/portfolio-1.jpg" width="400" height="260" viewBox="0 0 400 260"></div></div>
                <div class="col-lg-4 col-md-6" data-aos="flip-left" data-aos-delay="200"><div class="portfolio-item"><img src="assets/img/portfolio/portfolio-2.jpg" width="400" height="260" viewBox="0 0 400 260"></div></div>
                <div class="col-lg-4 col-md-6" data-aos="flip-left" data-aos-delay="300"><div class="portfolio-item"><img src="assets/img/portfolio/portfolio-3.jpg" width="400" height="260" viewBox="0 0 400 260"></div></div>
                <div class="col-lg-4 col-md-6" data-aos="flip-left" data-aos-delay="300"><div class="portfolio-item"><img src="assets/img/portfolio/portfolio-4.jpg" width="400" height="260" viewBox="0 0 400 260"></div></div>
                <div class="col-lg-4 col-md-6" data-aos="flip-left" data-aos-delay="400"><div class="portfolio-item"><img src="assets/img/portfolio/portfolio-5.jpg" width="400" height="260" viewBox="0 0 400 260"></div></div>
                <div class="col-lg-4 col-md-6" data-aos="flip-left" data-aos-delay="500"><div class="portfolio-item"><img src="assets/img/portfolio/portfolio-6.jpg" width="400" height="260" viewBox="0 0 400 260"></div></div>
                <div class="col-lg-4 col-md-6" data-aos="flip-left" data-aos-delay="500"><div class="portfolio-item"><img src="assets/img/portfolio/portfolio-7.jpg" width="400" height="260" viewBox="0 0 400 260"></div></div>
                <div class="col-lg-4 col-md-6" data-aos="flip-left" data-aos-delay="500"><div class="portfolio-item"><img src="assets/img/portfolio/portfolio-8.jpg" width="400" height="260" viewBox="0 0 400 260"></div></div>
                <div class="col-lg-4 col-md-6" data-aos="flip-left" data-aos-delay="500"><div class="portfolio-item"><img src="assets/img/portfolio/portfolio-9.jpg" width="400" height="260" viewBox="0 0 400 260"></div></div>
                <div class="col-lg-4 col-md-6" data-aos="flip-left" data-aos-delay="500"><div class="portfolio-item"><img src="assets/img/portfolio/portfolio-7.jpg" width="400" height="260" viewBox="0 0 400 260"></div></div>


               </div>
        </div>
    </section>

    <section id="faq" class="apex-section">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h2 class="apex-section-title">Frequently Asked <span class="apex-title-orange">Questions</span></h2>
                <div class="apex-underline"></div>
            </div>
            <div class="row justify-content-center mt-4">
                <div class="col-xl-9">
                    <div class="faq-list">
                        <div class="question">How do children view results? <i class="fas fa-chevron-down float-end"></i></div>
                        <div style="display:none; padding:20px; background:#f9f9f9; border-radius:16px; margin-top:-10px; margin-bottom:15px;">Students can get their exam information through the links given above. For this, their index number must be entered.</div>
                        <div class="question mt-2">How can I request Re-Correction for exams? <i class="fas fa-chevron-down float-end"></i></div>
                        <div style="display:none; padding:20px; background:#f9f9f9; border-radius:16px; margin-top:-10px; margin-bottom:15px;">You can get the recorrection results within 4 to 5 days after providing the requested information via the recorrection link.</div>
                        <div class="question mt-2"> How can I connect with Samitha Fernando Sir? <i class="fas fa-chevron-down float-end"></i></div>
                        <div style="display:none; padding:20px; background:#f9f9f9; border-radius:16px; margin-top:-10px;">Contact via our hotline +94 75 909 8096 or email info@maxicon.lk</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="apex-section apex-section-light">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h2 class="apex-section-title">Get in <span class="apex-title-orange">Touch</span></h2>
                <div class="apex-underline"></div>
            </div>
            <div class="row g-4">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="info-box"><i class="fas fa-map-marker-alt fa-3x mb-2" style="color:#f57c00;"></i><h3>Our Address</h3><p>145/2/2 Kandy Rd, Kiribathgoda 11600</p></div>
                    <div class="row">
                        <div class="col-6"><div class="info-box"><i class="fas fa-envelope fa-2x mb-2" style="color:#f57c00;"></i><h3>Email</h3><p>info@maxicon.lk</p></div></div>
                        <div class="col-6"><div class="info-box"><i class="fas fa-phone-alt fa-2x mb-2" style="color:#f57c00;"></i><h3>Phone</h3><p>+94 75 909 8096</p></div></div>
                    </div>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15841.142797385013!2d79.924849!3d6.9755799!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae257dcd762236d%3A0xdace87e17f1a692b!2sMaxicon%20Higher%20Education%20Institute!5e0!3m2!1sen!2slk!4v1703682021948" width="100%" height="250" style="border:0; border-radius:24px;" allowfullscreen></iframe>
                </div>
                <div class="col-lg-7" data-aos="fade-left">
                    <div class="apex-form-white">
                        <h4 class="mb-3"> Send us a message</h4>
                        <form action="" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6"><input type="text" name="name" class="form-control apex-input" placeholder="Your Name" required></div>
                                <div class="col-md-6"><input type="email" name="email" class="form-control apex-input" placeholder="Your Email" required></div>
                                <div class="col-12"><input type="text" name="subject" class="form-control apex-input" placeholder="Subject" required></div>
                                <div class="col-12"><textarea name="message" rows="15" class="form-control apex-input" placeholder="Your Message" required></textarea></div>
                                <div class="col-12"><button type="submit" name="submit_contact" class="btn apex-btn-glow w-100">Send Message <i class="fas fa-paper-plane ms-2"></i></button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="apex-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h3 class="text-white">Maxicon <span style="color:#f57c00;">.</span>Institute</h3>
                <p class="mt-3 text-white-50">145/2/2 Kandy Rd, Kiribathgoda 11600<br><strong class="text-warning">Phone:</strong> +94 75 909 8096<br><strong class="text-warning">Email:</strong> info@maxicon.lk</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-warning">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#home" class="text-white-50 text-decoration-none">Home</a></li>
                    <li><a href="#about" class="text-white-50 text-decoration-none">About</a></li>
                    <li><a href="#services" class="text-white-50 text-decoration-none">Services</a></li>
                    <li><a href="#contact" class="text-white-50 text-decoration-none">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="text-warning">Connect With Us</h5>
                <div class="d-flex gap-3 mt-2">
                    <a href="#" class="text-white-50 fs-4"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-white-50 fs-4"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white-50 fs-4"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white-50 fs-4"><i class="fab fa-linkedin"></i></a>
                </div>
                <div class="mt-4 small text-white-50">© 2025 Maxicon Exams. All rights reserved.<br>Designed by <a href="https://Apexinventives.com/" class="text-warning">apexinventives</a></div>
            </div>
        </div>
    </div>
</footer>

<div class="back-to-top" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="fas fa-arrow-up"></i></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true, offset: 100 });
    window.addEventListener('load', function() { document.getElementById('preloader').style.display = 'none'; });
    
    const heroSwiper = new Swiper('.apex-swiper-slider', {
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        speed: 800,
        effect: 'slide'
    });
    
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.apex-navbar');
        const backToTop = document.querySelector('.back-to-top');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
            backToTop.classList.add('active');
        } else {
            navbar.classList.remove('scrolled');
            backToTop.classList.remove('active');
        }
        let current = '';
        const sections = document.querySelectorAll('section');
        sections.forEach(section => {
            const top = section.offsetTop - 200;
            if (pageYOffset >= top) current = section.getAttribute('id');
        });
        document.querySelectorAll('.apex-nav-link').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) link.classList.add('active');
        });
    });
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target && this.getAttribute('href') !== '#') {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    document.querySelectorAll('.faq-list .question').forEach(q => {
        q.addEventListener('click', function() {
            const content = this.nextElementSibling;
            if (content.style.display === 'none' || !content.style.display) content.style.display = 'block';
            else content.style.display = 'none';
        });
    });
</script>
</body>
</html>