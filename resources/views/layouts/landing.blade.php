<!DOCTYPE html>
<html lang="tr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'SimdiGetir - Hızlı ve Güvenilir Kurye Hizmeti. 7/24 teslimat. Zamanın paradan daha değerli olduğu anlarda yanınızdayız.')">
    <meta name="keywords" content="@yield('meta_keywords', 'kurye, moto kurye, acil kurye, araçlı kurye, istanbul kurye, hızlı teslimat, aynı gün teslimat, kurye hizmeti, 7/24 kurye, moto kurye istanbul')">
    <meta name="author" content="SimdiGetir">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'SimdiGetir - Hızlı ve Güvenilir Kurye')">
    <meta property="og:description" content="@yield('meta_description', 'Hızlı ve güvenilir kurye hizmeti. 7/24 teslimat.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="SimdiGetir">
    <meta property="og:locale" content="tr_TR">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.svg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'SimdiGetir - Hızlı ve Güvenilir Kurye')">
    <meta name="twitter:description" content="@yield('meta_description', 'Hızlı ve güvenilir kurye hizmeti. 7/24 teslimat.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.svg'))">
    
    <!-- Geo Tags (Istanbul) -->
    <meta name="geo.region" content="TR-34">
    <meta name="geo.placename" content="@yield('geo_placename', 'İstanbul')">
    <meta name="geo.position" content="@yield('geo_position', '41.0882;29.0014')">
    <meta name="ICBM" content="@yield('geo_position', '41.0882, 29.0014')">
    
    <title>@yield('title', 'SimdiGetir - Hızlı ve Güvenilir Kurye')</title>
    
    <!-- Favicon & PWA -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon-32x32.svg') }}">
    <link rel="icon" type="image/svg+xml" sizes="16x16" href="{{ asset('images/favicon-16x16.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#FF6B35">

    <!-- JSON-LD Structured Data -->
    @yield('structured_data')
    
    <!-- Google Ads & Analytics -->
    @php
        $gtagId = \Modules\Settings\Models\Setting::getValue('seo.gtag_id', '');
    @endphp
    @if($gtagId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gtagId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gtagId }}');
        @php $adsId = \Modules\Settings\Models\Setting::getValue('seo.gads_id', ''); @endphp
        @if($adsId)
        gtag('config', '{{ $adsId }}');
        @endif
        
        // Google Ads Conversion Tracking Helper
        function trackConversion(label, url) {
            gtag('event', 'conversion', {
                'send_to': '{{ $adsId }}/' + label,
                'event_callback': function() {
                    if (url) {
                        window.location = url;
                    }
                }
            });
            return false;
        }

        // Event Listeners for Ads
        document.addEventListener('DOMContentLoaded', function() {
            // Phone Clicks
            document.querySelectorAll('a[href^="tel:"]').forEach(function(el) {
                el.addEventListener('click', function() {
                    gtag('event', 'click_phone', {
                        'event_category': 'Contact',
                        'event_label': this.href
                    });
                });
            });

            // WhatsApp Clicks
            document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp.com"]').forEach(function(el) {
                el.addEventListener('click', function() {
                    gtag('event', 'click_whatsapp', {
                        'event_category': 'Contact',
                        'event_label': this.href
                    });
                });
            });
            
            // Form Submissions (Handled in submitLeadForm JS but adding global listener as backup)
            window.addEventListener('lead_submit', function(e) {
                gtag('event', 'generate_lead', {
                    'event_category': 'Form',
                    'event_label': e.detail.lead_type
                });
            });
        });
    </script>
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
        :root {
            /* AI/Tech Color Palette - Inspired by AIForge */
            --primary: #7c3aed;
            --primary-light: #a78bfa;
            --primary-dark: #5b21b6;
            --secondary: #6366f1;
            --accent: #22d3ee;
            --accent-2: #f472b6;
            --success: #10b981;
            --warning: #f59e0b;
            
            /* Dark Theme */
            --bg-dark: #0c0118;
            --bg-darker: #06000d;
            --bg-card: rgba(124, 58, 237, 0.08);
            --bg-glass: rgba(255, 255, 255, 0.03);
            --border-glass: rgba(255, 255, 255, 0.08);
            --border-glow: rgba(124, 58, 237, 0.3);
            
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-muted: rgba(255, 255, 255, 0.5);
            
            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);
            --gradient-accent: linear-gradient(135deg, #22d3ee 0%, #7c3aed 100%);
            --gradient-purple: linear-gradient(135deg, #7c3aed 0%, #6366f1 50%, #22d3ee 100%);
            --gradient-glow: radial-gradient(ellipse at center, rgba(124, 58, 237, 0.15) 0%, transparent 70%);
        }
        
        /* ===== LIGHT MODE ===== */
        [data-theme="light"] {
            --bg-dark: #f8f9fc;
            --bg-darker: #eef0f5;
            --bg-card: rgba(124, 58, 237, 0.06);
            --bg-glass: rgba(0, 0, 0, 0.03);
            --border-glass: rgba(0, 0, 0, 0.1);
            --border-glow: rgba(124, 58, 237, 0.2);
            --text-primary: #1a1a2e;
            --text-secondary: rgba(26, 26, 46, 0.7);
            --text-muted: rgba(26, 26, 46, 0.5);
        }
        
        /* Light Mode Specific Overrides */
        [data-theme="light"] .header.scrolled {
            background: rgba(248, 249, 252, 0.97);
            box-shadow: 0 5px 30px rgba(0,0,0,0.08);
        }
        
        [data-theme="light"] .mobile-menu {
            background: rgba(248, 249, 252, 0.98);
        }
        
        [data-theme="light"] .offcanvas-sidebar {
            background: linear-gradient(180deg, #f0edf6 0%, #f8f9fc 100%);
        }
        
        [data-theme="light"] .preloader {
            background: var(--bg-darker);
        }
        
        [data-theme="light"] .logo-icon {
            background: rgba(124, 58, 237, 0.08);
            border-color: rgba(124, 58, 237, 0.15);
        }
        
        [data-theme="light"] .blog-card-image::after {
            background: linear-gradient(180deg, transparent 40%, rgba(248, 249, 252, 0.9) 100%);
        }
        
        [data-theme="light"] .hero {
            background: radial-gradient(ellipse at 30% 20%, rgba(124, 58, 237, 0.08) 0%, transparent 50%),
                        radial-gradient(ellipse at 70% 80%, rgba(34, 211, 238, 0.06) 0%, transparent 50%);
        }
        
        [data-theme="light"] .hero-card {
            background: rgba(255, 255, 255, 0.7);
            border-color: rgba(0, 0, 0, 0.08);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
        }
        
        [data-theme="light"] .footer {
            background: linear-gradient(180deg, #eef0f5 0%, #e2e5ec 100%);
        }
        
        [data-theme="light"] .offcanvas-close:hover {
            background: rgba(236, 72, 153, 0.1);
        }
        
        [data-theme="light"] .testimonial-avatar {
            border-color: rgba(0, 0, 0, 0.1);
        }
        
        [data-theme="light"] .service-card,
        [data-theme="light"] .feature-card,
        [data-theme="light"] .blog-card {
            background: rgba(255, 255, 255, 0.7);
            border-color: rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        
        [data-theme="light"] .service-card:hover,
        [data-theme="light"] .feature-card:hover,
        [data-theme="light"] .blog-card:hover {
            box-shadow: 0 20px 50px rgba(124, 58, 237, 0.1);
        }
        
        [data-theme="light"] .process-section {
            background: #eef0f5;
        }
        
        [data-theme="light"] .cta-section {
            background: rgba(255, 255, 255, 0.7);
            border-color: rgba(0, 0, 0, 0.06);
        }
        
        [data-theme="light"] .form-group input,
        [data-theme="light"] .form-group textarea,
        [data-theme="light"] .form-group select {
            background: rgba(255, 255, 255, 0.8);
            border-color: rgba(0, 0, 0, 0.12);
        }
        
        [data-theme="light"] .header-top-bar {
            background: rgba(124, 58, 237, 0.05);
        }
        
        [data-theme="light"] .glass {
            background: rgba(255, 255, 255, 0.6);
        }
        
        /* Theme Toggle Button */
        .theme-toggle-btn {
            width: 45px;
            height: 45px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 50%;
            color: var(--text-primary);
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .theme-toggle-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.4);
            transform: rotate(30deg);
        }
        
        .theme-toggle-btn .icon-sun,
        .theme-toggle-btn .icon-moon {
            position: absolute;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Dark mode aktif: güneş görünür */
        .theme-toggle-btn .icon-sun {
            opacity: 1;
            transform: rotate(0deg) scale(1);
        }
        .theme-toggle-btn .icon-moon {
            opacity: 0;
            transform: rotate(180deg) scale(0);
        }
        
        /* Light mode aktif: ay görünür */
        [data-theme="light"] .theme-toggle-btn .icon-sun {
            opacity: 0;
            transform: rotate(-180deg) scale(0);
        }
        [data-theme="light"] .theme-toggle-btn .icon-moon {
            opacity: 1;
            transform: rotate(0deg) scale(1);
        }
        
        /* Smooth theme transition */
        body, .header, .header-top-bar, .header-main-bar,
        .footer, .offcanvas-sidebar, .mobile-menu,
        .service-card, .feature-card, .blog-card,
        .hero-card, .hero, .process-section,
        .cta-section, .glass, .preloader,
        .form-group input, .form-group textarea, .form-group select,
        .testimonial-btn, .offcanvas-close,
        .footer-social a, .offcanvas-social a,
        .header-sidebar-btn, .theme-toggle-btn {
            transition: background-color 0.4s ease, color 0.4s ease,
                        border-color 0.4s ease, box-shadow 0.4s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            line-height: 1.7;
            overflow-x: hidden;
        }
        
        /* Preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-darker);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 9999;
            transition: opacity 0.5s, visibility 0.5s;
        }
        
        .preloader.loaded {
            opacity: 0;
            visibility: hidden;
        }
        
        .preloader-text {
            display: flex;
            gap: 0.1rem;
            font-size: 3rem;
            font-weight: 800;
        }
        
        .preloader-text span {
            display: inline-block;
            animation: bounce 1.4s infinite;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .preloader-text span:nth-child(1) { animation-delay: 0s; }
        .preloader-text span:nth-child(2) { animation-delay: 0.1s; }
        .preloader-text span:nth-child(3) { animation-delay: 0.2s; }
        .preloader-text span:nth-child(4) { animation-delay: 0.3s; }
        .preloader-text span:nth-child(5) { animation-delay: 0.4s; }
        .preloader-text span:nth-child(6) { animation-delay: 0.5s; }
        .preloader-text span:nth-child(7) { animation-delay: 0.6s; }
        .preloader-text span:nth-child(8) { animation-delay: 0.7s; }
        .preloader-text span:nth-child(9) { animation-delay: 0.8s; }
        .preloader-text span:nth-child(10) { animation-delay: 0.9s; }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); opacity: 0.5; }
            50% { transform: translateY(-15px); opacity: 1; }
        }
        
        .preloader-subtitle {
            margin-top: 1rem;
            color: var(--text-muted);
            font-size: 1rem;
        }
        
        /* Custom Cursor */
        .cursor-outer {
            position: fixed;
            width: 40px;
            height: 40px;
            border: 2px solid var(--primary);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9998;
            transition: transform 0.15s, opacity 0.15s;
            opacity: 0.5;
        }
        
        .cursor-inner {
            position: fixed;
            width: 8px;
            height: 8px;
            background: var(--accent);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9998;
            transition: transform 0.1s;
        }
        
        /* Marquee Section - AIForge Style */
        .marquee-section {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            overflow: hidden;
            padding: 1.25rem 0;
            position: relative;
        }
        
        .marquee-wrapper {
            display: flex;
            animation: marqueeScroll 20s linear infinite;
            width: max-content;
        }
        
        .marquee-group {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        
        .marquee-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0 2rem;
            white-space: nowrap;
            font-weight: 800;
            font-size: 1.5rem;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .marquee-item i {
            color: rgba(255,255,255,0.7);
            font-size: 0.875rem;
        }
        
        @keyframes marqueeScroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        
        /* Testimonial Section */
        .testimonial-section {
            padding: 6rem 0;
            background: linear-gradient(180deg, rgba(124, 58, 237, 0.05) 0%, var(--bg-dark) 100%);
            position: relative;
        }
        
        .testimonial-slider {
            position: relative;
            overflow: hidden;
        }
        
        .testimonial-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .testimonial-slide {
            min-width: 100%;
            padding: 0 1rem;
        }
        
        .testimonial-card {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 3rem;
            align-items: center;
        }
        
        .testimonial-avatar {
            width: 100%;
            aspect-ratio: 1;
            background: var(--gradient-primary);
            border-radius: 2rem;
            position: relative;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .testimonial-avatar::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.3) 100%);
        }
        
        .testimonial-content {
            padding: 2rem 0;
        }
        
        .testimonial-stars {
            display: flex;
            gap: 0.25rem;
            margin-bottom: 1.5rem;
        }
        
        .testimonial-stars i {
            color: #facc15;
            font-size: 1.125rem;
        }
        
        .testimonial-text {
            font-size: 1.25rem;
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 2rem;
            font-style: italic;
            position: relative;
            padding-left: 2rem;
        }
        
        .testimonial-text::before {
            content: '"';
            position: absolute;
            left: 0;
            top: -0.5rem;
            font-size: 3rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        
        .testimonial-author h4 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .testimonial-author span {
            color: var(--accent);
            font-size: 0.95rem;
        }
        
        .testimonial-controls {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .testimonial-btn {
            width: 50px;
            height: 50px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 50%;
            color: var(--text-primary);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .testimonial-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.4);
        }
        
        /* Process Steps Section */
        .process-section {
            padding: 6rem 0;
            background: var(--bg-darker);
            position: relative;
            overflow: hidden;
        }
        
        .process-section::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .process-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            position: relative;
        }
        
        .process-grid::before {
            content: '';
            position: absolute;
            top: 60px;
            left: 15%;
            right: 15%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), var(--accent), var(--primary), transparent);
            opacity: 0.3;
        }
        
        .process-card {
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .process-number {
            width: 80px;
            height: 80px;
            background: var(--bg-glass);
            border: 2px solid var(--border-glass);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.75rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }
        
        .process-number::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: var(--gradient-primary);
            opacity: 0.15;
            z-index: -1;
        }
        
        .process-card h3 {
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .process-card p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            max-width: 300px;
            margin: 0 auto;
        }
        
        /* Blog/News Section */
        .blog-section {
            padding: 6rem 0;
        }
        
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }
        
        .blog-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 1.5rem;
            overflow: hidden;
            transition: all 0.4s ease;
        }
        
        .blog-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 20px 50px rgba(124, 58, 237, 0.15);
        }
        
        .blog-card-image {
            width: 100%;
            height: 220px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            position: relative;
            overflow: hidden;
        }
        
        .blog-card-image::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 40%, rgba(12, 1, 24, 0.8) 100%);
        }
        
        .blog-card-content {
            padding: 1.75rem;
        }
        
        .blog-card-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        
        .blog-card-meta i {
            color: var(--primary-light);
        }
        
        .blog-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }
        
        .blog-card h3 a {
            color: var(--text-primary);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .blog-card h3 a:hover {
            color: var(--accent);
        }
        
        .blog-card .read-more {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .blog-card .read-more:hover {
            gap: 0.75rem;
        }
        
        .blog-card .read-more i {
            font-size: 0.8rem;
            transition: transform 0.3s;
        }
        
        .blog-card:hover .read-more i {
            transform: translateX(4px);
        }
        
        /* Container */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        
        .container-fluid {
            width: 100%;
            padding: 0 2rem;
        }
        
        /* Header - AIForge Style */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 0;
            transition: all 0.4s ease;
        }
        
        .header-top-bar {
            background: rgba(124, 58, 237, 0.1);
            border-bottom: 1px solid var(--border-glass);
            padding: 0.5rem 0;
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        
        .header-top-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .header-top-left {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        
        .header-top-left a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .header-top-left a:hover {
            color: var(--accent);
        }
        
        .header-top-right {
            display: flex;
            gap: 0.75rem;
        }
        
        .header-top-right a {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--text-muted);
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.85rem;
        }
        
        .header-top-right a:hover {
            color: var(--accent);
            background: var(--bg-glass);
        }
        
        .header-main-bar {
            padding: 1rem 0;
            transition: all 0.4s ease;
        }
        
        .header.scrolled {
            background: rgba(12, 1, 24, 0.97);
            backdrop-filter: blur(25px);
            box-shadow: 0 5px 30px rgba(0,0,0,0.3);
        }
        
        .header.scrolled .header-top-bar {
            display: none;
        }
        
        .header.scrolled .header-main-bar {
            padding: 0.6rem 0;
        }
        
        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .logo {
            font-size: 1.75rem;
            font-weight: 800;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        
        .logo-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logo-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--accent);
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(124, 58, 237, 0.3);
        }
        
        .logo-icon::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.2) 0%, transparent 100%);
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(124, 58, 237, 0.4); }
            50% { box-shadow: 0 0 40px rgba(124, 58, 237, 0.6), 0 0 60px rgba(34, 211, 238, 0.3); }
        }
        
        .nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.4rem 0;
        }
        
        .nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--gradient-primary);
            transition: width 0.3s ease;
        }
        
        .nav a:hover {
            color: var(--text-primary);
        }
        
        .nav a:hover::after {
            width: 100%;
        }
        
        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .header-sidebar-btn {
            width: 45px;
            height: 45px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .header-sidebar-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.4);
        }
        
        /* Offcanvas Sidebar - AIForge Style */
        .offcanvas-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            z-index: 200;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
        }
        
        .offcanvas-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .offcanvas-sidebar {
            position: fixed;
            top: 0;
            right: -420px;
            width: 400px;
            max-width: 90vw;
            height: 100vh;
            background: linear-gradient(180deg, #110125 0%, #0c0118 100%);
            border-left: 1px solid var(--border-glass);
            z-index: 201;
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            padding: 2.5rem;
        }
        
        .offcanvas-sidebar.active {
            right: 0;
        }
        
        .offcanvas-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-glass);
        }
        
        .offcanvas-close {
            width: 40px;
            height: 40px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 50%;
            color: var(--text-primary);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .offcanvas-close:hover {
            background: rgba(236, 72, 153, 0.2);
            border-color: #ec4899;
            transform: rotate(90deg);
        }
        
        .offcanvas-desc {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }
        
        .offcanvas-nav {
            list-style: none;
            margin-bottom: 2rem;
        }
        
        .offcanvas-nav li {
            border-bottom: 1px solid var(--border-glass);
        }
        
        .offcanvas-nav a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .offcanvas-nav a:hover {
            color: var(--accent);
            padding-left: 0.75rem;
        }
        
        .offcanvas-nav a i {
            font-size: 0.8rem;
            transition: transform 0.3s;
        }
        
        .offcanvas-nav a:hover i {
            transform: translateX(5px);
        }
        
        .offcanvas-contact {
            margin-bottom: 2rem;
        }
        
        .offcanvas-contact h4 {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }
        
        .offcanvas-contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        
        .offcanvas-contact-icon {
            width: 36px;
            height: 36px;
            background: var(--gradient-primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            color: white;
            flex-shrink: 0;
        }
        
        .offcanvas-contact-item a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
            font-size: 0.9rem;
        }
        
        .offcanvas-contact-item a:hover {
            color: var(--accent);
        }
        
        .offcanvas-social {
            display: flex;
            gap: 0.75rem;
        }
        
        .offcanvas-social a {
            width: 42px;
            height: 42px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .offcanvas-social a:hover {
            background: var(--gradient-primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-3px);
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.9rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.4s ease;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 25px rgba(124, 58, 237, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 35px rgba(124, 58, 237, 0.5);
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transition: left 0.6s ease;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--text-primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(124, 58, 237, 0.4);
        }
        
        .btn-accent {
            background: var(--gradient-accent);
            color: var(--bg-dark);
            font-weight: 700;
        }
        
        .btn-accent:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 35px rgba(34, 211, 238, 0.4);
        }
        
        /* Sections */
        .section {
            padding: 6rem 0;
            position: relative;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 50px;
            font-size: 0.9rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }
        
        .section-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .section-subtitle {
            font-size: 1.125rem;
            color: var(--text-secondary);
            max-width: 650px;
            margin: 0 auto;
        }
        
        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 8rem 0 4rem;
            position: relative;
            background: radial-gradient(ellipse at 30% 20%, rgba(124, 58, 237, 0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at 70% 80%, rgba(34, 211, 238, 0.1) 0%, transparent 50%);
        }
        
        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 50px;
            font-size: 0.875rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
        }
        
        .hero-badge .pulse {
            width: 8px;
            height: 8px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }
        
        .hero h1 {
            font-size: 3.75rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }
        
        .hero p {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            max-width: 520px;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }
        
        .hero-stats {
            display: flex;
            gap: 3rem;
        }
        
        .hero-stat {
            text-align: center;
        }
        
        .hero-stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-stat-label {
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        
        /* Hero Visual */
        .hero-visual {
            position: relative;
        }
        
        .hero-card {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-glass);
            border-radius: 1.5rem;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }
        
        .hero-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .ai-avatar {
            width: 55px;
            height: 55px;
            background: var(--gradient-primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            animation: pulse-glow 3s infinite;
        }
        
        .ai-status {
            flex: 1;
        }
        
        .ai-status-name {
            font-weight: 700;
            font-size: 1.125rem;
        }
        
        .ai-status-text {
            font-size: 0.875rem;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .typing-dots {
            display: flex;
            gap: 4px;
        }
        
        .typing-dots span {
            width: 6px;
            height: 6px;
            background: var(--accent);
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }
        
        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.3; }
            30% { transform: translateY(-10px); opacity: 1; }
        }
        
        .hero-card-content {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 2;
        }
        
        .hero-card-content .highlight {
            color: var(--accent);
        }
        
        .hero-card-content .success {
            color: var(--success);
        }
        
        /* Floating Elements */
        .floating-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            animation: float 8s ease-in-out infinite;
        }
        
        .floating-orb.orb-1 {
            width: 200px;
            height: 200px;
            background: var(--primary);
            opacity: 0.2;
            top: -50px;
            right: -50px;
        }
        
        .floating-orb.orb-2 {
            width: 150px;
            height: 150px;
            background: var(--accent);
            opacity: 0.15;
            bottom: -30px;
            left: -30px;
            animation-delay: -4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(10deg); }
        }
        
        /* Service Cards */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        
        .service-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 1.5rem;
            padding: 2.5rem;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.4s ease;
            transform-origin: left;
        }
        
        .service-card:hover::before {
            transform: scaleX(1);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            box-shadow: 0 25px 50px rgba(124, 58, 237, 0.15);
        }
        
        .service-card-number {
            position: absolute;
            top: 2rem;
            right: 2rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        
        .service-card-icon {
            width: 70px;
            height: 70px;
            background: var(--gradient-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            transition: all 0.4s ease;
        }
        
        .service-card:hover .service-card-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 15px 40px rgba(124, 58, 237, 0.4);
        }
        
        .service-card h3 {
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .service-card p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }
        
        .service-card-features {
            list-style: none;
        }
        
        .service-card-features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        .service-card-features li i {
            color: var(--accent);
            font-size: 0.75rem;
        }
        
        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }
        
        .feature-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 1.25rem;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.4s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            border-color: var(--accent);
            box-shadow: 0 0 40px rgba(34, 211, 238, 0.15);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .feature-card h4 {
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .feature-card p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        /* Fun Facts / Stats */
        .funfact-section {
            background: var(--gradient-primary);
            padding: 4rem 0;
        }
        
        .funfact-wrapper {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 2rem;
        }
        
        .funfact-item {
            text-align: center;
            color: white;
        }
        
        .funfact-value {
            font-size: 3rem;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 0.5rem;
        }
        
        .funfact-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        /* CTA Section */
        .cta-section {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 2rem;
            padding: 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.1) 0%, transparent 60%);
            animation: rotate 30s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .cta-content {
            position: relative;
            z-index: 1;
        }
        
        .cta-section h2 {
            font-size: 2.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .cta-section p {
            font-size: 1.125rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 1rem 1.25rem;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--text-muted);
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 25px rgba(124, 58, 237, 0.25);
        }
        
        /* Glass Card */
        .glass {
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-glass);
            border-radius: 1.5rem;
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(180deg, var(--bg-dark) 0%, var(--bg-darker) 100%);
            border-top: 1px solid var(--border-glass);
            padding: 5rem 0 2rem;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr repeat(4, 1fr);
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .footer-brand p {
            color: var(--text-secondary);
            margin-top: 1rem;
            max-width: 320px;
        }
        
        .footer-social {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .footer-social a {
            width: 45px;
            height: 45px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }
        
        .footer-social a:hover {
            background: var(--gradient-primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-3px);
        }
        
        .footer h4 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }
        
        .footer ul {
            list-style: none;
        }
        
        .footer li {
            margin-bottom: 0.75rem;
        }
        
        .footer a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--accent);
            padding-left: 5px;
        }
        
        .footer-bottom {
            border-top: 1px solid var(--border-glass);
            padding-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        
        /* WhatsApp Float */
        .whatsapp-float {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 65px;
            height: 65px;
            background: linear-gradient(135deg, #25d366 0%, #128C7E 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
            box-shadow: 0 4px 25px rgba(37, 211, 102, 0.4);
            z-index: 1000;
            text-decoration: none;
            transition: all 0.4s ease;
        }
        
        .whatsapp-float:hover {
            transform: scale(1.1) rotate(10deg);
            box-shadow: 0 8px 35px rgba(37, 211, 102, 0.5);
        }
        
        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 7rem;
            width: 50px;
            height: 50px;
            background: var(--gradient-primary);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.4s ease;
            opacity: 0;
            visibility: hidden;
        }
        
        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(124, 58, 237, 0.5);
        }
        
        /* Alert */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success);
        }
        
        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-primary);
        }
        
        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(12, 1, 24, 0.98);
            backdrop-filter: blur(20px);
            z-index: 99;
            padding: 6rem 2rem 2rem;
        }
        
        .mobile-menu.active {
            display: block;
        }
        
        .mobile-menu a {
            display: block;
            font-size: 1.5rem;
            color: var(--text-primary);
            text-decoration: none;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-glass);
            transition: all 0.3s ease;
        }
        
        .mobile-menu a:hover {
            color: var(--accent);
            padding-left: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .services-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .process-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 992px) {
            .nav {
                display: none;
            }
            
            .header-top-bar {
                display: none;
            }
            
            .testimonial-card {
                grid-template-columns: 1fr;
            }
            
            .testimonial-avatar {
                max-width: 280px;
                margin: 0 auto;
            }
        }
        
        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                margin-left: auto;
                margin-right: auto;
            }
            
            .hero-buttons {
                justify-content: center;
            }
            
            .hero-stats {
                justify-content: center;
            }
            
            .hero-visual {
                display: none;
            }
            
            .section-title {
                font-size: 2.25rem;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .cta-section {
                padding: 3rem 1.5rem;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .funfact-value {
                font-size: 2.25rem;
            }
            
            .preloader-text {
                font-size: 2rem;
            }
            
            .process-grid {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            
            .process-grid::before {
                display: none;
            }
            
            .blog-grid {
                grid-template-columns: 1fr;
            }
            
            .marquee-item {
                font-size: 1.125rem;
            }
            
            .testimonial-text {
                font-size: 1.05rem;
            }
        }
        
        /* WOW Animation Classes */
        .fadeInUp {
            animation: fadeInUp 0.8s ease forwards;
        }
        
        .fadeInLeft {
            animation: fadeInLeft 0.8s ease forwards;
        }
        
        .fadeInRight {
            animation: fadeInRight 0.8s ease forwards;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        /* Testimonial Section */
        .testimonial-section {
            padding: 6rem 0;
            background: linear-gradient(180deg, rgba(124, 58, 237, 0.05) 0%, var(--bg-dark) 100%);
            position: relative;
        }
        
        .testimonial-slider {
            position: relative;
            overflow: hidden;
        }
        
        .testimonial-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .testimonial-slide {
            min-width: 100%;
            padding: 0 1rem;
        }
        
        .testimonial-card {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 3rem;
            align-items: center;
        }
        
        .testimonial-avatar {
            width: 100%;
            aspect-ratio: 1;
            background: var(--gradient-primary);
            border-radius: 2rem;
            position: relative;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .testimonial-content {
            padding: 2rem 0;
        }
        
        .testimonial-stars {
            display: flex;
            gap: 0.25rem;
            margin-bottom: 1.5rem;
        }
        
        .testimonial-stars i {
            color: #facc15;
            font-size: 1.125rem;
        }
        
        .testimonial-text {
            font-size: 1.25rem;
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 2rem;
            font-style: italic;
            position: relative;
            padding-left: 2rem;
        }
        
        .testimonial-text::before {
            content: '"';
            position: absolute;
            left: 0;
            top: -0.5rem;
            font-size: 3rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        
        .testimonial-author h4 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .testimonial-author span {
            color: var(--accent);
            font-size: 0.95rem;
        }
        
        .testimonial-controls {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .testimonial-btn {
            width: 50px;
            height: 50px;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 50%;
            color: var(--text-primary);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .testimonial-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.4);
        }
        
        /* Process Steps Section */
        .process-section {
            padding: 6rem 0;
            background: var(--bg-darker);
            position: relative;
            overflow: hidden;
        }
        
        .process-section::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .process-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            position: relative;
        }
        
        .process-grid::before {
            content: '';
            position: absolute;
            top: 60px;
            left: 15%;
            right: 15%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), var(--accent), var(--primary), transparent);
            opacity: 0.3;
        }
        
        .process-card {
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .process-number {
            width: 80px;
            height: 80px;
            background: var(--bg-glass);
            border: 2px solid var(--border-glass);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.75rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }
        
        .process-number::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: var(--gradient-primary);
            opacity: 0.15;
            z-index: -1;
        }
        
        .process-card h3 {
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .process-card p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            max-width: 300px;
            margin: 0 auto;
        }
        
        /* Blog/News Section */
        .blog-section {
            padding: 6rem 0;
        }
        
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }
        
        .blog-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 1.5rem;
            overflow: hidden;
            transition: all 0.4s ease;
        }
        
        .blog-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 20px 50px rgba(124, 58, 237, 0.15);
        }
        
        .blog-card-image {
            width: 100%;
            height: 220px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            position: relative;
            overflow: hidden;
        }
        
        .blog-card-image::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 40%, rgba(12, 1, 24, 0.8) 100%);
        }
        
        .blog-card-content {
            padding: 1.75rem;
        }
        
        .blog-card-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        
        .blog-card-meta i {
            color: var(--primary-light);
        }
        
        .blog-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }
        
        .blog-card h3 a {
            color: var(--text-primary);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .blog-card h3 a:hover {
            color: var(--accent);
        }
        
        .blog-card .read-more {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .blog-card .read-more:hover {
            gap: 0.75rem;
        }
        
        .blog-card .read-more i {
            font-size: 0.8rem;
            transition: transform 0.3s;
        }
        
        .blog-card:hover .read-more i {
            transform: translateX(4px);
        }
        
        /* ===== WHATSAPP FLOATING BUTTON ===== */
        .whatsapp-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 999;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #25D366;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
            text-decoration: none;
            transition: all 0.3s ease;
            animation: wp-pulse 2s infinite;
        }
        .whatsapp-float:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 30px rgba(37, 211, 102, 0.6);
        }
        @keyframes wp-pulse {
            0%, 100% { box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4); }
            50% { box-shadow: 0 4px 30px rgba(37, 211, 102, 0.7); }
        }
        
        /* ===== BACK TO TOP ===== */
        .back-to-top {
            position: fixed;
            bottom: 96px;
            right: 28px;
            z-index: 998;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
        }
        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .back-to-top:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
        }
        
        /* ===== COOKIE BANNER ===== */
        .cookie-banner {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: var(--bg-darker);
            border-top: 1px solid var(--border-glass);
            padding: 1rem 0;
            transform: translateY(100%);
            transition: transform 0.4s ease;
        }
        .cookie-banner.visible {
            transform: translateY(0);
        }
        .cookie-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .cookie-text {
            flex: 1;
            font-size: 0.875rem;
            color: var(--text-secondary);
            min-width: 300px;
        }
        .cookie-text a {
            color: var(--accent);
            text-decoration: underline;
        }
        .cookie-buttons {
            display: flex;
            gap: 0.75rem;
            flex-shrink: 0;
        }
        .cookie-btn {
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .cookie-btn-accept {
            background: var(--primary);
            color: white;
        }
        .cookie-btn-accept:hover { background: var(--primary-dark); }
        .cookie-btn-info {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-glass);
        }
        .cookie-btn-info:hover { border-color: var(--primary); color: var(--primary); }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .hero-content {
                grid-template-columns: 1fr !important;
                text-align: center;
            }
            .hero-visual {
                display: none;
            }
            .hero h1 { font-size: 2.5rem; }
            .hero-stats { justify-content: center; }
            .hero-buttons { justify-content: center; }
            .services-grid { grid-template-columns: 1fr 1fr; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
        }
        
        @media (max-width: 768px) {
            .header-top-bar { display: none; }
            .nav { display: none; }
            .hero { padding: 7rem 0 3rem; min-height: auto; }
            .hero h1 { font-size: 2rem; }
            .hero-stats {
                flex-direction: column;
                gap: 0.75rem;
            }
            .section { padding: 3rem 0; }
            .section-title { font-size: 1.75rem; }
            .services-grid,
            .features-grid,
            .process-grid,
            .blog-grid,
            .benefits-grid { 
                grid-template-columns: 1fr !important; 
            }
            .footer-grid { grid-template-columns: 1fr; }
            .funfact-wrapper {
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }
            .container { padding: 0 1rem; }
            /* 2-column grids to single */
            [style*="grid-template-columns: 1fr 1fr"],
            [style*="grid-template-columns: 1fr 1.2fr"] {
                grid-template-columns: 1fr !important;
            }
            .marquee-section { display: none; }
            .whatsapp-float { bottom: 16px; right: 16px; width: 52px; height: 52px; font-size: 1.5rem; }
            .back-to-top { bottom: 80px; right: 20px; width: 38px; height: 38px; }
            .cookie-inner { flex-direction: column; text-align: center; }
            .cookie-text { min-width: auto; }
        }
        
        @media (max-width: 480px) {
            .hero h1 { font-size: 1.6rem; }
            .hero p { font-size: 0.95rem; }
            .section-title { font-size: 1.4rem; }
            .btn { padding: 0.7rem 1.2rem; font-size: 0.9rem; }
            .funfact-wrapper { grid-template-columns: 1fr 1fr; }
            .process-card, .service-card, .feature-card { padding: 1.5rem; }
        }
        
        /* ===== ACCESSIBILITY ===== */
        .skip-nav {
            position: absolute;
            top: -100%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0 0 8px 8px;
            text-decoration: none;
            font-weight: 600;
            transition: top 0.3s;
        }
        .skip-nav:focus {
            top: 0;
        }
        /* Focus-visible for keyboard navigation */
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
        /* Phone input invalid state */
        input[type="tel"]:invalid:not(:placeholder-shown) {
            border-color: #ef4444;
        }
    </style>
    
    @stack('styles')
    
    {!! \Modules\Settings\Models\Setting::getValue('marketing.gtm_head', '') !!}
</head>
<body>
    {!! \Modules\Settings\Models\Setting::getValue('marketing.gtm_body', '') !!}
    
    <!-- Skip Navigation -->
    <a href="#main-content" class="skip-nav">Ana içeriğe geç</a>
    
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-text">
            <span>S</span>
            <span>İ</span>
            <span>M</span>
            <span>D</span>
            <span>İ</span>
            <span>G</span>
            <span>E</span>
            <span>T</span>
            <span>İ</span>
            <span>R</span>
        </div>
        <div class="preloader-subtitle">Kurye Sistemi Yükleniyor...</div>
    </div>
    
    <!-- Custom Cursor -->
    <div class="cursor-outer" id="cursor-outer"></div>
    <div class="cursor-inner" id="cursor-inner"></div>
    


    <!-- Header - AIForge Style -->
    <header class="header" id="header">
        <!-- Top Bar -->
        <div class="header-top-bar">
            <div class="header-top-inner">
                <div class="header-top-left">
                    <a href="tel:+905324847292">
                        <i class="fa-solid fa-phone"></i> +90 532 484 72 92
                    </a>
                    <a href="mailto:webgetir@simdigetir.com">
                        <i class="fa-solid fa-envelope"></i> webgetir@simdigetir.com
                    </a>
                    <span><i class="fa-solid fa-clock"></i> 7/24 Aktif Hizmet</span>
                </div>
                <div class="header-top-right">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        
        <!-- Main Bar -->
        <div class="header-main-bar">
            <div class="header-inner">
                @include('components.logo', ['size' => 'md'])
                
                <nav class="nav">
                    <a href="/">Ana Sayfa</a>
                    <a href="/hakkimizda">Hakkımızda</a>
                    <a href="/hizmetler">Hizmetler</a>
                    <a href="/sss">SSS</a>
                    <a href="/iletisim">İletişim</a>
                    <a href="/kurye-basvuru">Kurye Ol</a>
                </nav>
                
                <div class="nav-right">
                    <a href="tel:+905324847292" class="btn btn-accent">
                        <i class="fa-solid fa-phone"></i> Kurye Çağır
                    </a>
                    <button class="theme-toggle-btn" id="theme-toggle" aria-label="Tema Değiştir" title="Tema Değiştir">
                        <i class="fa-solid fa-sun icon-sun"></i>
                        <i class="fa-solid fa-moon icon-moon"></i>
                    </button>
                    <button class="header-sidebar-btn" id="sidebar-toggle" aria-label="Menü">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Offcanvas Sidebar - AIForge Style -->
    <div class="offcanvas-overlay" id="offcanvas-overlay"></div>
    <div class="offcanvas-sidebar" id="offcanvas-sidebar">
        <div class="offcanvas-header">
            @include('components.logo', ['size' => 'sm'])
            <button class="offcanvas-close" id="offcanvas-close">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        
        <p class="offcanvas-desc">
            Zamanın paradan daha değerli olduğu anlarda yanınızdayız. İstanbul'un her noktasına 7/24 hızlı ve güvenilir teslimat.
        </p>
        
        <ul class="offcanvas-nav">
            <li><a href="/">Ana Sayfa <i class="fa-solid fa-arrow-right"></i></a></li>
            <li><a href="/hakkimizda">Hakkımızda <i class="fa-solid fa-arrow-right"></i></a></li>
            <li><a href="/hizmetler">Hizmetler <i class="fa-solid fa-arrow-right"></i></a></li>
            <li><a href="/sss">SSS <i class="fa-solid fa-arrow-right"></i></a></li>
            <li><a href="/iletisim">İletişim <i class="fa-solid fa-arrow-right"></i></a></li>
            <li><a href="/kurye-basvuru">Kurye Ol <i class="fa-solid fa-arrow-right"></i></a></li>
            <li><a href="/kurumsal">Kurumsal <i class="fa-solid fa-arrow-right"></i></a></li>
        </ul>
        
        <div class="offcanvas-contact">
            <h4>İletişim</h4>
            <div class="offcanvas-contact-item">
                <div class="offcanvas-contact-icon"><i class="fa-solid fa-location-dot"></i></div>
                <span style="color:var(--text-secondary); font-size:0.9rem;">Yeşilce Mah. Aytekin Sok. No:5/2 Kağıthane / İstanbul</span>
            </div>
            <div class="offcanvas-contact-item">
                <div class="offcanvas-contact-icon"><i class="fa-solid fa-envelope"></i></div>
                <a href="mailto:webgetir@simdigetir.com">webgetir@simdigetir.com</a>
            </div>
            <div class="offcanvas-contact-item">
                <div class="offcanvas-contact-icon"><i class="fa-solid fa-clock"></i></div>
                <span style="color:var(--text-secondary); font-size:0.9rem;">7/24 Aktif Hizmet</span>
            </div>
            <div class="offcanvas-contact-item">
                <div class="offcanvas-contact-icon"><i class="fa-solid fa-phone"></i></div>
                <a href="tel:+905324847292">+90 532 484 72 92</a>
            </div>
        </div>
        
        <a href="/kurumsal" class="btn btn-primary" style="width:100%; margin-bottom: 1.5rem;">
            Teklif Alın <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
        
        <div class="offcanvas-social">
            <a href="https://www.instagram.com/simdigetir" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://www.facebook.com/simdigetir" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="https://wa.me/905324847292" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
    </div>

    <!-- Main Content -->
    <main id="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    @include('components.logo', ['size' => 'md'])
                    <p>Zamanın paradan daha değerli olduğu anlarda yanınızdayız. 7/24 hızlı ve güvenilir teslimat.</p>
                    <div class="footer-social">
                        <a href="https://www.instagram.com/simdigetir" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://www.facebook.com/simdigetir" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://wa.me/905324847292" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>
                <div>
                    <h4>Hizmetler</h4>
                    <ul>
                        <li><a href="/hizmetler">Motorlu Kurye</a></li>
                        <li><a href="/hizmetler">Acil Kurye</a></li>
                        <li><a href="/hizmetler">Araçlı Kurye</a></li>
                        <li><a href="/kurumsal">Kurumsal</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Hizmet Bölgeleri</h4>
                    <ul>
                        <li><a href="/kurye">Tüm İstanbul</a></li>
                        <li><a href="/kurye/sisli">Şişli Kurye</a></li>
                        <li><a href="/kurye/besiktas">Beşiktaş Kurye</a></li>
                        <li><a href="/kurye/kadikoy">Kadıköy Kurye</a></li>
                        <li><a href="/kurye/uskudar">Üsküdar Kurye</a></li>
                        <li><a href="/kurye/sariyer">Sarıyer Kurye</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Şirket</h4>
                    <ul>
                        <li><a href="/hakkimizda">Hakkımızda</a></li>
                        <li><a href="/sss">SSS</a></li>
                        <li><a href="/kurye-basvuru">Kurye Ol</a></li>
                        <li><a href="/kvkk">KVKK</a></li>
                    </ul>
                </div>
                <div>
                    <h4>İletişim</h4>
                    <ul>
                        <li><i class="fa-solid fa-phone"></i> {{ \Modules\Settings\Models\Setting::getValue('contact.phone', '+90 532 484 72 92') }}</li>
                        <li><i class="fa-solid fa-envelope"></i> {{ \Modules\Settings\Models\Setting::getValue('contact.email', 'webgetir@simdigetir.com') }}</li>
                        <li><i class="fa-solid fa-location-dot"></i> Kağıthane / İstanbul</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} SimdiGetir. Tüm hakları saklıdır.</p>
                <p>Zamanın paradan daha değerli olduğu anlarda yanınızdayız.</p>
            </div>
        </div>
    </footer>



    <script>
        // Theme Toggle (run immediately to prevent flash)
        (function() {
            const savedTheme = localStorage.getItem('simdigetir-theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
        
        // Preloader
        window.addEventListener('load', () => {
            const delay = sessionStorage.getItem('simdigetir-visited') ? 300 : 1500;
            setTimeout(() => {
                document.getElementById('preloader').classList.add('loaded');
                sessionStorage.setItem('simdigetir-visited', '1');
            }, delay);
        });
        
        // Custom Cursor
        const cursorOuter = document.getElementById('cursor-outer');
        const cursorInner = document.getElementById('cursor-inner');
        
        document.addEventListener('mousemove', (e) => {
            cursorOuter.style.left = e.clientX + 'px';
            cursorOuter.style.top = e.clientY + 'px';
            cursorInner.style.left = e.clientX + 'px';
            cursorInner.style.top = e.clientY + 'px';
        });
        
        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // Offcanvas Sidebar
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const offcanvasSidebar = document.getElementById('offcanvas-sidebar');
        const offcanvasOverlay = document.getElementById('offcanvas-overlay');
        const offcanvasClose = document.getElementById('offcanvas-close');
        
        function openSidebar() {
            offcanvasSidebar.classList.add('active');
            offcanvasOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeSidebar() {
            offcanvasSidebar.classList.remove('active');
            offcanvasOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        sidebarToggle.addEventListener('click', openSidebar);
        offcanvasClose.addEventListener('click', closeSidebar);
        offcanvasOverlay.addEventListener('click', closeSidebar);
        
        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSidebar();
        });
        
        // Close sidebar on link click
        offcanvasSidebar.querySelectorAll('.offcanvas-nav a').forEach(link => {
            link.addEventListener('click', closeSidebar);
        });
        
        // Track events for GA
        function trackEvent(eventName, params = {}) {
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, params);
            }
        }
        
        // Track phone clicks
        document.querySelectorAll('a[href^="tel:"]').forEach(link => {
            link.addEventListener('click', () => trackEvent('click_call'));
        });
        
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.service-card, .feature-card, .faq-item').forEach(el => {
            el.style.opacity = '0';
            observer.observe(el);
        });
        
        // Counter Animation
        document.addEventListener('DOMContentLoaded', () => {
            const animateCounter = (element, target, duration = 2000) => {
                let start = 0;
                const frameDuration = 16;
                const totalFrames = Math.round(duration / frameDuration);
                const easeOutQuad = t => t * (2 - t);
                
                let frame = 0;
                
                const timer = setInterval(() => {
                    frame++;
                    const progress = easeOutQuad(frame / totalFrames);
                    const current = Math.floor(target * progress);
                    
                    if (frame >= totalFrames) {
                        element.textContent = target.toLocaleString('tr-TR');
                        clearInterval(timer);
                    } else {
                        element.textContent = current.toLocaleString('tr-TR');
                    }
                }, frameDuration);
            };
            
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = parseFloat(entry.target.getAttribute('data-count'));
                        if (!isNaN(target)) {
                            animateCounter(entry.target, target);
                        }
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('[data-count]').forEach(el => counterObserver.observe(el));
        });
        
        // Theme Toggle
        const themeToggleBtn = document.getElementById('theme-toggle');
        
        themeToggleBtn.addEventListener('click', () => {
            const htmlEl = document.documentElement;
            const current = htmlEl.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            htmlEl.setAttribute('data-theme', next);
            localStorage.setItem('simdigetir-theme', next);
        });
    </script>
    
    @stack('scripts')
    
    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/905324847292?text=Merhaba" class="whatsapp-float" target="_blank" rel="noopener" aria-label="WhatsApp ile iletişime geçin">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
    
    <!-- Back to Top -->
    <button class="back-to-top" id="back-to-top" aria-label="Sayfa başına dön">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
    
    <!-- Cookie/KVKK Banner -->
    <div class="cookie-banner" id="cookie-banner">
        <div class="cookie-inner">
            <p class="cookie-text">
                Bu web sitesi deneyiminizi iyileştirmek için çerezler kullanmaktadır. 
                Siteyi kullanmaya devam ederek <a href="/kvkk">KVKK Aydınlatma Metni</a>'ni kabul etmiş olursunuz.
            </p>
            <div class="cookie-buttons">
                <button class="cookie-btn cookie-btn-accept" id="cookie-accept">Kabul Et</button>
                <a href="/kvkk" class="cookie-btn cookie-btn-info">Detaylı Bilgi</a>
            </div>
        </div>
    </div>
    
    <script>
        // Back to Top
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        // Cookie Banner
        const cookieBanner = document.getElementById('cookie-banner');
        const cookieAccept = document.getElementById('cookie-accept');
        if (!localStorage.getItem('simdigetir-cookies')) {
            setTimeout(() => cookieBanner.classList.add('visible'), 1500);
        }
        cookieAccept.addEventListener('click', () => {
            localStorage.setItem('simdigetir-cookies', 'accepted');
            cookieBanner.classList.remove('visible');
        });
    </script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
