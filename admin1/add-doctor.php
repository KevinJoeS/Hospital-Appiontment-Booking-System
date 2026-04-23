<?php
session_start();
include("../config.php");

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
    exit();
}

$success_message = "";
$error_message = "";

if(isset($_POST['add']))
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $availability = mysqli_real_escape_string($conn, $_POST['availability']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if(empty($name) || empty($specialization) || empty($phone) || empty($password)) {
        $error_message = "All fields are required";
    } else {
        // Check if phone already exists
        $check_sql = "SELECT * FROM doctors WHERE phone='$phone'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if(mysqli_num_rows($check_result) > 0) {
            $error_message = "A doctor with this phone number already exists";
        } else {
            $sql = "INSERT INTO doctors (name, specialization, availability, phone, password)
                    VALUES ('$name', '$specialization', '$availability', '$phone', '$password')";

            if(mysqli_query($conn, $sql)) {
                $success_message = "Doctor added successfully!";
                $_POST = array();
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// List of common specializations
$specializations = array(
    "Cardiology",
    "Dermatology",
    "General Surgery",
    "Internal Medicine",
    "Neurology",
    "Orthopedics",
    "Pediatrics",
    "Psychiatry",
    "Radiology",
    "Oncology",
    "Gastroenterology",
    "Pulmonology",
    "Ophthalmology",
    "ENT",
    "Dentistry"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Doctor — Joe Medical Center</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .form-container {
      max-width: 600px;
      margin: var(--spacing-2xl) auto;
      padding: var(--spacing-lg);
    }

    .form-card {
      background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.9));
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      padding: var(--spacing-xl);
      box-shadow: var(--shadow-lg);
    }

    .form-header {
      text-align: center;
      margin-bottom: var(--spacing-xl);
      padding-bottom: var(--spacing-lg);
      border-bottom: 1px solid var(--dark-border);
    }

    .form-header h1 {
      font-size: 1.8rem;
      margin-bottom: var(--spacing-sm);
    }

    .form-header p {
      color: var(--muted-text);
      font-size: 14px;
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
    .form-group select {
      width: 100%;
      padding: 12px 14px;
      background: rgba(15, 23, 42, 0.8);
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-md);
      color: var(--light-text);
      font-size: 14px;
      font-family: inherit;
      transition: var(--transition);
    }

    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--primary);
      background: rgba(14, 165, 233, 0.05);
      box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    .form-group input::placeholder {
      color: var(--muted-text);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: var(--spacing-md);
    }

    .form-group-info {
      background: rgba(14, 165, 233, 0.1);
      border: 1px solid var(--primary);
      border-radius: var(--radius-md);
      padding: var(--spacing-md);
      color: var(--primary);
      font-size: 12px;
      margin-bottom: var(--spacing-lg);
      line-height: 1.6;
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

    .error-alert {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid var(--danger);
      border-radius: var(--radius-md);
      padding: var(--spacing-lg);
      color: var(--danger);
      margin-bottom: var(--spacing-lg);
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
    }

    .form-actions {
      display: flex;
      gap: var(--spacing-md);
      margin-top: var(--spacing-xl);
      padding-top: var(--spacing-lg);
      border-top: 1px solid var(--dark-border);
    }

    .form-actions .btn {
      flex: 1;
    }

    @media (max-width: 600px) {
      .form-container {
        padding: 0;
      }

      .form-card {
        border-radius: 0;
        padding: var(--spacing-lg);
      }

      .form-row {
        grid-template-columns: 1fr;
      }

      .form-actions {
        flex-direction: column;
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
      <a href="manage-doctors.php" class="btn btn-secondary btn-sm">← Back to Doctors</a>
    </div>
  </nav>

  <div class="form-container">
    <div class="form-card">
      <div class="form-header">
        <h1>Add <span style="color: var(--primary);">Doctor</span></h1>
        <p>Register a new doctor to the hospital system</p>
      </div>

      <?php if($success_message !== ""): ?>
        <div class="success-alert">
          <span><img src="/Hospital-system/images/icons/check.svg" alt="Check" class="emoji-icon"></span>
          <span><?php echo $success_message; ?></span>
        </div>
      <?php endif; ?>

      <?php if($error_message !== ""): ?>
        <div class="error-alert">
          <span><img src="/Hospital-system/images/icons/warning.svg" alt="Warning" class="emoji-icon"></span>
          <span><?php echo $error_message; ?></span>
        </div>
      <?php endif; ?>

      <div class="form-group-info">
        <img src="/Hospital-system/images/icons/idea.svg" alt="Idea" class="emoji-icon"> Once added, doctors can login using their name and password to access the system.
      </div>

      <form method="POST">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input 
            type="text" 
            id="name"
            name="name" 
            placeholder="Dr. John Smith" 
            required
            value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
          >
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="specialization">Specialization</label>
            <select id="specialization" name="specialization" required>
              <option value="">-- Select Specialization --</option>
              <?php foreach($specializations as $spec): ?>
                <option value="<?php echo $spec; ?>" <?php echo (isset($_POST['specialization']) && $_POST['specialization'] === $spec) ? 'selected' : ''; ?>>
                  <?php echo $spec; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input 
              type="tel" 
              id="phone"
              name="phone" 
              placeholder="+91 9876543210" 
              required
              value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
            >
          </div>
        </div>

        <div class="form-group">
          <label for="availability">Availability</label>
          <input 
            type="text" 
            id="availability"
            name="availability" 
            placeholder="e.g., Mon-Fri 9AM-5PM, Sat 9AM-1PM" 
            value="<?php echo isset($_POST['availability']) ? htmlspecialchars($_POST['availability']) : ''; ?>"
          >
        </div>

        <div class="form-group">
          <label for="password">Login Password</label>
          <input 
            type="password" 
            id="password"
            name="password" 
            placeholder="Create a secure password" 
            required
            minlength="6"
          >
        </div>

        <div class="form-actions">
          <button type="submit" name="add" class="btn btn-primary">Add Doctor</button>
          <a href="manage-doctors.php" class="btn btn-secondary" style="text-decoration: none;">Cancel</a>
        </div>
      </form>
    </div>
  </div>

</body>
</html>
