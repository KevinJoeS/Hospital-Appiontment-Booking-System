<?php
session_start();
include("../config.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION['patient_id'];

$patient_stmt = mysqli_prepare($conn, "SELECT name, email, phone, gender, age, address, created_at FROM patient WHERE id = ?");
mysqli_stmt_bind_param($patient_stmt, "i", $patient_id);
mysqli_stmt_execute($patient_stmt);
$patient_result = mysqli_stmt_get_result($patient_stmt);
$patient = mysqli_fetch_assoc($patient_result);
mysqli_stmt_close($patient_stmt);

if (!$patient) {
    header("Location: dashboard.php");
    exit();
}

$stats_sql = "SELECT
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_count,
    SUM(CASE WHEN status = 'Confirmed' THEN 1 ELSE 0 END) AS confirmed_count,
    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed_count,
    COUNT(*) AS total_count
  FROM appointment
  WHERE patient_id = ?";
$stats_stmt = mysqli_prepare($conn, $stats_sql);
mysqli_stmt_bind_param($stats_stmt, "i", $patient_id);
mysqli_stmt_execute($stats_stmt);
$stats_result = mysqli_stmt_get_result($stats_stmt);
$stats = mysqli_fetch_assoc($stats_result);
mysqli_stmt_close($stats_stmt);

$appointments_sql = "SELECT appointment_date, appointment_time, status
  FROM appointment
  WHERE patient_id = ?
  ORDER BY appointment_date DESC, appointment_time DESC
  LIMIT 5";
$appointments_stmt = mysqli_prepare($conn, $appointments_sql);
mysqli_stmt_bind_param($appointments_stmt, "i", $patient_id);
mysqli_stmt_execute($appointments_stmt);
$appointments = mysqli_stmt_get_result($appointments_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Record - Joe Medical Center</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .record-wrap {
      padding: var(--spacing-xl) 0;
    }

    .record-grid {
      display: grid;
      grid-template-columns: 1.1fr 0.9fr;
      gap: var(--spacing-lg);
    }

    .record-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.86), rgba(255, 255, 255, 0.78));
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      padding: var(--spacing-xl);
    }

    .record-title {
      margin-bottom: var(--spacing-lg);
    }

    .detail-list {
      display: grid;
      gap: var(--spacing-md);
    }

    .detail-item {
      padding: var(--spacing-md);
      border-radius: var(--radius-md);
      background: rgba(14, 165, 233, 0.05);
      border: 1px solid rgba(14, 165, 233, 0.12);
    }

    .detail-label {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--muted-text);
      margin-bottom: 6px;
    }

    .detail-value {
      color: var(--light-text);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: var(--spacing-md);
      margin-top: var(--spacing-lg);
    }

    .stat-box {
      padding: var(--spacing-lg);
      border-radius: var(--radius-md);
      text-align: center;
      background: rgba(34, 197, 94, 0.08);
      border: 1px solid rgba(34, 197, 94, 0.16);
    }

    .stat-box strong {
      display: block;
      font-size: 1.8rem;
      color: var(--primary);
      margin-bottom: 6px;
    }

    .appointment-list {
      display: grid;
      gap: var(--spacing-md);
      margin-top: var(--spacing-lg);
    }

    .appointment-item {
      padding: var(--spacing-md);
      border-radius: var(--radius-md);
      border: 1px solid var(--dark-border);
      background: rgba(255, 255, 255, 0.55);
      display: flex;
      justify-content: space-between;
      gap: var(--spacing-md);
      flex-wrap: wrap;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      background: rgba(14, 165, 233, 0.10);
      color: var(--primary);
    }

    @media (max-width: 900px) {
      .record-grid {
        grid-template-columns: 1fr;
      }

      .stats-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 520px) {
      .stats-grid {
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
        <a href="dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="record-wrap">
      <div style="margin-bottom: var(--spacing-xl);">
        <h1>Patient Record</h1>
        <p style="color: var(--muted-text); margin: 0;">A quick summary of your account and appointment history.</p>
      </div>

      <div class="record-grid">
        <div class="record-card">
          <div class="record-title">
            <h2 style="margin-bottom: var(--spacing-sm);"><?php echo htmlspecialchars($patient['name']); ?></h2>
            <p style="color: var(--muted-text); margin: 0;">Registered on <?php echo date('d M Y', strtotime($patient['created_at'])); ?></p>
          </div>

          <div class="detail-list">
            <div class="detail-item">
              <div class="detail-label">Email</div>
              <div class="detail-value"><?php echo htmlspecialchars($patient['email']); ?></div>
            </div>

            <div class="detail-item">
              <div class="detail-label">Phone</div>
              <div class="detail-value"><?php echo htmlspecialchars($patient['phone']); ?></div>
            </div>

            <div class="detail-item">
              <div class="detail-label">Gender</div>
              <div class="detail-value"><?php echo !empty($patient['gender']) ? htmlspecialchars($patient['gender']) : 'Not provided'; ?></div>
            </div>

            <div class="detail-item">
              <div class="detail-label">Age</div>
              <div class="detail-value"><?php echo !empty($patient['age']) ? (int) $patient['age'] . ' years' : 'Not provided'; ?></div>
            </div>

            <div class="detail-item">
              <div class="detail-label">Address</div>
              <div class="detail-value"><?php echo !empty($patient['address']) ? nl2br(htmlspecialchars($patient['address'])) : 'Not provided'; ?></div>
            </div>
          </div>
        </div>

        <div class="record-card">
          <h2 class="record-title">Appointment Overview</h2>

          <div class="stats-grid">
            <div class="stat-box">
              <strong><?php echo (int) ($stats['pending_count'] ?? 0); ?></strong>
              Pending
            </div>
            <div class="stat-box">
              <strong><?php echo (int) ($stats['confirmed_count'] ?? 0); ?></strong>
              Confirmed
            </div>
            <div class="stat-box">
              <strong><?php echo (int) ($stats['completed_count'] ?? 0); ?></strong>
              Completed
            </div>
            <div class="stat-box">
              <strong><?php echo (int) ($stats['total_count'] ?? 0); ?></strong>
              Total
            </div>
          </div>

          <h3 style="margin-top: var(--spacing-xl); margin-bottom: var(--spacing-md);">Recent Appointments</h3>
          <div class="appointment-list">
            <?php if (mysqli_num_rows($appointments) === 0): ?>
              <div class="detail-item">
                <div class="detail-value">No appointments available yet.</div>
              </div>
            <?php else: ?>
              <?php while ($appointment = mysqli_fetch_assoc($appointments)): ?>
                <div class="appointment-item">
                  <div>
                    <div style="font-weight: 600; color: var(--light-text);">
                      <?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?>
                    </div>
                    <div style="color: var(--muted-text);">
                      <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                    </div>
                  </div>
                  <span class="status-pill"><?php echo htmlspecialchars($appointment['status']); ?></span>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
