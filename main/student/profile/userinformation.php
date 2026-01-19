<?php
session_start();

$conn_user = new mysqli("localhost", "root", "root", "user");
if ($conn_user->connect_error) die("Connection failed: " . $conn_user->connect_error);

if (!isset($_SESSION['userid'])) {
    header("Location: ../credentials/userlogin.php");
    exit();
}

$username = $_SESSION['userid'];
$user_result = mysqli_query($conn_user, "SELECT * FROM login WHERE username = '$username'");
$profile = mysqli_fetch_assoc($user_result);

if (!$profile) {

    header("Location: ../credentials/userlogin.php");
    exit();
}

$email = $profile['email'] ?? '';
$first_name = $profile['first_name'] ?? '';  
$last_name = $profile['last_name'] ?? '';  
$full_name = $first_name . ' ' . $last_name; 

$conn_therapist = new mysqli("localhost", "root", "root", "therapist");
if ($conn_therapist->connect_error) die("Connection failed: " . $conn_therapist->connect_error);

date_default_timezone_set('Asia/Kuala_Lumpur');
$current_time = date("Y-m-d H:i:s");

mysqli_query($conn_therapist, "
    UPDATE appointments a
    JOIN availability v ON a.availability_id = v.id
    SET a.status = 'completed'
    WHERE a.status = 'confirmed' AND CONCAT(v.date, ' ', v.end_time) < '$current_time'
");

$appointments_by_email = mysqli_query($conn_therapist, "
    SELECT a.*, t.name AS therapist_name, v.date, v.start_time, v.end_time
    FROM appointments a
    JOIN therapists t ON a.therapist_id = t.id
    JOIN availability v ON a.availability_id = v.id
    WHERE a.email = '$email' 
    ORDER BY v.date DESC
");

$dass_result = mysqli_query($conn_user, "SELECT * FROM dass_results WHERE username = '$username' ORDER BY taken_at DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Information</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f0f1ec; color: #2a2a2a; font-size: 14px; }

        .navbar {
            width: 100%; background-color: #5e5c4f; padding: 14px 32px;
            display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100;
        }

        .navbar-left { 
            display: flex; align-items: center; 
        }
        .navbar-left img { 
            width: 42px; height: 42px; border-radius: 50%; margin-right: 14px; 
        }
        .navbar-left span { 
            font-size: 1.3rem; font-weight: 700; color: white; 
        }

        .navbar-right { 
            display: flex; align-items: center; gap: 20px; 
        }
        .navbar-right a, .dropbtn {
            color: white; text-decoration: none; font-size: 15px; font-weight: 600; background: none; border: none; cursor: pointer;
        }
        .navbar-right a:hover, .dropbtn:hover { 
            opacity: 0.85; 
        }

        .dropdown { 
            position: relative; }
        .dropdown-content {
            display: none; position: absolute; background-color: white; min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.1); border-radius: 6px; z-index: 1;
        }
        .dropdown-content a {
            color: #333; padding: 8px 14px; text-decoration: none; display: block;
            font-weight: 500; font-size: 13px;
        }
        .dropdown-content a:hover { background-color: #f0f0f0; }
        .dropdown:hover .dropdown-content { display: block; }

        .main {
            padding: 30px; max-width: 1000px; margin: auto;
        }

        .profile-box, .appointments-box {
            background-color: #fff; padding: 20px 24px;
            border-radius: 12px; margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        h2 { margin-bottom: 16px; font-size: 18px; font-weight: 600; }

        .profile-field { margin-bottom: 12px; }
        .profile-label { font-weight: 600; color: #333; }
        .profile-value { margin-top: 4px; margin-left: 10px; color: #555; }

        .edit-button {
            margin-top: 10px; padding: 8px 16px;
            background-color: #5e5c4f; color: white; border: none;
            border-radius: 6px; cursor: pointer; font-weight: 600;
            text-decoration: none; display: inline-block;
        }

        table {
            width: 100%; border-collapse: collapse; font-size: 13px;
        }
        th, td {
            padding: 10px; text-align: center; border: 1px solid #ddd;
        }
        th {
            background-color: #5e5c4f; color: #fff; font-weight: 700; font-size: 13px;
        }

        select, textarea {
            font-size: 13px; padding: 6px; border-radius: 5px; border: 1px solid #ccc;
        }

        .submit-btn {
            background-color: #5e5c4f; color: white; border: none;
            padding: 6px 12px; border-radius: 6px; font-weight: 600; margin-top: 6px;
            cursor: pointer;
        }

        .stars { color: #f1c40f; font-size: 16px; letter-spacing: 1px; }
        .feedback-box {
            font-style: italic; font-size: 12px; color: #333;
            margin-top: 4px; display: inline-block; max-width: 200px;
            word-break: break-word;
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
            <button class="dropbtn">Counsellor <span>▾</span></button>
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
    <div class="profile-box">
        <h2>My Profile</h2>
        <div class="profile-field">
            <div class="profile-label">Student ID</div>
            <div class="profile-value"><?= htmlspecialchars($profile['username'] ?? '') ?></div>
        </div>
        <div class="profile-field">
            <div class="profile-label">Full Name</div>
            <div class="profile-value"><?= htmlspecialchars($full_name) ?></div>
        </div>
        <div class="profile-field">
            <div class="profile-label">Email</div>
            <div class="profile-value"><?= htmlspecialchars($profile['email'] ?? '') ?></div>
        </div>
        <a href="edit_profile.php" class="edit-button">Edit Profile</a>
    </div>

    <div class="appointments-box">
        <h2>My Appointments</h2>
        <label for="statusFilter"><strong>Filter by Status:</strong></label>
        <select id="statusFilter" onchange="filterAppointments()" style="margin: 10px 0;">
            <option value="all">All</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>

        <?php if ($appointments_by_email && mysqli_num_rows($appointments_by_email) > 0): ?>
            <p style="margin: 10px 0; font-weight: 600; color: #333;">
                Location: Building TA, Level 3
            </p>

            <table id="appointmentTable">

            <table id="appointmentTable">
    <tr>
        <th>No.</th>
        <th>Therapist</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php $count = 1; while ($row = mysqli_fetch_assoc($appointments_by_email)): ?>
    <tr>
        <td><?= $count++ ?></td>
        <td><?= htmlspecialchars($row['therapist_name'] ?? '') ?></td>
        <td><?= !empty($row['date']) ? date('j F Y', strtotime($row['date'])) : '' ?></td>
        <td>
            <?php
                if (!empty($row['start_time']) && !empty($row['end_time'])) {
                    echo date('g:i A', strtotime($row['start_time'])) . ' - ' . date('g:i A', strtotime($row['end_time']));
                }
            ?>
        </td>
        <td>
            <?php
                $status = $row['status'] ?? '';
                $end_time = !empty($row['date']) && !empty($row['end_time']) ? strtotime($row['date'] . ' ' . $row['end_time']) : 0;
                $is_past = time() > $end_time;
                echo ($status === 'confirmed' && $is_past) ? "Completed" : ucfirst($status);
            ?>
        </td>
        <td>
            <?php
                $aid = $row['id'] ?? 0;
                $check_pending = mysqli_query($conn_therapist, "SELECT * FROM cancellation_requests WHERE appointment_id = $aid AND status = 'pending'");
                $has_pending = $check_pending && mysqli_num_rows($check_pending) > 0;
                $already_rated = isset($row['rating']) && $row['rating'] !== null;
                $feedback = isset($row['feedback']) ? trim($row['feedback']) : '';

                if ($status === 'completed') {
                    if (!$already_rated) {
            ?>
            <form method="POST" action="rate_appointment.php">
                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['id'] ?? '') ?>">
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <select name="rating" required>
                        <option value="">Rate Counsellors</option>
                        <option value="1">⭐</option>
                        <option value="2">⭐⭐</option>
                        <option value="3">⭐⭐⭐</option>
                        <option value="4">⭐⭐⭐⭐</option>
                        <option value="5">⭐⭐⭐⭐⭐</option>
                    </select>
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
            <?php } else {
                $stars = str_repeat("★", $row['rating']) . str_repeat("☆", 5 - $row['rating']);
                echo "<div class='stars'> $stars</div>";
                
            }
        } elseif ($status === 'confirmed' && !$has_pending) {
            ?>
            <form method="POST" action="request_deletion.php" class="cancel-form">
                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['id'] ?? '') ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <button type="submit" class="submit-btn" style="background-color:#e74c3c;">Request Cancellation</button><br><br>
                <select name="reason" required>
                <option value="">Select a reason</option>
                <option value="Not feeling well">Not feeling well</option>
                <option value="Schedule conflict">Schedule conflict</option>
                <option value="No longer needed">No longer needed</option>
                <option value="Personal emergency">Personal emergency</option>
                <option value="Found another appointment">Found another appointment</option>
                <option value="Other">Other</option>
                </select> <br>
            </form>
            <?php
                } elseif ($has_pending) {
                    echo "<span style='color: orange;'>Awaiting Approval</span>";
                } elseif ($status === 'cancelled') {
                    echo "<span style='color: #bbb;'>Already Cancelled</span>";
                } else {
                    echo "<span style='color: #aaa;'>No Action</span>";
                }
            ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<p id="noDataMessage" style="text-align: center; margin-top: 10px; color: #777; display: none;">
    No appointments found for the selected status.
</p>

        <?php else: ?>
        <p>No appointments found for your email.</p>
        <?php endif; ?>
    </div>

    <?php if ($dass_result && mysqli_num_rows($dass_result) > 0): ?>
    <div class="appointments-box">
        <h2>DASS Test History</h2>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Date Taken</th>
                    <th>Stress</th>
                    <th>Anxiety</th>
                    <th>Depression</th>
                </tr>
            </thead>
            <tbody>
                <?php $dass_count = 1; while($row = mysqli_fetch_assoc($dass_result)): ?>
                <tr>
                    <td><?= $dass_count++ ?></td>
                    <td><?= !empty($row['taken_at']) ? date('j F Y, g:i A', strtotime($row['taken_at'])) : '' ?></td>
                    <td><?= htmlspecialchars($row['stress_level'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['anxiety_level'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['depression_level'] ?? '') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
function filterAppointments() {
    const filter = document.getElementById("statusFilter").value.toLowerCase();
    const rows = document.querySelectorAll("#appointmentTable tr");
    let visibleCount = 0;

    rows.forEach((row, index) => {
        if (index === 0) return; 
        
        const statusCell = row.cells[4];
        if (!statusCell) return; // Skip if no status cell is found

        const status = statusCell.textContent.trim().toLowerCase();
        const match = (filter === "all" || status === filter);

        row.style.display = match ? "" : "none";

        if (match) {
            visibleCount++;

            row.cells[0].textContent = visibleCount;  // Assuming the "No" column is the first column (index 0)
        }
    });

    document.getElementById("noDataMessage").style.display = visibleCount === 0 ? "block" : "none";
}


</script>

<?php if (isset($_SESSION['rated'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Thank you!',
    text: 'Your rating has been submitted.',
    confirmButtonColor: '#5e5c4f'
});
</script>
<?php unset($_SESSION['rated']); endif; ?>
<script>
document.querySelectorAll('.cancel-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to request a cancellation.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#999',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>


<?php if (isset($_SESSION['cancel_requested'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Cancellation Confirmed!',
    text: 'Your appointment has been successfully cancelled.',
    confirmButtonColor: '#5e5c4f'
});

</script>

<?php unset($_SESSION['cancel_requested']); endif; ?>

</body>
</html>
