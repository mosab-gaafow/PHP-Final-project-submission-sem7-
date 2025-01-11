<?php
include('header.php');
include('sidebar.php');
include('conn.php'); // Include your database connection

if (isset($_POST['submit'])) {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $age_in_years = $_POST['age_in_years'] ?? null;
    $age_in_months = $_POST['age_in_months'] ?? null;
    $phone = $_POST['phone'] ?? '';

    if (!empty($id)) {
        // Update existing patient
        $stmt = $conn->prepare("UPDATE patients SET name = ?, gender = ?, age_in_years = ?, age_in_months = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssiiii", $name, $gender, $age_in_years, $age_in_months, $phone, $id);
        $result = $stmt->execute();

        if ($result) {
            echo "<script>displayMessage('success', 'Patient updated successfully!');</script>";
        } else {
            echo "<script>displayMessage('error', 'Error updating patient: " . $conn->error . "');</script>";
        }
    } else {
        // Insert new patient
        $stmt = $conn->prepare("INSERT INTO patients (name, gender, age_in_years, age_in_months, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $name, $gender, $age_in_years, $age_in_months, $phone);
        $result = $stmt->execute();

        if ($result) {
            echo "<script>displayMessage('success', 'Patient added successfully!');</script>";
        } else {
            echo "<script>displayMessage('error', 'Error adding patient: " . $conn->error . "');</script>";
        }
    }
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'] ?? '';
    $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>displayMessage('success', 'Patient deleted successfully!');</script>";
    } else {
        echo "<script>displayMessage('error', 'Error deleting patient: " . $conn->error . "');</script>";
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
                                        <h5>Patients</h5>
                                    </div>
                                    <div class="card-block table-border-style">
                                        <div class="table-responsive">
                                            <button class="btn btn-info float-right" data-toggle="modal"
                                                data-target="#patientModal" onclick="resetForm()">Add New
                                                Patient</button>
                                            
                                            <table class="table" id="patientTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Gender</th>
                                                        <th>Age (Years)</th>
                                                        <th>Age (Months)</th>
                                                        <th>Phone</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $result = $conn->query("SELECT * FROM patients");
                                                    if ($result && $result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "<tr>
                                                                <td>" . htmlspecialchars($row['id']) . "</td>
                                                                <td>" . htmlspecialchars($row['name']) . "</td>
                                                                <td>" . htmlspecialchars($row['gender']) . "</td>
                                                                <td>" . htmlspecialchars($row['age_in_years']) . "</td>
                                                                <td>" . htmlspecialchars($row['age_in_months']) . "</td>
                                                                <td>" . htmlspecialchars($row['phone']) . "</td>
                                                                <td>
                                                                    <button class='btn btn-warning btn-sm' onclick='editPatient(" . htmlspecialchars($row['id']) . ", \"" . htmlspecialchars(addslashes($row['name'])) . "\", \"" . htmlspecialchars($row['gender']) . "\", \"" . htmlspecialchars($row['age_in_years']) . "\", \"" . htmlspecialchars($row['age_in_months']) . "\", \"" . htmlspecialchars(addslashes($row['phone'])) . "\")'>Edit</button>
                                                                    <a href='?delete_id=" . htmlspecialchars($row['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this patient?\")'>Delete</a>
                                                                </td>
                                                            </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='7'>No patients found.</td></tr>";
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
                        <div class="modal" tabindex="-1" role="dialog" id="patientModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Patient</h5>
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
                                                <label for="gender">Gender</label>
                                                <select name="gender" id="gender" class="form-control" required>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="age_in_years">Age (Years)</label>
                                                <input type="number" name="age_in_years" id="age_in_years"
                                                    class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="age_in_months">Age (Months)</label>
                                                <input type="number" name="age_in_months" id="age_in_months"
                                                    class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">Phone</label>
                                                <input type="text" name="phone" id="phone" class="form-control">
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
    function editPatient(id, name, gender, ageInYears, ageInMonths, phone) {
        document.getElementById('id').value = id;
        document.getElementById('name').value = name;
        document.getElementById('gender').value = gender;
        document.getElementById('age_in_years').value = ageInYears;
        document.getElementById('age_in_months').value = ageInMonths;
        document.getElementById('phone').value = phone;
        $('#patientModal').modal('show');
    }

    function resetForm() {
        document.getElementById('id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('gender').value = '';
        document.getElementById('age_in_years').value = '';
        document.getElementById('age_in_months').value = '';
        document.getElementById('phone').value = '';
    }

    function displayMessage(type, message) {
        alert(message); // Simple alert for now
    }
</script>

<?php
include 'footer.php';
?>
