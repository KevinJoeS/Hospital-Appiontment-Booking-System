<?php 
$marquee_messages = [
    "<span>Joe</span> Medical Center - Your Trusted Healthcare Partner!",
    "Welcome to Joe Medical Center!",
    "Book your appointments with ease.",
    "Quality healthcare at your fingertips.",
    "Trusted by thousands of patients."
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hospital Appointment Booking System</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      background: var(--dark-bg);
      color: var(--light-text);
    }
    .hero {
      min-height: 60vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: var(--spacing-2xl) var(--spacing-lg);
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: var(--spacing-lg);
      background: linear-gradient(135deg, var(--primary), var(--accent));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-subtitle {
      font-size: 1.2rem;
      color: var(--muted-text);
      margin-bottom: var(--spacing-xl);
      max-width: 600px;
    }

    .hero-description {
      max-width: 800px;
      color: var(--light-text);
      line-height: 1.8;
      margin-bottom: var(--spacing-2xl);
      font-size: 15px;
    }

    .cta-buttons {
      display: flex;
      gap: var(--spacing-lg);
      flex-wrap: wrap;
      justify-content: center;
      margin-bottom: var(--spacing-2xl);
    }

    .feature-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: var(--spacing-lg);
      margin: var(--spacing-2xl) 0;
    }

    .feature-card {
      background: var(--dark-surface);
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      padding: 0;
      text-align: center;
      transition: var(--transition);
      overflow: hidden;
      box-shadow: var(--shadow-md);
    }

    .feature-card:hover {
      border-color: var(--primary);
      transform: translateY(-4px);
      box-shadow: 0 0 0 1px var(--primary), var(--shadow-lg);
    }

    .feature-card:hover .feature-img {
      transform: scale(1.05);
    }

    .feature-img-wrapper {
      width: 100%;
      height: 180px;
      overflow: hidden;
    }

    .feature-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .feature-card-body {
      padding: var(--spacing-lg) var(--spacing-xl) var(--spacing-xl);
    }

    .feature-title {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: var(--spacing-sm);
      color: var(--light-text);
    }

    .feature-text {
      color: var(--muted-text);
      font-size: 14px;
      line-height: 1.6;
    }

  

    .marquee {
      margin: 10px 0;
      padding-top: 10px;  
      overflow: hidden;
    }

    .marquee-content {
      display: flex;
      gap: 10px;
      animation: scroll 5s linear infinite;
      white-space: nowrap;
    }

    @keyframes scroll {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }

    .marquee-text {
      color: #64748b;
      font-weight: 600;
      letter-spacing: 1px;
    }

    .marquee-text span {
      color: var(--success);
      margin: 0 var(--spacing-sm);
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <a href="#" class="navbar-brand">
        <span>Joe</span> Medical Center
      </a>
      <div class="navbar-nav">
        <a href="doc/login.php" class="btn btn-secondary">Doctor Login</a>
        <a href="admin1/login.php" class="btn btn-secondary ">Admin Login</a>
      </div>
    </div>
  </nav>

  <div class="marquee">
      <div class="marquee-content">
        <?php for($i = 0; $i < 2; $i++): ?>
            <?php foreach($marquee_messages as $message): ?>
                <span class="marquee-text"><?php echo $message; ?></span>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
  </div>

  <section class="hero">
    <h1>Welcome to <span>Joe</span> Medical Center</h1>
    <p class="hero-subtitle">Book appointments with ease, manage your healthcare efficiently</p>
    <div class="hero-description">
      <p>
        Joe Medical Center is a trusted healthcare facility dedicated to providing quality medical services and compassionate patient care. 
        Our hospital brings together experienced doctors, modern medical practices, and efficient patient management to ensure the best healthcare experience for everyone.
        We believe that accessible healthcare is essential for a healthy community.
      </p>
    </div>

    <div class="cta-buttons">
      <a href="pat/reg.php" class="btn btn-primary btn-lg">Register as Patient</a>
      <a href="pat/login.php" class="btn btn-secondary btn-lg">Patient Login</a>
    </div>
  </section>
  <section class="container">
    <h2 style="text-align: center; color: var(--light-text); margin-bottom: var(--spacing-2xl);">Why Choose Joe Medical Center?</h2>
    
    <div class="feature-grid">
      <div class="feature-card">
        <div class="feature-img-wrapper">
          <img src="images/experienced_doctors.png" alt="Experienced Doctors" class="feature-img">
        </div>
        <div class="feature-card-body">
          <h3 class="feature-title">Experienced Doctors</h3>
          <p class="feature-text">Board-certified doctors with years of medical expertise and patient care experience</p>
        </div>
      </div>

      <div class="feature-card">
        <div class="feature-img-wrapper">
          <img src="images/easy_booking.png" alt="Easy Booking" class="feature-img">
        </div>
        <div class="feature-card-body">
          <h3 class="feature-title">Instant Appointment Access</h3>
          <p class="feature-text">Check availability and secure your slot instantly with real-time updates.</p>
        </div>
      </div>

      <div class="feature-card">
        <div class="feature-img-wrapper">
          <img src="images/modern_facilities.png" alt="Modern Facilities" class="feature-img">
        </div>
        <div class="feature-card-body">
          <h3 class="feature-title">Modern Facilities</h3>
          <p class="feature-text">State-of-the-art equipment and comfortable environment for patient care</p>
        </div>
      </div>

      <div class="feature-card">
        <div class="feature-img-wrapper">
          <img src="images/secure_private.png" alt="Secure and Private" class="feature-img">
        </div>
        <div class="feature-card-body">
          <h3 class="feature-title">Safe & Reliable</h3>
          <p class="feature-text">Built with strong security practices to keep your data protected at all times.</p>
        </div>
      </div>

      <div class="feature-card">
        <div class="feature-img-wrapper">
          <img src="images/support_247.png" alt="24/7 Support" class="feature-img">
        </div>
        <div class="feature-card-body">
          <h3 class="feature-title">24/7 Support</h3>
          <p class="feature-text">Round-the-clock customer support to assist with any appointment or health queries</p>
        </div>
      </div>

      <div class="feature-card">
        <div class="feature-img-wrapper">
          <img src="images/patient_care.png" alt="Patient Care" class="feature-img">
        </div>
        <div class="feature-card-body">
          <h3 class="feature-title">Patient Care</h3>
          <p class="feature-text">Compassionate care focused on your health and well-being throughout your visit</p>
        </div>
      </div>
    </div>
  </section>

  <div style="margin-top: var(--spacing-2xl); text-align: center; color: var(--muted-text); padding: var(--spacing-xl);">
    <p style="margin-bottom: 0;">Joe Medical Center © 2026. All rights reserved.</p>
  </div>

  <script src="java script/script.js"></script>
</body>
</html>
