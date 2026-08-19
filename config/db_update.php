<?php
// Automatic database updater to ensure platform fee & subscription columns exist

function check_and_update_db() {
    $servername = defined('DB_HOST') ? DB_HOST : "localhost";
    $username   = defined('DB_USER') ? DB_USER : "root";
    $password   = defined('DB_PASSWORD') ? DB_PASSWORD : "";
    $dbname     = defined('DB_NAME') ? DB_NAME : "worker";

    $conn = @new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        return false;
    }

    // 1. Check cregistration table columns
    $res = $conn->query("SHOW COLUMNS FROM cregistration LIKE 'c_plan_type'");
    if ($res && $res->num_rows == 0) {
        @$conn->query("ALTER TABLE cregistration ADD COLUMN c_reg_date DATETIME DEFAULT CURRENT_TIMESTAMP");
        @$conn->query("ALTER TABLE cregistration ADD COLUMN c_plan_type VARCHAR(20) DEFAULT 'none'");
        @$conn->query("ALTER TABLE cregistration ADD COLUMN c_plan_expires DATE DEFAULT NULL");
    }

    // 2. Check wregistration table columns
    $res_w = $conn->query("SHOW COLUMNS FROM wregistration LIKE 'w_plan_type'");
    if ($res_w && $res_w->num_rows == 0) {
        @$conn->query("ALTER TABLE wregistration ADD COLUMN w_reg_date DATETIME DEFAULT CURRENT_TIMESTAMP");
        @$conn->query("ALTER TABLE wregistration ADD COLUMN w_plan_type VARCHAR(20) DEFAULT 'none'");
        @$conn->query("ALTER TABLE wregistration ADD COLUMN w_plan_expires DATE DEFAULT NULL");
    }

    // 3. Create platform_payments table if it doesn't exist
    $create_pay_table = "CREATE TABLE IF NOT EXISTS platform_payments (
        pid INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_type VARCHAR(20) NOT NULL,
        plan_type VARCHAR(20) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        transaction_id VARCHAR(100) NOT NULL,
        payment_date DATETIME NOT NULL,
        expiry_date DATE NOT NULL,
        status VARCHAR(20) DEFAULT 'completed'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    @$conn->query($create_pay_table);

    $conn->close();
    return true;
}

// Run updater on include
check_and_update_db();
?>
