<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header("Location: ../credentials/userlogin.php");  // Redirect if not logged in
    exit();
}

$conn = mysqli_connect("localhost", "root", "root", "therapist") or die("Cannot connect to the database");

$tid = $_GET['id'];
$therapist = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM therapists WHERE id=$tid"));

$current_date = date('Y-m-d');
$current_time = date('H:i:s');

$slots_query = "
    SELECT * FROM availability
    WHERE therapist_id = $tid 
    AND status = 'available' 
    AND (date > '$current_date' OR (date = '$current_date' AND start_time > '$current_time'))
";
$slots = mysqli_query($conn, $slots_query);

$slots_exist = mysqli_num_rows($slots) > 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book with <?= htmlspecialchars($therapist['name']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
    body { background: #f0f1ec; color: #2a2a2a; min-height: 100vh; }

    .navbar {
      width: 100%;
      background-color: #5e5c4f;
      padding: 14px 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .navbar-left {
      display: flex;
      align-items: center;
    }

    .navbar-left img {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      margin-right: 12px;
    }

    .navbar-left span {
      font-size: 1.2rem;
      font-weight: 700;
      color: #fff;
      line-height: 1;
    }

    .navbar-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .navbar-right a, .dropbtn {
      color: white;
      text-decoration: none;
      font-size: 15px;
      font-weight: 600;
      background: none;
      border: none;
      cursor: pointer;
      margin-top: 4px;
    }

    .dropdown {
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      top: 36px;
      background-color: white;
      min-width: 160px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
      z-index: 10;
      border-radius: 6px;
      overflow: hidden;
    }

    .dropdown-content a {
      color: #333;
      padding: 10px 16px;
      text-decoration: none;
      display: block;
      font-size: 14px;
      font-weight: bold;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    .main {
      max-width: 700px;
      margin: 40px auto;
      padding: 20px;
    }

    .form-container {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 14px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
      text-align: center;
    }

    .form-container img {
      width: 160px;
      height: 160px;
      border-radius: 10px;
      object-fit: cover;
      margin-bottom: 16px;
    }

    label {
      display: block;
      text-align: left;
      margin-top: 16px;
      font-weight: 600;
    }

    input, select {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-top: 5px;
      font-size: 14px;
    }

    .error-message {
      color: red;
      font-size: 13px;
      margin-top: 4px;
      text-align: left;
      display: none;
    }

    .no-slots-message {
      margin-top: 30px;
      font-weight: 700;
      color: #721c24;
      background-color: #f8d7da;
      padding: 15px;
      border-radius: 8px;
    }

    button, .back-button {
      margin-top: 28px;
      width: 100%;
      background-color: #5e5c4f;
      color: white;
      padding: 12px;
      font-size: 15px;
      border-radius: 8px;
      border: none;
      font-weight: 600;
      cursor: pointer;
      text-align: center;
      text-decoration: none;
      display: inline-block;
    }

    button:hover, .back-button:hover {
      background-color: #44433a;
    }
  </style>
</head>
<body>

<div class="navbar">
  <div class="navbar-left">
    <img src="../../uploads/logo_official.png" alt="Logo">
    <span>UNITEN CARESPACE</span>
  </div>
  <div class="navbar-right">
    <a href="../../mainpage.php">Home</a>
    <a href="../articles/view_articles.php">Articles</a>
    <a href="../dass/dass_intro.php">DASS Test</a>
    <div class="dropdown">
      <button class="dropbtn">Counsellor ▾</button>
      <div class="dropdown-content">
        <a href="../book/therapist_page.php">View Counsellors</a>
        <a href="../book/book_appointment.php">Book Appointment</a>
      </div>
    </div>
    <?php if (isset($_SESSION['userid'])): ?>
      <a href="../profile/userinformation.php">Profile</a>
      <a href="../credentials/logout.php" id="logout-link">Logout</a>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('logout-link').addEventListener('click', function(e) {
  e.preventDefault();
  Swal.fire({
    title: 'Are you sure?',
    text: "You will be logged out.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#5e5c51',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, logout!'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = this.href;
    }
  });
});
</script>

    <?php else: ?>
      <a href="../credentials/userlogin.php">Login</a>
    <?php endif; ?>
  </div>
</div>

<div class="main">
  <div class="form-container">
    <h2>Book a Session with<br> <?= htmlspecialchars($therapist['name']) ?></h2><br>
    <img src="../../counsellor/uploads/<?= htmlspecialchars($therapist['photo']) ?>" 
    onerror="this.onerror=null; this.src='../../uploads/empty_profile_pic.png';"
    alt="Photo" width="50" height="50" style="border-radius: 50%;">
    <p style="text-align: justify; line-height: 1.9;"><?= htmlspecialchars($therapist['bio']) ?></p>

    <?php if (!$slots_exist): ?>
      <div class="no-slots-message">No available slots — booking is currently full.</div>
      <a href="book_appointment.php" class="back-button">Back</a>
    <?php else: ?>
      <form id="bookingForm" action="submit_booking.php" method="POST" novalidate>
        <input type="hidden" name="therapist_id" value="<?= htmlspecialchars($tid) ?>">


        <label for="availability_id">Available Slot:</label>
        <select name="availability_id" id="availability_id">
          <option value="">Please select a slot</option>
          <?php
          mysqli_data_seek($slots, 0);
          while ($row = mysqli_fetch_assoc($slots)) { ?>
            <option value="<?= htmlspecialchars($row['id']) ?>">
              <?= date('j F Y', strtotime($row['date'])) ?> | <?= date('g:i A', strtotime($row['start_time'])) ?> - <?= date('g:i A', strtotime($row['end_time'])) ?>
            </option>
          <?php } ?>
        </select>
        <div id="slotError" class="error-message">Please select an available time slot.</div>

        <button type="submit">Book Now</button>
      </form>
      <a href="book_appointment.php" class="back-button">Back</a>
    <?php endif; ?>
  </div>
</div>

<script>
  document.getElementById("bookingForm")?.addEventListener("submit", function (e) {
    let isValid = true;
    const name = document.getElementById("student_name");
    const slot = document.getElementById("availability_id");
    const nameError = document.getElementById("nameError");
    const slotError = document.getElementById("slotError");

    if (name && !/^[a-zA-Z\s]{2,50}$/.test(name.value.trim())) {
      nameError.style.display = "block";
      isValid = false;
    } else if (name) {
      nameError.style.display = "none";
    }

    if (slot && slot.value === "") {
      slotError.style.display = "block";
      isValid = false;
    } else if (slot) {
      slotError.style.display = "none";
    }

    if (!isValid) e.preventDefault();
  });
</script>

</body>
</html>
