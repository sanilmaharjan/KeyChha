<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-keyboard me-2"></i>KeyChha
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"
                        href="index.php">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>"
                        href="about.php">
                        <i class="bi bi-info-circle me-1"></i>About
                    </a>
                </li>
                <li class="nav-item">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>"
                            href="profile.php">
                            <i class="bi bi-person-circle me-1"></i>My Profile
                        </a>
                    <?php else: ?>
                        <a class="nav-link" href="#" onclick="alert('You need to login first to access your profile.')">
                            <i class="bi bi-person-circle me-1"></i>My Profile
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
            <div class="d-flex">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="btn btn-outline-light me-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </a>
                    <a href="register.php" class="btn btn-warning">
                        <i class="bi bi-person-plus me-1"></i>Register
                    </a>
                <?php else: ?>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>