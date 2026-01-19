<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DASS Test</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f0f3ef;
            color: #333;
            overflow-x: hidden;
        }

        .navbar {
            width: 100%;
            background-color: #5e5c4f;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-left {
            display: flex;
            align-items: center;
        }

        .navbar-left img {
            width: 45px;
            height: 45px;
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
            max-width: 900px;
            margin: auto;
        }

        .info-box {
            text-align: center;
            margin-bottom: 30px;
        }

        .info-box h2 {
            font-size: 26px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .info-box p {
            font-size: 15px;
            color: #555;
            line-height: 1.6;
        }

        .question {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            padding: 20px;
            margin-bottom: 22px;
            transition: border 0.3s ease;
        }

        .question.unanswered {
            border: 2px solid red;
        }

        .question strong {
            display: block;
            margin-bottom: 12px;
            font-size: 15px;
            color: #222;
        }

        .question label {
            display: inline-block;
            margin: 6px 10px 6px 0;
            font-size: 14px;
        }

        .question p {
            margin-top: 12px;
            font-size: 13px;
            color: #777;
        }

        .submit-btn {
            display: block;
            background-color: #5e5c4f;
            color: white;
            padding: 14px 28px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin: 40px auto 0;
        }

        .submit-btn:hover {
            background-color: #4a493f;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 14px 16px;
            }

            .navbar-right {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                margin-top: 10px;
            }

            .main {
                padding: 20px 12px;
            }
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
    <div class="info-box">
        <h2>DASS-21 Self Assessment</h2>
        <p>Please answer all 21 questions honestly based on your experience over the past week.</p>
    </div>

    <form id="dassForm" action="dass_result.php" method="post">
        <?php
        $questions = [
            "I found it hard to wind down",
            "I was aware of dryness of my mouth",
            "I couldn’t seem to experience any positive feeling at all",
            "I experienced breathing difficulty",
            "I found it difficult to work up the initiative to do things",
            "I tended to over-react to situations",
            "I experienced trembling",
            "I felt that I was using a lot of nervous energy",
            "I was worried about situations in which I might panic",
            "I felt that I had nothing to look forward to",
            "I found myself getting agitated",
            "I found it difficult to relax",
            "I felt down-hearted and blue",
            "I was intolerant of anything that kept me from getting on with what I was doing",
            "I felt I was close to panic",
            "I was unable to become enthusiastic about anything",
            "I felt I wasn’t worth much as a person",
            "I felt that I was rather touchy",
            "I was aware of the action of my heart in the absence of physical exertion",
            "I felt scared without any good reason",
            "I felt that life was meaningless"
        ];

        foreach ($questions as $i => $q) {
            echo "<div class='question' id='qbox" . ($i + 1) . "'>";
            echo "<strong>Q" . ($i + 1) . ": $q</strong>";
            for ($j = 0; $j <= 3; $j++) {
                echo "<label><input type='radio' name='q" . ($i + 1) . "' value='$j'> $j</label>";
            }
            echo "<p><em>0 = Did not apply at all | 3 = Applied very much or most of the time</em></p>";
            echo "</div>";
        }
        ?>
        <button class="submit-btn" type="submit">Submit DASS Test</button>
    </form>
</div>

<script>
document.getElementById("dassForm").addEventListener("submit", function(e) {
    const totalQuestions = 21;
    let unanswered = [];

    for (let i = 1; i <= totalQuestions; i++) {
        const options = document.getElementsByName("q" + i);
        const box = document.getElementById("qbox" + i);
        let answered = false;

        for (let option of options) {
            if (option.checked) {
                answered = true;
                break;
            }
        }


        if (!answered) {
            unanswered.push(i);
            box.classList.add("unanswered");
        } else {
            box.classList.remove("unanswered");
        }
    }

    if (unanswered.length > 0) {
        e.preventDefault();
        const firstUnanswered = document.getElementById("qbox" + unanswered[0]);
        firstUnanswered.scrollIntoView({ behavior: 'smooth' });

        Swal.fire({
    icon: 'warning',
    title: '<span style="font-size:18px;">Incomplete Submission</span>',
    html: '<div style="font-size:14px;">You missed some questions.<br><br><strong>Unanswered:</strong> Q' + unanswered.join(", Q") + '</div>',
    confirmButtonColor: '#5e5c4f',
    width: 400


        });
    }
});
</script><script>

document.getElementById("dassForm").addEventListener("submit", function(e) {
    const totalQuestions = 21;
    let unanswered = [];

    for (let i = 1; i <= totalQuestions; i++) {
        const options = document.getElementsByName("q" + i);
        const box = document.getElementById("qbox" + i);
        let answered = false;

        for (let option of options) {
            if (option.checked) {
                answered = true;
                break;
            }
        }

        if (!answered) {
            unanswered.push(i);
            box.classList.add("unanswered");
        } else {
            box.classList.remove("unanswered");
        }
    }

    if (unanswered.length > 0) {
        e.preventDefault();
        const firstUnanswered = document.getElementById("qbox" + unanswered[0]);
        firstUnanswered.scrollIntoView({ behavior: 'smooth' });

        Swal.fire({
            icon: 'warning',
            title: '<span style="font-size:18px;">Incomplete Submission</span>',
            html: '<div style="font-size:14px;">You missed some questions.<br><br><strong>Unanswered:</strong> Q' + unanswered.join(", Q") + '</div>',
            confirmButtonColor: '#5e5c4f',
            width: 400
        });
    } else {
        isFormSubmitted = true; 
    }
});

window.addEventListener("unload", function (e) {
    if (!isFormSubmitted) {
        navigator.sendBeacon("log_exit.php"); 
    }
});

let isFormSubmitted = false;

window.addEventListener("unload", function (e) {
    if (!isFormSubmitted) {
        navigator.sendBeacon("log_exit.php");
    }
});

document.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", function (e) {
        if (!isFormSubmitted) {
            e.preventDefault();
            Swal.fire({
                title: 'Leave the test?',
                text: "You're still answering the DASS test. Are you sure?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5e5c4f',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, leave',
                cancelButtonText: 'Stay',
                width: 350
            }).then((result) => {
                if (result.isConfirmed) {
                    window.removeEventListener("beforeunload", () => {}); 
                    window.location.href = this.href;
                }
            });
        }
    });
});
</script>

</body>
</html>
