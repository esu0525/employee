<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>201 System</title>
    <link rel="icon" href="{{ asset('assets/images/HRNTP-logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #6366f1;
            --accent: #a855f7;
            --secondary: #c084fc;
            --bg-dark: #2e2c69; /* Slightly lightened indigo for better contrast */
            --bg-card: rgba(255, 255, 255, 0.05);
            --text-main: #ffffff;
            --text-muted: #cbd5e1; /* Lighter muted text */
            --geometric-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a1942; /* Deep base color */
            color: var(--text-main);
            overflow-x: hidden;
            line-height: 1.6;
            position: relative;
        }

        /* ─── Animated Background ─── */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: #1a1942;
        }

        .bg-canvas::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(99,102,241,0.5) 0%, transparent 60%),
                radial-gradient(circle at 80% 70%, rgba(168,85,247,0.4) 0%, transparent 60%),
                radial-gradient(circle at 50% 10%, rgba(139,92,246,0.3) 0%, transparent 50%),
                radial-gradient(circle at 30% 90%, rgba(79,70,229,0.4) 0%, transparent 60%);
            animation: meshShift 10s ease-in-out infinite alternate;
        }

        @keyframes meshShift {
            0%   { filter: hue-rotate(0deg) brightness(1); }
            50%  { filter: hue-rotate(15deg) brightness(1.2); }
            100% { filter: hue-rotate(-10deg) brightness(0.9); }
        }

        .bg-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.2) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.2) 1px, transparent 1px);
            background-size: 60px 60px;
            opacity: 0.8;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0;
            animation: particleFly linear infinite;
            z-index: 10;
        }

        @keyframes particleFly {
            0%   { opacity: 0; transform: translateY(110vh) scale(0); }
            10%  { opacity: 1; }
            90%  { opacity: 0.6; }
            100% { opacity: 0; transform: translateY(-10vh) scale(1); }
        }

        h1, h2, h3, .logo-text {
            font-family: 'Outfit', sans-serif;
        }

        /* --- Navbar --- */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 1.5rem 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            transition: all 0.3s ease;
            backdrop-filter: blur(16px);
            background: rgba(15, 23, 42, 0.4);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: white;
        }

        .logo-circle {
            width: 28px;
            height: 28px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo-circle img {
            width: 80%;
            height: 80%;
            object-fit: contain;
        }

        .logo-text {
            font-size: 1.125rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #ffffff; /* Explicitly white for readability */
            font-weight: 600;
            font-size: 0.95rem;
            transition: color 0.3s ease;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-right {
            display: flex;
            align-items: center;
        }

        .btn-login {
            padding: 0.75rem 2rem;
            background: var(--geometric-gradient);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(124, 58, 237, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(124, 58, 237, 0.6);
        }

        /* --- Hero Section --- */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 5%;
            overflow: hidden;
            background: transparent;
        }

        .hero-content {
            position: relative;
            z-index: 20;
            max-width: 900px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 2rem;
            backdrop-filter: blur(4px);
            animation: fadeInUp 0.8s ease-out;
        }

        .hero-title {
            font-size: clamp(3rem, 8vw, 5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: #ffffff;
            text-shadow: 0 4px 20px rgba(0,0,0,0.4);
            letter-spacing: -0.04em;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #f1f5f9;
            margin-bottom: 3rem;
            max-width: 600px;
            margin-inline: auto;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        .btn-primary {
            padding: 1.125rem 2.5rem;
            background: var(--geometric-gradient);
            color: white;
            text-decoration: none;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 20px 40px -10px rgba(99, 102, 241, 0.5);
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 50px -10px rgba(99, 102, 241, 0.7);
        }

        .btn-secondary {
            padding: 1.125rem 2.5rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-4px);
        }

        /* --- Sections --- */
        section {
            padding: 8rem 5%;
            position: relative;
            background: transparent;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-badge {
            font-size: 0.85rem;
            font-weight: 800;
            color: #d8b4fe; /* Light violet */
            text-transform: uppercase;
            letter-spacing: 0.25em;
            margin-bottom: 1rem;
            display: block;
            text-shadow: 0 0 15px rgba(168, 85, 247, 0.6);
        }

        .section-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #ffffff;
        }

        /* --- About Section --- */
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .about-image-container {
            position: relative;
        }

        .about-image {
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .about-card {
            position: absolute;
            bottom: -20px;
            right: -20px;
            background: rgba(30, 41, 59, 0.8);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 1rem;
            backdrop-filter: blur(8px);
        }

        .about-card-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-dark);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .about-text h3 {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            color: #ffffff;
        }

        .about-text p {
            font-size: 1.15rem;
            color: #e2e8f0; /* Lighter text for accessibility */
            margin-bottom: 2rem;
            text-shadow: 0 1px 5px rgba(0,0,0,0.2);
        }

        /* --- Services Section --- */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: rgba(255, 255, 255, 0.08);
            padding: 3.5rem 2.5rem;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .service-card:hover {
            transform: translateY(-10px);
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 25px 50px -10px rgba(0,0,0,0.4);
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--geometric-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .service-card:hover::before {
            opacity: 1;
        }

        .service-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            margin-bottom: 2rem;
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .service-card p {
            color: #f1f5f9;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .service-action {
            margin-top: auto;
        }

        .btn-service {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            transition: gap 0.3s ease;
        }

        .btn-service:hover {
            gap: 0.75rem;
        }

        /* Request Document Section in Services */
        .cta-service {
            grid-column: 1 / -1;
            background: var(--geometric-gradient);
            padding: 4rem;
            border-radius: 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            margin-top: 2rem;
        }

        .cta-info h3 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .cta-info p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .btn-cta {
            padding: 1.25rem 3rem;
            background: #ffffff;
            color: #4f46e5 !important; /* Force high contrast dark blue */
            text-decoration: none;
            border-radius: 16px;
            font-weight: 800;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            border: none;
            display: inline-block;
        }

        .btn-cta:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        /* --- Contact Section --- */
        .contact-container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border-radius: 32px;
            padding: 4rem;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 4rem;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .contact-info h3 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            color: var(--text-muted);
        }

        .contact-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.57);
            border-radius: 12px;
            color: white;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.06);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6); /* Lighter placeholder */
            opacity: 1; /* For cross-browser consistency */
        }

        .btn-submit {
            width: 100%;
            padding: 1.125rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* --- Footer --- */
        footer {
            padding: 4rem 5% 2rem;
            background: #070b14;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 4rem;
            margin-bottom: 4rem;
        }

        .footer-logo-text {
            color: var(--text-muted);
            margin-top: 1.5rem;
            max-width: 300px;
        }

        .footer-links h4 {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            text-decoration: none;
            color: var(--text-muted);
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes move {
            from { transform: translate(0, 0) rotate(0); }
            to { transform: translate(5%, 5%) rotate(5deg); }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-title { font-size: 3.5rem; }
            .about-grid { grid-template-columns: 1fr; }
            .contact-container { grid-template-columns: 1fr; }
            .cta-service { flex-direction: column; text-align: center; gap: 2rem; }
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero-title { font-size: 2.75rem; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

    <!-- Animated Background -->
    <div class="bg-canvas">
        <div class="bg-grid"></div>
    </div>
    <div id="particleContainer" style="position:fixed;inset:0;z-index:5;pointer-events:none;"></div>

    <!-- Navigation -->
    <nav id="navbar">
        <div class="nav-left">
            <a href="#" class="logo">
                <div class="logo-circle">
                    <img src="{{ asset('images/logos/HRNTP-logo.jpg') }}" alt="Logo">
                </div>
                <span class="logo-text">201 System</span>
            </a>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <a href="{{ route('login') }}" class="btn-login">Login</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <div class="hero-badge">
                <i data-lucide="shield-check" style="width:16px;"></i>
                Personnel Information & Records Management System
            </div>
            <h1 class="hero-title">201 System</h1>
            <p class="hero-subtitle">Personnel Information & Records Management System. Secure, efficient, and user-friendly management at your fingertips.</p>
            <div class="hero-actions">
                <a href="#services" class="btn-secondary">Explore Services</a>
                <a href="{{ route('portal.index') }}" class="btn-primary">Request Document</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about">
        <div class="section-header">
            <span class="section-badge" style="color: #c084fc; text-shadow: 0 0 15px rgba(192, 132, 252, 0.6);">About the 201 System</span>
            <h2 class="section-title">Centralized Employee Data</h2>
        </div>
        <div class="about-grid">
            <div class="about-image-container">
                <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1200" alt="Office" class="about-image">
                <div class="about-card">
                    <div class="about-card-icon">
                        <i data-lucide="shield-check"></i>
                    </div>
                    <div>
                        <h4 style="margin:0;">Secure Data</h4>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-muted);">Encrypted & Protected</p>
                    </div>
                </div>
            </div>
            <div class="about-text">
                <h3>Ensuring Record Integrity</h3>
                <p>The 201 System is dedicated to maintaining high-quality service records for personnel. We specialize in digital archival and swift document processing.</p>
                <p>Whether you're managing a small team or a large organization, our tools provide the scalability and precision required for today's professional standards.</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <i data-lucide="check-circle-2" style="color:var(--primary);"></i>
                        <span>Secure Database</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <i data-lucide="check-circle-2" style="color:var(--primary);"></i>
                        <span>Swift Document Request</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services">
        <div class="section-header">
            <span class="section-badge" style="color: #c084fc; text-shadow: 0 0 15px rgba(192, 132, 252, 0.6);">Request Services Portal</span>
            <h2 class="section-title">Official Employee Support</h2>
        </div>
        <div class="services-grid" style="display: flex; flex-direction: column; align-items: center; gap: 3rem;">
            <div class="service-card" style="max-width: 600px; width: 100%; text-align: center;">
                <div class="service-icon" style="margin-inline: auto;"><i data-lucide="file-text"></i></div>
                <h3>Official Personnel Records</h3>
                <p>Need a Service Record, Certificate of Employment, or other official personnel documents? File your request through our secure portal.</p>
                <div style="display: flex; flex-direction: column; gap: 1rem; align-items: center;">
                    <a href="{{ route('portal.index') }}" class="btn-primary" style="width: fit-content;">Start Request Procedure</a>
                    <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Public Access • No Login Required</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <div class="section-header">
            <span class="section-badge" style="color: #c084fc; text-shadow: 0 0 15px rgba(192, 132, 252, 0.6);">Support & Inquiries</span>
            <h2 class="section-title">Get in Touch</h2>
        </div>
        <div class="contact-container">
            <div class="contact-info">
                <h3>Contact Information</h3>
                <p style="color:var(--text-muted); margin-bottom: 2rem;">Have questions about the system or need technical assistance? Reach out to our team.</p>
                <div class="contact-item">
                    <div class="contact-icon"><i data-lucide="mail"></i></div>
                    <span>human.resource@depedqc.ph</span>
                </div>
                <div class="contact-item">
                    <div class="contact-icon"><i data-lucide="phone"></i></div>
                    <span>(02)8538-6900</span>
                </div>
                <div class="contact-item">
                    <div class="contact-icon"><i data-lucide="map-pin"></i></div>
                    <span>43 Nueva Ecija Street, Bago Bantay, Quezon City, Philippines</span>
                </div>
            </div>
            <div class="contact-form">
                <form>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-input" placeholder="Juan Dela Cruz">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-input" placeholder="juan@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <textarea class="form-input" rows="4" placeholder="How can we help you?"></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-grid">
            <div>
                <a href="#" class="logo">
                    <div class="logo-circle">
                        <img src="{{ asset('images/logos/HRNTP-logo.jpg') }}" alt="Logo">
                    </div>
                    <span class="logo-text">201 System</span>
                </a>
                <p class="footer-logo-text">A professional employee management platform dedicated to administrative efficiency and data integrity.</p>
            </div>
            <div class="footer-links">
                <h4>Platform</h4>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="{{ route('portal.index') }}">Portal</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Legal</h4>
                <ul>
                    <li><a href="{{ route('legal.privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('legal.terms') }}">Terms of Service</a></li>
                    <li><a href="{{ route('legal.data-protection') }}">Data Protection</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Support</h4>
                <ul>
                    <li><a href="{{ route('support.contact') }}">Contact Us</a></li>
                    <li><a href="{{ route('support.faq') }}">FAQ</a></li>
                    <li><a href="{{ route('support.documentation') }}">Documentation</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 201 System • Personnel Information & Records Management System. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('assets/js/landing.js') }}"></script>
</body>
</html>
