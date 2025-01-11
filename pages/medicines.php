<?php
include('header.php');
include('sidebar.php');
include('conn.php'); // Include your database connection

if (isset($_POST['submit'])) {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $type = $_POST['type'] ?? '';
    $price = $_POST['price'] ?? 0;
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        // Update existing medicine
        // $stmt = $conn->prepare("UPDATE medicines SET name = ?, description = ?, category_id = ?, type = ?, price = ? WHERE id = ?");
        // $stmt->bind_param("ssisd", $name, $description, $category_id, $type, $price, $id);

        
        // $result = $stmt->execute();

        // if ($result) {
        //     echo "<script>displayMessage('success', 'Medicine updated successfully!');</script>";
        // } else {
        //     echo "<script>displayMessage('error', 'Error updating medicine: " . $conn->error . "');</script>";
        // }
        // Prepare the statement
    $stmt = $conn->prepare("UPDATE medicines SET name = ?, description = ?, category_id = ?, type = ?, price = ? WHERE id = ?");
    // Bind the parameters
    $stmt->bind_param("ssisdi", $name, $description, $category_id, $type, $price, $id);
    // Execute the query
    $result = $stmt->execute();

    if ($result) {
        echo "Medicine updated successfully!";
    } else {
        echo "Error updating medicine: " . $conn->error;
    }
    } else {
        // Insert new medicine
        $stmt = $conn->prepare("INSERT INTO medicines (name, description, category_id, type, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisd", $name, $description, $category_id, $type, $price);
        $result = $stmt->execute();

        if ($result) {
            echo "<script>displayMessage('success', 'Medicine added successfully!');</script>";
        } else {
            echo "<script>displayMessage('error', 'Error adding medicine: " . $conn->error . "');</script>";
        }
    }
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'] ?? '';
    $stmt = $conn->prepare("DELETE FROM medicines WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>displayMessage('success', 'Medicine deleted successfully!');</script>";
    } else {
        echo "<script>displayMessage('error', 'Error deleting medicine: " . $conn->error . "');</script>";
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
                                        <h5>Medicines</h5>
                                    </div>
                                    <div class="card-block table-border-style">
                                        <div class="table-responsive">
                                            <button class="btn btn-info float-right" data-toggle="modal"
                                                data-target="#medicineModal" onclick="resetForm()">Add New
                                                Medicine</button>
                                            
                                            <table class="table" id="medicineTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Category</th>
                                                        <th>Type</th>
                                                        <th>Price</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $result = $conn->query("SELECT m.*, c.name AS category_name FROM medicines m LEFT JOIN categories c ON m.category_id = c.id");
                                                    if ($result && $result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr>
                                                                <td>" . htmlspecialchars($row['id']) . "</td>
                                                                <td>" . htmlspecialchars($row['name']) . "</td>
                                                                <td>" . htmlspecialchars($row['description']) . "</td>
                                                                <td>" . htmlspecialchars($row['category_name']) . "</td>
                                                                <td>" . htmlspecialchars($row['type']) . "</td>
                                                                <td>" . htmlspecialchars(number_format($row['price'], 2)) . "</td>
                                                                <td>
                                                                    <button class='btn btn-warning btn-sm' 
                                                                        onclick='editMedicine(
                                                                            " . htmlspecialchars($row['id']) . ", 
                                                                            \"" . htmlspecialchars(addslashes($row['name'])) . "\", 
                                                                            \"" . htmlspecialchars(addslashes($row['description'])) . "\", 
                                                                            " . htmlspecialchars($row['category_id']) . ", 
                                                                            \"" . htmlspecialchars($row['type']) . "\", 
                                                                            " . htmlspecialchars($row['price']) . "
                                                                        )'>Edit</button>
                                                                    <a href='?delete_id=" . htmlspecialchars($row['id']) . "' 
                                                                        class='btn btn-danger btn-sm' 
                                                                        onclick='return confirm(\"Are you sure you want to delete this medicine?\")'>Delete</a>
                                                                </td>
                                                            </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='7'>No medicines found.</td></tr>";
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
                        <div class="modal" tabindex="-1" role="dialog" id="medicineModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Medicine</h5>
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
                                                <textarea name="description" id="description" class="form-control"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="category_id">Category</label>
                                                <select name="category_id" id="category_id" class="form-control">
                                                    <?php
                                                    $categories = $conn->query("SELECT * FROM categories");
                                                    while ($cat = $categories->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($cat['id']) . "'>" . htmlspecialchars($cat['name']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="type">Type</label>
                                                <select name="type" id="type" class="form-control" required>
                                                    <option value="tablet">Tablet</option>
                                                    <option value="injection">Injection</option>
                                                    <option value="syrup">Syrup</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="price">Price</label>
                                                <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                                            </div>
                                            <button type="submit" name="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    function editMedicine(id, name, description, category_id, type, price) {
        document.getElementById('id').value = id;
        document.getElementById('name').value = name;
        document.getElementById('description').value = description;
        document.getElementById('category_id').value = category_id;
        document.getElementById('type').value = type;
        document.getElementById('price').value = price;
        $('#medicineModal').modal('show');
    }

    function resetForm() {
        document.getElementById('id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('category_id').value = '';
        document.getElementById('type').value = 'tablet';
        document.getElementById('price').value = '';
    }
</script>

<?php
include 'footer.php';
?>
