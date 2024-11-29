<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('location: ../index.php');
    exit;
}

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: unauthorized.php');
    exit;
}

include 'header.php';
include 'sidebar.php';
include '../database/connection.php';

function fetchFacultyList($conn)
{
    $stmt = $conn->query("SELECT faculty_id, CONCAT(firstname, ' ', lastname) AS faculty_name FROM college_faculty_list");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$facultyList = fetchFacultyList($conn);
?>
<nav class="main-header">
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm rounded">
                    <div class="card-body">
                        <h4>Welcome, <?php echo $_SESSION['login_name']; ?>!</h4>
                        <div class="col-md-5 mt-3">
                            <div class="callout callout-info" style="border-left: 5px solid rgb(51, 128, 64);">
                                <h5><b>Academic Year: Semester</b></h5>
                                <h6><b>Evaluation Status:</b> Ongoing</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <?php
            $dashboardData = [
                ["Total Tertiary Faculties", "college_faculty_list", "fa-user-friends"],
                ["Total Students", "student_list", "ion-ios-people-outline"],
                ["Total Users", "users", "fa-users"],
                ["Total Secondary Faculties", "secondary_faculty_list", "fa-user-friends"],
                ["Total Head Faculties", "head_faculty_list", "fa-users"],
            ];

            foreach ($dashboardData as $data) {
                $stmt = $conn->query("SELECT * FROM {$data[1]}");
                $total = $stmt->rowCount();
                echo "
                    <div class='col-12 col-sm-6 col-md-4 mb-3'>
                        <div class='small-box bg-white shadow-sm rounded border'>
                            <div class='inner'>
                                <h3>{$total}</h3>
                                <p>{$data[0]}</p>
                            </div>
                            <div class='icon'>
                                <i class='fa {$data[2]}'></i>
                            </div>
                        </div>
                    </div>";
            }
            ?>
        </div>

        <div class="row mt-3">
            <div class="col-md-8 offset-md-2">
                <div class="card shadow-sm rounded">
                    <div class="card-header text-center py-2">
                        <h5 class="mb-0">Select Faculty to Monitor</h5>
                    </div>
                    <div class="card-body py-3">
                        <form id="facultyForm">
                            <div class="form-group">
                                <label for="facultySelect" class="form-label">Faculty:</label>
                                <select class="form-control form-control-sm" id="facultySelect" name="faculty_id">
                                    <option value="" selected disabled>Select Faculty</option>
                                    <?php foreach ($facultyList as $faculty): ?>
                                        <option value="<?php echo $faculty['faculty_id']; ?>">
                                            <?php echo $faculty['faculty_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-8 offset-md-2">
                <div class="card shadow-sm rounded">
                    <div class="card-header text-center py-2">
                        <h5 class="mb-0">Faculty Performance Over Time</h5>
                    </div>
                    <div class="card-body py-3">
                        <canvas id="facultyLineChart" style="max-height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    document.getElementById('facultySelect').addEventListener('change', function () {
        const facultyId = this.value;

        if (facultyId) {
            fetch(`fetch_faculty_data.php?faculty_id=${facultyId}`)
                .then(response => response.json())
                .then(data => {
                    updateLineChart(data.labels, data.dataset);
                })
                .catch(error => console.error('Error fetching faculty data:', error));
        }
    });

    const ctx = document.getElementById('facultyLineChart').getContext('2d');
    const lineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Faculty Ratings',
                data: [],
                borderColor: 'rgb(51, 128, 64)',
                tension: 0.1,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Rating (1-4)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Evaluation Sequence'
                    }
                }
            }
        }
    });

    function updateLineChart(labels, dataset) {
        lineChart.data.labels = labels;
        lineChart.data.datasets[0].data = dataset;
        lineChart.update();
    }
</script>

<style>
    .card {
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        background: rgb(51, 128, 64);
        color: white;
    }

    .card-header h5 {
        font-size: 1rem;
    }

    #facultySelect {
        border: 1px solid rgb(51, 128, 64);
        border-radius: 5px;
        font-size: 0.9rem;
    }

    .container-fluid {
        max-width: 90%;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 500;
    }

    body {
        font-size: 0.9rem;
        overflow-y: hidden;
    }

    html {
        scroll-behavior: smooth;
    }

    .main-header {
        max-height: 90vh;
        overflow-y: scroll;
        scrollbar-width: thin;
    }

    .main-header::-webkit-scrollbar {
        width: 4px;
    }

    .main-header::-webkit-scrollbar-thumb {
        background: rgb(51, 128, 64);
        border-radius: 10px;
    }

    .card-body {
        padding: 0.75rem;
    }

    .form-control-sm {
        height: calc(1.5em + 0.5rem + 2px);
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>


<?php include 'footer.php'; ?>