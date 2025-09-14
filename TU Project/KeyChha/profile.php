<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$db = new mysqli('localhost', 'root', '', 'keychha');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get user data
$username = $_SESSION['username'];
$user_query = $db->prepare("SELECT * FROM user WHERE username = ?");
$user_query->bind_param("s", $username);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

// Initialize recent_stats as empty array
$recent_stats = [];
$recent_stats_result = null;

// Get user stats summary
$stats_summary_query = $db->prepare("
    SELECT 
        COUNT(*) as total_sessions,
        AVG(wpm) as average_wpm,
        AVG(accuracy) as average_accuracy,
        MAX(wpm) as highest_wpm,
        SUM(correct) as total_correct,
        SUM(errors) as total_errors
    FROM user_stats 
    WHERE user_id = ?
");
$stats_summary_query->bind_param("i", $user['user_id']);
$stats_summary_query->execute();
$stats_summary = $stats_summary_query->get_result()->fetch_assoc();

// Calculate current level based on highest WPM (not average)
$highest_wpm = $stats_summary['highest_wpm'] ?? 0;
$current_level = min(floor($highest_wpm / 10) + 1, 10); // Levels 1-10 based on highest WPM

// Get recent stats (5 most recent)
$recent_stats_query = $db->prepare("
    SELECT * FROM user_stats 
    WHERE user_id = ? 
    ORDER BY complete_date_time DESC 
    LIMIT 5
");
$recent_stats_query->bind_param("i", $user['user_id']);
$recent_stats_query->execute();
$recent_stats_result = $recent_stats_query->get_result();
if ($recent_stats_result) {
    $recent_stats = $recent_stats_result->fetch_all(MYSQLI_ASSOC);
}

// Handle profile updates
$update_success = false;
$password_success = false;
$password_error = '';
$reset_success = false;
$delete_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $new_username = trim($_POST['username']);
        $email = trim($_POST['email']);

        // Validate inputs
        if (!empty($new_username) && !empty($email)) {
            // Check if username is already taken (excluding current user)
            $check_username = $db->prepare("SELECT user_id FROM user WHERE username = ? AND user_id != ?");
            $check_username->bind_param("si", $new_username, $user['user_id']);
            $check_username->execute();

            if ($check_username->get_result()->num_rows > 0) {
                $username_error = "Username already taken";
            } else {
                // Update user info
                $update_query = $db->prepare("UPDATE user SET username = ?, email = ? WHERE user_id = ?");
                $update_query->bind_param("ssi", $new_username, $email, $user['user_id']);

                if ($update_query->execute()) {
                    $update_success = true;
                    // Update session if username changed
                    if ($new_username !== $username) {
                        $_SESSION['username'] = $new_username;
                        $username = $new_username;
                        // Refresh user data
                        $user['username'] = $new_username;
                        $user['email'] = $email;
                    }
                } else {
                    $update_error = "Error updating profile: " . $db->error;
                }
            }
        } else {
            $update_error = "Username and email cannot be empty";
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify current password
        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 8) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_pass = $db->prepare("UPDATE user SET password = ? WHERE user_id = ?");
                    $update_pass->bind_param("si", $hashed_password, $user['user_id']);

                    if ($update_pass->execute()) {
                        $password_success = "Password updated successfully!";
                    } else {
                        $password_error = "Error updating password: " . $db->error;
                    }
                } else {
                    $password_error = "Password must be at least 8 characters long";
                }
            } else {
                $password_error = "New passwords do not match";
            }
        } else {
            $password_error = "Current password is incorrect";
        }
    } elseif (isset($_POST['reset_stats'])) {
        // Reset user stats
        $reset_query = $db->prepare("DELETE FROM user_stats WHERE user_id = ?");
        $reset_query->bind_param("i", $user['user_id']);

        if ($reset_query->execute()) {
            $reset_success = true;
            // Refresh stats
            $stats_summary = [
                'total_sessions' => 0,
                'average_wpm' => 0,
                'average_accuracy' => 0,
                'highest_wpm' => 0,
                'total_correct' => 0,
                'total_errors' => 0
            ];
            $current_level = 1;
            $recent_stats = []; // Empty array instead of mysqli_result
        } else {
            $reset_error = "Error resetting stats: " . $db->error;
        }
    } elseif (isset($_POST['delete_account'])) {
        // Delete user account
        $delete_query = $db->prepare("DELETE FROM user WHERE user_id = ?");
        $delete_query->bind_param("i", $user['user_id']);

        if ($delete_query->execute()) {
            $delete_success = true;
            session_destroy();
            header('Location: login.php');
            exit();
        } else {
            $delete_error = "Error deleting account: " . $db->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile | KeyChha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #6e48aa;
            --secondary-color: #9d50bb;
            --accent-color: #4776e6;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .profile-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 25px;
            width: 100%;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: bold;
            margin: 0 auto 15px;
        }

        .stats-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            width: 100%;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background-color: rgba(110, 72, 170, 0.05);
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 4px;
        }

        .nav-pills .nav-link {
            color: var(--dark-color);
            font-weight: 500;
            border-radius: 6px;
            padding: 8px 16px;
            margin-right: 8px;
            font-size: 0.9rem;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .tab-content {
            background-color: white;
            border-radius: 0 0 10px 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-top: none;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(110, 72, 170, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #c82333);
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .btn-replay {
            background: linear-gradient(135deg, var(--accent-color), #4776e6);
            color: white;
            padding: 5px 10px;
            font-size: 0.8rem;
            border-radius: 4px;
            border: none;
        }

        .badge-stat {
            background-color: var(--primary-color);
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 50px;
            font-weight: 500;
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
            font-size: 0.9rem;
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            padding: 12px;
        }

        .table td {
            vertical-align: middle;
            padding: 10px 12px;
        }

        .level-badge {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 500;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 0.75rem;
        }

        .danger-zone {
            border-left: 4px solid var(--danger-color);
            padding: 15px;
            background-color: rgba(220, 53, 69, 0.05);
            border-radius: 8px;
            margin-top: 20px;
        }

        .list-group-item {
            padding: 12px 15px;
            font-size: 0.9rem;
        }

        .alert {
            font-size: 0.9rem;
            padding: 10px 15px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <div class="container py-4">
        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-5">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($username, 0, 1)); ?>
                        </div>
                        <h4 class="mb-1"><?php echo htmlspecialchars($username); ?></h4>
                        <span class="badge-stat">Level <?php echo $current_level; ?></span>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-clock-history me-2"></i>Total Sessions</span>
                            <span
                                class="badge bg-primary rounded-pill"><?php echo $stats_summary['total_sessions'] ?? 0; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-check-circle me-2"></i>Correct Keystrokes</span>
                            <span
                                class="badge bg-primary rounded-pill"><?php echo $stats_summary['total_correct'] ?? 0; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-x-circle me-2"></i>Error Keystrokes</span>
                            <span
                                class="badge bg-primary rounded-pill"><?php echo $stats_summary['total_errors'] ?? 0; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-envelope me-2"></i>Email</span>
                            <span class="text-muted"><?php echo htmlspecialchars($user['email'] ?? 'Not set'); ?></span>
                        </li>
                    </ul>
                </div>

                <div class="stats-card">
                    <h5 class="mb-3"><i class="bi bi-graph-up me-2"></i> Performance Summary</h5>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Average Accuracy</span>
                            <span><?php echo round($stats_summary['average_accuracy'] ?? 0, 1); ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar"
                                style="width: <?php echo $stats_summary['average_accuracy'] ?? 0; ?>%"
                                aria-valuenow="<?php echo $stats_summary['average_accuracy'] ?? 0; ?>" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Average WPM</span>
                            <span><?php echo round($stats_summary['average_wpm'] ?? 0, 1); ?></span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar"
                                style="width: <?php echo min(($stats_summary['average_wpm'] ?? 0) / 2, 100); ?>%"
                                aria-valuenow="<?php echo $stats_summary['average_wpm'] ?? 0; ?>" aria-valuemin="0"
                                aria-valuemax="200"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Highest WPM</span>
                            <span><?php echo round($stats_summary['highest_wpm'] ?? 0, 1); ?></span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar"
                                style="width: <?php echo min(($stats_summary['highest_wpm'] ?? 0) / 2, 100); ?>%"
                                aria-valuenow="<?php echo $stats_summary['highest_wpm'] ?? 0; ?>" aria-valuemin="0"
                                aria-valuemax="200"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-7">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-stats-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-stats" type="button" role="tab">
                            <i class="bi bi-bar-chart-line me-1"></i> Statistics
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-profile" type="button" role="tab">
                            <i class="bi bi-person me-1"></i> Profile
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-password-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-password" type="button" role="tab">
                            <i class="bi bi-lock me-1"></i> Security
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <!-- Statistics Tab -->
                    <div class="tab-pane fade show active" id="pills-stats" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4><i class="bi bi-bar-chart-line me-2"></i>Your Typing Statistics</h4>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#resetStatsModal">
                                <i class="bi bi-trash me-1"></i> Reset Stats
                            </button>
                        </div>

                        <div class="row text-center g-3 mb-3">
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo $stats_summary['total_sessions'] ?? 0; ?></div>
                                    <div class="stat-label">Total Sessions</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <div class="stat-value"><?php echo round($stats_summary['average_wpm'] ?? 0, 1); ?>
                                    </div>
                                    <div class="stat-label">Average WPM</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-item">
                                    <div class="stat-value">
                                        <?php echo round($stats_summary['average_accuracy'] ?? 0, 1); ?>%
                                    </div>
                                    <div class="stat-label">Avg Accuracy</div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <h5 class="mb-3"><i class="bi bi-clock-history me-2"></i>Recent Sessions</h5>

                        <?php if (!empty($recent_stats)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Level</th>
                                            <th>Date & Time</th>
                                            <th>WPM</th>
                                            <th>Accuracy</th>
                                            <th>Correct</th>
                                            <th>Errors</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_stats as $session): ?>
                                            <tr>
                                                <td><span class="level-badge">Level <?php echo $session['level']; ?></span></td>
                                                <td><?php echo date('M j, Y H:i', strtotime($session['complete_date_time'])); ?>
                                                </td>
                                                <td><?php echo $session['wpm']; ?></td>
                                                <td><?php echo $session['accuracy']; ?>%</td>
                                                <td><?php echo $session['correct']; ?></td>
                                                <td><?php echo $session['errors']; ?></td>
                                                <td>
                                                    <button class="btn btn-replay btn-sm"
                                                        onclick="replayLevel(<?php echo $session['level']; ?>)">
                                                        <i class="bi bi-arrow-repeat me-1"></i> Replay
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> No typing sessions recorded yet. Start
                                practicing to see your stats!
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Edit Profile Tab -->
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel">
                        <h4 class="mb-3"><i class="bi bi-person me-2"></i>Edit Profile</h4>

                        <?php if (isset($update_error)): ?>
                            <div class="alert alert-danger mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $update_error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo htmlspecialchars($username); ?>" required>
                                <?php if (isset($username_error)): ?>
                                    <div class="text-danger small mt-1"><?php echo $username_error; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Update Profile
                            </button>
                        </form>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="pills-password" role="tabpanel">
                        <h4 class="mb-3"><i class="bi bi-lock me-2"></i>Change Password</h4>

                        <?php if ($password_error): ?>
                            <div class="alert alert-danger mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $password_error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password"
                                    name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                    required>
                                <div class="form-text">Password must be at least 8 characters long</div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="confirm_password" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary">
                                <i class="bi bi-key-fill me-1"></i> Change Password
                            </button>

                            <div class="danger-zone mt-4">
                                <h5 class="text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Danger Zone
                                </h5>
                                <p class="small text-muted mb-0">These actions are irreversible. Proceed with caution.
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div>
                                        <h6 class="mb-1">Delete Account</h6>
                                        <p class="small text-muted mb-0">Permanently delete your account and all data
                                        </p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteAccountModal">
                                        <i class="bi bi-trash-fill me-1"></i> Delete Account
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Stats Modal -->
    <div class="modal fade" id="resetStatsModal" tabindex="-1" aria-labelledby="resetStatsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="resetStatsModalLabel">Reset Statistics</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reset all your typing statistics? This action cannot be undone.</p>
                    <p>All your session data will be permanently deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="reset_stats" class="btn btn-warning">Reset Statistics</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to permanently delete your account? This action cannot be undone.</p>
                    <p>All your data will be erased including your typing statistics.</p>
                    <div class="mb-3">
                        <label for="deletePassword" class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" id="deletePassword" name="delete_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="delete_account" class="btn btn-danger">Delete Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation validation
        document.querySelector('form[name="change_password"]')?.addEventListener('submit', function (e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New password and confirmation do not match!');
            }

            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
            }
        });

        // Replay level function
        function replayLevel(level) {
            window.location.href = `index.php?level=${level}`;
        }

        // Delete account confirmation
        document.querySelector('form[action*="delete_account"]')?.addEventListener('submit', function (e) {
            const password = document.getElementById('deletePassword').value;
            if (!password) {
                e.preventDefault();
                alert('Please enter your password to confirm account deletion');
            }
        });

        // Show alerts for success messages
        <?php if ($update_success): ?>
            alert('Profile updated successfully!');
        <?php endif; ?>

        <?php if ($password_success): ?>
            alert('<?php echo $password_success; ?>');
        <?php endif; ?>

        <?php if ($reset_success): ?>
            alert('Your stats have been reset successfully!');
        <?php endif; ?>

        <?php if (isset($reset_error)): ?>
            alert('<?php echo $reset_error; ?>');
        <?php endif; ?>

        <?php if (isset($delete_error)): ?>
            alert('<?php echo $delete_error; ?>');
        <?php endif; ?>
    </script>
</body>

</html>