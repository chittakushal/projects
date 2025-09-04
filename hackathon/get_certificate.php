<?php
session_start();
include 'db.php';

if (!isset($_GET['hackathon_id']) || !isset($_GET['user_id'])) {
    die("Required parameters are missing.");
}

$hackathon_id = $_GET['hackathon_id'];
$user_id = $_GET['user_id'];

// Fetch hackathon details
$hack_query = "SELECT h.*, o.name as organizer_name, o.company_name_or_college_name 
               FROM hackathons h 
               JOIN organizers o ON h.org_id = o.org_id 
               WHERE h.hackathon_id = ?";
$hack_stmt = $conn->prepare($hack_query);
$hack_stmt->bind_param("i", $hackathon_id);
$hack_stmt->execute();
$hackathon = $hack_stmt->get_result()->fetch_assoc();

// Fetch user details
$user_query = "SELECT * FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Verify if user participated
$verify_query = "SELECT * FROM participants WHERE user_id = ? AND hackathon_id = ?";
$verify_stmt = $conn->prepare($verify_query);
$verify_stmt->bind_param("ii", $user_id, $hackathon_id);
$verify_stmt->execute();
if ($verify_stmt->get_result()->num_rows === 0) {
    die("User did not participate in this hackathon.");
}

// Generate certificate number
$certificate_number = "CERT-" . strtoupper(substr(md5($hackathon_id . $user_id . time()), 0, 8));

// Format dates
$start_date = date("F j, Y", strtotime($hackathon['start_date']));
$end_date = date("F j, Y", strtotime($hackathon['end_date']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Participation - <?= htmlspecialchars($hackathon['name']) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap');
        
        body {
            margin: 0;
            padding: 40px;
            background: #f0f0f0;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .certificate-container {
            background: #fff;
            padding: 50px;
            width: 842px; /* A4 width */
            height: 595px; /* A4 height */
            position: relative;
            margin: auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .certificate {
            border: 25px solid #f8b42c;
            padding: 25px;
            height: calc(100% - 100px);
            position: relative;
            background: linear-gradient(135deg, #fff 50%, #f9f9f9 50%);
        }

        .certificate:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M20 20L0 20L0 0L20 0L20 20L40 20L40 40L20 40L20 20Z' fill='%23f8b42c' fill-opacity='0.05'/%3E%3C/svg%3E");
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 1;
            text-align: center;
            height: 100%;
        }

        .title {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            color: #2c3e50;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .subtitle {
            font-size: 24px;
            color: #7f8c8d;
            margin-bottom: 40px;
        }

        .recipient {
            font-size: 36px;
            color: #2c3e50;
            margin: 20px 0;
            font-family: 'Playfair Display', serif;
        }

        .description {
            font-size: 18px;
            color: #34495e;
            line-height: 1.6;
            margin: 20px 0;
        }

        .dates {
            font-size: 16px;
            color: #7f8c8d;
            margin: 15px 0;
        }

        .certificate-number {
            position: absolute;
            bottom: 20px;
            left: 50px;
            font-size: 12px;
            color: #95a5a6;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
        }

        .signature {
            text-align: center;
        }

        .signature img {
            width: 150px;
            height: 60px;
            margin-bottom: 10px;
        }

        .signature-line {
            width: 200px;
            border-top: 2px solid #34495e;
            margin: 10px auto;
        }

        .signature-name {
            font-size: 16px;
            color: #34495e;
        }

        .signature-title {
            font-size: 14px;
            color: #7f8c8d;
        }

        @media print {
            body {
                padding: 0;
                background: none;
            }

            .certificate-container {
                box-shadow: none;
            }

            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate">
            <div class="content">
                <div class="title">Certificate of Participation</div>
                <div class="subtitle">This is to certify that</div>
                <div class="recipient"><?= htmlspecialchars($user['name']) ?></div>
                <div class="description">
                    has successfully participated in<br>
                    <strong><?= htmlspecialchars($hackathon['name']) ?></strong><br>
                    organized by <?= htmlspecialchars($hackathon['organizer_name']) ?><br>
                    <?= htmlspecialchars($hackathon['company_name_or_college_name']) ?>
                </div>
                <div class="dates">
                    From <?= $start_date ?> to <?= $end_date ?>
                </div>
                
                <div class="signatures">
                    <div class="signature">
                        <img src="/api/placeholder/150/60" alt="Event Director Signature">
                        <div class="signature-line"></div>
                        <div class="signature-name">G. G Thirumalesh</div>
                        <div class="signature-title">Event Director</div>
                    </div>
                    <div class="signature">
                        <img src="/api/placeholder/150/60" alt="Organization Head Signature">
                        <div class="signature-line"></div>
                        <div class="signature-name">Kushal</div>
                        <div class="signature-title">Organization Head</div>
                    </div>
                </div>
                
                 
            </div>
        </div>
    </div>
    
    <button onclick="window.print()" class="print-button" style="position: fixed; top: 20px; right: 20px; padding: 10px 20px; background: #f8b42c; border: none; border-radius: 5px; cursor: pointer;">
        🖨️ Print Certificate
    </button>
</body>
</html>

<?php
$hack_stmt->close();
$user_stmt->close();
$verify_stmt->close();
$conn->close();
?>