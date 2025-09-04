<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

$org_id = $_SESSION['org_id'];

// Fetch hackathons using only existing fields
$query = "SELECT * FROM hackathons WHERE org_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $org_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle hackathon deletion
if (isset($_POST['delete_hackathon'])) {
    $hackathon_id = $_POST['hackathon_id'];

    $delete_query = "DELETE FROM hackathons WHERE hackathon_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $hackathon_id);
    
    if ($delete_stmt->execute()) {
        echo "<script>alert('Hackathon deleted successfully'); window.location.href = 'manage_hackathons.php';</script>";
    } else {
        echo "<script>alert('Error deleting hackathon'); window.location.href = 'manage_hackathons.php';</script>";
    }

    $delete_stmt->close();
}

// Handle certificate issuance
if (isset($_POST['issue_certificates'])) {
    $hackathon_id = $_POST['hackathon_id'];
    
    // Update hackathon status
    $update_query = "UPDATE hackathons SET is_certificates_issued = TRUE WHERE hackathon_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("i", $hackathon_id);
    
    if ($update_stmt->execute()) {
        echo "<script>alert('Certificates marked as issued successfully'); window.location.href = 'manage_hackathons.php';</script>";
    } else {
        echo "<script>alert('Error marking certificates as issued'); window.location.href = 'manage_hackathons.php';</script>";
    }
    
    $update_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hackathons</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.2) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.18);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
        }
        
        .gradient-bg {
            background: linear-gradient(120deg, #1a1c2e 0%, #2a2d4a 100%);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .delete-btn {
            transition: all 0.3s ease;
        }
        
        .delete-btn:hover {
            transform: scale(1.1);
            color: #ff4757;
        }

        .action-link {
            transition: all 0.2s ease;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .action-link:hover {
            transform: translateY(-2px);
        }

        .modal-backdrop {
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <?php include 'navbar.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-white">Manage Hackathons</h1>
            <a href="create_hackathon.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-all duration-300 transform hover:scale-105">
                + New Hackathon
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($hackathon = $result->fetch_assoc()) : ?>
                <div class="card rounded-xl p-6 relative group">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-2xl font-bold text-white"><?= htmlspecialchars($hackathon['name']) ?></h2>
                        <button onclick="confirmDelete(<?= $hackathon['hackathon_id'] ?>)" 
                                class="delete-btn text-gray-400 opacity-0 group-hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-gray-300 mb-6"><?= htmlspecialchars($hackathon['description']) ?></p>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="text-gray-300">
                            <span class="font-semibold">Start:</span> 
                            <?= date('M d, Y', strtotime($hackathon['start_date'])) ?>
                        </div>
                        <div class="text-gray-300">
                            <span class="font-semibold">End:</span> 
                            <?= date('M d, Y', strtotime($hackathon['end_date'])) ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 lg:grid-cols-2 gap-3">
                        <a href="hackathon.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>" 
                           class="action-link bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 text-center">
                            Overview
                        </a>
                        <a href="manage_rounds.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>" 
                           class="action-link bg-green-600/20 text-green-400 hover:bg-green-600/30 text-center">
                            Rounds
                        </a>
                        <a href="participants.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>" 
                           class="action-link bg-purple-600/20 text-purple-400 hover:bg-purple-600/30 text-center">
                            Participants
                        </a>
                        <?php if (!$hackathon['is_certificates_issued']) : ?>
                            <button onclick="confirmIssueCertificates(<?= $hackathon['hackathon_id'] ?>)"
                                    class="action-link bg-yellow-600/20 text-yellow-400 hover:bg-yellow-600/30 text-center">
                                Issue Certificates
                            </button>
                        <?php else : ?>
                            <button disabled
                                    class="action-link bg-gray-600/20 text-gray-400 cursor-not-allowed">
                                Certificates Issued
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <!-- Delete Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 modal-backdrop bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-md w-full mx-4 overflow-hidden">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Delete Hackathon</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to delete this hackathon? This action cannot be undone.</p>
                
                <form method="POST" class="flex gap-4">
                    <input type="hidden" id="deleteHackathonId" name="hackathon_id">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" name="delete_hackathon" 
                            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Issue Certificates Modal -->
    <div id="certificateModal" class="hidden fixed inset-0 modal-backdrop bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-md w-full mx-4 overflow-hidden">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Issue Certificates</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to mark certificates as issued for this hackathon? This action cannot be undone.</p>
                
                <form method="POST" class="flex gap-4">
                    <input type="hidden" id="certificateHackathonId" name="hackathon_id">
                    <button type="button" onclick="closeCertificateModal()" 
                            class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" name="issue_certificates" 
                            class="flex-1 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                        Issue Certificates
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Delete Modal Functions
        function confirmDelete(hackathonId) {
            document.getElementById('deleteHackathonId').value = hackathonId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Certificate Modal Functions
        function confirmIssueCertificates(hackathonId) {
            document.getElementById('certificateHackathonId').value = hackathonId;
            document.getElementById('certificateModal').classList.remove('hidden');
        }
        
        function closeCertificateModal() {
            document.getElementById('certificateModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.querySelectorAll('.modal-backdrop').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-backdrop').forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>