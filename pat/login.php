<?php
session_start();
include("../config.php");

$error_message = "";

if(isset($_POST['login'])) {

    echo "FORM SUBMITTED"; //  TEMP DEBUG

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM patient WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        $_SESSION['patient_id'] = $row['id'];
        $_SESSION['patient_name'] = $row['name'];

        header("Location: dashboard.php");
        exit();

    } else {
        $error_message = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Login — Joe Medical Center</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body{
        background-color: var(--dark-bg);
        min-height: 100vh;
    }
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: var(--spacing-lg);
    }

    .login-card {
      width: 100%;
      max-width: 400px;
      background: var(--dark-bg);
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      padding: var(--spacing-xl);
      box-shadow: var(--shadow-xl);
    }

    .login-header {
      text-align: center;
      margin-bottom: var(--spacing-xl);
    }

    .login-header h2 {
      font-size: 1.8rem;
      margin-bottom: var(--spacing-md);
    }

    .login-header p {
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

    .form-group input {
      width: 100%;
      padding: 12px 14px;
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
      background: rgba(255, 255, 255, 0.05);
      box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    .form-group input::placeholder {
      color: var(--muted-text);
    }

    .login-button {
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
    }

    .login-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
    }

    .login-footer {
      text-align: center;
      margin-top: var(--spacing-lg);
      padding-top: var(--spacing-lg);
      border-top: 1px solid var(--dark-border);
    }

    .login-footer p {
      color: var(--light-text);
      font-size: 14px;
      margin-bottom: var(--spacing-md);
    }

    .login-footer a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: var(--transition);
    }

    .login-footer a:hover {
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
    }

    .register-link {
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

    .register-link:hover {
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
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h2><span style="color: var(--primary);">Patient</span> <span style="color: var(--success);">Login</span></h2>
        <p>Access your appointments and health records</p>
      </div>

      <?php if($error_message !== ""): ?>
        <div class="error-alert">
          <img src="/Hospital-system/images/icons/warning.svg" alt="Warning" class="emoji-icon"> <?php echo $error_message; ?>
        </div>
      <?php endif; ?>

      <form  method="POST">
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

        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password"
            name="password" 
            placeholder="Enter your password" 
            required
            autocomplete="current-password"
          >
        </div>

        <button type="submit"  name="login" class="login-button">Sign In</button>
      </form>

      <div class="login-footer">
        <p>Don't have an account? <a href="reg.php" >Create New Account</a></p>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
      </div>
    </div>
  </div>

  <script src="../java script/script.js"></script>
</body>
</html>
