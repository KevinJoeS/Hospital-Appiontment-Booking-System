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
    $whereClause = "WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%'";
}

$sql = "SELECT id, name, email, phone, gender, age, created_at FROM patient $whereClause ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Patients</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<main class="page-wrapper">
  <section class="doctor-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
      <h2 style="margin: 0;">Manage Patients</h2>
      <form action="" method="GET" style="display: flex; gap: 10px; margin: 0;">
        <input type="text" name="search" placeholder="Search patients..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px 12px; border: 1px solid var(--dark-border); border-radius: var(--radius-sm); width: 250px;">
        <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">Search</button>
        <?php if($search !== ''): ?>
          <a href="manage-patients.php" class="btn btn-secondary" style="padding: 8px 16px;">Clear</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="table-scroll">
      <table class="doctor-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Age</th>
            <th>Joined</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?php echo (int) $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo htmlspecialchars($row['gender'] ?: 'N/A'); ?></td>
                <td><?php echo $row['age'] ? (int) $row['age'] : 'N/A'; ?></td>
                <td><?php echo htmlspecialchars(date('d M Y', strtotime($row['created_at']))); ?></td>
                <td>
                  <a href="delete-patient.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this patient? This will also remove their appointments.');" style="padding: 4px 10px; font-weight: bold; border-radius: 4px;" title="Remove Patient">&times;</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="empty-state">No patients found.</td>
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
