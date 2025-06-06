<?php
require_once('../../view/config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    echo '<script>location.href="../../view/login"</script>';
    exit;
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถานะการขนส่ง - Wanawat Tracking System</title>
    
    <!-- CSS Dependencies -->
    <link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    
    <style>
        /* Google Fonts Import Link */
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #F0592E 0%, #FF8A65 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Main Content */
        .home-section {
            position: relative;
            background: transparent;
            min-height: 100vh;
            left: 300px;
            width: calc(100% - 300px);
            transition: all 0.5s ease;
            padding: 12px;
            overflow-y: auto;
        }

        .sidebar.close ~ .home-section {
            left: 78px;
            width: calc(100% - 78px);
        }

        /* Header Content */
        .home-content {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .home-section .home-content .bx-menu,
        .home-section .home-content .text {
            color: #fff;
            font-size: 35px;
        }

        .home-section .home-content .bx-menu {
            cursor: pointer;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .home-section .home-content .bx-menu:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .home-section .home-content .text {
            font-size: 26px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Content Container */
        .content-container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(240, 89, 46, 0.3);
            border-radius: 10px;
            color: #F0592E;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.2);
            font-size: 0.95rem;
        }

        .back-button:hover {
            background: rgba(240, 89, 46, 0.1);
            color: #D84315;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.3);
        }

        .back-button i {
            margin-right: 8px;
            font-size: 1rem;
        }

        /* Page Title */
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #F0592E;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .page-title i {
            margin-right: 15px;
            color: #F0592E;
            font-size: 1.8rem;
        }

        /* Search Section */
        .search-section {
            background: rgba(240, 89, 46, 0.05);
            border: 1px solid rgba(240, 89, 46, 0.2);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.1);
        }

        .search-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #F0592E;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-bar {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .insearch {
            flex: 1;
            min-width: 250px;
            padding: 12px 20px;
            border: 2px solid rgba(240, 89, 46, 0.3);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .insearch:focus {
            outline: none;
            border-color: #F0592E;
            box-shadow: 0 0 0 3px rgba(240, 89, 46, 0.2);
            transform: translateY(-1px);
        }

        .insearch::placeholder {
            color: #adb5bd;
        }

        .search {
            padding: 12px 24px;
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
        }

        .search:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.4);
        }

        /* Instruction Box */
        .instruction-box {
            background: rgba(33, 150, 243, 0.05);
            border: 1px solid rgba(33, 150, 243, 0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.1);
        }

        .instruction-box:hover {
            background: rgba(33, 150, 243, 0.08);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15);
        }

        .instruction-box h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2196F3;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .expand-icon {
            background: rgba(33, 150, 243, 0.1);
            color: #2196F3 !important;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .instruction-list {
            padding-left: 0;
            margin: 0;
        }

        .instruction-list h4 {
            color: #2d3748;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .instruction-list li {
            list-style: none;
            margin-bottom: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            border-left: 4px solid;
            font-size: 0.95rem;
        }

        .instruction-list li:nth-child(2) { border-left-color: #dc3545; } /* Red */
        .instruction-list li:nth-child(3) { border-left-color: #28a745; } /* Green */
        .instruction-list li:nth-child(4) { border-left-color: #007bff; } /* Blue */
        .instruction-list li:nth-child(5) { border-left-color: #ffc107; } /* Yellow */
        .instruction-list li:nth-child(6) { border-left-color: #6c757d; } /* Grey */
        .instruction-list li:nth-child(7) { border-left-color: #6f42c1; } /* Purple */

        /* Action Buttons */
        .action-buttons {
            margin-bottom: 20px;
            padding: 15px 20px;
            background: rgba(240, 89, 46, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(240, 89, 46, 0.2);
            display: none;
        }

        .action-buttons.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-custom {
            padding: 10px 20px;
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-right: 10px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
        }

        .btn-custom:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.4);
        }

        .btn-red {
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3) !important;
        }

        .btn-red:hover {
            background: linear-gradient(135deg, #c82333, #bd2130) !important;
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
        }

        /* Table Container */
        .table-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 30px;
        }

        /* Table Styling */
        #myTable {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        #myTable thead th {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            font-size: 0.95rem;
            position: relative;
        }

        #myTable thead th:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: rgba(255, 255, 255, 0.3);
        }

        #myTable tbody td {
            padding: 15px 12px;
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid rgba(240, 89, 46, 0.1);
            color: #2d3748;
            font-weight: 500;
            font-size: 0.9rem;
        }

        #myTable tbody tr:hover {
            background-color: rgba(240, 89, 46, 0.08);
            transition: background-color 0.3s ease;
            transform: scale(1.002);
        }

        /* Status Colors */
        .status-red { background-color: rgba(220, 53, 69, 0.1) !important; }
        .status-green { background-color: rgba(40, 167, 69, 0.1) !important; }
        .status-blue { background-color: rgba(0, 123, 255, 0.1) !important; }
        .status-yellow { background-color: rgba(255, 193, 7, 0.1) !important; }
        .status-grey { background-color: rgba(108, 117, 125, 0.1) !important; }
        .status-purple { background-color: rgba(111, 66, 193, 0.1) !important; }

        /* Completed Row Styling */
        .completed-row {
            opacity: 0.7 !important;
            background-color: rgba(40, 167, 69, 0.05) !important;
            border-left: 3px solid #28a745 !important;
        }

        .completed-row:hover {
            background-color: rgba(40, 167, 69, 0.1) !important;
            transform: none !important; /* ไม่ให้ scale เมื่อ hover */
        }

        /* Disabled Checkbox Styling */
        .disabled-checkbox {
            opacity: 0.4;
            cursor: not-allowed !important;
            pointer-events: none;
        }

        .disabled-checkbox:hover {
            cursor: not-allowed !important;
        }

        /* Completed Status Styling */
        .completed-status {
            color: #28a745;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .completed-time {
            background: rgba(40, 167, 69, 0.15) !important;
            color: #1e7e34 !important;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        /* Status Circle Animation Override for Completed */
        .completed-row .status-circle.green {
            animation: completedPulse 3s infinite;
        }

        @keyframes completedPulse {
            0% { 
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(40, 167, 69, 0.4); 
                transform: scale(1);
            }
            50% { 
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 6px rgba(40, 167, 69, 0); 
                transform: scale(1.05);
            }
            100% { 
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(40, 167, 69, 0); 
                transform: scale(1);
            }
        }

        /* Success Icon Animation */
        .completed-status i {
            animation: checkBounce 2s infinite;
        }

        @keyframes checkBounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Pagination info styling */
        .pagination-info {
            text-align: center;
            margin: 15px 0;
            color: #6c757d;
            font-size: 0.9rem;
            padding: 10px;
            background: rgba(240, 89, 46, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(240, 89, 46, 0.1);
        }

        /* Search highlight in pagination info */
        .search-highlight {
            color: #F0592E;
            font-weight: 600;
            background: rgba(240, 89, 46, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
        }

        /* Status Circle */
        .status-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.8);
            position: relative;
            animation: pulse 2s infinite;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .status-circle:hover {
            transform: scale(1.2);
            z-index: 10;
        }

        .status-circle.red {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .status-circle.green {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .status-circle.blue {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .status-circle.yellow {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        .status-circle.grey {
            background: linear-gradient(135deg, #6c757d, #545b62);
        }

        .status-circle.purple {
            background: linear-gradient(135deg, #6f42c1, #59339d);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(240, 89, 46, 0.7);
            }
            70% {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 8px rgba(240, 89, 46, 0);
            }
            100% {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(240, 89, 46, 0);
            }
        }

        /* Pulse animation for different status colors */
        .status-circle.red {
            animation: pulseRed 2s infinite;
        }

        .status-circle.green {
            animation: pulseGreen 2s infinite;
        }

        .status-circle.blue {
            animation: pulseBlue 2s infinite;
        }

        .status-circle.yellow {
            animation: pulseYellow 2s infinite;
        }

        .status-circle.grey {
            animation: pulseGrey 2s infinite;
        }

        .status-circle.purple {
            animation: pulsePurple 2s infinite;
        }

        @keyframes pulseRed {
            0% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 8px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(220, 53, 69, 0); }
        }

        @keyframes pulseGreen {
            0% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 8px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(40, 167, 69, 0); }
        }

        @keyframes pulseBlue {
            0% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(0, 123, 255, 0.7); }
            70% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 8px rgba(0, 123, 255, 0); }
            100% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(0, 123, 255, 0); }
        }

        @keyframes pulseYellow {
            0% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(255, 193, 7, 0.7); }
            70% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 8px rgba(255, 193, 7, 0); }
            100% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(255, 193, 7, 0); }
        }

        @keyframes pulseGrey {
            0% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(108, 117, 125, 0.7); }
            70% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 8px rgba(108, 117, 125, 0); }
            100% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(108, 117, 125, 0); }
        }

        @keyframes pulsePurple {
            0% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(111, 66, 193, 0.7); }
            70% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 8px rgba(111, 66, 193, 0); }
            100% { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(111, 66, 193, 0); }
        }

        /* Checkbox Styling */
        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #F0592E;
            cursor: pointer;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .pagination .btn-custom {
            padding: 10px 16px;
            border: 2px solid rgba(240, 89, 46, 0.3);
            border-radius: 10px;
            color: #F0592E;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            min-width: 45px;
            text-align: center;
            box-shadow: none;
        }

        .pagination .btn-custom:hover {
            background: rgba(240, 89, 46, 0.1);
            color: #D84315;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(240, 89, 46, 0.2);
        }

        .pagination .btn-custom.active {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border-color: #F0592E;
            box-shadow: 0 5px 15px rgba(240, 89, 46, 0.4);
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .modal-header {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
            padding: 20px 25px;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.3rem;
        }

        .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body {
            padding: 25px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            border: none;
            padding: 20px 25px;
            gap: 10px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .empty-state i {
            font-size: 4rem;
            color: #adb5bd;
            margin-bottom: 20px;
            display: block;
        }

        .empty-state h3 {
            color: #2d3748;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .empty-state p {
            color: #718096;
            font-size: 1rem;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
        }

        .status-time-badge {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 4px 8px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .status-time-badge:hover {
            background: rgba(40, 167, 69, 0.2);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .no-data-badge {
            color: #6c757d;
            font-style: italic;
            font-size: 0.85rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .home-section {
                left: 0;
                width: 100%;
                padding: 12px 8px;
            }

            .home-content .text {
                font-size: 20px;
            }

            .home-content .bx-menu {
                font-size: 28px;
            }

            .content-container {
                padding: 20px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .search-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .insearch {
                min-width: auto;
            }

            .table-container {
                overflow-x: auto;
            }

            #myTable {
                min-width: 1200px;
            }

            #myTable thead th,
            #myTable tbody td {
                padding: 10px 8px;
                font-size: 0.85rem;
            }

            .pagination {
                gap: 5px;
            }

            .pagination .btn-custom {
                padding: 8px 12px;
                font-size: 0.9rem;
                min-width: 40px;
            }
        }

        @media (max-width: 480px) {
            .home-section {
                padding: 8px;
            }

            .home-content .text {
                font-size: 18px;
            }

            .home-content .bx-menu {
                font-size: 24px;
            }

            .content-container {
                padding: 15px;
            }

            .page-title {
                font-size: 1.3rem;
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }

            .search-section {
                padding: 20px;
            }

            #myTable {
                min-width: 1100px;
            }
        }

        /* Animations */
        .animate__fadeInUp {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate__fadeIn {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Sticky Instructions */
        .instruction-box.sticky {
            position: fixed;
            top: 20px;
            z-index: 999;
            width: calc(100% - 340px);
            max-width: 1370px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .instruction-box.sticky {
                width: calc(100% - 40px);
                left: 20px;
            }
        }
    </style>
</head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>

    <section class="home-section">
        <!-- Header with menu button -->
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">สถานะการขนส่ง</span>
        </div>

        <div class="content-container animate__fadeInUp">
            <a href="dashboard" class="back-button">
                <i class="bi bi-arrow-left"></i> กลับไปหน้า Dashboard
            </a>
            
            <div class="page-title">
                <i class="bi bi-truck"></i>
                สถานะการขนส่ง
            </div>

            <!-- Search Section -->
            <div class="search-section">
                <div class="search-title">
                    <i class="bi bi-search"></i>
                    ค้นหาข้อมูลการขนส่ง
                </div>
                <div class="search-bar">
                    <form method="GET" action="" style="display: flex; gap: 12px; width: 100%; align-items: center; flex-wrap: wrap;">
                        <input class="insearch" type="text" name="search" placeholder="ค้นหาด้วยเลขที่การขนส่ง..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="search">
                            <i class="bi bi-search"></i>
                            ค้นหา
                        </button>
                    </form>
                </div>
            </div>

            <!-- Instruction Box -->
            <div class="instruction-box" onclick="toggleInstructions()">
                <h2>
                    <span><i class="bi bi-info-circle"></i> คำแนะนำในการใช้งานระบบ</span>
                    <span class="expand-icon">+</span>
                </h2>
                <ol class="instruction-list" style="display:none;">
                    <h4>ความหมายของสีสถานะสินค้า</h4>
                    <li>
                        <b style="color: red;">สีแดง</b>
                        <i style="color:black;">: สถานะสินค้าที่เกิดปัญหา</i>
                    </li>
                    <li>
                        <b style="color: green;">สีเขียว</b>
                        <i style="color:black;">: สถานะสินค้าที่ถึงนำส่งให้ลูกค้าสำเร็จ</i>
                    </li>
                    <li>
                        <b style="color: blue;">สีน้ำเงิน</b>
                        <i style="color:black;">: สถานะสินค้าที่คำสั่งซื้อเข้าสู่ระบบ</i>
                    </li>
                    <li>
                        <b style="color: orange;">สีเหลือง</b>
                        <i style="color:black;">: สถานะสินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า</i>
                    </li>
                    <li>
                        <b style="color: grey;">สีเทา</b>
                        <i style="color:black;">: สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลาย</i>
                    </li>
                    <li>
                        <b style="color: purple;">สีม่วง</b>
                        <i style="color:black;">: สถานะสินค้าที่กำลังนำส่งให้ลูกค้า</i>
                    </li>
                    <hr style="margin: 15px 0; border: 1px solid rgba(33, 150, 243, 0.2);">
                    <li style="border-left-color: #17a2b8; background: rgba(23, 162, 184, 0.05);">
                        <b style="color: #17a2b8;">💡 เคล็ดลับ:</b>
                        <i style="color:black;">คุณสามารถดูสีสถานะในรูปแบบวงกลมสีที่คอลัมน์ "สีสถานะ" และเมื่อวางเมาส์เหนือวงกลมจะแสดงรายละเอียดสถานะ พร้อมดู Timeline การขนส่งใน Modal รายการที่ส่งเสร็จแล้วจะอยู่ล่างสุดและไม่สามารถเลือกได้</i>
                    </li>
                </ol>
            </div>

            <?php 
            // Include search term processing (ต้องใช้ไฟล์ searchterm.php ที่ปรับปรุงแล้ว)
            require_once "function/statusbill/searchterm.php";
            ?>

            <!-- Action Buttons -->
            <div id="action-buttons" class="action-buttons">
                <button class="btn-custom" id="manageAllBtn">
                    <i class="bi bi-gear"></i>
                    จัดการที่เลือก
                </button>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <table id="myTable">
                    <thead>
                        <tr>
                            <th width="5%">เลือก</th>
                            <th width="5%">#</th>
                            <th width="8%">สีสถานะ</th>
                            <th width="15%">เลขที่การขนส่ง</th>
                            <th width="10%">จำนวนสินค้า</th>
                            <th width="18%">สถานะปัจจุบัน</th>
                            <th width="13%">วันที่สร้างบิล</th>
                            <th width="13%">วันเวลาสถานะล่าสุด</th>
                            <th width="13%">ประเภทการขนย้าย</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $i = ($current_page - 1) * 20 + 1; // เริ่มนับจากหน้าปัจจุบัน
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Determine status text, class, and circle color
                                $is_completed = ($row['delivery_status'] == 5);
                                $is_disabled = $is_completed; // ปิดการใช้งาน checkbox สำหรับสถานะส่งเสร็จ
                                
                                switch ($row['delivery_status']) {
                                    case 1:
                                        $status_text = 'รับคำสั่งซื้อ';
                                        $status_class = 'status-blue';
                                        $circle_color = 'blue';
                                        $latest_step_time = $row['delivery_step1_received'];
                                        break;
                                    case 2:
                                        $status_text = 'กำลังจัดส่งไปศูนย์';
                                        $status_class = 'status-yellow';
                                        $circle_color = 'yellow';
                                        $latest_step_time = $row['delivery_step2_transit'];
                                        break;
                                    case 3:
                                        $status_text = 'ถึงศูนย์กระจาย';
                                        $status_class = 'status-grey';
                                        $circle_color = 'grey';
                                        $latest_step_time = $row['delivery_step3_warehouse'];
                                        break;
                                    case 4:
                                        $status_text = 'กำลังส่งลูกค้า';
                                        $status_class = 'status-purple';
                                        $circle_color = 'purple';
                                        $latest_step_time = $row['delivery_step4_last_mile'];
                                        break;
                                    case 5:
                                        $status_text = 'ส่งสำเร็จ';
                                        $status_class = 'status-green completed-row';
                                        $circle_color = 'green';
                                        $latest_step_time = $row['delivery_step5_completed'];
                                        break;
                                    case 99:
                                        $status_text = 'เกิดปัญหา';
                                        $status_class = 'status-red';
                                        $circle_color = 'red';
                                        // Find the latest non-null timestamp for problem status
                                        $timestamps = [
                                            $row['delivery_step5_completed'],
                                            $row['delivery_step4_last_mile'],
                                            $row['delivery_step3_warehouse'],
                                            $row['delivery_step2_transit'],
                                            $row['delivery_step1_received']
                                        ];
                                        $latest_step_time = null;
                                        foreach ($timestamps as $timestamp) {
                                            if (!empty($timestamp)) {
                                                $latest_step_time = $timestamp;
                                                break;
                                            }
                                        }
                                        break;
                                    default:
                                        $status_text = 'ไม่ทราบสถานะ';
                                        $status_class = '';
                                        $circle_color = 'grey';
                                        $latest_step_time = null;
                                        break;
                                }

                                echo '<tr class="' . $status_class . '">';
                                
                                // Checkbox column with conditional disable
                                echo '<td><center>';
                                if ($is_disabled) {
                                    echo '<input type="checkbox" disabled class="disabled-checkbox" title="ไม่สามารถเลือกรายการที่ส่งเสร็จแล้วได้">';
                                    echo '<i class="bi bi-check-circle-fill text-success ms-1" title="ส่งเสร็จแล้ว"></i>';
                                } else {
                                    echo '<input type="checkbox" name="select" value="' . $row['delivery_id'] . '" data-status-text="' . $status_text . '" data-delivery-number="' . $row['delivery_number'] . '">';
                                }
                                echo '</center></td>';
                                
                                echo '<td>' . $i . '</td>';
                                echo '<td><center><div class="status-circle ' . $circle_color . '" title="' . $status_text . '"></div></center></td>';
                                echo '<td><strong>' . $row['delivery_number'] . '</strong></td>';
                                echo '<td><center><span style="background: rgba(240, 89, 46, 0.1); padding: 4px 8px; border-radius: 12px; font-weight: 600; color: #F0592E;">' . $row['item_count'] . ' รายการ</span></center></td>';
                                
                                // Status text with completion badge
                                echo '<td>';
                                if ($is_completed) {
                                    echo '<span class="completed-status">';
                                    echo '<i class="bi bi-check-circle-fill text-success me-1"></i>';
                                    echo $status_text;
                                    echo '</span>';
                                } else {
                                    echo $status_text;
                                }
                                echo '</td>';
                                
                                echo '<td>' . date('d/m/Y H:i', strtotime($row['delivery_date'])) . '</td>';
                                
                                // Display latest step time
                                if (!empty($latest_step_time)) {
                                    $formatted_time = date('d/m/Y H:i', strtotime($latest_step_time));
                                    if ($is_completed) {
                                        echo '<td><span class="status-time-badge completed-time">' . $formatted_time . '</span></td>';
                                    } else {
                                        echo '<td><span class="status-time-badge">' . $formatted_time . '</span></td>';
                                    }
                                } else {
                                    echo '<td><span class="no-data-badge">ยังไม่มีข้อมูล</span></td>';
                                }
                                
                                // แสดง transfer_type อย่างปลอดภัย
                                $transfer_type = isset($row['transfer_type']) ? $row['transfer_type'] : 
                                               (isset($row['transfer_type_default']) ? $row['transfer_type_default'] : 'ทั่วไป');
                                
                                echo '<td><span style="background: rgba(33, 150, 243, 0.1); padding: 4px 8px; border-radius: 8px; color: #2196F3; font-weight: 500;">' . htmlspecialchars($transfer_type) . '</span></td>';
                                echo '</tr>';

                                $i++;
                            }
                        } else {
                            echo "<tr><td colspan='9' class='empty-state'>";
                            echo "<i class='bi bi-inbox'></i>";
                            echo "<h3>ไม่พบข้อมูลการขนส่ง</h3>";
                            echo "<p>ยังไม่มีข้อมูลการขนส่งที่ตรงกับการค้นหา</p>";
                            echo "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info -->
            <?php if ($total_records > 0): ?>
            <div class="pagination-info">
                <i class="bi bi-info-circle me-1"></i>
                แสดงรายการที่ <?php echo number_format($start_record); ?> - <?php echo number_format($end_record); ?> 
                จากทั้งหมด <?php echo number_format($total_records); ?> รายการ
                <?php if (!empty($search)): ?>
                    <span class="search-highlight">
                        (ผลการค้นหา: "<?php echo htmlspecialchars($search); ?>")
                    </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="btn-custom">
                        <i class="bi bi-chevron-left"></i> ก่อนหน้า
                    </a>
                <?php endif; ?>

                <?php 
                // Show page numbers with smart pagination
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                // Show first page if not in range
                if ($start_page > 1) {
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="btn-custom">1</a>';
                    if ($start_page > 2) {
                        echo '<span class="btn-custom" style="border: none; background: transparent; cursor: default;">...</span>';
                    }
                }
                
                // Show page range
                for ($i = $start_page; $i <= $end_page; $i++): 
                ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                       class="btn-custom <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php
                // Show last page if not in range
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<span class="btn-custom" style="border: none; background: transparent; cursor: default;">...</span>';
                    }
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $total_pages])) . '" class="btn-custom">' . $total_pages . '</a>';
                }
                ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="btn-custom">
                        ถัดไป <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Quick Page Jump -->
            <?php if ($total_pages > 10): ?>
            <div style="text-align: center; margin-top: 15px;">
                <form method="GET" action="" style="display: inline-flex; align-items: center; gap: 10px;">
                    <?php foreach ($_GET as $key => $value): ?>
                        <?php if ($key !== 'page'): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <label for="pageJump" style="font-size: 0.9rem; color: #6c757d;">ไปที่หน้า:</label>
                    <input type="number" id="pageJump" name="page" min="1" max="<?php echo $total_pages; ?>" 
                           value="<?php echo $current_page; ?>" 
                           style="width: 80px; padding: 5px 8px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
                    <button type="submit" style="padding: 5px 12px; background: #F0592E; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                    <small style="color: #6c757d;">(จาก <?php echo $total_pages; ?> หน้า)</small>
                </form>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="manageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-gear me-2"></i>
                        อัปเดตสถานะการขนส่ง
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Modal body content -->
                </div>
                <div class="modal-footer">
                    <button id="updateStatusBtn" class="btn-custom">
                        <i class="bi bi-arrow-clockwise"></i>
                        อัปเดตสถานะการจัดส่งสินค้า
                    </button>
                    <button id="reportProblemBtn" class="btn-custom btn-red">
                        <i class="bi bi-exclamation-triangle"></i>
                        แจ้งว่าสินค้ามีปัญหา
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="function/statusbill/js/modal.js"></script>
    <script src="function/statusbill/js/updatestatusbtn.js"></script>
    <script src="function/statusbill/js/reportstatusbtn.js"></script>

    <script>
        // Enhanced JavaScript functionality
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="select"]:not(.disabled-checkbox)'); // ไม่รวม checkbox ที่ปิดการใช้งาน
            const actionButtons = document.getElementById('action-buttons');

            // Monitor checkbox changes (เฉพาะ checkbox ที่ใช้งานได้)
            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    const anyChecked = Array.from(checkboxes).some(chk => chk.checked);
                    
                    if (anyChecked) {
                        actionButtons.classList.add('show');
                    } else {
                        actionButtons.classList.remove('show');
                    }
                    
                    // Update checkbox row styling
                    updateCheckboxStyling();
                });
            });

            function updateCheckboxStyling() {
                checkboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    if (checkbox.checked && !row.classList.contains('completed-row')) {
                        row.style.backgroundColor = 'rgba(240, 89, 46, 0.15)';
                        row.style.transform = 'scale(1.002)';
                    } else {
                        if (!row.classList.contains('completed-row')) {
                            row.style.backgroundColor = '';
                            row.style.transform = 'scale(1)';
                        }
                    }
                });
            }

            // Add hover effects to table rows (ยกเว้นแถวที่เสร็จสิ้น)
            const tableRows = document.querySelectorAll('#myTable tbody tr:not(.completed-row)');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    const checkbox = this.querySelector('input[type="checkbox"]:not(.disabled-checkbox)');
                    if (checkbox && !checkbox.checked) {
                        this.style.transform = 'scale(1.002)';
                    }
                });
                
                row.addEventListener('mouseleave', function() {
                    const checkbox = this.querySelector('input[type="checkbox"]:not(.disabled-checkbox)');
                    if (checkbox && !checkbox.checked) {
                        this.style.transform = 'scale(1)';
                    }
                });
            });

            // Add special handling for completed rows
            const completedRows = document.querySelectorAll('#myTable tbody tr.completed-row');
            completedRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.cursor = 'default';
                });
            });

            // Add notification for completed items
            if (completedRows.length > 0) {
                console.log(`พบรายการที่ส่งเสร็จแล้ว ${completedRows.length} รายการ - ไม่สามารถจัดการได้`);
            }

            // Update statistics
            updateTableStatistics();
        });

        // Function to update table statistics
        function updateTableStatistics() {
            const totalRows = document.querySelectorAll('#myTable tbody tr').length;
            const completedRows = document.querySelectorAll('#myTable tbody tr.completed-row').length;
            const activeRows = totalRows - completedRows;
            const emptyState = document.querySelector('.empty-state');
            
            if (!emptyState && totalRows > 0) {
                // Add statistics info if not exists
                let statsDiv = document.getElementById('table-statistics');
                if (!statsDiv) {
                    statsDiv = document.createElement('div');
                    statsDiv.id = 'table-statistics';
                    statsDiv.style.cssText = `
                        text-align: center;
                        margin: 10px 0;
                        padding: 8px 15px;
                        background: rgba(33, 150, 243, 0.05);
                        border: 1px solid rgba(33, 150, 243, 0.2);
                        border-radius: 8px;
                        font-size: 0.85rem;
                        color: #2196F3;
                    `;
                    
                    const tableContainer = document.querySelector('.table-container');
                    tableContainer.parentNode.insertBefore(statsDiv, tableContainer);
                }
                
                let statsText = `รายการทั้งหมด: ${totalRows} รายการ`;
                if (completedRows > 0) {
                    statsText += ` | รายการที่สามารถจัดการได้: ${activeRows} รายการ`;
                    statsText += ` | รายการที่ส่งเสร็จแล้ว: ${completedRows} รายการ`;
                }
                
                statsDiv.innerHTML = `<i class="bi bi-info-circle me-1"></i>${statsText}`;
            }
        }

        // Instructions toggle function
        function toggleInstructions() {
            var instructions = document.querySelector('.instruction-list');
            var expandIcon = document.querySelector('.expand-icon');
            
            if (instructions.style.display === 'none') {
                instructions.style.display = 'block';
                expandIcon.textContent = '-';
            } else {
                instructions.style.display = 'none';
                expandIcon.textContent = '+';
            }
        }

        // Sticky instructions
        window.onscroll = function() {
            myFunction();
        };

        var instructionsbox = document.querySelector('.instruction-box');
        var sticky = instructionsbox.offsetTop;

        function myFunction() {
            if (window.pageYOffset >= sticky) {
                instructionsbox.classList.add("sticky");
            } else {
                instructionsbox.classList.remove("sticky");
            }
        }

        // Enhanced modal functionality for Bootstrap 5
        function handleSelectedItems() {
            const selectedItems = [];
            const checkboxes = document.querySelectorAll('input[name="select"]:checked:not(.disabled-checkbox)'); // เฉพาะที่เลือกและไม่ disable

            checkboxes.forEach((checkbox) => {
                selectedItems.push(checkbox.value);
            });

            if (selectedItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่ได้เลือกรายการ',
                    text: 'กรุณาเลือกการจัดส่งอย่างน้อยหนึ่งรายการที่สามารถจัดการได้',
                    confirmButtonColor: '#F0592E'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'กำลังโหลดข้อมูล...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch data and show modal
            $.ajax({
                url: 'function/fetch_modal_data.php',
                type: 'POST',
                data: {
                    deliveryIds: selectedItems.join(',')
                },
                success: function(data) {
                    Swal.close();
                    
                    if (data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.error,
                            confirmButtonColor: '#F0592E'
                        });
                        return;
                    }

                    if (!data.items) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'ไม่พบข้อมูล',
                            text: 'ไม่มีข้อมูลที่สามารถแสดงได้',
                            confirmButtonColor: '#F0592E'
                        });
                        return;
                    }

                    openModal(data);
                    const modal = new bootstrap.Modal(document.getElementById('manageModal'));
                    modal.show();
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error('Error:', error);
                    console.error('XHR Response:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถดึงข้อมูลได้: ' + error,
                        confirmButtonColor: '#F0592E'
                    });
                }
            });
        }

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+A to select all available checkboxes
            if (e.ctrlKey && e.key === 'a') {
                e.preventDefault();
                const checkboxes = document.querySelectorAll('input[name="select"]:not(.disabled-checkbox)');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });
                
                // Trigger change event
                if (checkboxes.length > 0) {
                    checkboxes[0].dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
</body>
</html>