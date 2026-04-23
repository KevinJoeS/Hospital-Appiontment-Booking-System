<?php
session_start();
include("../config.php");

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION['patient_id'];
$success_message = "";
$error_message = "";
$is_edit_mode = isset($_GET['edit']) && $_GET['edit'] === '1';

if (isset($_POST['save_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = trim($_POST['gender']);
    $age = trim($_POST['age']);
    $address = trim($_POST['address']);

    if ($name === "" || $email === "" || $phone === "") {
        $error_message = "Name, email, and phone are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif ($age !== "" && (!ctype_digit($age) || (int) $age <= 0)) {
        $error_message = "Please enter a valid age.";
    } else {
        $check_stmt = mysqli_prepare($conn, "SELECT id FROM patient WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($check_stmt, "si", $email, $patient_id);
        mysqli_stmt_execute($check_stmt);
        $email_result = mysqli_stmt_get_result($check_stmt);
        $email_exists = mysqli_num_rows($email_result) > 0;
        mysqli_stmt_close($check_stmt);

        if ($email_exists) {
            $error_message = "That email address is already in use.";
        } else {
            $age_value = $age === "" ? null : (int) $age;
            $update_stmt = mysqli_prepare(
                $conn,
                "UPDATE patient SET name = ?, email = ?, phone = ?, gender = ?, age = ?, address = ? WHERE id = ?"
            );
            mysqli_stmt_bind_param($update_stmt, "ssssisi", $name, $email, $phone, $gender, $age_value, $address, $patient_id);

            if (mysqli_stmt_execute($update_stmt)) {
                $_SESSION['patient_name'] = $name;
                $success_message = "Profile updated successfully.";
                $is_edit_mode = false;
            } else {
                $error_message = "Unable to update profile right now.";
                $is_edit_mode = true;
            }

            mysqli_stmt_close($update_stmt);
        }
    }
}

$stmt = mysqli_prepare($conn, "SELECT name, email, phone, gender, age, address, created_at FROM patient WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $patient_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$patient = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$patient) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - Joe Medical Center</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .page-wrap {
      max-width: 900px;
      margin: 0 auto;
      padding: var(--spacing-xl) 0;
    }

    .page-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: var(--spacing-md);
      margin-bottom: var(--spacing-xl);
      flex-wrap: wrap;
    }

    .profile-card {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.86), rgba(255, 255, 255, 0.78));
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      overflow: hidden;
    }

    .profile-banner {
      padding: var(--spacing-xl);
      background: linear-gradient(135deg, rgba(14, 165, 233, 0.14), rgba(34, 197, 94, 0.10));
      border-bottom: 1px solid var(--dark-border);
      display: flex;
      align-items: center;
      gap: var(--spacing-lg);
      flex-wrap: wrap;
    }

    .avatar {
      width: 82px;
      height: 82px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
      font-weight: 700;
      color: white;
      background: linear-gradient(135deg, var(--primary), #22c55e);
    }

    .profile-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: var(--spacing-lg);
      padding: var(--spacing-xl);
    }

    .info-block {
      padding: var(--spacing-lg);
      border-radius: var(--radius-md);
      background: rgba(14, 165, 233, 0.05);
      border: 1px solid rgba(14, 165, 233, 0.12);
    }

    .info-label {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--muted-text);
      margin-bottom: var(--spacing-sm);
    }

    .info-value {
      font-size: 1rem;
      color: var(--light-text);
      word-break: break-word;
    }

    .info-value.empty {
      color: var(--muted-text);
      font-style: italic;
    }

    .profile-input {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-md);
      background: rgba(255, 255, 255, 0.92);
      color: var(--light-text);
      font-family: inherit;
      font-size: 14px;
    }

    .profile-textarea {
      min-height: 110px;
      resize: vertical;
    }

    .success-alert,
    .error-alert {
      border-radius: var(--radius-md);
      padding: var(--spacing-md);
      font-size: 14px;
      font-weight: 600;
    }

    .success-alert {
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid var(--success);
      color: var(--success);
    }

    .error-alert {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid var(--danger);
      color: var(--danger);
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
    <div class="page-wrap">
      <div class="page-head">
        <div>
          <h1>My Profile</h1>
          <p style="color: var(--muted-text); margin: 0;">View your personal account details.</p>
        </div>
        <?php if (!$is_edit_mode): ?>
          <a href="profile.php?edit=1" class="btn btn-primary">Edit Profile</a>
        <?php endif; ?>
      </div>

      <?php if ($success_message !== ""): ?>
        <div class="success-alert" style="margin-bottom: var(--spacing-lg);">
          <?php echo htmlspecialchars($success_message); ?>
        </div>
      <?php endif; ?>

      <?php if ($error_message !== ""): ?>
        <div class="error-alert" style="margin-bottom: var(--spacing-lg);">
          <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>

      <div class="profile-card">
        <div class="profile-banner">
          <div class="avatar"><?php echo htmlspecialchars(strtoupper(substr($patient['name'], 0, 1))); ?></div>
          <div>
            <h2 style="margin-bottom: var(--spacing-sm);"><?php echo htmlspecialchars($patient['name']); ?></h2>
            <p style="color: var(--muted-text); margin: 0;"><?php echo htmlspecialchars($patient['email']); ?></p>
          </div>
        </div>

        <?php if ($is_edit_mode): ?>
          <form method="POST" class="profile-grid">
            <div class="info-block">
              <div class="info-label">Full Name</div>
              <input type="text" name="name" value="<?php echo htmlspecialchars($patient['name']); ?>" class="profile-input" required>
            </div>

            <div class="info-block">
              <div class="info-label">Email Address</div>
              <input type="email" name="email" value="<?php echo htmlspecialchars($patient['email']); ?>" class="profile-input" required>
            </div>

            <div class="info-block">
              <div class="info-label">Phone Number</div>
              <input type="text" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" class="profile-input" required>
            </div>

            <div class="info-block">
              <div class="info-label">Gender</div>
              <select name="gender" class="profile-input">
                <option value="">Select gender</option>
                <option value="Male" <?php echo $patient['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $patient['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo $patient['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
              </select>
            </div>

            <div class="info-block">
              <div class="info-label">Age</div>
              <input type="number" min="1" name="age" value="<?php echo htmlspecialchars((string) $patient['age']); ?>" class="profile-input">
            </div>

            <div class="info-block">
              <div class="info-label">Member Since</div>
              <div class="info-value"><?php echo date('d M Y', strtotime($patient['created_at'])); ?></div>
            </div>

            <div class="info-block" style="grid-column: 1 / -1;">
              <div class="info-label">Address</div>
              <textarea name="address" class="profile-input profile-textarea"><?php echo htmlspecialchars($patient['address']); ?></textarea>
            </div>

            <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: var(--spacing-md); padding: 0 var(--spacing-xl) var(--spacing-xl);">
              <a href="profile.php" class="btn btn-secondary">Cancel</a>
              <button type="submit" name="save_profile" class="btn btn-primary">Save Profile</button>
            </div>
          </form>
        <?php else: ?>
          <div class="profile-grid">
            <div class="info-block">
              <div class="info-label">Full Name</div>
              <div class="info-value"><?php echo htmlspecialchars($patient['name']); ?></div>
            </div>

            <div class="info-block">
              <div class="info-label">Email Address</div>
              <div class="info-value"><?php echo htmlspecialchars($patient['email']); ?></div>
            </div>

            <div class="info-block">
              <div class="info-label">Phone Number</div>
              <div class="info-value"><?php echo htmlspecialchars($patient['phone']); ?></div>
            </div>

            <div class="info-block">
              <div class="info-label">Gender</div>
              <div class="info-value <?php echo empty($patient['gender']) ? 'empty' : ''; ?>">
                <?php echo !empty($patient['gender']) ? htmlspecialchars($patient['gender']) : 'Not provided'; ?>
              </div>
            </div>

            <div class="info-block">
              <div class="info-label">Age</div>
              <div class="info-value <?php echo empty($patient['age']) ? 'empty' : ''; ?>">
                <?php echo !empty($patient['age']) ? (int) $patient['age'] . ' years' : 'Not provided'; ?>
              </div>
            </div>

            <div class="info-block">
              <div class="info-label">Member Since</div>
              <div class="info-value"><?php echo date('d M Y', strtotime($patient['created_at'])); ?></div>
            </div>

            <div class="info-block" style="grid-column: 1 / -1;">
              <div class="info-label">Address</div>
              <div class="info-value <?php echo empty($patient['address']) ? 'empty' : ''; ?>">
                <?php echo !empty($patient['address']) ? nl2br(htmlspecialchars($patient['address'])) : 'Not provided'; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
