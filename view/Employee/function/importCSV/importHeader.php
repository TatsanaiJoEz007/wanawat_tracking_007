<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['login'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Verify CSRF token (if provided)
if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
        exit;
    }
}

// Function to convert various date formats to MySQL format (Y-m-d)
function convertDateToMySQLFormat($dateString) {
    $dateString = trim($dateString);
    
    // If empty, return as is
    if (empty($dateString)) {
        return $dateString;
    }
    
    // Array of date formats to try (order matters - most specific first)
    $dateFormats = [
        // ISO format
        'Y-m-d',
        'Y/m/d',
        // Full year formats
        'd/m/Y',
        'd-m-Y', 
        'd.m.Y',
        // 2-digit year formats (most common in Thai context)
        'd-m-y',  // 02-05-67
        'd/m/y',  // 02/05/67
        'd.m.y',  // 02.05.67
        // Alternative formats
        'm/d/Y',
        'm-d-Y',
        'm/d/y',
        'm-d-y'
    ];
    
    foreach ($dateFormats as $format) {
        $dateObj = DateTime::createFromFormat($format, $dateString);
        
        if ($dateObj && $dateObj->format($format) === $dateString) {
            // Handle 2-digit years - assume years 00-30 are 20xx, 31-99 are 19xx
            if (strpos($format, 'y') !== false) {
                $year = $dateObj->format('Y');
                if ($year >= 2000 && $year <= 2030) {
                    // Keep as 20xx
                } elseif ($year >= 1931 && $year <= 1999) {
                    // Keep as 19xx
                } else {
                    // Convert based on Thai Buddhist calendar context
                    // Years 00-30 = 2000-2030, 31-99 = 1931-1999
                    $twoDigitYear = intval($dateObj->format('y'));
                    if ($twoDigitYear <= 30) {
                        $dateObj->setDate(2000 + $twoDigitYear, $dateObj->format('n'), $dateObj->format('j'));
                    } else {
                        $dateObj->setDate(1900 + $twoDigitYear, $dateObj->format('n'), $dateObj->format('j'));
                    }
                }
            }
            
            return $dateObj->format('Y-m-d');
        }
    }
    
    // Try to handle Buddhist calendar dates (BE to AD conversion)
    // Pattern: d-m-yyyy where yyyy might be Buddhist year
    if (preg_match('/^(\d{1,2})[-\/\.](\d{1,2})[-\/\.](\d{4})$/', $dateString, $matches)) {
        $day = intval($matches[1]);
        $month = intval($matches[2]);
        $year = intval($matches[3]);
        
        // Convert Buddhist year to Christian year if year > 2500
        if ($year > 2500) {
            $year = $year - 543;
        }
        
        // Validate the date
        if (checkdate($month, $day, $year)) {
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }
    }
    
    return false; // Invalid date
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csvData'])) {
    require_once('../../../../view/config/connect.php');
    
    $csvData = $_POST['csvData'];
    $rows = str_getcsv($csvData, "\n");
    
    // Remove empty rows
    $rows = array_filter($rows, function($row) {
        return !empty(trim($row));
    });
    
    if (empty($rows)) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลที่ถูกต้องในไฟล์ CSV']);
        exit;
    }

    try {
        // Connect to the database using config variables
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start a transaction
        $pdo->beginTransaction();
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $duplicateCount = 0;

        foreach ($rows as $rowIndex => $row) {
            $data = str_getcsv($row);
            
            // Skip if not enough columns (expecting 6 columns)
            if (count($data) < 6) {
                $errorCount++;
                $errors[] = "แถวที่ " . ($rowIndex + 1) . ": ข้อมูลไม่ครบ (ต้องการ 6 คอลัมน์ แต่ได้ " . count($data) . " คอลัมน์)";
                continue;
            }
            
            // Clean data - trim whitespace
            $data = array_map('trim', $data);
            
            // Validate required fields
            if (empty($data[0]) || empty($data[1])) {
                $errorCount++;
                $errors[] = "แถวที่ " . ($rowIndex + 1) . ": วันที่หรือเลขที่บิลไม่สามารถเป็นค่าว่างได้";
                continue;
            }

            // Convert date format
            if (!empty($data[0])) {
                $convertedDate = convertDateToMySQLFormat($data[0]);
                if ($convertedDate === false) {
                    $errorCount++;
                    $errors[] = "แถวที่ " . ($rowIndex + 1) . ": รูปแบบวันที่ไม่ถูกต้อง ('{$data[0]}') รองรับ: DD-MM-YY, DD-MM-YYYY, DD/MM/YYYY, YYYY-MM-DD";
                    continue;
                } else {
                    $data[0] = $convertedDate;
                }
            }

            // Validate numeric fields
            if (!empty($data[4]) && !is_numeric($data[4])) {
                $errorCount++;
                $errors[] = "แถวที่ " . ($rowIndex + 1) . ": ยอดรวมบิลต้องเป็นตัวเลข (ได้: '{$data[4]}')";
                continue;
            }

            try {
                // Check for duplicate bill_number first
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM tb_header WHERE bill_number = ?");
                $checkStmt->execute([$data[1]]);
                
                if ($checkStmt->fetchColumn() > 0) {
                    $duplicateCount++;
                    $errors[] = "แถวที่ " . ($rowIndex + 1) . ": เลขที่บิล '{$data[1]}' มีอยู่แล้วในระบบ";
                    continue;
                }

                // Insert into tb_header table
                $stmt = $pdo->prepare("INSERT INTO tb_header (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data[0], // bill_date (converted to Y-m-d format)
                    $data[1], // bill_number
                    $data[2], // bill_customer_id
                    $data[3], // bill_customer_name
                    $data[4], // bill_total
                    $data[5]  // bill_isCanceled
                ]);
                $successCount++;
            } catch (PDOException $e) {
                $errorCount++;
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $duplicateCount++;
                    $errors[] = "แถวที่ " . ($rowIndex + 1) . ": ข้อมูลซ้ำกับที่มีอยู่แล้ว (Bill: {$data[1]})";
                } else {
                    $errors[] = "แถวที่ " . ($rowIndex + 1) . ": " . $e->getMessage();
                }
            }
        }

        // Commit the transaction
        $pdo->commit();

        // Prepare response message
        $message = "นำเข้าข้อมูล Header สำเร็จ: {$successCount} แถว";
        if ($errorCount > 0) {
            $message .= ", ผิดพลาด: {$errorCount} แถว";
        }
        if ($duplicateCount > 0) {
            $message .= ", ข้อมูลซ้ำ: {$duplicateCount} แถว";
        }

        // Return JSON response
        echo json_encode([
            'status' => $successCount > 0 ? 'success' : 'error',
            'message' => $message,
            'details' => [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'duplicate_count' => $duplicateCount,
                'total_rows' => count($rows),
                'errors' => array_slice($errors, 0, 10) // Limit to first 10 errors
            ]
        ]);

    } catch (PDOException $e) {
        // Rollback the transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        echo json_encode([
            'status' => 'error', 
            'message' => 'เกิดข้อผิดพลาดในฐานข้อมูล: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid request method or missing CSV data parameter'
    ]);
}
?>