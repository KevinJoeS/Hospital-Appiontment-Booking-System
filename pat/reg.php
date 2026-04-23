<?php
session_start();
include("../config.php");

$error_message = "";
$success_message = "";

if(isset($_POST['register']))
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender = isset($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : '';
    $age = isset($_POST['age']) ? mysqli_real_escape_string($conn, $_POST['age']) : '';
    $dob = isset($_POST['dob']) ? mysqli_real_escape_string($conn, $_POST['dob']) : '';

    // Validation
    if(empty($name) || empty($email) || empty($password) || empty($phone) || empty($gender) || empty($age) || empty($dob)) {
        $error_message = "All fields are required";
    } elseif(strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long";
    } elseif($password !== $confirm_password) {
        $error_message = "Passwords do not match";
    } else {
        // Check if email already exists
        $check_sql = "SELECT * FROM patient WHERE email='$email'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if(mysqli_num_rows($check_result) > 0) {
            $error_message = "Email address already registered";
        } else {
            $sql = "INSERT INTO patient (name, email, password, phone, gender, age, dob)
                    VALUES ('$name', '$email', '$password', '$phone', '$gender', '$age', '$dob')";

            if(mysqli_query($conn, $sql)) {
                $patient_id = mysqli_insert_id($conn);
                $_SESSION['id'] = $patient_id;
                $_SESSION['name'] = $name;
                $_SESSION['register_success'] = "Registration successful! Welcome to Joe Medical Center.";
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Registration — Joe Medical Center</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .register-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: var(--spacing-md);
    }

    .register-card {
      width: 100%;
      max-width: 450px;
      background: var(--dark-surface);
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      padding: var(--spacing-lg) var(--spacing-xl);
      box-shadow: var(--shadow-xl);
    }

    .register-header {
      text-align: center;
      margin-bottom: var(--spacing-lg);
    }

    .register-header h2 {
      font-size: 1.5rem;
      margin-bottom: var(--spacing-xs);
    }

    .register-header p {
      color: var(--muted-text);
      font-size: 14px;
    }

    .form-group {
      margin-bottom: var(--spacing-md);
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

    .form-group input {
      width: 100%;
      padding: 10px 12px;
      background: var(--input-bg);
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-md);
      color: var(--light-text);
      font-size: 14px;
      transition: var(--transition);
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--primary);
      background: rgba(14, 165, 233, 0.05);
      box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    .form-group input::placeholder {
      color: var(--muted-text);
    }

    .register-button {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, var(--primary), var(--accent));
      color: white;
      border: none;
      border-radius: var(--radius-md);
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: var(--transition);
      box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
      letter-spacing: 0.5px;
      margin-top: var(--spacing-md);
    }

    .register-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
    }

    .register-footer {
      text-align: center;
      margin-top: var(--spacing-md);
      padding-top: var(--spacing-md);
      border-top: 1px solid var(--dark-border);
    }

    .register-footer p {
      color: var(--muted-text);
      font-size: 14px;
      margin-bottom: var(--spacing-md);
    }

    .register-footer a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: var(--transition);
    }

    .register-footer a:hover {
      color: var(--primary-light);
    }

    .error-alert {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid var(--danger);
      border-radius: var(--radius-md);
      padding: var(--spacing-md);
      color: var(--danger);
      margin-bottom: var(--spacing-lg);
      font-size: 13px;
      font-weight: 500;
      display: flex;
      align-items: flex-start;
      gap: var(--spacing-sm);
    }

    .login-link {
      display: block;
      width: 100%;
      padding: 10px;
      text-align: center;
      background: transparent;
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-md);
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
      margin-bottom: var(--spacing-md);
    }

    .login-link:hover {
      background: rgba(14, 165, 233, 0.1);
      border-color: var(--primary);
    }

    .back-link {
      display: block;
      text-align: center;
      color: var(--muted-text);
      text-decoration: none;
      font-size: 13px;
      transition: var(--transition);
    }

    .back-link:hover {
      color: var(--primary);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: var(--spacing-md);
    }

    @media (max-width: 480px) {
      .form-row {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <h2><span style="color: var(--primary);">Patient</span> <span style="color: var(--success);">Registration</span></h2>
        <p>Create your account and start booking appointments</p>
      </div>

      <?php if($error_message !== ""): ?>
        <div class="error-alert">
          <span><img src="/Hospital-system/images/icons/warning.svg" alt="Warning" class="emoji-icon"></span>
          <span><?php echo $error_message; ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <div class="form-row">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input 
              type="text" 
              id="name"
              name="name" 
              placeholder="John Doe" 
              required
              autocomplete="name"
            >
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input 
              type="email" 
              id="email"
              name="email" 
              placeholder="your@email.com" 
              required
              autocomplete="email"
            >
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password">Password</label>
            <input 
              type="password" 
              id="password"
              name="password" 
              placeholder="Min 6 characters" 
              required
              autocomplete="new-password"
            >
          </div>

          <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input 
              type="password" 
              id="confirm_password"
              name="confirm_password" 
              placeholder="Re-enter password" 
              required
              autocomplete="new-password"
            >
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
              <option value="" disabled selected>Select Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input 
              type="date" 
              id="dob"
              name="dob" 
              required
            >
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="age">Age</label>
            <input 
              type="number" 
              id="age"
              name="age" 
              placeholder="e.g. 25" 
              min="0"
              max="150"
              required
            >
          </div>

          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input 
              type="tel" 
              id="phone"
              name="phone" 
              placeholder="+91 9876543210" 
              required
              autocomplete="tel"
            >
          </div>
        </div>

        <button type="submit" name="register" class="register-button">Create Account</button>
      </form>

      <div class="register-footer">
        <p>Already have an account?</p>
        <a href="login.php" class="login-link">Sign In Here</a>
        <a href="../index.php" class="back-link">← Back to Home</a>
      </div>
    </div>
  </div>

  <script src="../java script/script.js"></script>
</body>
</html>
