# Database Synchronization Tool
## Installation and User Guide

![Database Backup Banner](https://github.com/speedsri/Database-Synchronization-Tool/blob/main/syn.png)

# Database Synchronization Tool
## Installation and User Guide

![Database Sync Header](https://via.placeholder.com/800x150/0062cc/ffffff?text=Database+Synchronization+Tool)

## Table of Contents
1. [Introduction](#introduction)
2. [System Overview](#system-overview)
3. [Features](#features)
4. [Prerequisites](#prerequisites)
5. [Installation Guide](#installation-guide)
6. [Configuration](#configuration)
7. [Usage](#usage)
8. [Data Visualization with index.php](#data-visualization)
9. [Troubleshooting](#troubleshooting)
10. [Security Considerations](#security-considerations)
11. [Customization](#customization)
12. [GitHub Repository](#github-repository)

## Introduction

The Database Synchronization Tool is a PHP-based application designed to synchronize data between two MySQL databases. This tool is particularly useful for maintaining consistent data across multiple systems or for migrating data from one database to another. The tool provides a user-friendly interface to monitor the synchronization process and view detailed results.

## System Overview

The system connects to a source database (`dt_database`) and a target database (`employee_tracker`), transferring data from the `job_raised` table in the source database to the same table in the target database. If the table doesn't exist in the target database, the tool will automatically create it.

### Data Flow
```
Source Database (dt_database) → PHP Synchronization Script → Target Database (employee_tracker) → Data Visualization (index.php)
```

## Features

- **Automatic Table Creation**: Creates the required table in the target database if it doesn't exist
- **Upsert Functionality**: Inserts new records and updates existing ones
- **Transaction Support**: Ensures data integrity with database transactions
- **Visual Feedback**: Provides a clean, responsive interface to monitor synchronization
- **Detailed Reporting**: Shows the number of inserted and updated records
- **Data Visualization**: View synchronized data through the index.php interface
- **Error Handling**: Comprehensive error reporting and handling
- **Responsive Design**: Works on both desktop and mobile devices

## Prerequisites

- Web server (Apache, Nginx, etc.)
- PHP 7.2 or higher
- MySQL/MariaDB databases
- Network connectivity between the web server and both database servers

## Installation Guide

### Step 1: Set Up the Web Server

1. Install a web server (Apache, Nginx, etc.) with PHP support
2. Enable the following PHP extensions:
   - mysqli
   - pdo_mysql

### Step 2: Deploy the Application

1. Create a directory for the application on your web server
2. Upload the following files to this directory:
   - `sync_databases.php` (main synchronization file)
   - `index.php` (data visualization file)
   - `include/navigation.php` (navigation component)

### Step 3: Set Up Database Access

1. Ensure the web server has network access to both database servers
2. Create MySQL users with appropriate permissions:
   - Source database: SELECT permissions
   - Target database: SELECT, INSERT, CREATE, UPDATE permissions

## Configuration

### Edit Database Connection Details

Open `sync_databases.php` and modify the following sections with your database credentials:

```php
// Source database connection details
$source_host = "192.168.1.212";  // Change to your source database host
$source_port = "3306";           // Change if using a non-standard port
$source_user = "root";           // Change to your source database username
$source_pass = "e5706567e1c2aa3c";  // Change to your source database password
$source_db = "dt_database";      // Change to your source database name

// Target database connection details
$target_host = "192.168.1.210";  // Change to your target database host
$target_port = "3306";           // Change if using a non-standard port
$target_user = "speedsri";       // Change to your target database username
$target_pass = "root";           // Change to your target database password
$target_db = "employee_tracker"; // Change to your target database name
```

Similarly, update the database connection details in `index.php` to connect to the target database:

```php
// Target database connection details (same as in sync_databases.php)
$db_host = "192.168.1.210";      // Change to your target database host
$db_port = "3306";               // Change if using a non-standard port
$db_user = "speedsri";           // Change to your target database username
$db_pass = "root";               // Change to your target database password
$db_name = "employee_tracker";   // Change to your target database name
```

### Table Structure

The tool will create the following table structure in the target database if it doesn't exist:

```sql
CREATE TABLE job_raised (
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
)
```

## Usage

### Running the Synchronization

1. Open a web browser and navigate to the location where you installed the application
   ```
   http://your-server-address/path/to/sync_databases.php
   ```

2. The synchronization process will run automatically when the page loads

3. Review the results displayed on the page:
   - Synchronization status
   - Number of records inserted
   - Number of records updated
   - Any errors that occurred

### Understanding the UI

The web interface provides the following information:

- **Status Summary**: Shows the last execution time and completion status
- **Execution Results**: Displays detailed information about the synchronization process
- **Database Connections**: Shows the connection details for both source and target databases

## Data Visualization with index.php

The `index.php` file serves as a data visualization tool that displays the synchronized data from the target database. This provides a complete solution for both synchronizing and viewing the data.

### Features of index.php

- **Data Listing**: Displays all records from the `job_raised` table in the target database
- **Filtering Options**: Allows users to filter data by various criteria
- **Sorting**: Enables sorting by different columns
- **Pagination**: Divides large datasets into manageable pages
- **Responsive Design**: Works on both desktop and mobile devices

### How to Use index.php

1. After running the synchronization process, navigate to:
   ```
   http://your-server-address/path/to/index.php
   ```

2. The page will load and display all synchronized records from the target database

3. Use the filtering and sorting options to explore the data

4. Click on individual records to view detailed information

### Customizing index.php

You can customize the `index.php` file to meet your specific requirements:

- Modify the columns displayed in the data table
- Add additional filtering options
- Change the page layout or styling
- Implement additional features such as data export or printing

## Troubleshooting

### Common Issues and Solutions

| Issue | Possible Cause | Solution |
|-------|---------------|----------|
| Connection failed | Incorrect database credentials | Verify username, password, host, and port |
| Connection failed | Network connectivity issues | Check firewall settings and network access |
| Table creation error | Insufficient permissions | Ensure the target database user has CREATE permissions |
| No data found | Source table is empty | Verify that the source table contains data |
| Duplicate key error | Record already exists | Check for conflicting unique keys in the target database |
| index.php not displaying data | Synchronization not run | Run sync_databases.php first to populate the target database |

### Enabling Error Reporting

The application has error reporting enabled by default. You can see PHP errors in the browser for debugging purposes. For production environments, you may want to disable error reporting by commenting out these lines:

```php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
```

## Security Considerations

### Protecting Database Credentials

1. **Move credentials to a separate file**: Store database credentials in a separate configuration file outside the web root
2. **Use environment variables**: Consider using environment variables for sensitive information
3. **Implement IP restrictions**: Limit access to the synchronization tool to specific IP addresses

### User Authentication

The current implementation does not include user authentication. Consider adding a login system to protect the tool from unauthorized access.

## Customization

### Modifying the Table Structure

If you need to modify the table structure, edit the `$create_table_query` variable in the PHP code:

```php
$create_table_query = "CREATE TABLE job_raised (
    // Modify columns as needed
)";
```

### Customizing the REF Field Generation

The tool currently generates REF values using a simple prefix:

```php
$ref = "REF-" . $row['jobnumber_created'];
```

You can customize this logic to match your specific requirements.

### Adding Additional Tables

To synchronize additional tables, duplicate the synchronization logic for each table you want to include.

## GitHub Repository

### Accessing the Code

The complete source code for this project is available on GitHub. You can access it at:

```
https://github.com/your-username/database-sync-tool
```

### Updating the Repository

The repository is regularly updated with new features and improvements. To contribute or stay updated:

1. **Fork the repository**: Create your own copy of the repository
2. **Clone the repository**: Download the code to your local machine
3. **Create a new branch**: Make your changes in a separate branch
4. **Submit a pull request**: Share your changes with the main repository

### Installation from GitHub

To install the application directly from GitHub:

```bash
# Clone the repository
git clone https://github.com/your-username/database-sync-tool.git

# Navigate to the directory
cd database-sync-tool

# Configure the database connections
# Edit sync_databases.php and index.php with your database credentials
```

---

## MySQL Database Support

This tool is compatible with:
- MySQL 5.6 or higher
- MariaDB 10.0 or higher

The code uses standard MySQL features, including:
- Transactions
- Prepared statements
- ON DUPLICATE KEY UPDATE
- AUTO_INCREMENT

---

*Copyright © 2025 Database Synchronization Tool*
