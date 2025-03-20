
<?php include 'include/navigation.php'; ?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Source database connection details (dt_database)
$source_host = "192.168.1.212";
$source_port = "3306";
$source_user = "root";
$source_pass = "e5706567e1c2aa3c";
$source_db = "dt_database";

// Target database connection details (employee_tracker)
$target_host = "192.168.1.210";
$target_port = "3306";
$target_user = "speedsri";
$target_pass = "root";
$target_db = "employee_tracker";
// Success notification function with Tailwind styling
function showSuccessNotification($message) {
    echo '<div id="notification" class="fixed top-5 right-5 bg-green-500 text-white p-4 rounded-md shadow-lg z-50 max-w-md">';
    echo '<div class="flex items-center">';
    echo '<svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
    echo $message;
    echo '</div>';
    echo '</div>';
    echo '<script>
            setTimeout(function() {
                document.getElementById("notification").classList.add("opacity-0", "transition-opacity", "duration-500");
                setTimeout(function() {
                    document.getElementById("notification").style.display = "none";
                }, 500);
            }, 5000);
          </script>';
}

// Connect to source database
$source_conn = new mysqli($source_host, $source_user, $source_pass, $source_db, $source_port);
if ($source_conn->connect_error) {
    die("Source connection failed: " . $source_conn->connect_error);
}

// Connect to target database
$target_conn = new mysqli($target_host, $target_user, $target_pass, $target_db, $target_port);
if ($target_conn->connect_error) {
    die("Target connection failed: " . $target_conn->connect_error);
}

// Store results to display in the UI
$results = [];

// Check if the job_raised table exists in the target database
$check_table_query = "SHOW TABLES LIKE 'job_raised'";
$result = $target_conn->query($check_table_query);

if ($result->num_rows == 0) {
    // Table doesn't exist, create it
    $create_table_query = "CREATE TABLE job_raised (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ref VARCHAR(50) NOT NULL,
        jobnumber_created VARCHAR(50) NOT NULL,
        description TEXT,
        job_type_prefix VARCHAR(50),
        date_and_time_raised DATETIME,
        client_name VARCHAR(100),
        target_date DATE,
        status VARCHAR(20) DEFAULT 'Planned',
        priority VARCHAR(20) DEFAULT 'Medium',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY (jobnumber_created)
    )";
    
    if (!$target_conn->query($create_table_query)) {
        $results[] = ["type" => "error", "message" => "Error creating table: " . $target_conn->error];
    } else {
        $results[] = ["type" => "success", "message" => "Table 'job_raised' created successfully."];
    }
}

// Fetch all data from source database
$fetch_query = "SELECT jobnumber_created, description, job_type_prefix, date_and_time_raised, client_name, target_date FROM job_raised";
$source_result = $source_conn->query($fetch_query);

if ($source_result === false) {
    $results[] = ["type" => "error", "message" => "Error fetching data: " . $source_conn->error];
} elseif ($source_result->num_rows > 0) {
    // Begin transaction
    $target_conn->begin_transaction();
    
    try {
        // Prepare insert statement with ref field
        $insert_stmt = $target_conn->prepare("INSERT INTO job_raised 
            (ref, jobnumber_created, description, job_type_prefix, date_and_time_raised, client_name, target_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            ref = VALUES(ref),
            description = VALUES(description),
            job_type_prefix = VALUES(job_type_prefix),
            date_and_time_raised = VALUES(date_and_time_raised),
            client_name = VALUES(client_name),
            target_date = VALUES(target_date)");
        
        // Loop through each row and insert/update
        $insert_count = 0;
        $update_count = 0;
        
        while ($row = $source_result->fetch_assoc()) {
            // Generate a ref value (using jobnumber as ref for simplicity, but you can customize)
            $ref = "REF-" . $row['jobnumber_created'];
            
            $insert_stmt->bind_param(
                "sssssss",
                $ref,
                $row['jobnumber_created'],
                $row['description'],
                $row['job_type_prefix'],
                $row['date_and_time_raised'],
                $row['client_name'],
                $row['target_date']
            );
            
            // Execute the statement
            $insert_stmt->execute();
            
            // Count inserts and updates
            if ($target_conn->affected_rows == 1) {
                $insert_count++;
            } elseif ($target_conn->affected_rows == 2) {
                $update_count++;
            }
        }
        
        // Commit the transaction
        $target_conn->commit();
        
        $message = "Synchronization complete. Inserted: $insert_count, Updated: $update_count records.";
        showSuccessNotification($message);
        $results[] = ["type" => "success", "message" => $message, "insert_count" => $insert_count, "update_count" => $update_count];
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $target_conn->rollback();
        $results[] = ["type" => "error", "message" => "Error: " . $e->getMessage()];
    }
} else {
    $results[] = ["type" => "info", "message" => "No data found in the source database."];
}

// Close connections
$source_conn->close();
$target_conn->close();

$execution_time = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Synchronization</title>
    <!-- Include Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
  
</head>
<br><br>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto pt-12 pb-20 px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header Section -->
            <div class="bg-blue-600 text-white p-6">
                <h1 class="text-2xl font-bold">Database Synchronization Tool</h1>
                <p class="mt-2 text-blue-100">Synchronizing data between databases</p>
            </div>
            
            <!-- Status Summary -->
            <div class="p-6 border-b">
                <div class="flex items-center mb-4">
                    <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                    <h2 class="text-lg font-semibold text-gray-800">Synchronization Status</h2>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600">Last run: <span class="font-medium text-gray-800"><?php echo $execution_time; ?></span></p>
                        </div>
                        <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                            Completed
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Results Section -->
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Execution Results</h2>
                
                <?php foreach ($results as $result): ?>
                    <div class="mb-3 p-4 rounded-lg <?php 
                        echo $result['type'] === 'success' ? 'bg-green-50 border border-green-200' : 
                            ($result['type'] === 'error' ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200'); 
                    ?>">
                        <div class="flex items-start">
                            <?php if ($result['type'] === 'success'): ?>
                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            <?php elseif ($result['type'] === 'error'): ?>
                                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            <?php else: ?>
                                <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            <?php endif; ?>
                            <div>
                                <p class="<?php 
                                    echo $result['type'] === 'success' ? 'text-green-800' : 
                                        ($result['type'] === 'error' ? 'text-red-800' : 'text-blue-800'); 
                                ?>"><?php echo $result['message']; ?></p>
                                
                                <?php if (isset($result['insert_count']) && isset($result['update_count'])): ?>
                                <div class="flex mt-3 gap-4 text-sm">
                                    <div class="bg-white px-3 py-1 rounded-md shadow-sm">
                                        <span class="font-medium">Inserted:</span> 
                                        <span class="text-green-700"><?php echo $result['insert_count']; ?></span>
                                    </div>
                                    <div class="bg-white px-3 py-1 rounded-md shadow-sm">
                                        <span class="font-medium">Updated:</span> 
                                        <span class="text-blue-700"><?php echo $result['update_count']; ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($results)): ?>
                    <div class="text-center p-8 text-gray-500">
                        No results to display
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Connection Information -->
            <div class="bg-gray-50 p-6 border-t">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Database Connections</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Source DB -->
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <h3 class="font-medium text-gray-700 mb-2">Source Database</h3>
                        <div class="text-sm text-gray-600 mb-1">Host: <?php echo $source_host; ?>:<?php echo $source_port; ?></div>
                        <div class="text-sm text-gray-600 mb-1">Database: <?php echo $source_db; ?></div>
                        <div class="text-sm text-gray-600">User: <?php echo $source_user; ?></div>
                    </div>
                    
                    <!-- Target DB -->
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <h3 class="font-medium text-gray-700 mb-2">Target Database</h3>
                        <div class="text-sm text-gray-600 mb-1">Host: <?php echo $target_host; ?>:<?php echo $target_port; ?></div>
                        <div class="text-sm text-gray-600 mb-1">Database: <?php echo $target_db; ?></div>
                        <div class="text-sm text-gray-600">User: <?php echo $target_user; ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="p-4 bg-gray-800 text-gray-400 text-center text-sm">
                Database Synchronization Tool - &copy; <?php echo date('Y'); ?> DT
            </div>
        </div>
    </div>
</body>
</html>