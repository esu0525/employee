<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - 201 System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --secondary: #64748b;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Navbar */
        .navbar {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 1.25rem 5%;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
        }

        .btn-back {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            transition: 0.2s;
        }

        .btn-back:hover { transform: translateX(-4px); }

        /* Main Content */
        .page-container {
            max-width: 900px;
            margin: 4rem auto;
            padding: 0 2rem;
        }

        .card {
            background: var(--surface);
            border-radius: 24px;
            padding: 3.5rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .title-section { margin-bottom: 3rem; text-align: center; }
        .title-badge { 
            display: inline-block;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            padding: 0.5rem 1.25rem;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
        }

        h1 { 
            font-family: 'Outfit', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: var(--text-main);
            line-height: 1.1;
        }

        /* Content Blocks */
        section { margin-bottom: 2.5rem; }
        h2 { 
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        h2 i { color: var(--primary); width: 22px; }

        p { margin-bottom: 1.25rem; color: var(--text-muted); font-size: 1.05rem; }
        a { transition: all 0.2s ease-in-out; }
        a:hover { opacity: 0.8; text-decoration: underline; }
        ul { margin-bottom: 1.25rem; padding-left: 1.5rem; color: var(--text-muted); }
        li { margin-bottom: 0.5rem; }

        .faq-item {
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            background: var(--bg);
            border-radius: 16px;
            border: 1px solid var(--border);
        }
        .faq-q { font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; display: block; }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .contact-card {
            padding: 2rem;
            background: var(--bg);
            border-radius: 20px;
            text-align: center;
        }
        .contact-icon {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--primary);
            box-shadow: var(--shadow);
        }

        @media (max-width: 768px) {
            .page-container { margin: 2rem auto; }
            .card { padding: 2rem; }
            h1 { font-size: 2.25rem; }
            .contact-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="/" class="logo">
            <i data-lucide="shield-check" style="color:var(--primary);"></i>
            201 System
        </a>
        <a href="{{ url()->previous() }}" class="btn-back">
            <i data-lucide="arrow-left"></i>
            Go Back
        </a>
    </nav>

    <div class="page-container">
        <div class="card">
            <div class="title-section">
                @php
                    $badge = 'Legal Information';
                    if(in_array($section, ['contact', 'faq', 'documentation'])) $badge = 'System Support';
                @endphp
                <span class="title-badge">{{ $badge }}</span>
                <h1>{{ $title }}</h1>
            </div>

            @if($section == 'privacy')
                <section>
                    <h2><i data-lucide="shield-check"></i> 1. Introduction</h2>
                    <p>Your privacy is important to us. This policy explains how the 201 System ("we", "us", or "our") collects, uses, and protects your personal information in accordance with the <strong>Data Privacy Act of 2012 (Republic Act No. 10173)</strong> of the Philippines.</p>
                </section>

                <section>
                    <h2><i data-lucide="database"></i> 2. Information We Collect</h2>
                    <p>We collect personal information that you provide to us directly and information that is automatically gathered during your use of the system:</p>
                    <ul>
                        <li><strong>Personal Information:</strong> Includes your full name, employee ID, official email address, position, and uploaded personnel documents.</li>
                        <li><strong>Automatically Gathered Data:</strong> We may collect your IP address, browser type, device information, and login/logout timestamps recorded in our Audit Trail for security purposes.</li>
                        <li><strong>Cookies:</strong> We use essential session cookies to maintain your login state and provide a secure, personalized experience.</li>
                    </ul>
                </section>

                <section>
                    <h2><i data-lucide="cog"></i> 3. How We Use Information</h2>
                    <p>We use the collected information for the following purposes:</p>
                    <ul>
                        <li>To provide and maintain the personnel records services.</li>
                        <li>To process and manage your document requests.</li>
                        <li>To improve system performance and user experience.</li>
                        <li>To communicate with you regarding your account or requests.</li>
                        <li>To detect, prevent, and address technical issues or fraudulent activities.</li>
                    </ul>
                </section>

                <section>
                    <h2><i data-lucide="share-2"></i> 4. Data Sharing</h2>
                    <p>We do not sell, trade, or rent your personal data to third parties. We share information only with trusted internal departments or service providers who assist us in operating the system, or when required by law to comply with legal processes or government requests.</p>
                </section>

                <section>
                    <h2><i data-lucide="user-check"></i> 5. Your Rights & Choices</h2>
                    <p>Under the <strong>Data Privacy Act of 2012</strong>, you have the right to access, correct, or request the deletion of your personal data held within the system. You may also manage cookie settings in your browser, although disabling essential cookies may affect system functionality.</p>
                </section>

                <section>
                    <h2><i data-lucide="users"></i> 6. Children's Privacy</h2>
                    <p>Our services are not directed to individuals under the age of 18 (or 13 in general public contexts), and we do not knowingly collect personal data from minors. The system is strictly for professional personnel management.</p>
                </section>

                <section>
                    <h2><i data-lucide="refresh-cw"></i> 7. Updates</h2>
                    <p>We may update this Privacy Policy occasionally to reflect changes in our practices or for other operational, legal, or regulatory reasons. We will notify you of any significant changes by posting the new policy on the system.</p>
                </section>

                <section>
                    <h2><i data-lucide="mail"></i> 8. Contact Information</h2>
                    <p>If you have any questions or concerns regarding this Privacy Policy or our data practices, please contact us at <span style="font-weight: 700; color: var(--primary);">it-support@201system.gov.ph</span>.</p>
                </section>

            @elseif($section == 'terms')
                <section>
                    <h2><i data-lucide="check-circle"></i> 1. Acceptance of Terms</h2>
                    <p>By accessing or using the 201 System, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service. Use of the system constitutes your agreement to comply with the <strong>Cybercrime Prevention Act of 2012 (Republic Act No. 10175)</strong> and all other relevant Philippine laws.</p>
                </section>

                <section>
                    <h2><i data-lucide="user-x"></i> 2. User Guidelines & Restrictions</h2>
                    <p>Users must maintain professional conduct. In accordance with the <strong>Cybercrime Prevention Act</strong>, you are strictly prohibited from:</p>
                    <ul>
                        <li>Engaging in illegal activities or unauthorized access (Hacking).</li>
                        <li>Harassing, threatening, or infringing upon the privacy of others.</li>
                        <li>Automated data extraction or "scraping" of system records.</li>
                        <li>Distributing malicious software or attempting to disrupt system operations.</li>
                    </ul>
                </section>

                <section>
                    <h2><i data-lucide="settings"></i> 3. Account Management</h2>
                    <p>Account creation is restricted to authorized personnel. Users are responsible for the security of their passwords and for all activities under their account. The administration reserves the right to terminate accounts that violate these terms or engage in abusive behavior.</p>
                </section>

                <section>
                    <h2><i data-lucide="copyright"></i> 4. Intellectual Property</h2>
                    <p>All content, including code, design, and trademarks, are protected under the <strong>Intellectual Property Code of the Philippines (Republic Act No. 8293)</strong>. Unauthorized use or reproduction is strictly prohibited.</p>
                </section>

                <section>
                    <h2><i data-lucide="alert-triangle"></i> 5. Disclaimer of Warranties</h2>
                    <p>The service is provided "as is." We do not guarantee that the 201 System will be error-free, uninterrupted, or perfectly secure at all times.</p>
                </section>

                <section>
                    <h2><i data-lucide="shield-alert"></i> 6. Limitation of Liability</h2>
                    <p>The provider's liability for any disputes arising from the use of the system is limited to the maximum extent permitted by Philippine law.</p>
                </section>

                <section>
                    <h2><i data-lucide="link"></i> 7. Privacy Policy</h2>
                    <p>Your use of the system is also governed by our <a href="{{ route('legal.privacy') }}" style="color: var(--primary); font-weight: 700;">Privacy Policy</a>, which details our compliance with the <strong>Data Privacy Act of 2012 (RA 10173)</strong>.</p>
                </section>

                <section>
                    <h2><i data-lucide="gavel"></i> 8. Governing Law</h2>
                    <p>These terms are governed by the laws of the <strong>Republic of the Philippines</strong>. All legal matters shall be settled in the competent courts within the jurisdiction of the Philippines.</p>
                </section>

            @elseif($section == 'data_protection')
                <section>
                    <h2><i data-lucide="shield"></i> Data Privacy Standards</h2>
                    <p>We implement a comprehensive data privacy framework to protect your information. Below are the key standards and examples we follow:</p>
                </section>

                <div class="faq-item">
                    <span class="faq-q">Cookie Consent Banners</span>
                    <p>Websites and systems that ask visitors to opt-in or opt-out of data tracking before storing cookies on their device to ensure user autonomy.</p>
                </div>

                <div class="faq-item">
                    <span class="faq-q">Privacy Policies & Notices</span>
                    <p>Clear and accessible documentation outlining exactly how the organization collects, uses, and shares personal information.</p>
                </div>

                <div class="faq-item">
                    <span class="faq-q">Data Minimization</span>
                    <p>We reduce risk by strictly collecting only necessary data (e.g., official email) and skipping unnecessary personal fields like private phone numbers where possible.</p>
                </div>

                <div class="faq-item">
                    <span class="faq-q">Secure Data Disposal</span>
                    <p>Properly deleting or destroying digital data and physical files once they are no longer needed for official administrative purposes to prevent future breaches.</p>
                </div>

                <div class="faq-item">
                    <span class="faq-q">Subject Access Requests (SARs)</span>
                    <p>Giving individuals the formal right to request a copy of the personal data held on them, or to have inaccurate information corrected or deleted.</p>
                </div>

                <div class="faq-item">
                    <span class="faq-q">Health Information Privacy</span>
                    <p>Ensuring that sensitive health-related documents or medical records require explicit authorization before being released or processed.</p>
                </div>

                <div class="faq-item">
                    <span class="faq-q">Encryption in Transit</span>
                    <p>Utilizing HTTPS and secure protocols to ensure personal data (such as login credentials) is encrypted and secure while traveling over the internet.</p>
                </div>

                <div class="faq-item">
                    <span class="faq-q">Internal Data Access Control</span>
                    <p>Ensuring that only authorized SDO QC HRNTP staff and administrators can access sensitive personal information, rather than all system users.</p>
                </div>

            @elseif($section == 'contact')
                <section>
                    <h2><i data-lucide="mail"></i> Support Channels</h2>
                    <p>Facing issues? Reach out to our technical support team for assistance.</p>
                    <div class="contact-grid">
                        <div class="contact-card">
                            <div class="contact-icon"><i data-lucide="mail"></i></div>
                            <span style="font-weight:700;">Email Support</span>
                            <p style="margin-top:0.5rem; font-size:0.9rem;">it-support@201system.gov.ph</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon"><i data-lucide="phone"></i></div>
                            <span style="font-weight:700;">Phone Helpdesk</span>
                            <p style="margin-top:0.5rem; font-size:0.9rem;">(02) 8123-4567</p>
                        </div>
                    </div>
                </section>

            @elseif($section == 'faq')
                <section>
                    <h2 style="margin-bottom: 2rem; color: var(--primary);"><i data-lucide="help-circle"></i> For Public Requestors</h2>
                    <div class="faq-item">
                        <span class="faq-q">What documents can I request through the portal?</span>
                        <p>You can request official personnel documents including Service Records, Certificate of Employment (COE), and Appointment Papers. The available list is visible when you initiate a new request.</p>
                    </div>
                    <div class="faq-item">
                        <span class="faq-q">How do I receive my requested document?</span>
                        <p>Our processing is immediate. Once you have submitted your request via the portal, please proceed to the HR office in person to claim your requested document.</p>
                    </div>
                    <div class="faq-item">
                        <span class="faq-q">How long does the request process take?</span>
                        <p>We prioritize swift service and process requests as they come in. Provided all requirements are met, documents are typically available for in-person collection shortly after filing.</p>
                    </div>
                </section>

                <hr style="border:0; border-top:1px dashed var(--border); margin: 3rem 0;">

                <section>
                    <h2 style="margin-bottom: 2rem; color: var(--primary);"><i data-lucide="user-cog"></i> For System Users (Employees/Admins)</h2>
                    <div class="faq-item">
                        <span class="faq-q">What should I do if I forgot my password?</span>
                        <p>On the Login page, click the 'Forgot Password' link. Enter your registered work email, and we will send you a secure link to reset your credentials.</p>
                    </div>
                    <div class="faq-item">
                        <span class="faq-q">What should I do if I lost my email address?</span>
                        <p>Please contact the System Administrator to update your email address.</p>
                    </div>
                    <div class="faq-item">
                        <span class="faq-q">Why do I need an OTP every time I log in?</span>
                        <p>One-Time Passwords (OTP) provide an additional layer of security to protect sensitive personnel data. This ensures that only you can access your account, even if someone else knows your password.</p>
                    </div>
                    <div class="faq-item">
                        <span class="faq-q">Can I update my own personal information?</span>
                        <p>System users can only update their profile picture, email address, and password. Official information such as your Name and Role must be updated by the System Administrator to ensure data integrity.</p>
                    </div>
                </section>
            @elseif($section == 'documentation')
                <section>
                    <h2><i data-lucide="book-open"></i> System Overview</h2>
                    <p>The 201 System is a Personnel Information & Records Management System designed to digitize and streamline HR operations for personnel.</p>
                </section>
                <section>
                    <h2><i data-lucide="layers"></i> Core Modules</h2>
                    <ul>
                        <li><strong>Masterlist:</strong> Centralized database of all active personnel records.</li>
                        <li><strong>Request Portal:</strong> Public-facing interface for document filings.</li>
                        <li><strong>Audit Trail:</strong> Logs all administrative activities for security.</li>
                        <li><strong>Archive:</strong> Secure storage for retired or resigned personnel records.</li>
                    </ul>
                </section>
            @endif
        </div>
        
        <div style="text-align:center; margin-top:2rem; color:var(--text-muted); font-size:0.85rem;">
            &copy; {{ date('Y') }} 201 System • Personnel Information & Records Management System
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
