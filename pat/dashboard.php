<?php
session_start();
include("../config.php");

if(!isset($_SESSION['patient_id']))
{
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$patient_name = $_SESSION['patient_name'];

// Get statistics
$upcoming_sql = "SELECT COUNT(*) as count FROM appointment WHERE patient_id='$patient_id' AND status='Confirmed'";
$upcoming_result = mysqli_query($conn, $upcoming_sql);
$upcoming_row = mysqli_fetch_assoc($upcoming_result);
$upcoming_count = $upcoming_row['count'];

$completed_sql = "SELECT COUNT(*) as count FROM appointment WHERE patient_id='$patient_id' AND status='Completed'";
$completed_result = mysqli_query($conn, $completed_sql);
$completed_row = mysqli_fetch_assoc($completed_result);
$completed_count = $completed_row['count'];

$total_sql = "SELECT COUNT(*) as count FROM appointment WHERE patient_id='$patient_id'";
$total_result = mysqli_query($conn, $total_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_count = $total_row['count'];

$pending_sql = "SELECT COUNT(*) as count FROM appointment WHERE patient_id='$patient_id' AND status='Pending'";
$pending_result = mysqli_query($conn, $pending_sql);
$pending_row = mysqli_fetch_assoc($pending_result);
$pending_count = $pending_row['count'];

$success_message = "";
if(isset($_SESSION['register_success']))
{
    $success_message = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Dashboard — Joe Medical Center</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: var(--spacing-2xl);
      flex-wrap: wrap;
      gap: var(--spacing-lg);
    }

    .welcome-message h1 {
      margin-bottom: var(--spacing-md);
    }

    .welcome-message p {
      color: var(--muted-text);
    }

    .header-actions {
      display: flex;
      gap: var(--spacing-md);
      flex-wrap: wrap;
    }

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
      padding: var(--spacing-lg);
      text-align: center;
      transition: var(--transition);
    }

    .stat-card:hover {
      border-color: var(--primary);
      transform: translateY(-2px);
    }

    .stat-icon {
      font-size: 2rem;
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

    .quick-actions {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8));
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: var(--spacing-lg);
      margin-bottom: var(--spacing-2xl);
    }

    .action-card {
      background: linear-gradient(135deg,rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8));
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      padding: var(--spacing-lg);
      text-align: center;
      transition: var(--transition);
      text-decoration: none;
      color: var(--light-text);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: var(--spacing-md);
    }

    .action-card:hover {
      border-color: var(--primary);
      background: rgba(14, 165, 233, 0.1);
      transform: translateY(-2px);
    }

    .action-icon {
      font-size: 2rem;
    }

    .action-title {
      font-weight: 600;
      color: var(--light-text);
    }

    .action-description {
      font-size: 12px;
      color: var(--muted-text);
    }

    .success-alert {
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid var(--success);
      border-radius: var(--radius-md);
      padding: var(--spacing-lg);
      color: var(--success);
      margin-bottom: var(--spacing-lg);
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
    }

    .success-alert::before {
      content: '<img src="/Hospital-system/images/icons/check.svg" alt="Check" class="emoji-icon">';
      font-size: 1.5rem;
      font-weight: 700;
    }

    .recent-appointments {
      margin-bottom: var(--spacing-2xl);
    }

    .recent-appointments h3 {
      margin-bottom: var(--spacing-lg);
    }

    .no-data {
      text-align: center;
      padding: var(--spacing-2xl);
      color: var(--muted-text);
    }

    @media (max-width: 768px) {
      .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .header-actions {
        width: 100%;
      }

      .header-actions .btn {
        flex: 1;
      }

      .stats-grid,
      .quick-actions {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <div class="container">
      <a href="../index.php" class="navbar-brand">
        <span>Joe</span> Medical Center
      </a>
      <div class="navbar-nav">
       
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container" style="padding-top: var(--spacing-xl); padding-bottom: var(--spacing-xl);">

    <div class="dashboard-header">
      <div class="welcome-message">
        <h1>Welcome, <span style="color: var(--primary);"><?php echo htmlspecialchars($patient_name); ?></span></h1>
        <p>Manage your appointments and health records</p>
      </div>
      <div class="header-actions">
        <a href="book-appointment.php" class="btn btn-primary">+ Book Appointment</a>
        <a href="appointments.php" class="btn btn-secondary">View My Appointments</a>
      </div>
    </div>

    <!-- Success Message -->
    <?php if($success_message !== ""): ?>
      <div class="success-alert">
        <span><?php echo $success_message; ?></span>
      </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon"><img src="/Hospital-system/images/icons/bell.svg" alt="Pending" class="emoji-icon"></div>
        <div class="stat-number"><?php echo $pending_count; ?></div>
        <div class="stat-label">Pending Approval</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><img src="/Hospital-system/images/icons/calendar.svg" alt="Calendar" class="emoji-icon"></div>
        <div class="stat-number"><?php echo $upcoming_count; ?></div>
        <div class="stat-label">Upcoming Appointments</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><img src="/Hospital-system/images/icons/check.svg" alt="Check" class="emoji-icon"></div>
        <div class="stat-number"><?php echo $completed_count; ?></div>
        <div class="stat-label">Completed Visits</div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><img src="/Hospital-system/images/icons/stats.svg" alt="Stats" class="emoji-icon"></div>
        <div class="stat-number"><?php echo $total_count; ?></div>
        <div class="stat-label">Total Appointments</div>
      </div>
    </div>

    <!-- Quick Actions -->
    <h2 style="margin-bottom: var(--spacing-xl);">Quick Actions</h2>
    <div class="quick-actions">
      <a href="book-appointment.php" class="action-card">
        <div class="action-icon"><img src="/Hospital-system/images/icons/bell.svg" alt="Bell" class="emoji-icon"></div>
        <div class="action-title">Book Appointment</div>
        <div class="action-description">Schedule with available doctors</div>
      </a>

      <a href="appointments.php" class="action-card">
        <div class="action-icon"><img src="/Hospital-system/images/icons/clipboard.svg" alt="Clipboard" class="emoji-icon"></div>
        <div class="action-title">My Appointments</div>
        <div class="action-description">View all your appointments</div>
      </a>

      <a href="profile.php" class="action-card">
        <div class="action-icon"><img src="/Hospital-system/images/icons/profile.svg" alt="Profile" class="emoji-icon"></div>
        <div class="action-title">My Profile</div>
        <div class="action-description">Manage your information</div>
      </a>

      <a href="patient-record.php" class="action-card">
        <div class="action-icon"><img src="/Hospital-system/images/icons/folder.svg" alt="Folder" class="emoji-icon"></div>
        <div class="action-title">Patient Record</div>
        <div class="action-description">View your personal record</div>
      </a>
    </div>

    <!-- Footer -->
    <div style="text-align: center; color: var(--muted-text); margin-top: var(--spacing-2xl); padding-top: var(--spacing-lg); border-top: 1px solid var(--dark-border);">
      <p>Need help? <a href="#" style="color: var(--primary); text-decoration: none;">Contact Support</a></p>
    </div>

  </div>

</body>
</html>
