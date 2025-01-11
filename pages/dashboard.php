<?php
// Start the session
session_start();

// Check if the session variable 'username' is set
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    // If not, redirect to the login page
    header("Location: index.php");
    exit();
}

// Include database connection
include 'conn.php'; // Ensure the correct path for your DB connection

// Fetch total users, patients, and medicines
$query_users = "SELECT COUNT(*) as total_users FROM users";
$query_patients = "SELECT COUNT(*) as total_patients FROM patients"; // Assuming there is a 'patients' table
$query_medicines = "SELECT COUNT(*) as total_medicines FROM medicines"; // Assuming there is a 'medicines' table

$result_users = $conn->query($query_users);
$result_patients = $conn->query($query_patients);
$result_medicines = $conn->query($query_medicines);

$total_users = $result_users->fetch_assoc()['total_users'];
$total_patients = $result_patients->fetch_assoc()['total_patients'];
$total_medicines = $result_medicines->fetch_assoc()['total_medicines'];
?>

<?php
include 'header.php';
include 'sidebar.php';
?>

<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">
                        <div class="row">
                            <!-- Total Users -->
                            <div class="col-md-6 col-xl-4">
                                <div class="card daily-sales">
                                    <div class="card-block">
                                        <h6 class="mb-4">Total Users</h6>
                                        <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                            <i class="feather icon-users text-c-green f-30 m-r-10"></i>
                                            <?php echo $total_users; ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Patients -->
                            <div class="col-md-6 col-xl-4">
                                <div class="card daily-sales">
                                    <div class="card-block">
                                        <h6 class="mb-4">Total Patients</h6>
                                        <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                            <i class="feather icon-heart text-c-red f-30 m-r-10"></i>
                                            <?php echo $total_patients; ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Medicines -->
                            <div class="col-md-6 col-xl-4">
                                <div class="card daily-sales">
                                    <div class="card-block">
                                        <h6 class="mb-4">Total Medicines</h6>
                                        <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                            <i class="feather icon-box text-c-blue f-30 m-r-10"></i>
                                            <?php echo $total_medicines; ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <!-- [ Other sections like Daily Sales, Monthly Sales, etc.] -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>
