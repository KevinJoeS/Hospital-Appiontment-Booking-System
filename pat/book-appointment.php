<?php
session_start();
include("../config.php");

if(!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

$success_message = "";
$error_message = "";

// Handle booking
if(isset($_POST['book'])) {

    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);

    $appointment_datetime = strtotime("$date $time");

    if($appointment_datetime < time()) {
        $error_message = "Please select a future date and time";
    } else {

        $check_sql = "SELECT * FROM appointment 
                      WHERE doctor_id='$doctor_id' 
                      AND appointment_date='$date' 
                      AND appointment_time='$time'";

        $check_result = mysqli_query($conn, $check_sql);

        if(mysqli_num_rows($check_result) > 0) {
            $error_message = "Doctor already booked for this time!";
        } else {

            $check_patient = "SELECT * FROM appointment 
                              WHERE patient_id='$patient_id' 
                              AND appointment_date='$date' 
                              AND appointment_time='$time'";

            $patient_result = mysqli_query($conn, $check_patient);

            if(mysqli_num_rows($patient_result) > 0) {
                $error_message = "You already have an appointment at this time!";
            } else {

                $sql = "INSERT INTO appointment 
                        (patient_id, doctor_id, appointment_date, appointment_time, status)
                        VALUES ('$patient_id', '$doctor_id', '$date', '$time', 'Pending')";

                if(mysqli_query($conn, $sql)) {
                    $success_message = "Appointment request sent successfully. Please wait for the doctor to accept it.";
                } else {
                    $error_message = "Error: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Fetch doctors
$doctor_sql = "SELECT id, name, specialization FROM doctor ORDER BY name";
$doctor_result = mysqli_query($conn, $doctor_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Appointment — Joe Medical Center</title>

<link rel="stylesheet" href="../css/style.css">
<style>
  body {
    background: var(--dark-bg);
    min-height: 100vh;
  }
  .booking-container {
    padding: var(--spacing-lg);
    display: flex;
    justify-content: center;
  }
  .booking-card {
    width: 100%;
    max-width: 600px;
    background: var(--dark-surface);
    border: 1px solid var(--dark-border);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    box-shadow: var(--shadow-xl);
  }
  .booking-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
    border-bottom: 1px solid var(--dark-border);
    padding-bottom: var(--spacing-lg);
  }
  .booking-header h1 {
    font-size: 1.8rem;
    margin-bottom: var(--spacing-sm);
    color: var(--light-text);
  }
  .booking-header p {
    color: var(--muted-text);
    font-size: 14px;
  }
  .form-section {
    margin-bottom: var(--spacing-xl);
  }
  .form-section-title {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
  }
  .section-number {
    width: 28px;
    height: 28px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
  }
  .form-section-title h3 {
    font-size: 1.2rem;
    color: var(--light-text);
  }
  .form-group {
    margin-bottom: var(--spacing-md);
  }
  .form-group label {
    display: block;
    margin-bottom: var(--spacing-xs);
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted-text);
  }
  .form-group input, .form-group select {
    width: 100%;
    padding: 10px 12px;
    background: var(--input-bg);
    border: 1px solid var(--dark-border);
    border-radius: var(--radius-md);
    color: var(--light-text);
    font-size: 14px;
    transition: var(--transition);
  }
  .form-group input:focus, .form-group select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(31, 162, 201, 0.1);
  }
  .form-actions {
    display: flex;
    gap: var(--spacing-md);
    margin-top: var(--spacing-xl);
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--dark-border);
  }
  .form-actions button, .form-actions a {
    flex: 1;
    text-align: center;
    padding: 12px;
  }
  .info-box {
    margin-top: var(--spacing-lg);
    padding: var(--spacing-md);
    background: rgba(31, 162, 201, 0.05);
    border: 1px solid var(--primary-light);
    border-radius: var(--radius-md);
    color: var(--light-text);
  }
  .info-box h4 {
    color: var(--primary-dark);
    margin-bottom: var(--spacing-sm);
  }
  .info-box ul {
    padding-left: var(--spacing-lg);
    color: var(--muted-text);
    font-size: 14px;
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
    <a href="dashboard.php" class="btn btn-secondary btn-sm">← Back to Dashboard</a>
  </div>
</nav>

<div class="booking-container">
  <div class="booking-card">

    <div class="booking-header">
      <h1>Book <span style="color: var(--primary);">Appointment</span></h1>
      <p>Schedule your visit with our experienced doctors</p>
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
<div class ="form-section">

</div>
<div class="booking-form">
    <form method="POST">

      <div class="form-section">
        <div class="form-section-title">
          <div class="section-number">1</div>
          <h3>Select Doctor</h3>
        </div>

        <div class="form-group">
          <label>Choose from available doctors</label>

          <select name="doctor_id" required>
            <option value="">-- Select a Doctor --</option>
            <?php
            while($row = mysqli_fetch_assoc($doctor_result))
            {
              echo '<option value="'.$row['id'].'">';
              echo 'Dr. '.htmlspecialchars($row['name']).' - '.htmlspecialchars($row['specialization']);
              echo '</option>';
            }
            ?>
          </select>

        </div>
      </div>

      <div class="form-section">
        <div class="form-section-title">
          <div class="section-number">2</div>
          <h3>Select Date & Time</h3>
        </div>

        <div class="form-group">
          <label>Date</label>
          <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
          <label>Time</label>
          <input type="time" name="time" required>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" name="book" class="btn btn-primary">Confirm Booking</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
      </div>

    </form>
    </div>

  </div>
</div>
  <div class="info-box">
    <h4>Booking Information</h4>
    <ul>
      <li>Select your preferred doctor</li>
      <li>Choose a future date</li>
      <li>Appointments are 30 minutes</li>
    </ul>
  </div>

</div>

  <script src="../java script/script.js"></script>
</body>
</html>
