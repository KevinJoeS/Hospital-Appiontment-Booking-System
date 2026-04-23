<?php
session_start();
include("../config.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

$sql = "SELECT appointment.*, appointment.id AS appointment_id, doctor.name AS doctor_name, doctor.specialization
        FROM appointment
        JOIN doctor ON appointment.doctor_id = doctor.id
        WHERE appointment.patient_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $patient_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

function get_appointment_identifier($row) {
  if (isset($row['appointment_id'])) {
    return (int) $row['appointment_id'];
  }

  if (isset($row['id'])) {
    return (int) $row['id'];
  }

  return 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Appointments — Joe's Medical Center</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>

    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      padding: 48px 24px 60px;
      background:var(--dark-bg);
      color: var(--light-text);
    }

    /* ── Overlay ─────────────────────────────────────── */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background:var(--light-bg);
      backdrop-filter: blur(3px);
      -webkit-backdrop-filter: blur(3px);
      z-index: 0;
    }

    body > * { position: relative; z-index: 1; }

    /* ── Page header ─────────────────────────────────── */
    .page-header {
      text-align: center;
      margin-bottom: 36px;
    }

    .page-header .badge {
      display: inline-block;
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: #00e5c8;
      border: 1px solid rgba(0, 229, 200, 0.3);
      border-radius: 20px;
      padding: 4px 14px;
      margin-bottom: 14px;
    }

    .page-header h1 {
      font-size: 28px;
      font-weight: 600;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #e8f4ff;
      line-height: 1.2;
    }

    .page-header h1 span {
      color: #4ab8e8;
    }

    .page-header p {
      margin-top: 8px;
      font-size: 12px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: #3a6080;
    }

    /* ── Panel ───────────────────────────────────────── */
    .panel {
      width: 100%;
      max-width: 1120px;
      margin: 0 auto;
      border-radius: 14px;
      overflow: hidden;
      background: rgba(218, 223, 241, 0.82);
      border: 1px solid rgba(0, 160, 255, 0.14);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      box-shadow:
        0 0 0 1px rgba(0, 180, 255, 0.05),
        0 20px 60px rgba(119, 119, 119, 1);
    }

    /* ── Panel top bar ───────────────────────────────── */
    .panel-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 22px;
      background:var(--dark-bg);
      border-bottom: 1px solid rgba(0, 160, 255, 0.14);
    }


    .panel-topbar .panel-title {
      font-size: 11px;
      font-weight: 500;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #000000ff;
    }

    .panel-topbar .record-count {
      font-size: 11px;
      color: #2a5060;
      letter-spacing: 1px;
    }

    /* ── Table ───────────────────────────────────────── */
    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead tr {
      background: var(--dark-bg);
      border-bottom: 1px solid rgba(0, 160, 255, 0.18);
    }

    th {
      padding: 13px 20px;
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      color: #000000ff;
      text-align: left;
      white-space: nowrap;
    }

    th:first-child { padding-left: 24px; }
    th:last-child  { padding-right: 24px; text-align: center; }

    td {
      padding: 15px 20px;
      font-size: 13px;
      font-weight: 400;
      color: #000000ff;
      border-bottom: 1px solid rgba(0, 100, 180, 0.08);
      vertical-align: middle;
    }

    td:first-child { padding-left: 24px; }
    td:last-child  { padding-right: 24px; text-align: center; }

    tbody tr {
      transition: background 0.18s ease;
    }

    tbody tr:hover {
      background: rgba(0, 100, 220, 0.09);
    }

    tbody tr:last-child td {
      border-bottom: none;
    }

    /* ── Doctor name cell ────────────────────────────── */
    .doctor-cell {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .doctor-avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background:var(--dark-bg);
      border: 1px solid rgba(0, 160, 255, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 600;
      color: #000000ff;
      flex-shrink: 0;
      text-transform: uppercase;
    }

    .doctor-name {
      font-weight: 500;
      color: #000000ff;
      font-size: 13px;
    }

    /* ── Specialization pill ─────────────────────────── */
    .spec-pill {
      display: inline-block;
      padding: 3px 10px;
      font-size: 11px;
      font-weight: 500;
      border-radius: 20px;
      background:  var(--light-bg);
      border: 1px solid rgba(0, 140, 255, 0.18);
      color: #000000ff;
      white-space: nowrap;
    }

    /* ── Date & time ─────────────────────────────────── */
    .date-cell {
      color: #000000ff;
      font-size: 13px;
    }

    .time-cell {
      font-family: 'Inter', monospace;
      font-size: 13px;
      color: #000000ff;
      letter-spacing: 0.5px;
    }

    /* ── Status badge ────────────────────────────────── */
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .status-badge::before {
      content: '';
      width: 6px;
      height: 6px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .status-confirmed {
      background: rgba(43, 255, 0, 1);
      border: 1px solid rgba(141, 255, 221, 0.3);
      color: #fdfdfdff;
    }
    .status-confirmed::before { background: #03ff10ff; box-shadow: 0 0 6px #00e5a0; border-color:black; }

    .status-pending {
      background: rgba(240, 180, 40, 0.1);
      border: 1px solid rgba(240, 180, 40, 0.3);
      color: #f0b429;
    }
    .status-pending::before { background: #f0b429; box-shadow: 0 0 6px #f0b429; }

    .status-cancelled {
      background: rgba(255, 80, 80, 0.1);
      border: 1px solid rgba(255, 80, 80, 0.28);
      color: #ff6060;
    }
    .status-cancelled::before { background: #ff6060; box-shadow: 0 0 6px #ff6060; }

    .status-declined {
      background: rgba(255, 80, 80, 0.1);
      border: 1px solid rgba(255, 80, 80, 0.28);
      color: #ff6060;
    }
    .status-declined::before { background: #ff6060; box-shadow: 0 0 6px #ff6060; }

    .status-completed {
      background: rgba(100, 140, 180, 0.1);
      border: 1px solid rgba(100, 140, 180, 0.25);
      color: #5d7995;
    }
    .status-completed::before { background: #7aaabb; box-shadow: 0 0 6px #7aaabb; }

    /* fallback for other statuses */
    .status-default {
      background: rgba(100, 140, 180, 0.1);
      border: 1px solid rgba(100, 140, 180, 0.25);
      color: #7aaabb;
    }
    .status-default::before { background: #7aaabb; }

    /* ── Cancel button ───────────────────────────────── */
    .btn-cancel {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 16px;
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: #e06060;
      border: 1px solid rgba(220, 70, 70, 0.3);
      border-radius: 6px;
      background: rgba(200, 50, 50, 0.08);
      text-decoration: none;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .btn-cancel::before {
      content: '×';
      font-size: 14px;
      line-height: 1;
      font-weight: 400;
    }

    .btn-cancel:hover {
      background: rgba(220, 60, 60, 0.2);
      border-color: rgba(255, 80, 80, 0.6);
      color: #ff8080;
      transform: translateY(-1px);
    }

    /* ── Empty state ─────────────────────────────────── */
    .empty-state {
      padding: 64px 24px;
      text-align: center;
    }

    .empty-state .icon {
      font-size: 36px;
      margin-bottom: 16px;
      opacity: 0.3;
    }

    .empty-state p {
      font-size: 14px;
      color: #3a6080;
      letter-spacing: 0.5px;
    }

    /* ── Footer row ──────────────────────────────────── */
    .panel-footer {
      padding: 18px 24px;
      border-top: 1px solid rgba(0, 100, 180, 0.1);
      display: flex;
      align-items: center;
      justify-content: space-between;
      background:var(--light-bg);
    }

    .panel-footer .info {
      font-size: 11px;
      color: #2a4a60;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 22px;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #4ab8e8;
      border: 1px solid rgba(0, 180, 255, 0.25);
      border-radius: 6px;
      background: var(--light-bg);
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .btn-back::before {
      content: '←';
      font-size: 14px;
    }

    .btn-back:hover {
      background: var(--light-bg);
      border-color: rgba(0, 180, 255, 0.5);
      color: #000000ff;
      transform: translateX(-2px);
    }

  </style>
</head>
<body>

  <!-- Page Header -->
  <div class="page-header">
    <div class="badge">Patient Portal</div>
    <h1>My <span>Appointments</span></h1>
    <p>Joe's Medical Center &mdash; Appointment Registry</p>
  </div>

  <!-- Panel -->
  <div class="panel">

    

    <!-- Table -->
    <table>
      <thead>
        <tr>
          <th>Doctor</th>
          <th>Specialization</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) === 0): ?>
          <tr>
            <td colspan="6">
              <div class="empty-state">
                                <img src="/Hospital-system/images/icons/prescription.svg" alt="Prescription" class="emoji-icon">
                <p>No appointments found for your account.</p>
              </div>
            </td>
          </tr>
        <?php else: ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php
              $raw_status = strtolower(trim($row['status']));
              $status_class = in_array($raw_status, ['confirmed', 'pending', 'cancelled', 'declined', 'completed'])
                ? 'status-' . $raw_status
                : 'status-default';
              $appointment_id = get_appointment_identifier($row);
              $can_cancel = in_array($raw_status, ['pending', 'confirmed']) && $appointment_id > 0;

              $initials = '';
              $name_parts = explode(' ', trim($row['doctor_name']));
              foreach ($name_parts as $part) {
                $initials .= strtoupper(substr($part, 0, 1));
              }
              $initials = substr($initials, 0, 2);

              $formatted_date = !empty($row['appointment_date'])
                ? date('d M Y', strtotime($row['appointment_date']))
                : '—';

              $formatted_time = !empty($row['appointment_time'])
                ? date('h:i A', strtotime($row['appointment_time']))
                : '—';
            ?>
            <tr>
              <td>
                <div class="doctor-cell">
                  <div class="doctor-avatar"><?php echo htmlspecialchars($initials); ?></div>
                  <div class="doctor-name">Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></div>
                </div>
              </td>
              <td><span class="spec-pill"><?php echo htmlspecialchars($row['specialization']); ?></span></td>
              <td class="date-cell"><?php echo $formatted_date; ?></td>
              <td class="time-cell"><?php echo $formatted_time; ?></td>
              <td>
                <span class="status-badge <?php echo $status_class; ?>">
                  <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                </span>
              </td>
              <td>
                <?php if ($can_cancel): ?>
                  <a class="btn-cancel" href="cancel.php?id=<?php echo $appointment_id; ?>">
                    Cancel
                  </a>
                <?php else: ?>
                  <span style="font-size:11px;letter-spacing:1px;color:#2a5060;text-transform:uppercase;">No action</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Footer -->
    <div class="panel-footer">
      <span class="info">Joe's Medical Center &mdash; Secure Patient Portal</span>
      <a href="dashboard.php" class="btn-back">Dashboard</a>
    </div>

  </div>
<script>
  document.querySelectorAll('.btn-cancel').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      var href = this.href;
      var cell = this.parentElement;

      this.style.transition = 'all 0.3s ease';
      this.style.opacity = '0';
      this.style.transform = 'scale(0.8)';

      setTimeout(function() {
        cell.innerHTML = '<span style="font-size:11px;letter-spacing:1px;color:#2a5060;text-transform:uppercase;">Cancelled</span>';
        window.location.href = href;
      }, 300);
    });
  });
</script>
  <script src="../java script/script.js"></script>
</body>
</html>
