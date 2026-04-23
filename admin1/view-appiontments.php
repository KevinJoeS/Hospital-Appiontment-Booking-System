<?php
session_start();
include("../config.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$whereClause = "";
if ($search !== '') {
    $whereClause = "WHERE patient.name LIKE '%$search%' OR doctor.name LIKE '%$search%' OR appointment.status LIKE '%$search%'";
}

$sql = "SELECT
            appointment.id AS appointment_id,
            patient.name AS patient_name,
            doctor.name AS doctor_name,
            appointment.appointment_date,
            appointment.appointment_time,
            appointment.status
        FROM appointment
        JOIN patient ON appointment.patient_id = patient.id
        JOIN doctor ON appointment.doctor_id = doctor.id
        $whereClause
        ORDER BY appointment.appointment_date DESC, appointment.appointment_time DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Appointments</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<main class="page-wrapper">
  <section class="doctor-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
      <h2 style="margin: 0;">All Appointments</h2>
      <form action="" method="GET" style="display: flex; gap: 10px; margin: 0;">
        <input type="text" name="search" placeholder="Search appointments..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px 12px; border: 1px solid var(--dark-border); border-radius: var(--radius-sm); width: 250px;">
        <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">Search</button>
        <?php if($search !== ''): ?>
          <a href="view-appiontments.php" class="btn btn-secondary" style="padding: 8px 16px;">Clear</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="table-scroll">
      <table class="doctor-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?php echo (int) $row['appointment_id']; ?></td>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars(date('d M Y', strtotime($row['appointment_date']))); ?></td>
                <td><?php echo htmlspecialchars(date('h:i A', strtotime($row['appointment_time']))); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                  <a href="delete-appointment.php?id=<?php echo (int) $row['appointment_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this appointment?');" style="padding: 4px 10px; font-weight: bold; border-radius: 4px;" title="Remove Appointment">&times;</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="empty-state">No appointments found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="action-row">
      <a href="dashboard.php" class="btn btn-secondary back-btn">Back</a>
    </div>
  </section>
</main>

</body>
</html>
