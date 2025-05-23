<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global Styles for white background and black text */
        body {
            background-color: #f8f9fa; /* A very light grey, almost white */
            color: #212529; /* Dark charcoal, almost black */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Full viewport height */
            margin: 0;
            flex-direction: column; /* Stack elements vertically */
        }

        .login-container {
            background-color: #ffffff; /* White background for the login card */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            width: 100%;
            max-width: 400px; /* Max width for the form */
        }

        /* Styling for the orange button */
        .btn-orange {
            background-color: #fd7e14; /* A vibrant orange */
            border-color: #fd7e14; /* Match the background color */
            color: #ffffff; /* White text */
            width: 100%; /* Full width button */
            padding: 10px;
            font-size: 1.1rem;
        }
        .btn-orange:hover {
            background-color: #e66a00; /* Slightly darker orange on hover */
            border-color: #e66a00;
            color: #ffffff; /* Ensure text remains white on hover */
        }

        /* Styling for alert messages */
        .alert-error {
            color: #dc3545; /* Bootstrap red for error */
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .alert-success {
            color: #198754; /* Bootstrap green for success */
            background-color: #d1e7dd;
            border-color: #badbco;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Input field styling */
        .form-control {
            color: #212529; /* Dark charcoal for input text */
            background-color: #ffffff; /* White background for input fields */
            border-color: #ced4da; /* Default Bootstrap border color */
        }
        .form-control:focus {
            border-color: #fd7e14; /* Orange border on focus */
            box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.25); /* Orange shadow on focus */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">Login Admin</h2>

        <?php if (isset($_GET['error'])): ?>
            <p class="alert-error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <p class="alert-success"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <form action="pages/proses/proses_login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-orange">Login</button>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] = bin2hex(random_bytes(50)) ?>">
        </form>

        <p class="text-center mt-3">Belum punya akun? <a href="register.php" style="color: #fd7e14; text-decoration: none;">Daftar</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>