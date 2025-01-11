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
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        // Update existing category
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        $result = $stmt->execute();

        if ($result) {
            echo "<script>displayMessage('success', 'Category updated successfully!');</script>";
        } else {
            echo "<script>displayMessage('error', 'Error updating category: " . $conn->error . "');</script>";
        }
    } else {
        // Insert new category
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        $result = $stmt->execute();

        if ($result) {
            echo "<script>displayMessage('success', 'Category added successfully!');</script>";
        } else {
            echo "<script>displayMessage('error', 'Error adding category: " . $conn->error . "');</script>";
        }
    }
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'] ?? '';
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>displayMessage('success', 'Category deleted successfully!');</script>";
    } else {
        echo "<script>displayMessage('error', 'Error deleting category: " . $conn->error . "');</script>";
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
                                        <h5>Categories</h5>
                                    </div>
                                    <div class="card-block table-border-style">
                                        <div class="table-responsive">
                                            <button class="btn btn-info float-right" data-toggle="modal"
                                                data-target="#categoryModal" onclick="resetForm()">Add New
                                                Category</button>
                                            
                                            <table class="table" id="categoryTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $result = $conn->query("SELECT * FROM categories");
                                                    if ($result && $result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr>
                                                                <td>" . htmlspecialchars($row['id']) . "</td>
                                                                <td>" . htmlspecialchars($row['name']) . "</td>
                                                                <td>" . htmlspecialchars($row['description']) . "</td>
                                                                <td>
                                                                    <button class='btn btn-warning btn-sm' onclick='editCategory(" . htmlspecialchars($row['id']) . ", \"" . htmlspecialchars(addslashes($row['name'])) . "\", \"" . htmlspecialchars(addslashes($row['description'])) . "\")'>Edit</button>
                                                                    <a href='?delete_id=" . htmlspecialchars($row['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this category?\")'>Delete</a>
                                                                </td>
                                                            </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='4'>No categories found.</td></tr>";
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
                        <div class="modal" tabindex="-1" role="dialog" id="categoryModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Category</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="">
                                            <input type="hidden" name="id" id="id">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" name="name" id="name" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <input type="text" name="description" id="description"
                                                    class="form-control">
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
    function editCategory(id, name, description) {
        document.getElementById('id').value = id;
        document.getElementById('name').value = name;
        document.getElementById('description').value = description;
        $('#categoryModal').modal('show');
    }

    function resetForm() {
        document.getElementById('id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
    }

    function displayMessage(type, message) {
        let success = document.querySelector(".alert-success");
        let error = document.querySelector(".alert-danger");

        if (type === "success") {
            error.classList.add("d-none");
            success.classList.remove("d-none");
            success.innerHTML = message;

            setTimeout(() => {
                $("#categoryModal").modal('hide');
                success.classList.add("d-none");
                document.getElementById("categoryForm").reset();
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
