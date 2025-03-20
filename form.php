<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Database 2 connection details (dt_database)
$host2 = "192.168.1.212";  
$port2 = "3306";             
$user2 = "root";            
$pass2 = "e5706567e1c2aa3c"; 
$dbname2 = "dt_database";   

// Connect to the remote database (dt_database) to fetch job numbers
$mysqli2 = new mysqli($host2, $user2, $pass2, $dbname2, $port2);

// Check the connection
if ($mysqli2->connect_error) {
    die("Connection failed: " . $mysqli2->connect_error);
}

// Fetch job details from the job_raised table
$job_query = "SELECT jobnumber_created, description, job_type_prefix, date_and_time_raised, client_name, target_date FROM job_raised";
$result2 = $mysqli2->query($job_query);
$jobs = [];
if ($result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        $jobs[] = $row; // Collect all job details into an array
    }
} else {
    die("No job details found in the remote database.");
}

$mysqli2->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Tracker Form</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6">Job Tracker Form</h2>
        <form method="post" action="process_form1.php" class="space-y-4">
            <div>
                <label for="job_number" class="block text-sm font-medium text-gray-700">Job Number:</label>
                <select id="job_number" name="job_number" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                    <?php
                    // Populate the dropdown with job numbers
                    foreach ($jobs as $job) {
                        echo "<option value=\"{$job['jobnumber_created']}\">{$job['jobnumber_created']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                <textarea id="description" name="description" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm"></textarea>
            </div>

            <div>
                <label for="job_type_prefix" class="block text-sm font-medium text-gray-700">Job Type Prefix:</label>
                <input type="text" id="job_type_prefix" name="job_type_prefix" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label for="date_and_time_raised" class="block text-sm font-medium text-gray-700">Date and Time Raised:</label>
                <input type="datetime-local" id="date_and_time_raised" name="start_date" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label for="client_name" class="block text-sm font-medium text-gray-700">Client Name:</label>
                <input type="text" id="client_name" name="client_name" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label for="target_date" class="block text-sm font-medium text-gray-700">Target Date:</label>
                <input type="date" id="target_date" name="end_date" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status:</label>
                <select id="status" name="status" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                    <option value="Planned">Planned</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="On Hold">On Hold</option>
                </select>
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700">Priority:</label>
                <select id="priority" name="priority" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>

            <div>
                <input type="submit" value="Submit" class="w-full bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600 cursor-pointer">
            </div>
        </form>
    </div>

    <script>
        // JavaScript to auto-fill form fields when a job number is selected
        document.getElementById('job_number').addEventListener('change', function() {
            const jobNumber = this.value;
            const job = <?php echo json_encode($jobs); ?>.find(job => job.jobnumber_created === jobNumber);

            if (job) {
                document.getElementById('description').value = job.description || '';
                document.getElementById('job_type_prefix').value = job.job_type_prefix || '';
                document.getElementById('date_and_time_raised').value = job.date_and_time_raised || '';
                document.getElementById('client_name').value = job.client_name || '';
                document.getElementById('target_date').value = job.target_date || '';
            }
        });
        document.querySelector('form').addEventListener('submit', function(event) {
    const jobNumber = document.getElementById('job_number').value;
    if (jobNumber.length > 50) { // Adjust this to match the new column length
        alert("Job number cannot exceed 50 characters.");
        event.preventDefault(); // Prevent form submission
    }
});
    </script>
</body>
</html>