<?php
session_start();

$conn = mysqli_connect("localhost", "root", "root", "therapist");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['admin'])) {
    header("Location: admin/credentials/adminlogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
    header('Content-Type: application/json');

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $specialty = mysqli_real_escape_string($conn, $_POST['specialty']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $check_email = mysqli_query($conn, "SELECT id FROM therapists WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
        exit;
    }

    $photo_filename = "";
    if (!empty($_FILES["photo"]["name"])) {
        $photo_filename = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_dir = __DIR__ . "/../../counsellor/uploads/";
        $target_file = $target_dir . $photo_filename;

        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            echo json_encode(['success' => false, 'message' => 'Photo upload failed.']);
            exit;
        }
    }
    $insert_sql = "INSERT INTO therapists (name, email, specialty, bio, password, photo)
                   VALUES ('$name', '$email', '$specialty', '$bio', '$password', '$photo_filename')";

if (mysqli_query($conn, $insert_sql)) {
echo json_encode(['success' => true, 'message' => 'Therapist added successfully.']);
} else {
echo json_encode(['success' => false, 'message' => 'Failed to add therapist.']);
}
exit;

}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $delete_query = "DELETE FROM therapists WHERE id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Therapist has been deleted.',
            }).then(function() {
                window.location.href = 'view_therapist.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to delete therapist.',
            });
        </script>";
    }
}
if (isset($_GET['reset'])) {

  header("Location: " . $_SERVER['PHP_SELF']); 
  exit;
}
$search_query = "";
if (isset($_GET['search'])) {
    $search_value = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = " WHERE name LIKE '%$search_value%' OR email LIKE '%$search_value%'";
}

$therapists_query = "SELECT * FROM therapists" . $search_query;
$therapists_result = mysqli_query($conn, $therapists_query);
$therapist_list = [];
while ($row = mysqli_fetch_assoc($therapists_result)) {
    $therapist_list[] = $row;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Therapist Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f1ec;
      margin: 0;
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 32px;
      background: #5e5c51;
    }

    nav .brand {
      display: flex;
      align-items: center;
      font-size: 1.3rem;
      font-weight: 700;
      color: white;
    }

    nav .brand img {
      height: 48px;
      width: 48px;
      border-radius: 50%;
      margin-right: 12px;
    }

    .nav-right {
      display: flex;
      align-items: center;
      gap: 24px;
    }

    .nav-right a,
    .dropbtn {
      color: white;
      font-size: 15px;
      font-weight: 600;
      text-decoration: none;
      background: none;
      border: none;
      cursor: pointer;
    }

    .dropdown {
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: white;
      min-width: 160px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
      z-index: 1;
      border-radius: 6px;
    }

    .dropdown-content a {
      color: #333;
      padding: 10px 16px;
      text-decoration: none;
      display: block;
      font-size: 14px;
      font-weight: 600;
    }

    .dropdown-content a:hover {
      background-color: #f3f3f3;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    .main {
        padding: 30px;
        max-width: 1600px;         
        margin: auto;
     }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .table-container {
    padding: 30px;
    max-width: 1200px;
    margin: auto;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        font-size: 14px;
        border-radius: 8px;
    }

    th, td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
        vertical-align: middle;
    }

    th {
        background-color: #5e5c4f;
        color: white;
        font-weight: 700;
        font-size: 14px;
        text-align: center;
    }

    td:nth-child(5), th:nth-child(5) {
        width: 600px;  
        text-align: justify;
    }

    td:nth-child(2), th:nth-child(2) {
        width: 180px;   
        text-align: left;
    }
    td:nth-child(4), th:nth-child(4) {
        width: 400px;   
        text-align: left;
    }
    .therapist-photo {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        margin-left: auto;
        margin-right: auto;
        box-shadow: 0 0 4px rgba(0,0,0,0.1);
    }

    .delete-button {
        padding: 5px 20px;
        background-color: red;
        color: white;
        font-size: 15px;
        border-radius: 6px;
        text-decoration: none;
    }

    .delete-button:hover {
        background-color: darkred;
    }

    .button {
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        display: inline-block;
    }

    .card {
        margin: 20px auto;
        max-width: 1200px;
        padding: 10px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }


    .delete-button:hover {
      background-color: darkred;
    }

    .form-container {
      background: #fff;
      padding: 20px;
      margin-top: 40px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .form-container h3 {
      margin-bottom: 20px;
      text-align: center;
    }

    .form-container form {
      display: flex;
      flex-direction: column;
    }

    .form-container label {
      font-weight: 600;
      margin-bottom: 5px;
    }

    .form-container input,
    .form-container textarea,
    .form-container button {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 6px;
      border: 1px solid #ddd;
      font-family: 'Poppins', sans-serif;
    }

    .form-container button {
      background-color: #5e5c51;
      color: white;
      font-weight: 600;
      width: 150px;
      align-self: center;
      border: none;
      cursor: pointer;
    }

    .form-container button:hover {
      background-color: #4a4a3c;
    }
    #therapistTable_wrapper .dataTables_length{
      margin-bottom: 20px; 
    }
    #therapistTable_wrapper .dataTables_filter{
      margin-bottom: 20px; 
    }   
    #therapistTable tbody tr:hover {
      background-color:rgb(239, 239, 239);         
    }

  </style>
</head>
<body>
<nav>
    <div class="brand">
        <img src="../../uploads/logo_official.png" alt="Logo">
        UNITEN CARESPACE (Admin)
    </div>
    <div class="nav-right">
        <a href="../../admin_page.php">Dashboard</a>
        <a href="view_therapist.php">Counsellors</a>
        <div class="dropdown">
            <button class="dropbtn">Articles ▾</button>
            <div class="dropdown-content">
                <a href="../articles/publish_article.php">Publish Articles</a>
                <a href="../articles/manage_articles.php">Manage Articles</a>
            </div>
        </div>
        <div class="dropdown">
            <button class="dropbtn">Appointments ▾</button>
            <div class="dropdown-content">
                <a href="view_appointments.php">View Appointments</a>
                <a href="sumary_therapist.php">Summary</a>
                <a href="review_cancellations.php">Cancellations</a>
                
            </div>
        </div>
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

    </div>
</nav>

<div class="main">

    <div class="table-container" >
        <h2 style="text-align:center; margin-bottom: 20px;">Counsellor Management</h2>
       
            <table id="therapistTable" style="width: 100%; border-collapse: collapse; background-color: #fff; font-size: 14px;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Specialty</th>
                        <th>Bio</th>
                        <th>Photo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1; 
                    foreach ($therapist_list as $therapist): ?>
                        <tr>
                            <td><?= $count++ ?></td>  
                            <td><?= $therapist['name'] ?></td>
                            <td><?= $therapist['email'] ?></td>
                            <td><?= $therapist['specialty'] ?></td>
                            <td><?= $therapist['bio'] ?></td>
                            <td>
                                <?php if ($therapist['photo']): ?>
                                    <img src="../../counsellor/uploads/<?= htmlspecialchars($therapist['photo']) ?>" 
                                        onerror="this.onerror=null; this.src='../../uploads/empty_profile_pic.png';"
                                        alt="Photo" 
                                        class="therapist-photo">
                                <?php else: ?>
                                    No photo
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="button delete-button" onclick="confirmDelete(<?= $therapist['id'] ?>);">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-container" style="padding: 30px; max-width: 1200px; margin: 40px auto; background: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 8px;">
        <div class="add-therapist-form">
            <h3 style="text-align: center;">Add New Counsellor</h3>
            <form id="therapistForm" enctype="multipart/form-data" novalidate>
                <label for="name">Counsellor Name:</label>
                <input type="text" name="name" id="name">

                <label for="email">Email:</label>
                <input type="email" name="email" id="email">

                <label for="specialty">Specialty:</label>
                <input type="text" name="specialty" id="specialty" value="" placeholder="Counselor will update this field later" readonly>

                <label for="bio">Bio:</label>
                <input type="text" name="bio" id="bio" value="" placeholder="Counselor will update this field later" readonly>

                <label for="password">Password:</label>
                <input type="text" name="password" id="password" value="Uni10pass!" readonly>

                <label for="photo">Photo (optional):</label>
                <input type="file" name="photo" id="photo" accept="image/*">

                <button type="submit">Add Therapist</button>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#therapistTable').DataTable({
        "pageLength": 2, 
        "lengthMenu": [10, 25, 50, 100],  
        "searching": true,  
        "ordering": true,  
        "paging": true,  
        "info": true,  
    });
});

function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will delete the therapist permanently.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#5e5c51',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '?delete_id=' + id;
        }
    });
}
</script>


<script>
document.getElementById('therapistForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const specialty = document.getElementById('specialty').value.trim();
    const bio = document.getElementById('bio').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!name || !email ) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Fields',
            text: 'Please fill in all required fields.'
        });
        return;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.'
        });
        return;
    }

    if (password.length < 6) {
        Swal.fire({
            icon: 'error',
            title: 'Weak Password',
            text: 'Password must be at least 6 characters long.'
        });
        return;
    }

    const formData = new FormData(this);

    const response = await fetch('view_therapist.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    Swal.fire({
        icon: result.success ? 'success' : 'error',
        title: result.success ? 'Success' : 'Error',
        text: result.message
    }).then(() => {
        if (result.success) {
            location.reload();
        }
    });
});
</script>

</body>
</html>
