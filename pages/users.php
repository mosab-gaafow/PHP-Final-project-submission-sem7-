<?php
// Start the session
session_start();

// Check if the session variable 'username' is set
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    // If not, redirect to the login page
    header("Location: index.php");
    exit();
}
?>

<?php
include('header.php');
include('sidebar.php');
include('conn.php'); // Include your database connection

if (isset($_POST['submit'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        // Handle file upload
        $imagePath = '';
        if (!empty($_FILES['image']['name'])) {
            $imageName = time() . "_" . basename($_FILES['image']['name']);
            $targetDir = "uploads/"; // Directory to store images
            $targetFile = $targetDir . $imageName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            }
        }

        // Update existing users
        if ($imagePath) {
            $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ?, email = ?, phone = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $username, $password, $role, $email, $phone, $imagePath, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $username, $password, $role, $email, $phone, $id);
        }

        $result = $stmt->execute();
        if ($result) {
            echo "<script>displayMessage('success', 'User updated successfully!');</script>";
        } else {
            echo "<script>displayMessage('error', 'Error updating user: " . $conn->error . "');</script>";
        }
    } else {
        // Handle file upload
        $imagePath = '';
        if (!empty($_FILES['image']['name'])) {
            $imageName = time() . "_" . basename($_FILES['image']['name']);
            $targetDir = "uploads/"; // Directory to store images
            $targetFile = $targetDir . $imageName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            }
        }

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, email, phone, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $password, $role, $email, $phone, $imagePath);

        $result = $stmt->execute();
        if ($result) {
            echo "<script>displayMessage('success', 'User added successfully!');</script>";
        } else {
            echo "<script>displayMessage('error', 'Error adding user: " . $conn->error . "');</script>";
        }
    }

}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'] ?? '';
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>displayMessage('success', 'users deleted successfully!');</script>";
    } else {
        echo "<script>displayMessage('error', 'Error deleting users: " . $conn->error . "');</script>";
    }
}
?>

<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Users</h5>
                                    </div>
                                    <div class="card-block table-border-style">
                                        <div class="table-responsive">
                                            <button class="btn btn-info float-right" data-toggle="modal"
                                                data-target="#usersModal" onclick="resetForm()">Add New
                                                users</button>

                                            <table class="table" id="usersTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>username</th>
                                                        <th>password</th>
                                                        <th>role</th>
                                                        <th>email</th>
                                                        <th>phone</th>
                                                        <th>Profile Image</th>

                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <style>
                                                        .profile-img {
                                                            width: 50px;
                                                            height: 50px;
                                                            border-radius: 50%;
                                                            object-fit: cover;
                                                        }
                                                    </style>

                                                    <?php
                                                    $result = $conn->query("SELECT * FROM users");
                                                    if ($result && $result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr>
    <td>" . htmlspecialchars($row['id']) . "</td>
    <td>" . htmlspecialchars($row['username']) . "</td>
    <td>" . htmlspecialchars($row['password']) . "</td>
    <td>" . htmlspecialchars($row['role']) . "</td>
    <td>" . htmlspecialchars($row['email']) . "</td>
    <td>" . htmlspecialchars($row['phone']) . "</td>
    <td>
   
    <img src='" . (!empty($row['image']) ? htmlspecialchars($row['image']) : 'uploads/default.jpg') . "' alt='Profile Image' class='profile-img'>

    <td>
        <button class='btn btn-warning btn-sm' onclick='editusers(" . htmlspecialchars($row['id']) . ", \"" . htmlspecialchars(addslashes($row['username'])) . "\", \"" . htmlspecialchars(addslashes($row['password'])) . "\", \"" . htmlspecialchars(addslashes($row['role'])) . "\", \"" . htmlspecialchars(addslashes($row['email'])) . "\", \"" . htmlspecialchars(addslashes($row['phone'])) . "\", \"" . htmlspecialchars(addslashes($row['image'])) . "\")'>Edit</button>
        <a href='?delete_id=" . htmlspecialchars($row['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
    </td>
</tr>";

                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='4'>No users found.</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal" tabindex="-1" role="dialog" id="usersModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">users</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                       <form method="POST" action="" enctype="multipart/form-data">

                                            <input type="hidden" name="id" id="id">
                                            <div class="form-group">
                                                <label for="username">username</label>
                                                <input type="text" name="username" id="username" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">password</label>
                                                <input type="password" name="password" id="password"
                                                    class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="role">Role</label>
                                                <select name="role" id="role" class="form-control" required>
                                                    <option value="admin">Admin</option>
                                                    <option value="doctor">Doctor</option>
                                                    <option value="pharmacist">Pharmacist</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="email">email</label>
                                                <input type="text" name="email" id="email" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">phone</label>
                                                <input type="tel" name="phone" id="phone" class="form-control" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="image">Profile Image</label>
                                                <input type="file" name="image" id="image" class="form-control">
                                            </div>



                                            <button type="submit" name="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function editusers(id, username, password, role, email, phone) {
        document.getElementById('id').value = id;
        document.getElementById('username').value = username;
        document.getElementById('password').value = password;
        document.getElementById('role').value = role;
        document.getElementById('email').value = email;
        document.getElementById('phone').value = phone;
        $('#usersModal').modal('show');
    }


    function resetForm() {
        document.getElementById('id').value = '';
        document.getElementById('username').value = '';
        document.getElementById('password').value = '';
        document.getElementById('role').value = '';
        document.getElementById('email').value = '';
        document.getElementById('phone').value = '';
    }

    function displayMessage(type, message) {
        let success = document.querySelector(".alert-success");
        let error = document.querySelector(".alert-danger");

        if (type === "success") {
            error.classList.add("d-none");
            success.classList.remove("d-none");
            success.innerHTML = message;

            setTimeout(() => {
                $("#usersModal").modal('hide');
                success.classList.add("d-none");
                document.getElementById("usersForm").reset();
            }, 3000);
        } else {
            error.classList.remove("d-none");
            error.innerHTML = message;
        }
    }
</script>



<?php
include 'footer.php';
?>