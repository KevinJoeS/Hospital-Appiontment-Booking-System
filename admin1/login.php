<?php
session_start();
include("../config.php");

$error_message = "";

if(isset($_POST['login']))
{
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1)
    {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
        exit();
    }
    else
    {
        $error_message = "Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Joe Medical Center</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
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
      background: var(--dark-surface);
      border: 1px solid var(--dark-border);
      border-radius: var(--radius-lg);
      padding: var(--spacing-xl);
      box-shadow: var(--shadow-xl);
    }

    .login-header {
      text-align: center;
      margin-bottom: var(--spacing-xl);
    }

    .login-header .admin-icon {
      font-size: 3rem;
      margin-bottom: var(--spacing-md);
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
      background: rgba(14, 165, 233, 0.05);
      box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    .form-group input::placeholder {
      color: var(--muted-text);
    }

    .login-button {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, var(--warning), #d97706);
      color: white;
      border: none;
      border-radius: var(--radius-md);
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: var(--transition);
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
      letter-spacing: 0.5px;
    }

    .login-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
    }

    .login-footer {
      text-align: center;
      margin-top: var(--spacing-lg);
      padding-top: var(--spacing-lg);
      border-top: 1px solid var(--dark-border);
    }

    .login-footer a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      font-size: 13px;
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
      display: flex;
      align-items: flex-start;
      gap: var(--spacing-sm);
    }

    .security-info {
      background: rgba(14, 165, 233, 0.1);
      border: 1px solid var(--primary);
      border-radius: var(--radius-md);
      padding: var(--spacing-md);
      color: var(--primary);
      font-size: 12px;
      margin-top: var(--spacing-lg);
      line-height: 1.6;
    }

    .security-info strong {
      display: block;
      margin-bottom: var(--spacing-sm);
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h2><span style="color: var(--primary);">Admin</span> <span style="color: var(--warning);">Login</span></h2>
      </div>

      <?php if($error_message !== ""): ?>
        <div class="error-alert">
          <span><img src="/Hospital-system/images/icons/warning.svg" alt="Warning" class="emoji-icon"></span>
          <span><?php echo $error_message; ?></span>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          
          <label for="username">Username</label>
          <input 
            type="text" 
            id="username"
            name="username" 
            placeholder="Enter admin username" 
            required
            autocomplete="username"
          >
        </div>

        <div class="form-group">
          <label for="password" style="margin-top: -10px;">Password</label>
          <input 
            type="password" 
            id="password"
            name="password" 
            placeholder="Enter admin password" 
            required
            autocomplete="current-password"
          >
        </div>

        <button type="submit" name="login" class="login-button">Sign In</button>
      </form>

      

      <div class="login-footer">
        <a href="../index.php">← Back to Home</a>
      </div>
    </div>
  </div>

</body>
</html>
