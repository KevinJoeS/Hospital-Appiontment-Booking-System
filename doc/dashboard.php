<?php
session_start();
include("../config.php");

if(!isset($_SESSION['doctor_id']))
{
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$doctor_name = $_SESSION['doctor_name'];

$update_message = "";
$action_message = "";

if(isset($_POST['update']))
{
    $availability = mysqli_real_escape_string($conn, $_POST['availability']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "UPDATE doctor SET availability='$availability'";
    if(!empty($password)) {
        $sql .= ", password='$password'";
    }
    $sql .= " WHERE doctor_id='$doctor_id'";

    if(mysqli_query($conn, $sql)) {
        $update_message = "Profile updated successfully!";
    }
}

$sql = "SELECT appointment.*, appointment.id AS appointment_id, patient.name AS patient_name, patient.phone AS patient_phone
        FROM appointment
        JOIN patient ON appointment.patient_id = patient.id
        WHERE doctor_id='$doctor_id'
        ORDER BY appointment_date DESC, appointment_time DESC";

$result = mysqli_query($conn, $sql);
$total_appointments = mysqli_num_rows($result);

// Get statistics
$upcoming_sql = "SELECT COUNT(*) as count FROM appointment WHERE doctor_id='$doctor_id' AND appointment_date >= CURDATE() AND status='Confirmed'";
$upcoming_result = mysqli_query($conn, $upcoming_sql);
$upcoming_row = mysqli_fetch_assoc($upcoming_result);
$upcoming_count = $upcoming_row['count'];

$completed_sql = "SELECT COUNT(*) as count FROM appointment WHERE doctor_id='$doctor_id' AND status='Completed'";
$completed_result = mysqli_query($conn, $completed_sql);
$completed_row = mysqli_fetch_assoc($completed_result);
$completed_count = $completed_row['count'];

$pending_sql = "SELECT COUNT(*) as count FROM appointment WHERE doctor_id='$doctor_id' AND status='Pending'";
$pending_result = mysqli_query($conn, $pending_sql);
$pending_row = mysqli_fetch_assoc($pending_result);
$pending_count = $pending_row['count'];

if(isset($_GET['status'])) {
    if($_GET['status'] === 'accepted') {
        $action_message = "Appointment accepted successfully.";
    } elseif($_GET['status'] === 'declined') {
        $action_message = "Appointment declined successfully.";
    } elseif($_GET['status'] === 'error') {
        $action_message = "Unable to update appointment status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor Dashboard — Joe Medical Center</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .dashboard-content {
      display: grid;
      grid-template-columns: 1fr 350px;
      gap: var(--spacing-lg);
      margin-top: var(--spacing-xl);
    }

    .appointments-section {
      grid-column: 1;
    }

    .sidebar {
      grid-column: 2;
      display: flex;
      flex-direction: column;
      gap: var(--spacing-lg);
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
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .appointments-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8));
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-lg);
    }

    .card-header {
      background: rgba(14, 165, 233, 0.1);
      border-bottom: 2px solid var(--primary);
      padding: var(--spacing-lg);
    }

    .card-header h2 {
      margin: 0;
      font-size: 1.3rem;
    }

    .card-body {
      padding: var(--spacing-lg);
    }

    .appointment-item {
      display: grid;
      grid-template-columns: auto 1fr auto;
      gap: var(--spacing-lg);
      padding: var(--spacing-lg);
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-md);
      margin-bottom: var(--spacing-md);
      transition: var(--transition);
    }

    .appointment-item:hover {
      border-color: var(--primary);
      background: rgba(14, 165, 233, 0.05);
    }

    .appointment-item:last-child {
      margin-bottom: 0;
    }

    .appointment-time {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .appointment-date {
      font-size: 12px;
      color: var(--muted-text);
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .appointment-hour {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary);
    }

    .appointment-details h4 {
      margin: 0 0 var(--spacing-sm) 0;
      color: var(--light-text);
    }

    .appointment-details p {
      margin: var(--spacing-sm) 0;
      font-size: 13px;
      color: var(--muted-text);
    }

    .appointment-status {
      display: flex;
      align-items: center;
    }

    .status-badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: var(--radius-full);
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: 1px solid;
    }

    .status-confirmed {
      background: rgba(16, 185, 129, 0.1);
      color: var(--success);
      border-color: var(--success);
    }

    .status-pending {
      background: rgba(245, 158, 11, 0.1);
      color: var(--warning);
      border-color: var(--warning);
    }

    .status-completed {
      background: rgba(100, 140, 180, 0.1);
      color: var(--muted-text);
      border-color: var(--muted-text);
    }

    .status-declined,
    .status-cancelled {
      background: rgba(239, 68, 68, 0.1);
      color: var(--danger);
      border-color: var(--danger);
    }

    .appointment-status {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: var(--spacing-sm);
    }

    .appointment-actions {
      display: flex;
      gap: var(--spacing-sm);
      flex-wrap: wrap;
      justify-content: flex-end;
    }

    .action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px 14px;
      border-radius: var(--radius-md);
      border: 1px solid transparent;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-decoration: none;
      transition: var(--transition);
    }

    .action-btn:hover {
      transform: translateY(-1px);
    }

    .action-btn-accept {
      background: rgba(16, 185, 129, 0.12);
      color: var(--success);
      border-color: rgba(16, 185, 129, 0.35);
    }

    .action-btn-decline {
      background: rgba(239, 68, 68, 0.12);
      color: var(--danger);
      border-color: rgba(239, 68, 68, 0.35);
    }

    .no-appointments {
      text-align: center;
      padding: var(--spacing-2xl) var(--spacing-lg);
      color: var(--muted-text);
    }

    .update-form {
      background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.8));
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      padding: var(--spacing-lg);
    }

    .update-form h3 {
      margin-top: 0;
      margin-bottom: var(--spacing-lg);
      font-size: 1.1rem;
    }

    .form-group {
      margin-bottom: var(--spacing-lg);
    }

    .form-group label {
      display: block;
      margin-bottom: var(--spacing-sm);
      font-weight: 600;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--muted-text);
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 10px 12px;
      background: rgba(15, 23, 42, 0.8);
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-md);
      color: var(--light-text);
      font-family: inherit;
      font-size: 13px;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 60px;
    }

    .success-message {
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid var(--success);
      border-radius: var(--radius-md);
      padding: var(--spacing-md);
      color: var(--success);
      margin-bottom: var(--spacing-lg);
      font-size: 13px;
      font-weight: 500;
    }

    .info-message {
      background: rgba(14, 165, 233, 0.1);
      border: 1px solid var(--primary);
      border-radius: var(--radius-md);
      padding: var(--spacing-md);
      color: var(--primary);
      margin-bottom: var(--spacing-lg);
      font-size: 13px;
      font-weight: 500;
    }

    @media (max-width: 1024px) {
      .dashboard-content {
        grid-template-columns: 1fr;
      }

      .sidebar {
        grid-column: 1;
      }

      .appointments-section {
        grid-column: 1;
      }
    }

    @media (max-width: 768px) {
      .appointment-item {
        grid-template-columns: 1fr;
        gap: var(--spacing-md);
      }

      .appointment-time {
        grid-column: 1;
      }

      .appointment-status {
        align-items: flex-start;
      }

      .appointment-actions {
        justify-content: flex-start;
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
        <span style="color: var(--muted-text); padding: var(--spacing-md);">Dr. <?php echo htmlspecialchars($doctor_name); ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container" style="padding-top: var(--spacing-xl); padding-bottom: var(--spacing-xl);">

    <!-- Header -->
    <div style="margin-bottom: var(--spacing-xl);">
      <h1>Welcome back, <span style="color: var(--primary);">Dr. <?php echo htmlspecialchars($doctor_name); ?></span></h1>
      <p style="color: var(--muted-text); margin-bottom: 0;">Manage your schedule and appointments</p>
    </div>

    <?php if($update_message !== ""): ?>
      <div class="success-message"><img src="/Hospital-system/images/icons/check.svg" alt="Check" class="emoji-icon"> <?php echo $update_message; ?></div>
    <?php endif; ?>

    <?php if($action_message !== ""): ?>
      <div class="<?php echo isset($_GET['status']) && $_GET['status'] === 'error' ? 'info-message' : 'success-message'; ?>">
        <img src="/Hospital-system/images/icons/check.svg" alt="Status" class="emoji-icon"> <?php echo $action_message; ?>
      </div>
    <?php endif; ?>

    <!-- Dashboard Content -->
    <div class="dashboard-content">

      <!-- Appointments Section -->
      <div class="appointments-section">
        <div class="appointments-card">
          <div class="card-header">
            <h2>Your Appointments</h2>
          </div>
          <div class="card-body">
            <?php if($total_appointments == 0): ?>
              <div class="no-appointments">
                <p style="font-size: 2rem; margin-bottom: var(--spacing-md);"><img src="/Hospital-system/images/icons/calendar.svg" alt="Calendar" class="emoji-icon"></p>
                <p>No appointments scheduled</p>
              </div>
            <?php else: ?>
              <?php mysqli_data_seek($result, 0); ?>
              <?php while($row = mysqli_fetch_assoc($result)): ?>
                <?php
                  $date = new DateTime($row['appointment_date']);
                  $day = $date->format('M d');
                  $time = date('H:i', strtotime($row['appointment_time']));
                  $status = strtolower($row['status']);
                  $status_class = 'status-' . $status;
                ?>
                <div class="appointment-item">
                  <div class="appointment-time">
                    <div class="appointment-date"><?php echo $day; ?></div>
                    <div class="appointment-hour"><?php echo $time; ?></div>
                  </div>
                  <div class="appointment-details">
                    <h4><?php echo htmlspecialchars($row['patient_name']); ?></h4>
                    <p><img src="/Hospital-system/images/icons/phone.svg" alt="Phone" class="emoji-icon"> <?php echo htmlspecialchars($row['patient_phone']); ?></p>
                  </div>
                  <div class="appointment-status">
                    <span class="status-badge <?php echo $status_class; ?>">
                      <?php echo ucfirst($status); ?>
                    </span>
                    <?php if($status === 'pending'): ?>
                      <div class="appointment-actions">
                        <a class="action-btn action-btn-accept" href="update-appointment-status.php?id=<?php echo (int)$row['appointment_id']; ?>&status=Confirmed">Accept</a>
                        <a class="action-btn action-btn-decline" href="update-appointment-status.php?id=<?php echo (int)$row['appointment_id']; ?>&status=Declined">Decline</a>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Statistics -->
        <div class="stat-card">
          <div class="stat-icon"><img src="/Hospital-system/images/icons/bell.svg" alt="Pending" class="emoji-icon"></div>
          <div class="stat-number"><?php echo $pending_count; ?></div>
          <div class="stat-label">Pending Requests</div>
        </div>

        <div class="stat-card">
          <div class="stat-icon"><img src="/Hospital-system/images/icons/calendar.svg" alt="Calendar" class="emoji-icon"></div>
          <div class="stat-number"><?php echo $upcoming_count; ?></div>
          <div class="stat-label">Upcoming</div>
        </div>

        <div class="stat-card">
          <div class="stat-icon"><img src="/Hospital-system/images/icons/check.svg" alt="Check" class="emoji-icon"></div>
          <div class="stat-number"><?php echo $completed_count; ?></div>
          <div class="stat-label">Completed</div>
        </div>

        <div class="stat-card">
          <div class="stat-icon"><img src="/Hospital-system/images/icons/stats.svg" alt="Stats" class="emoji-icon"></div>
          <div class="stat-number"><?php echo $total_appointments; ?></div>
          <div class="stat-label">Total Appointments</div>
        </div>

        
        </div>
      </div>

    </div>

  </div>

</body>
</html>
