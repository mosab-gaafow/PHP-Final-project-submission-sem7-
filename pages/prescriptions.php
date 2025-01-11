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
    $patient_id = $_POST['patient_id'] ?? '';
    $doctor_id = $_POST['doctor_id'] ?? '';
    $medicine_id = $_POST['medicine_id'] ?? '';
    $instructions = $_POST['instructions'] ?? '';
    $status = $_POST['status'] ?? '';
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        // Update existing prescription
        $stmt = $conn->prepare("UPDATE prescriptions SET patient_id = ?, doctor_id = ?, medicine_id = ?, instructions = ?, status = ? WHERE id = ?");
        $stmt->bind_param("iiissi", $patient_id, $doctor_id, $medicine_id, $instructions, $status, $id);

        $result = $stmt->execute();

        if ($result) {
            echo "<script>displayMessage('success', 'Prescription updated successfully!');</script>";
        } else {
            echo "<script>displayMessage('error', 'Error updating prescription: " . $conn->error . "');</script>";
        }
    } else {
        // Insert new prescription
        $stmt = $conn->prepare("INSERT INTO prescriptions (patient_id, doctor_id, medicine_id, instructions, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $patient_id, $doctor_id, $medicine_id, $instructions, $status);

        $result = $stmt->execute();

        if ($result) {
            echo "<script>displayMessage('success', 'Prescription added successfully!');</script>";
        } else {
            echo "<script>displayMessage('error', 'Error adding prescription: " . $conn->error . "');</script>";
        }
    }

}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'] ?? '';
    $stmt = $conn->prepare("DELETE FROM prescriptions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>displayMessage('success', 'Prescription deleted successfully!');</script>";
    } else {
        echo "<script>displayMessage('error', 'Error deleting prescription: " . $conn->error . "');</script>";
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
                                        <h5>prescriptions</h5>
                                    </div>
                                    <div class="card-block table-border-style">
                                        <div class="table-responsive">
                                            <button class="btn btn-info float-right" data-toggle="modal"
                                                data-target="#prescriptionsModal" onclick="resetForm()">Add New
                                                prescriptions</button>

                                            <table class="table" id="prescriptionTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Patient ID</th>
                                                        <th>Doctor ID</th>
                                                        <th>Medicine ID</th>
                                                        <th>instructions</th>
                                                        <th>Status</th>
                                                        <th>Created At</th>
                                                        <th>Updated At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php

                                                    $result = $conn->query("
   SELECT 
    prescriptions.id, 
    prescriptions.patient_id, 
    prescriptions.doctor_id, 
    prescriptions.medicine_id, 
    patients.name AS patient_name, 
    users.username AS doctor_name, 
    medicines.name AS medicine_name, 
    prescriptions.instructions, 
    prescriptions.status, 
    prescriptions.created_at, 
    prescriptions.updated_at
FROM 
    prescriptions
LEFT JOIN 
    patients ON prescriptions.patient_id = patients.id
LEFT JOIN 
    users ON prescriptions.doctor_id = users.id
LEFT JOIN
    medicines ON prescriptions.medicine_id = medicines.id


");




                                                    if ($result && $result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr>
                <td>" . htmlspecialchars($row['id']) . "</td>
                <td>" . htmlspecialchars($row['patient_name']) . "</td>
                <td>" . htmlspecialchars($row['doctor_name']) . "</td>
                <td>" . htmlspecialchars($row['medicine_name']) . "</td>
                <td>" . htmlspecialchars($row['instructions']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>" . htmlspecialchars($row['created_at']) . "</td>
                <td>" . htmlspecialchars($row['updated_at']) . "</td>
                <td>
                                                                    <button class='btn btn-warning btn-sm' 
    onclick='editprescriptions(
        " . htmlspecialchars($row['id']) . ", 
       " . htmlspecialchars($row['patient_id']) . ",  
       " . htmlspecialchars($row['doctor_id']) . ",  
       " . htmlspecialchars($row['medicine_id']) . ", 
       \"" . htmlspecialchars(addslashes($row['instructions'])) . "\", 
        \"" . htmlspecialchars(addslashes($row['status'])) . "\"
    )'>Edit</button>

                                                                    <a href='?delete_id=" . htmlspecialchars($row['id']) . "' 
                                                                        class='btn btn-danger btn-sm' 
                                                                        onclick='return confirm(\"Are you sure you want to delete this Prescription?\")'>Delete</a>
                                                                </td>
            </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='7'>No prescriptions found.</td></tr>";
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
                        <div class="modal" tabindex="-1" role="dialog" id="prescriptionsModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Prescriptions</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="">
                                            <input type="hidden" name="id" id="id">
                                            <div class="form-group">
                                                <label for="patient_id">Patient Name</label>
                                                <select name="patient_id" id="patient_id" class="form-control">
                                                    <?php
                                                    $categories = $conn->query("SELECT * FROM patients");
                                                    while ($cat = $categories->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($cat['id']) . "'>" . htmlspecialchars($cat['name']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="doctor_id">Doctor Name</label>
                                                <select name="doctor_id" id="doctor_id" class="form-control">
                                                    <?php
                                                    $categories = $conn->query("SELECT * FROM users WHERE role = 'doctor'");
                                                    while ($cat = $categories->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($cat['id']) . "'>" . htmlspecialchars($cat['username']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="medicine_id">Medicine Name</label>
                                                <select name="medicine_id" id="medicine_id" class="form-control">
                                                    <?php
                                                    $categories = $conn->query("SELECT * FROM medicines");
                                                    while ($cat = $categories->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($cat['id']) . "'>" . htmlspecialchars($cat['name']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="instructions">instructions</label>
                                                <input type="text" name="instructions" id="instructions"
                                                    class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-control" required>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Completed">Completed</option>
                                                    <option value="Cancelled">Cancelled</option>
                                                </select>
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
    function editprescriptions(id, doctor_id, patient_id, medicine_id, instructions, status) {
        document.getElementById('id').value = id;
        document.getElementById('doctor_id').value = doctor_id; // Set doctor dropdown
        document.getElementById('patient_id').value = patient_id; // Set patient dropdown
        document.getElementById('medicine_id').value = medicine_id; // Set patient dropdown
        document.getElementById('instructions').value = instructions; // Set patient dropdown
        document.getElementById('status').value = status; // Set status dropdown

        $('#prescriptionsModal').modal('show');
    }



    function resetForm() {
        document.getElementById('id').value = '';
        document.getElementById('doctor_id').value = '';
        document.getElementById('patient_id').value = '';
        document.getElementById('medicine_id').value = '';
        document.getElementById('instructions').value = '';
        document.getElementById('status').value = '';

    }

    function displayMessage(type, message) {
        let success = document.querySelector(".alert-success");
        let error = document.querySelector(".alert-danger");

        if (type === "success") {
            error.classList.add("d-none");
            success.classList.remove("d-none");
            success.innerHTML = message;

            setTimeout(() => {
                $("#prescriptionsModal").modal('hide');
                success.classList.add("d-none");
                document.getElementById("prescriptionsForm").reset();
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