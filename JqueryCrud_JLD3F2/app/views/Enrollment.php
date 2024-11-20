<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Table</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table-container {
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
        }
        .modal-title {
            font-weight: bold;
        }
        .btn-primary {
            background: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">College Enrollment Table</h2>

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <input type="text" class="form-control" id="searchInput" placeholder="Search by Name or Student ID">
        </div>
        <div class="d-flex">
            <select class="form-select me-2" id="yearFilter">
                <option value="">Filter by Year</option>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
            </select>
            <select class="form-select me-2" id="semesterFilter">
                <option value="">Filter by Semester</option>
                <option value="1">1st Semester</option>
                <option value="2">2nd Semester</option>
            </select>
            <button class="btn btn-primary" id="clearFilters">Clear Filters</button>
        </div>
    </div>

    <div class="table-container">
        <div class="text-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enrollmentModal" id="addEnrollmentButton">Add Enrollment</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="enrollmentTable">
                <thead class="table-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="enrollmentModal" tabindex="-1" aria-labelledby="enrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollmentModalLabel">Add Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="enrollmentForm">
                    <div class="mb-3">
                        <label for="studentID" class="form-label">Student ID</label>
                        <input type="text" class="form-control" id="studentID" placeholder="Enter student ID" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="studentName" placeholder="Enter full name" required>
                    </div>
                    <div class="mb-3">
                        <label for="course" class="form-label">Course</label>
                        <input type="text" class="form-control" id="course" placeholder="Enter course" required>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Year Level</label>
                        <select class="form-select" id="year" required>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester</label>
                        <select class="form-select" id="semester" required>
                            <option value="1">1st Semester</option>
                            <option value="2">2nd Semester</option>
                        </select>
                    </div>
                    <input type="hidden" id="rowIndex" value="-1">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEnrollmentButton">Save Enrollment</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    function loadTableData() {
        const enrollmentData = JSON.parse(localStorage.getItem('enrollments')) || [];
        const searchQuery = $('#searchInput').val().toLowerCase();
        const yearFilter = $('#yearFilter').val();
        const semesterFilter = $('#semesterFilter').val();

        $('#enrollmentTable tbody').empty();

        enrollmentData
            .filter(enrollment => {
                return (
                    (!searchQuery ||
                        enrollment.studentID.toLowerCase().includes(searchQuery) ||
                        enrollment.studentName.toLowerCase().includes(searchQuery)) &&
                    (!yearFilter || enrollment.year === yearFilter) &&
                    (!semesterFilter || enrollment.semester === semesterFilter)
                );
            })
            .forEach((enrollment, index) => {
                $('#enrollmentTable tbody').append(`
                    <tr data-index="${index}">
                        <td>${enrollment.studentID}</td>
                        <td>${enrollment.studentName}</td>
                        <td>${enrollment.course}</td>
                        <td>${enrollment.year}</td>
                        <td>${enrollment.semester}</td>
                        <td>
                            <button class="btn btn-warning btn-sm updateButton">Update</button>
                            <button class="btn btn-danger btn-sm deleteButton">Delete</button>
                        </td>
                    </tr>
                `);
            });
    }

    loadTableData();

    $('#saveEnrollmentButton').click(function () {
        const studentID = $('#studentID').val();
        const studentName = $('#studentName').val();
        const course = $('#course').val();
        const year = $('#year').val();
        const semester = $('#semester').val();
        const rowIndex = $('#rowIndex').val();

        if (studentID && studentName && course && year && semester) {
            const enrollmentData = JSON.parse(localStorage.getItem('enrollments')) || [];
            const newEnrollment = { studentID, studentName, course, year, semester };

            if (rowIndex === "-1") {
                enrollmentData.push(newEnrollment);
            } else {
                enrollmentData[rowIndex] = newEnrollment;
            }

            localStorage.setItem('enrollments', JSON.stringify(enrollmentData));
            loadTableData();
            $('#enrollmentModal').modal('hide');
            $('#enrollmentForm')[0].reset();
            $('#rowIndex').val('-1');
            $('#enrollmentModalLabel').text("Add Enrollment");
        } else {
            alert("Please fill in all fields.");
        }
    });

    $(document).on('click', '.updateButton', function () {
        const rowIndex = $(this).closest('tr').data('index');
        const enrollmentData = JSON.parse(localStorage.getItem('enrollments')) || [];
        const enrollment = enrollmentData[rowIndex];

        $('#studentID').val(enrollment.studentID);
        $('#studentName').val(enrollment.studentName);
        $('#course').val(enrollment.course);
        $('#year').val(enrollment.year);
        $('#semester').val(enrollment.semester);
        $('#rowIndex').val(rowIndex);

        $('#enrollmentModalLabel').text("Update Enrollment");
        $('#enrollmentModal').modal('show');
    });

    $(document).on('click', '.deleteButton', function () {
        const rowIndex = $(this).closest('tr').data('index');
        const enrollmentData = JSON.parse(localStorage.getItem('enrollments')) || [];

        if (confirm("Are you sure you want to delete this enrollment?")) {
            enrollmentData.splice(rowIndex, 1);
            localStorage.setItem('enrollments', JSON.stringify(enrollmentData));
            loadTableData();
        }
    });

    $('#addEnrollmentButton').click(function () {
        $('#enrollmentForm')[0].reset();
        $('#rowIndex').val('-1');
        $('#enrollmentModalLabel').text("Add Enrollment");
    });

    $('#searchInput, #yearFilter, #semesterFilter').on('input change', loadTableData);

    $('#clearFilters').click(function () {
        $('#searchInput').val('');
        $('#yearFilter').val('');
        $('#semesterFilter').val('');
        loadTableData();
    });
});
</script>
</body>
</html>
