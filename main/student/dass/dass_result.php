<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['userid'])) {
    header("Location: ../credentials/userlogin.php");
    exit();
}


$responses = [];
for ($i = 1; $i <= 21; $i++) {
    $responses["q$i"] = isset($_POST["q$i"]) ? $_POST["q$i"] : 0;
}

$depressionScore = $responses['q1'] + $responses['q3'] + $responses['q5'] + $responses['q10'] + $responses['q13'] + $responses['q16'] + $responses['q17'] + $responses['q21'];
$anxietyScore = $responses['q2'] + $responses['q4'] + $responses['q7'] + $responses['q9'] + $responses['q15'] + $responses['q19'] + $responses['q20'];
$stressScore = $responses['q6'] + $responses['q8'] + $responses['q11'] + $responses['q12'] + $responses['q14'] + $responses['q18'];

function getSeverity($score) {
    if ($score <= 9) return "Normal";
    elseif ($score <= 13) return "Mild";
    elseif ($score <= 20) return "Moderate";
    else return "Severe";
}

function getAdvice($type, $level) {
    $advices = [
        "Stress" => [
            "Normal" => "Your stress is within a healthy range. Maintain your current habits.",
            "Mild" => "Mild stress may arise from daily pressures. Try taking short breaks and practicing deep breathing.",
            "Moderate" => "Moderate stress may impact your focus and energy. Consider talking to someone or engaging in stress-reducing activities.",
            "Severe" => "High stress levels can affect your mental and physical well-being. Seek professional support and practice stress management."
        ],
        "Anxiety" => [
            "Normal" => "No signs of anxiety. Continue maintaining your healthy mindset.",
            "Mild" => "You have mild anxiety. Deep breathing and mindfulness may help manage it.",
            "Moderate" => "Moderate anxiety can interfere with daily routines. Try grounding techniques and talk to a counselor if needed.",
            "Severe" => "Severe anxiety may require professional help. It's okay to seek support—you're not alone."
        ],
        "Depression" => [
            "Normal" => "You are not experiencing signs of depression. Keep engaging in positive and meaningful activities.",
            "Mild" => "Mild depression signs present. Try engaging in enjoyable activities and staying socially connected.",
            "Moderate" => "Moderate depression may cause low motivation. Don’t hesitate to reach out to a support system.",
            "Severe" => "Severe symptoms can feel overwhelming. Professional help is highly recommended for recovery."
        ]
    ];

    return $advices[$type][$level];
}

$depressionLevel = getSeverity($depressionScore);
$anxietyLevel = getSeverity($anxietyScore);
$stressLevel = getSeverity($stressScore);


$conn = mysqli_connect("localhost", "root", "root", "user") or die("DB connection failed");
$username = $_SESSION['userid'];

$insert = "INSERT INTO dass_results (username, stress_level, anxiety_level, depression_level)
           VALUES ('$username', '$stressLevel', '$anxietyLevel', '$depressionLevel')";
mysqli_query($conn, $insert);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DASS-21 Results</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { background-color: #f0f1ec; color: #2a2a2a; }

        #loadingOverlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #5e5c4f;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            font-weight: 600;
            z-index: 9999;
            transition: opacity 0.4s ease;
        }

        .spinner {
            border: 5px solid rgba(255, 255, 255, 0.2);
            border-top: 5px solid white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-bottom: 16px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

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
            width: 42px;
            height: 42px;
            border-radius: 50%;
            margin-right: 14px;
        }

        .navbar-left span {
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .navbar-right a {
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
        }

        .navbar-right a:hover {
            opacity: 0.85;
        }

        .dropdown {
            position: relative;
        }

        .dropbtn {
            background: none;
            border: none;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
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

        .dropdown-content a:hover {
            background-color: #f3f3f3;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .main {
            padding: 40px 20px;
            max-width: 800px;
            margin: auto;
        }

        .result-box {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 0 18px rgba(0,0,0,0.06);
            text-align: center;
        }

        .result-box h2 {
            font-size: 24px;
            margin-bottom: 18px;
            color: #21875c;
        }

        .result-line {
            font-size: 16px;
            margin: 12px 0;
            color: #333;
        }

        .result-line span {
            font-weight: 700;
            color: #444;
        }

        .advice-text {
            margin-top: 30px;
            background: #f9f9f9;
            border-left: 5px solid #5e5c4f;
            padding: 20px 24px;
            text-align: left;
            font-size: 15px;
            color: #444;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            line-height: 1.6;
        }

        .advice-text p {
            margin-bottom: 12px;
        }

        .advice-text p:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>

<div id="loadingOverlay">
    <div class="spinner"></div>
    Calculating your DASS-21 results...
</div>

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

<div class="main" id="mainContent" style="display: none;">
    <div class="result-box">
        <h2>Your DASS-21 Assessment Summary</h2>
        <div class="result-line">Stress Level: <span><?= $stressLevel ?></span></div>
        <div class="result-line">Anxiety Level: <span><?= $anxietyLevel ?></span></div>
        <div class="result-line">Depression Level: <span><?= $depressionLevel ?></span></div>

        <div class="advice-text">
            <p><strong>Stress Insight:</strong> <?= getAdvice("Stress", $stressLevel) ?></p>
            <p><strong>Anxiety Insight:</strong> <?= getAdvice("Anxiety", $anxietyLevel) ?></p>
            <p><strong>Depression Insight:</strong> <?= getAdvice("Depression", $depressionLevel) ?></p>
        </div>
    </div>
</div>

<script>
window.addEventListener('load', function () {
    setTimeout(function () {
        document.getElementById("loadingOverlay").style.opacity = "0";
        setTimeout(() => {
            document.getElementById("loadingOverlay").style.display = "none";
            document.getElementById("mainContent").style.display = "block";
        }, 400);
    }, 1300);
});
</script>

</body>
</html>
