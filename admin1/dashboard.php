<?php
session_start();
include("../config.php");

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
    exit();
}

$doctors_sql = "SELECT COUNT(*) as count FROM doctor";
$doctors_result = mysqli_query($conn, $doctors_sql);
$doctors_row = mysqli_fetch_assoc($doctors_result);
$doctors_count = $doctors_row['count'];

$patients_sql = "SELECT COUNT(*) as count FROM patient";
$patients_result = mysqli_query($conn, $patients_sql);
$patients_row = mysqli_fetch_assoc($patients_result);
$patients_count = $patients_row['count'];

$appointments_sql = "SELECT COUNT(*) as count FROM appointment";
$appointments_result = mysqli_query($conn, $appointments_sql);
$appointments_row = mysqli_fetch_assoc($appointments_result);
$appointments_count = $appointments_row['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — Joe Medical Center</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: var(--spacing-lg);
      margin-bottom: var(--spacing-2xl);
    }

    .stat-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8));
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      padding: var(--spacing-xl);
      text-align: center;
      transition: var(--transition);
    }

    .stat-card:hover {
      border-color: var(--primary);
      transform: translateY(-2px);
      box-shadow: 0 0 0 1px var(--primary), var(--shadow-lg);
    }

    .stat-icon {
      font-size: 2.5rem;
      margin-bottom: var(--spacing-md);
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: var(--spacing-sm);
    }

    .stat-label {
      color: var(--muted-text);
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .admin-menu {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: var(--spacing-lg);
      margin-bottom: var(--spacing-2xl);
    }

    .menu-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8));
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      padding: var(--spacing-xl);
      text-decoration: none;
      color: var(--light-text);
      transition: var(--transition);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: var(--spacing-md);
      text-align: center;
    }

    .menu-card:hover {
      border-color: var(--primary);
      background: rgba(14, 165, 233, 0.1);
      transform: translateY(-4px);
      box-shadow: 0 0 0 1px var(--primary), var(--shadow-lg);
    }

    .menu-icon {
      font-size: 2.5rem;
    }

    .menu-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--light-text);
    }

    .menu-description {
      font-size: 13px;
      color: var(--muted-text);
      line-height: 1.4;
    }

    .quick-actions {
      background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.8));
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      padding: var(--spacing-xl);
      margin-bottom: var(--spacing-2xl);
    }

    .quick-actions h3 {
      margin-top: 0;
      margin-bottom: var(--spacing-lg);
    }

    .action-list {
      list-style: none;
      padding: 0;
      margin: 0;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: var(--spacing-md);
    }

    .action-list li {
      padding: var(--spacing-md);
      background: rgba(15, 23, 42, 0.8);
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-md);
      transition: var(--transition);
    }

    .action-list li:hover {
      border-color: var(--primary);
      background: rgba(14, 165, 233, 0.05);
    }

    .action-list a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: var(--spacing-sm);
      transition: var(--transition);
    }

    .action-list a:hover {
      color: var(--primary-light);
      gap: var(--spacing-md);
    }

    @media (max-width: 768px) {
      .stats-grid,
      .admin-menu {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar">
    <div class="container">
      <a href="../index.php" class="navbar-brand">
        <span>Joe</span> Medical Center
      </a>
      <div class="navbar-nav">
        <span style="color: var(--muted-text); padding: var(--spacing-md);">Admin: <?php echo htmlspecialchars($_SESSION['admin']); ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container" style="padding-top: var(--spacing-xl); padding-bottom: var(--spacing-xl);">

    <!-- Header -->
    <div style="margin-bottom: var(--spacing-xl);">
      <h1>Admin <span style="color: var(--primary);">Dashboard</span></h1>
      <p style="color: var(--muted-text);">Manage doctors, patients, and appointments</p>
    </div>

    <!-- Statistics -->
    <h2 style="margin-bottom: var(--spacing-lg);">System Overview</h2>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon"><img src="/Hospital-system/images/icons/doctor.svg" alt="Doctor" class="emoji-icon"></div>
        <div class="stat-number"><?php echo $doctors_count; ?></div>
        <div class="stat-label">Doctors</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><img src="/Hospital-system/images/icons/patients.svg" alt="Patients" class="emoji-icon"></div>
        <div class="stat-number"><?php echo $patients_count; ?></div>
        <div class="stat-label">Patients</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><img src="/Hospital-system/images/icons/calendar.svg" alt="Calendar" class="emoji-icon"></div>
        <div class="stat-number"><?php echo $appointments_count; ?></div>
        <div class="stat-label">Appointments</div>
      </div>
    </div>


    <h2 style="margin-bottom: var(--spacing-lg);">Management Tools</h2>
    <div class="admin-menu">
      <a href="add-doctor.php" class="menu-card">
        <div class="menu-icon"><img src="/Hospital-system/images/icons/add.svg" alt="Add" class="emoji-icon"></div>
        <div class="menu-title">Add Doctor</div>
        <div class="menu-description">Register a new doctor to the system</div>
      </a>

      <a href="manage-doctor.php" class="menu-card">
        <div class="menu-icon"><img src="/Hospital-system/images/icons/doctor.svg" alt="Doctor" class="emoji-icon"></div>
        <div class="menu-title">Manage Doctors</div>
        <div class="menu-description">Edit, view, or delete doctor profiles</div>
      </a>

      <a href="view-appiontments.php" class="menu-card">
        <div class="menu-icon"><img src="/Hospital-system/images/icons/calendar.svg" alt="Calendar" class="emoji-icon"></div>
        <div class="menu-title">View Appointments</div>
        <div class="menu-description">Monitor all system appointments</div>
      </a>

      <a href="manage-patients.php" class="menu-card">
        <div class="menu-icon"><img src="/Hospital-system/images/icons/patients.svg" alt="Patients" class="emoji-icon"></div>
        <div class="menu-title">Manage Patients</div>
        <div class="menu-description">View and manage patient accounts</div>
      </a>
    </div>

  </div>

</body>
</html>
