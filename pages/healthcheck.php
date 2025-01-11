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

// Create or Update
if (isset($_POST['submit'])) {
    $patient_id = $_POST['patient_id'] ?? '';
    $doctor_id = $_POST['doctor_id'] ?? '';
    $signs = $_POST['signs'] ?? '';
    $disease_id = $_POST['disease_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $id = $_POST['id'] ?? '';
    

    if (!empty($id)) {
        // Update existing health check
        $stmt = $conn->prepare("UPDATE health_checks SET patient_id = ?, doctor_id = ?, signs = ?, disease_id = ?, status = ? WHERE id = ?");
$stmt->bind_param("iisssi", $patient_id, $doctor_id, $signs, $disease_id, $status, $id);

        $result = $stmt->execute();

        if ($result) {
            echo "<script>alert('Health Check updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating Health Check: " . $conn->error . "');</script>";
        }
    } else {
        // Insert new health check
        $stmt = $conn->prepare("INSERT INTO health_checks (patient_id, doctor_id, signs, disease_id, status) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisis", $patient_id, $doctor_id, $signs, $disease_id, $status);

        $result = $stmt->execute();

        if ($result) {
            echo "<script>alert('Health Check added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding Health Check: " . $conn->error . "');</script>";
        }
    }
}

// Delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'] ?? '';
    $stmt = $conn->prepare("DELETE FROM health_checks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('Health Check deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting Health Check: " . $conn->error . "');</script>";
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
                                        <h5>Health Checks</h5>
                                    </div>
                                    <div class="card-block table-border-style">
                                        <div class="table-responsive">
                                            <button class="btn btn-info float-right" data-toggle="modal"
                                                data-target="#healthChecksModal" onclick="resetForm()">Add New Health Check</button>

                                            <table class="table" id="healthCheckTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Patient Name</th>
                                                        <th>Doctor Name</th>
                                                        <th>Signs</th>
                                                        <th>Disease</th>
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
                                                        health_checks.id, 
                                                        health_checks.patient_id, 
                                                        health_checks.doctor_id, 
                                                        health_checks.disease_id,
                                                        patients.name AS patient_name, 
                                                        users.username AS doctor_name, 
                                                        health_checks.signs, 
                                                        diseases.name AS disease_name, 
                                                        health_checks.status, 
                                                        health_checks.created_at, 
                                                        health_checks.updated_at 
                                                    FROM health_checks
                                                    LEFT JOIN patients ON health_checks.patient_id = patients.id
                                                    LEFT JOIN users ON health_checks.doctor_id = users.id
                                                    LEFT JOIN diseases ON health_checks.disease_id = diseases.id
                                                ");
                                                

                                                    
                                                    if ($result && $result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr>
                                                                <td>" . htmlspecialchars($row['id']) . "</td>
                                                                <td>" . htmlspecialchars($row['patient_name']) . "</td>
                                                                <td>" . htmlspecialchars($row['doctor_name']) . "</td>
                                                                <td>" . htmlspecialchars($row['signs']) . "</td>
                                                                <td>" . htmlspecialchars($row['disease_name']) . "</td>
                                                                <td>" . htmlspecialchars($row['status']) . "</td>
                                                                <td>" . htmlspecialchars($row['created_at']) . "</td>
                                                                <td>" . htmlspecialchars($row['updated_at']) . "</td>
                                                                <td>
                                                                    <button class='btn btn-warning btn-sm' 
                                                                        onclick='editHealthCheck(
                                                                            " . htmlspecialchars($row['id']) . ", 
                                                                            " . htmlspecialchars($row['patient_id']) . ", 
                                                                            " . htmlspecialchars($row['doctor_id']) . ", 
                                                                            \"" . htmlspecialchars(addslashes($row['signs'])) . "\", 
                                                                            " . htmlspecialchars($row['disease_id']) . ", 
                                                                            \"" . htmlspecialchars($row['status']) . "\"
                                                                        )'>Edit</button>
                                                                    <a href='?delete_id=" . htmlspecialchars($row['id']) . "' 
                                                                        class='btn btn-danger btn-sm' 
                                                                        onclick='return confirm(\"Are you sure you want to delete this Health Check?\")'>Delete</a>
                                                                </td>
                                                            </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='9'>No Health Checks found.</td></tr>";
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
                        <div class="modal" tabindex="-1" role="dialog" id="healthChecksModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Health Check</h5>
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
                                                    $patients = $conn->query("SELECT * FROM patients");
                                                    while ($patient = $patients->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($patient['id']) . "'>" . htmlspecialchars($patient['name']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="doctor_id">Doctor Name</label>
                                                <select name="doctor_id" id="doctor_id" class="form-control">
                                                    <?php
                                                    $doctors = $conn->query("SELECT * FROM users WHERE role = 'doctor'");
                                                    while ($doctor = $doctors->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($doctor['id']) . "'>" . htmlspecialchars($doctor['username']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="signs">Signs</label>
                                                <textarea name="signs" id="signs" class="form-control"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="disease_id">Disease</label>
                                                <select name="disease_id" id="disease_id" class="form-control">
                                                    <?php
                                                    $diseases = $conn->query("SELECT * FROM diseases");
                                                    while ($disease = $diseases->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($disease['id']) . "'>" . htmlspecialchars($disease['name']) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" id="status" class="form-control" required>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Completed">Completed</option>
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
    function editHealthCheck(id, patient_id, doctor_id, signs, disease_id, status) {
        document.getElementById('id').value = id;
        document.getElementById('patient_id').value = patient_id;
        document.getElementById('doctor_id').value = doctor_id;
        document.getElementById('signs').value = signs;
        document.getElementById('disease_id').value = disease_id;
        document.getElementById('status').value = status;

        $('#healthChecksModal').modal('show');
    }

    function resetForm() {
        document.getElementById('id').value = '';
        document.getElementById('patient_id').value = '';
        document.getElementById('doctor_id').value = '';
        document.getElementById('signs').value = '';
        document.getElementById('disease_id').value = '';
        document.getElementById('status').value = 'Pending';
    }
</script>

<?php
include 'footer.php';
?>
