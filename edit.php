<?php
include 'db_connection.php';

$error = "";
$data = ["username" => "", "email" => ""];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT username, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_GET['id'];
    $password = $_POST['password'];
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password'])) {
        $usernameForm = $_POST['username'];
        $emailForm = $_POST['email'];
        $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $usernameForm, $emailForm, $newPassword, $id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Error updating user: " . $conn->error;
        }
    } else {
        $error = "Wrong password. Please try again.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit User</h2>
    <?php if (!empty($error)): ?>
                <p style="color:red; font-size:20px;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="edit.php?id=<?php echo $_GET['id']; ?>" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username"
                   value="<?php echo htmlspecialchars($data['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                   value="<?php echo htmlspecialchars($data['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Edit</button>
    </form>
</div>
</body>
</html>
