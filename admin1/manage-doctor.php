<?php
session_start();
include("../config.php");

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
}

$sql = "SELECT id AS doctor_id, name, specialization, COALESCE(experience, 'N/A') AS experience, COALESCE(phone, 'N/A') AS phone FROM doctor";
$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Doctors</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<main class="page-wrapper">
  <section class="doctor-card">
    <h2>Manage Doctors</h2>

    <div class="table-scroll">
      <table class="doctor-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Specialization</th>
            <th>Experience</th>
            <th>Phone</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result && mysqli_num_rows($result) > 0):
            while($row = mysqli_fetch_assoc($result))
            {
          ?>

            <tr>
              <td><?php echo htmlspecialchars($row['doctor_id']); ?></td>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars($row['specialization']); ?></td>
              <td><?php echo htmlspecialchars($row['experience']); ?></td>
              <td><?php echo htmlspecialchars($row['phone']); ?></td>
              <td class="table-actions">
                <a href="delete-doctor.php?id=<?php echo urlencode($row['doctor_id']); ?>" class="btn btn-danger">Delete</a>
              </td>
            </tr>

          <?php
            }
          else:
          ?>
            <tr>
              <td colspan="6" class="empty-state">No doctors found.</td>
            </tr>
          <?php
          endif;
          ?>
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