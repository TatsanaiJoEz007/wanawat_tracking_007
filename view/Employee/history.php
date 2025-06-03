<?php
        require_once('../../view/config/connect.php');

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $user_id = $_SESSION['user_id'];
    ?>

<!DOCTYPE html>
<html lang="th">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ประวัติการจัดส่ง - Wanawat Tracking System</title>
        
        <!-- CSS Dependencies -->
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <script src="https://cdn.lordicon.com/lordicon.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
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

            /* Main Content - ใช้ home-section เหมือน dashboard */
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

            .container {
                position: relative;
                background: transparent;
                padding: 0;
                margin: 0;
                max-width: none;
            }

            /* Header Content - ใช้สไตล์เหมือน dashboard */
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

            .page-title {
                font-size: 2.5rem;
                font-weight: 700;
                color: white;
                margin-bottom: 10px;
                text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
            }

            .page-title i {
                color: white;
                font-size: 2.2rem;
            }

            .page-subtitle {
                color: rgba(255, 255, 255, 0.9);
                font-size: 1.1rem;
                font-weight: 400;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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

            /* ปุ่มย้อนกลับ */
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

            /* Status Tabs */
            .status-tabs {
                margin-bottom: 30px;
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: center;
                background: rgba(240, 89, 46, 0.05);
                padding: 20px;
                border-radius: 15px;
                border: 1px solid rgba(240, 89, 46, 0.2);
            }

            .tab-button {
                padding: 12px 24px;
                border: 2px solid rgba(240, 89, 46, 0.3);
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.9);
                color: #F0592E;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                font-size: 0.95rem;
            }

            .tab-button:hover {
                background: rgba(240, 89, 46, 0.1);
                color: #D84315;
                text-decoration: none;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(240, 89, 46, 0.2);
            }

            .tab-button.active {
                background: linear-gradient(135deg, #F0592E, #FF8A65);
                color: white;
                border-color: #F0592E;
                box-shadow: 0 5px 15px rgba(240, 89, 46, 0.4);
            }

            .tab-button i {
                font-size: 1.1rem;
            }

            /* Search Bar */
            .search-section {
                margin-bottom: 30px;
                padding: 25px;
                background: rgba(240, 89, 46, 0.05);
                border-radius: 15px;
                border: 1px solid rgba(240, 89, 46, 0.2);
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

            /* Stats Cards */
            .stats-section {
                margin-bottom: 30px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 20px;
            }

            .stat-card {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 25px;
                text-align: center;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.3);
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
                background: rgba(255, 255, 255, 1);
            }

            .stat-icon {
                font-size: 2.5rem;
                margin-bottom: 15px;
                color: #F0592E;
                display: block;
            }

            .stat-number {
                font-size: 2.2rem;
                font-weight: 700;
                color: #2d3748;
                margin-bottom: 8px;
                display: block;
            }

            .stat-title {
                font-size: 1rem;
                color: #718096;
                font-weight: 500;
            }

            /* Completed Row Styling */
            .completed-row {
                opacity: 0.7 !important;
            }

            .completed-row:hover {
                transform: none !important;
            }

            /* Table Section */
            .table-section {
                margin-bottom: 30px;
            }

            .section-title {
                font-size: 1.5rem;
                font-weight: 600;
                color: #2d3748;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .section-title i {
                color: #F0592E;
            }

            .table-container {
                background: rgba(255, 255, 255, 0.98);
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
            }

            table thead th {
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

            table thead th:not(:last-child)::after {
                content: '';
                position: absolute;
                right: 0;
                top: 25%;
                height: 50%;
                width: 1px;
                background: rgba(255, 255, 255, 0.3);
            }

            table tbody td {
                padding: 15px 12px;
                vertical-align: middle;
                text-align: center;
                border-bottom: 1px solid rgba(240, 89, 46, 0.1);
                color: #2d3748;
                font-weight: 500;
                font-size: 0.9rem;
            }

            table tbody tr:nth-child(even) {
                /* ลบสีพื้นหลัง */
            }

            table tbody tr:hover {
                /* ลบสีพื้นหลัง - เหลือแค่ transition และ transform */
                transition: all 0.3s ease;
                transform: scale(1.002);
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

            /* Status Badge Styles */
            .status-badge {
                padding: 8px 16px;
                border-radius: 20px;
                font-weight: 600;
                font-size: 0.85rem;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .status-blue {
                background: linear-gradient(135deg, #007bff, #0056b3);
                color: white;
                box-shadow: 0 3px 10px rgba(0, 123, 255, 0.3);
            }

            .status-yellow {
                background: linear-gradient(135deg, #ffc107, #e0a800);
                color: #212529;
                box-shadow: 0 3px 10px rgba(255, 193, 7, 0.3);
            }

            .status-grey {
                background: linear-gradient(135deg, #6c757d, #495057);
                color: white;
                box-shadow: 0 3px 10px rgba(108, 117, 125, 0.3);
            }

            .status-purple {
                background: linear-gradient(135deg, #6f42c1, #5a2a8a);
                color: white;
                box-shadow: 0 3px 10px rgba(111, 66, 193, 0.3);
            }

            .status-green {
                background: linear-gradient(135deg, #28a745, #20c997);
                color: white;
                box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
            }

            .status-red {
                background: linear-gradient(135deg, #dc3545, #c82333);
                color: white;
                box-shadow: 0 3px 10px rgba(220, 53, 69, 0.3);
            }

            /* Transfer Type Badge */
            .transfer-badge {
                padding: 6px 12px;
                border-radius: 15px;
                font-weight: 500;
                font-size: 0.85rem;
                background: rgba(240, 89, 46, 0.1);
                color: #F0592E;
                border: 1px solid rgba(240, 89, 46, 0.3);
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

            .btn-custom {
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
            }

            .btn-custom:hover {
                background: rgba(240, 89, 46, 0.1);
                color: #D84315;
                text-decoration: none;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(240, 89, 46, 0.2);
            }

            .btn-custom.active {
                background: linear-gradient(135deg, #F0592E, #FF8A65);
                color: white;
                border-color: #F0592E;
                box-shadow: 0 5px 15px rgba(240, 89, 46, 0.4);
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
                    border-radius: 15px;
                }

                .status-tabs {
                    flex-direction: column;
                    align-items: stretch;
                }

                .tab-button {
                    justify-content: center;
                    text-align: center;
                }

                .search-bar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .insearch {
                    min-width: auto;
                }

                .stats-grid {
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 15px;
                }

                .stat-card {
                    padding: 20px 15px;
                }

                .table-container {
                    overflow-x: auto;
                }

                table {
                    min-width: 1200px;
                }

                table thead th,
                table tbody td {
                    padding: 10px 8px;
                    font-size: 0.85rem;
                }

                .pagination {
                    gap: 5px;
                }

                .btn-custom {
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

                .search-section {
                    padding: 20px;
                }

                .stat-card {
                    padding: 15px;
                }

                .stat-icon {
                    font-size: 2rem;
                }

                .stat-number {
                    font-size: 1.8rem;
                }

                table {
                    min-width: 1100px;
                }
            }

            /* Loading Animation */
            .loading {
                text-align: center;
                padding: 40px;
                color: #718096;
            }

            .spinner {
                width: 40px;
                height: 40px;
                border: 4px solid rgba(240, 89, 46, 0.2);
                border-top: 4px solid #F0592E;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Custom scrollbar */
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
        </style>
    </head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>
    
    <section class="home-section">
        <!-- Header with menu button -->
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">ประวัติการดำเนินขนส่ง</span>
        </div>
        
        <div class="container">
            <!-- Page Description -->
            <div class="page-description animate__fadeIn" style="margin-bottom: 25px; text-align: center;">
                <p style="color: rgba(255, 255, 255, 0.9); font-size: 1.1rem; font-weight: 400; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); margin: 0;">
                    ระบบติดตามสถานะการจัดส่งทุกขั้นตอน
                </p>
            </div>

            <div class="content-container animate__fadeInUp">
                <!-- Back Button -->
                <a href="dashboard" class="back-button animate__fadeIn">
                    <i class="bi bi-arrow-left"></i> กลับไปหน้า Dashboard
                </a>

                <?php
                // Get status filter
                $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
                $search_term = isset($_GET['search']) ? $_GET['search'] : '';
                
                // Pagination settings
                $items_per_page = 20;
                $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $offset = ($current_page - 1) * $items_per_page;
                ?>

                <!-- Status Tabs -->
                <div class="status-tabs">
                    <a href="?status=all<?php echo $search_term ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="tab-button <?php echo $status_filter == 'all' ? 'active' : ''; ?>">
                        <i class="bi bi-list-ul"></i>
                        ทั้งหมด
                    </a>
                    <a href="?status=preparing<?php echo $search_term ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="tab-button <?php echo $status_filter == 'preparing' ? 'active' : ''; ?>">
                        <i class="bi bi-box-seam"></i>
                        กำลังจัดเตรียม
                    </a>
                    <a href="?status=sending<?php echo $search_term ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="tab-button <?php echo $status_filter == 'sending' ? 'active' : ''; ?>">
                        <i class="bi bi-truck"></i>
                        อยู่ระหว่างขนส่ง
                    </a>
                    <a href="?status=completed<?php echo $search_term ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="tab-button <?php echo $status_filter == 'completed' ? 'active' : ''; ?>">
                        <i class="bi bi-check-circle"></i>
                        สำเร็จแล้ว
                    </a>
                    <a href="?status=problem<?php echo $search_term ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="tab-button <?php echo $status_filter == 'problem' ? 'active' : ''; ?>">
                        <i class="bi bi-exclamation-triangle"></i>
                        มีปัญหา
                    </a>
                </div>

                <!-- Search Section -->
                <div class="search-section">
                    <div class="search-title">
                        <i class="bi bi-search"></i>
                        ค้นหาข้อมูลการจัดส่ง
                    </div>
                    <div class="search-bar">
                        <form method="GET" action="" style="display: flex; gap: 12px; width: 100%; align-items: center; flex-wrap: wrap;">
                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                            <input class="insearch" type="text" name="search" placeholder="ค้นหาด้วยเลขที่การจัดส่ง..." value="<?php echo htmlspecialchars($search_term); ?>">
                            <button type="submit" class="search">
                                <i class="bi bi-search"></i>
                                ค้นหา
                            </button>
                        </form>
                    </div>
                </div>

                <?php
                // Build status condition based on filter
                $status_condition = '';
                switch ($status_filter) {
                    case 'preparing':
                        $status_condition = "AND d.delivery_status = 1";
                        break;
                    case 'sending':
                        $status_condition = "AND d.delivery_status IN (2, 3, 4)";
                        break;
                    case 'completed':
                        $status_condition = "AND d.delivery_status = 5";
                        break;
                    case 'problem':
                        $status_condition = "AND d.delivery_status = 99";
                        break;
                    default:
                        $status_condition = ""; // Show all statuses
                        break;
                }

                // Search condition
                $search_condition = '';
                if ($search_term) {
                    $search_term_escaped = mysqli_real_escape_string($conn, $search_term);
                    $search_condition = " AND (d.delivery_number LIKE '%$search_term_escaped%' OR d.delivery_id LIKE '%$search_term_escaped%')";
                }

                // Query to get total number of items
                $total_items_query = "SELECT COUNT(DISTINCT d.delivery_id) as total 
                                    FROM tb_delivery d 
                                    INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                                    WHERE d.created_by = $user_id $status_condition $search_condition";

                // Execute query to get total count
                $total_items_result = mysqli_query($conn, $total_items_query);

                if (!$total_items_result) {
                    echo "Error fetching total items: " . mysqli_error($conn);
                    exit;
                }

                $total_items = mysqli_fetch_assoc($total_items_result)['total'];
                $total_pages = ceil($total_items / $items_per_page);

                // Updated main query to include step timestamps (same as statusbill.php)
                $query = "SELECT 
                            d.delivery_id, 
                            d.delivery_number, 
                            d.delivery_date, 
                            COUNT(di.item_code) AS item_count, 
                            d.delivery_status,
                            d.delivery_step1_received,
                            d.delivery_step2_transit,
                            d.delivery_step3_warehouse,
                            d.delivery_step4_last_mile,
                            d.delivery_step5_completed,
                            GROUP_CONCAT(DISTINCT di.transfer_type SEPARATOR ', ') as transfer_type 
                        FROM tb_delivery d 
                        INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                        WHERE d.created_by = $user_id $status_condition $search_condition
                        GROUP BY d.delivery_id, d.delivery_number, d.delivery_date, d.delivery_status, 
                                 d.delivery_step1_received, d.delivery_step2_transit, d.delivery_step3_warehouse, 
                                 d.delivery_step4_last_mile, d.delivery_step5_completed";

                // Special ordering for 'all' status to put completed items at bottom
                if ($status_filter == 'all') {
                    $query .= " ORDER BY 
                                CASE 
                                    WHEN d.delivery_status = 5 THEN 1
                                    ELSE 0
                                END ASC,
                                d.delivery_date DESC,
                                d.delivery_id DESC";
                } else {
                    $query .= " ORDER BY d.delivery_date DESC, d.delivery_id DESC";
                }

                $query .= " LIMIT $items_per_page OFFSET $offset";

                // Execute query to fetch data
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo "Error fetching data: " . mysqli_error($conn);
                    exit;
                }

                // Get status name for display
                $status_name = '';
                switch ($status_filter) {
                    case 'preparing':
                        $status_name = 'กำลังจัดเตรียม';
                        break;
                    case 'sending':
                        $status_name = 'อยู่ระหว่างขนส่ง';
                        break;
                    case 'completed':
                        $status_name = 'สำเร็จแล้ว';
                        break;
                    case 'problem':
                        $status_name = 'มีปัญหา';
                        break;
                    default:
                        $status_name = 'ทั้งหมด';
                        break;
                }

                // Calculate pagination info
                $start_record = $offset + 1;
                $end_record = min($offset + $items_per_page, $total_items);
                ?>

                <!-- Stats Section -->
                <div class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <i class="bi bi-list-check stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($total_items); ?></span>
                            <div class="stat-title">รายการ<?php echo $status_name; ?></div>
                        </div>
                        <div class="stat-card">
                            <i class="bi bi-calendar-check stat-icon"></i>
                            <span class="stat-number"><?php echo number_format($total_pages); ?></span>
                            <div class="stat-title">หน้าทั้งหมด</div>
                        </div>
                        <div class="stat-card">
                            <i class="bi bi-list-ol stat-icon"></i>
                            <span class="stat-number"><?php echo $current_page; ?></span>
                            <div class="stat-title">หน้าปัจจุบัน</div>
                        </div>
                        <div class="stat-card">
                            <i class="bi bi-box stat-icon"></i>
                            <span class="stat-number"><?php echo $items_per_page; ?></span>
                            <div class="stat-title">รายการต่อหน้า</div>
                        </div>
                    </div>
                </div>

                <!-- Pagination Info -->
                <?php if ($total_items > 0): ?>
                <div class="pagination-info">
                    <i class="bi bi-info-circle me-1"></i>
                    แสดงรายการที่ <?php echo number_format($start_record); ?> - <?php echo number_format($end_record); ?> 
                    จากทั้งหมด <?php echo number_format($total_items); ?> รายการ
                    <?php if ($search_term): ?>
                        <span style="color: #F0592E; font-weight: 600;">
                            (ผลการค้นหา: "<?php echo htmlspecialchars($search_term); ?>")
                        </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Table Section -->
                <div class="table-section">
                    <div class="section-title">
                        <i class="bi bi-table"></i>
                        รายละเอียดการจัดส่ง - <?php echo $status_name; ?>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="8%">สีสถานะ</th>
                                    <th width="18%">เลขที่การขนส่ง</th>
                                    <th width="10%">จำนวนสินค้า</th>
                                    <th width="15%">สถานะปัจจุบัน</th>
                                    <th width="15%">วันที่สร้างบิล</th>
                                    <th width="15%">วันเวลาสถานะล่าสุด</th>
                                    <th width="14%">ประเภทการขนย้าย</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    $i = $start_record;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // Determine status text, class, and circle color (same logic as statusbill.php)
                                        $is_completed = ($row['delivery_status'] == 5);
                                        $row_class = $is_completed ? 'completed-row' : ''; // เหลือแค่ completed-row
                                        switch ($row['delivery_status']) {
                                            case 1:
                                                $status_text = 'รับคำสั่งซื้อ';
                                                $circle_color = 'blue';
                                                $latest_step_time = $row['delivery_step1_received'];
                                                break;
                                            case 2:
                                                $status_text = 'กำลังจัดส่งไปศูนย์';
                                                $circle_color = 'yellow';
                                                $latest_step_time = $row['delivery_step2_transit'];
                                                break;
                                            case 3:
                                                $status_text = 'ถึงศูนย์กระจาย';
                                                $circle_color = 'grey';
                                                $latest_step_time = $row['delivery_step3_warehouse'];
                                                break;
                                            case 4:
                                                $status_text = 'กำลังส่งลูกค้า';
                                                $circle_color = 'purple';
                                                $latest_step_time = $row['delivery_step4_last_mile'];
                                                break;
                                            case 5:
                                                $status_text = 'ส่งสำเร็จ';
                                                $circle_color = 'green';
                                                $latest_step_time = $row['delivery_step5_completed'];
                                                break;
                                            case 99:
                                                $status_text = 'เกิดปัญหา';
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
                                                $circle_color = 'grey';
                                                $latest_step_time = null;
                                                break;
                                        }

                                        echo '<tr class="' . $row_class . '" data-delivery-id="' . $row['delivery_id'] . '" style="cursor: pointer;" title="คลิกเพื่อดูรายละเอียด">';
                                        echo '<td><strong>' . $i++ . '</strong></td>';
                                        echo '<td><center><div class="status-circle ' . $circle_color . '" title="' . $status_text . '"></div></center></td>';
                                        echo '<td><strong>' . htmlspecialchars($row['delivery_number']) . '</strong></td>';
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
                                        $transfer_type = isset($row['transfer_type']) ? $row['transfer_type'] : 'ทั่วไป';
                                        echo '<td><span class="transfer-badge">' . htmlspecialchars($transfer_type) . '</span></td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo "<tr>";
                                    echo "<td colspan='8' class='empty-state'>";
                                    echo "<i class='bi bi-inbox'></i>";
                                    echo "<h3>ไม่พบข้อมูลการจัดส่ง</h3>";
                                    echo "<p>ยังไม่มีรายการ" . $status_name . "" . ($search_term ? " ที่ตรงกับการค้นหา" : "") . "</p>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

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
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Modal สำหรับแสดงรายละเอียด -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>
                        รายละเอียดการจัดส่ง
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Modal body content -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-custom" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        ปิด
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include SweetAlert for modal notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript section for enhanced interactions -->
    <script>
        $(document).ready(function() {
            // Add loading effect to search
            $('form').on('submit', function() {
                $('.search').html('<div class="spinner" style="width: 20px; height: 20px; border-width: 2px; margin: 0 5px 0 0;"></div>กำลังค้นหา...');
            });

            // Add hover effects to stat cards
            $('.stat-card').hover(
                function() {
                    $(this).find('.stat-icon').css('transform', 'scale(1.1)');
                },
                function() {
                    $(this).find('.stat-icon').css('transform', 'scale(1)');
                }
            );

            // Add smooth scrolling to pagination and tabs
            $('.pagination a, .tab-button').on('click', function(e) {
                $('html, body').animate({
                    scrollTop: $('.content-container').offset().top - 20
                }, 500);
            });

            // Add row click effect (excluding completed rows)
            $('tbody tr:not(.completed-row)').on('click', function() {
                if (!$(this).find('.empty-state').length) {
                    // ลบการเปลี่ยนสีพื้นหลัง - เหลือแค่ effect อื่นๆ
                    $(this).css('transform', 'scale(1.005)');
                    setTimeout(() => {
                        $(this).css('transform', 'scale(1)');
                    }, 200);
                    
                    // เปิด modal รายละเอียด
                    const deliveryId = $(this).data('delivery-id');
                    if (deliveryId) {
                        openDeliveryDetail(deliveryId);
                    }
                }
            });

            // Special click effect for completed rows
            $('tbody tr.completed-row').on('click', function() {
                if (!$(this).find('.empty-state').length) {
                    // ลบการเปลี่ยนสีพื้นหลัง - เหลือแค่ effect อื่นๆ
                    $(this).css('transform', 'scale(1.005)');
                    setTimeout(() => {
                        $(this).css('transform', 'scale(1)');
                    }, 200);
                    
                    // เปิด modal รายละเอียด
                    const deliveryId = $(this).data('delivery-id');
                    if (deliveryId) {
                        openDeliveryDetail(deliveryId);
                    }
                }
            });

            // Animate elements on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            document.querySelectorAll('.stat-card, .table-container, .status-tabs').forEach((el) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });

            // Tab button hover effects
            $('.tab-button').hover(
                function() {
                    if (!$(this).hasClass('active')) {
                        $(this).find('i').css('transform', 'scale(1.1)');
                    }
                },
                function() {
                    $(this).find('i').css('transform', 'scale(1)');
                }
            );

            // Add status circle tooltip functionality
            $('.status-circle').hover(
                function() {
                    const status = $(this).attr('title');
                    $(this).attr('data-original-title', status);
                }
            );
        });

        // Auto-focus search input
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.insearch');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }
        });

        // Function to open delivery detail modal
        function openDeliveryDetail(deliveryId) {
            if (!deliveryId) return;
            
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
                    deliveryIds: deliveryId.toString()
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

                    if (!data.items || data.items.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'ไม่พบข้อมูล',
                            text: 'ไม่มีข้อมูลที่สามารถแสดงได้',
                            confirmButtonColor: '#F0592E'
                        });
                        return;
                    }

                    openModal(data);
                    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
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

        // Function to open modal (copied from modal.js)
        function openModal(data) {
            const modalContent = document.getElementById('modalContent');
            if (!modalContent) {
                console.error('Modal content element not found');
                return;
            }
            
            let content = '';
            
            if (data.items && data.items.length > 0) {
                content = `
                    <div style="max-height: 600px; overflow-y: auto;">
                        <h6 style="color: #F0592E; margin-bottom: 15px;">
                            <i class="bi bi-list-ul"></i> รายละเอียดการจัดส่ง (${data.items.length} รายการ)
                        </h6>`;
                
                data.items.forEach((item, index) => {
                    // Determine status text and color
                    let statusText = 'ไม่ทราบสถานะ';
                    let statusColor = '#6c757d';
                    
                    switch (parseInt(item.delivery_status)) {
                        case 1:
                            statusText = 'รับคำสั่งซื้อ';
                            statusColor = '#007bff';
                            break;
                        case 2:
                            statusText = 'กำลังจัดส่งไปศูนย์';
                            statusColor = '#ffc107';
                            break;
                        case 3:
                            statusText = 'ถึงศูนย์กระจาย';
                            statusColor = '#6c757d';
                            break;
                        case 4:
                            statusText = 'กำลังส่งลูกค้า';
                            statusColor = '#6f42c1';
                            break;
                        case 5:
                            statusText = 'ส่งสำเร็จ';
                            statusColor = '#28a745';
                            break;
                        case 99:
                            statusText = 'เกิดปัญหา';
                            statusColor = '#dc3545';
                            break;
                    }

                    // Generate timeline HTML
                    const timelineHtml = generateTimelineHtml(item);
                    
                    // Generate items detail HTML
                    let itemsHtml = '';
                    if (item.items && item.items.length > 0) {
                        itemsHtml = `
                            <div class="delivery-items" id="items-${item.delivery_id}" style="display: none; margin-top: 15px;">
                                <div style="background: rgba(248, 249, 250, 1); border-radius: 8px; padding: 15px; border: 1px solid #dee2e6;">
                                    <h6 style="color: #495057; margin-bottom: 15px; font-size: 1rem;">
                                        <i class="bi bi-box-seam"></i> รายละเอียดสินค้า (${item.items.length} รายการ)
                                    </h6>`;
                        
                        item.items.forEach((deliveryItem, itemIndex) => {
                            itemsHtml += `
                                <div style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 10px; border-left: 4px solid #F0592E; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.9rem;">
                                        <div><strong style="color: #495057;">เลขบิล:</strong> ${deliveryItem.bill_number}</div>
                                        <div><strong style="color: #495057;">ลูกค้า:</strong> ${deliveryItem.bill_customer_name}</div>
                                        <div><strong style="color: #495057;">รหัสสินค้า:</strong> <code style="background: #e9ecef; padding: 2px 6px; border-radius: 4px;">${deliveryItem.item_code}</code></div>
                                        <div><strong style="color: #495057;">จำนวน:</strong> <span style="color: #F0592E; font-weight: 600;">${deliveryItem.item_quantity} ${deliveryItem.item_unit}</span></div>
                                    </div>
                                    <div style="margin-top: 8px;">
                                        <strong style="color: #495057;">รายละเอียด:</strong> 
                                        <span style="color: #6c757d;">${deliveryItem.item_desc}</span>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-top: 8px; font-size: 0.85rem;">
                                        <div><strong style="color: #495057;">ราคา:</strong> <span style="color: #28a745;">฿${parseFloat(deliveryItem.item_price).toLocaleString()}</span></div>
                                        <div><strong style="color: #495057;">รวม:</strong> <span style="color: #F0592E; font-weight: 600;">฿${parseFloat(deliveryItem.line_total).toLocaleString()}</span></div>
                                        <div><strong style="color: #495057;">น้ำหนัก:</strong> ${deliveryItem.item_weight} กก.</div>
                                    </div>
                                </div>`;
                        });
                        
                        itemsHtml += `
                                </div>
                            </div>`;
                    }
                    
                    content += `
                        <div style="background: white; border-radius: 12px; margin-bottom: 15px; border: 1px solid #dee2e6; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <div style="background: linear-gradient(135deg, rgba(240, 89, 46, 0.1), rgba(255, 138, 101, 0.1)); padding: 15px; border-bottom: 1px solid #dee2e6;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <h6 style="margin: 0; color: #2d3748; font-size: 1.1rem;">
                                        <i class="bi bi-truck" style="color: #F0592E; margin-right: 8px;"></i>
                                        <strong>${item.delivery_number}</strong>
                                    </h6>
                                    <span style="background: ${statusColor}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                        ${statusText}
                                    </span>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; font-size: 0.9rem;">
                                    <div>
                                        <strong style="color: #495057;">จำนวนรายการ:</strong><br>
                                        <span style="background: rgba(240, 89, 46, 0.1); color: #F0592E; padding: 2px 8px; border-radius: 8px; font-weight: 600;">${item.item_count} รายการ</span>
                                    </div>
                                    <div>
                                        <strong style="color: #495057;">วันที่สร้าง:</strong><br>
                                        <span style="color: #6c757d;">${formatDate(item.delivery_date)}</span>
                                    </div>
                                    <div>
                                        <strong style="color: #495057;">ประเภทขนส่ง:</strong><br>
                                        <span style="background: rgba(33, 150, 243, 0.1); color: #2196F3; padding: 2px 8px; border-radius: 6px; font-weight: 500;">${item.transfer_type}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Timeline Section -->
                            <div style="padding: 15px; background: rgba(249, 249, 249, 0.5);">
                                <h6 style="color: #495057; margin-bottom: 15px; font-size: 1rem;">
                                    <i class="bi bi-clock-history"></i> Timeline การขนส่ง
                                </h6>
                                ${timelineHtml}
                            </div>
                            
                            <div style="padding: 12px 15px; border-top: 1px solid #dee2e6;">
                                <button 
                                    type="button" 
                                    class="btn btn-sm" 
                                    onclick="toggleDeliveryItems(${item.delivery_id})"
                                    style="background: linear-gradient(135deg, #F0592E, #FF8A65); color: white; border: none; border-radius: 6px; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; gap: 6px;"
                                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(240, 89, 46, 0.3)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                                >
                                    <i class="bi bi-chevron-down" id="icon-${item.delivery_id}"></i>
                                    <span id="text-${item.delivery_id}">ดูรายละเอียดสินค้า</span>
                                </button>
                            </div>
                            
                            ${itemsHtml}
                        </div>`;
                });
                
                content += `</div>`;
            } else {
                content = `
                    <div style="text-align: center; padding: 40px 20px; color: #718096;">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #adb5bd; margin-bottom: 15px; display: block;"></i>
                        <h5 style="color: #2d3748; margin-bottom: 8px;">ไม่พบข้อมูล</h5>
                        <p style="font-size: 0.9rem;">ไม่มีรายการการจัดส่งที่สามารถแสดงได้</p>
                    </div>
                `;
            }
            
            modalContent.innerHTML = content;
        }

        // Function to generate timeline HTML
        function generateTimelineHtml(item) {
            const steps = [
                {
                    id: 1,
                    title: 'รับคำสั่งซื้อ',
                    description: 'ระบบรับคำสั่งซื้อเข้าสู่ระบบ',
                    timestamp: item.delivery_step1_received,
                    icon: 'bi-clipboard-check',
                    color: '#007bff'
                },
                {
                    id: 2,
                    title: 'กำลังจัดส่งไปศูนย์',
                    description: 'สินค้าอยู่ระหว่างการขนส่งไปยังศูนย์กระจาย',
                    timestamp: item.delivery_step2_transit,
                    icon: 'bi-truck',
                    color: '#ffc107'
                },
                {
                    id: 3,
                    title: 'ถึงศูนย์กระจาย',
                    description: 'สินค้าถึงศูนย์กระจายสินค้าปลายทาง',
                    timestamp: item.delivery_step3_warehouse,
                    icon: 'bi-building',
                    color: '#6c757d'
                },
                {
                    id: 4,
                    title: 'กำลังส่งลูกค้า',
                    description: 'สินค้าอยู่ระหว่างการนำส่งให้ลูกค้า',
                    timestamp: item.delivery_step4_last_mile,
                    icon: 'bi-geo-alt',
                    color: '#6f42c1'
                },
                {
                    id: 5,
                    title: 'ส่งสำเร็จ',
                    description: 'สินค้าถึงลูกค้าเรียบร้อยแล้ว',
                    timestamp: item.delivery_step5_completed,
                    icon: 'bi-check-circle',
                    color: '#28a745'
                }
            ];

            let timelineHtml = '<div style="position: relative;">';
            
            steps.forEach((step, index) => {
                const isCompleted = step.timestamp && step.timestamp !== null;
                const isCurrent = parseInt(item.delivery_status) === step.id;
                const isProblem = parseInt(item.delivery_status) === 99;
                
                let stepStatus = '';
                let stepColor = '#e9ecef';
                let textColor = '#6c757d';
                let iconClass = 'bi-circle';
                
                if (isCompleted) {
                    stepStatus = 'completed';
                    stepColor = step.color;
                    textColor = '#2d3748';
                    iconClass = step.icon;
                } else if (isCurrent && !isProblem) {
                    stepStatus = 'current';
                    stepColor = step.color;
                    textColor = '#2d3748';
                    iconClass = step.icon;
                } else if (isProblem && isCompleted) {
                    stepStatus = 'problem';
                    stepColor = '#dc3545';
                    textColor = '#721c24';
                    iconClass = 'bi-exclamation-triangle';
                }
                
                timelineHtml += `
                    <div style="display: flex; align-items: flex-start; margin-bottom: ${index === steps.length - 1 ? '0' : '20px'}; position: relative;">
                        ${index < steps.length - 1 ? `
                            <div style="position: absolute; left: 19px; top: 40px; height: 20px; width: 2px; background: ${isCompleted ? stepColor : '#e9ecef'};"></div>
                        ` : ''}
                        
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: ${stepColor}; display: flex; align-items: center; justify-content: center; margin-right: 15px; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: relative; z-index: 1;">
                            <i class="${iconClass}" style="color: white; font-size: 16px;"></i>
                        </div>
                        
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                <h6 style="margin: 0; color: ${textColor}; font-size: 0.95rem; font-weight: 600;">
                                    ${step.title}
                                </h6>
                                ${isCompleted ? `
                                    <span style="background: rgba(40, 167, 69, 0.1); color: #28a745; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                        ${formatDate(step.timestamp)}
                                    </span>
                                ` : isCurrent ? `
                                    <span style="background: rgba(255, 193, 7, 0.1); color: #e0a800; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                        กำลังดำเนินการ
                                    </span>
                                ` : `
                                    <span style="color: #adb5bd; font-size: 0.8rem; font-style: italic;">
                                        รอดำเนินการ
                                    </span>
                                `}
                            </div>
                            <p style="margin: 0; color: #6c757d; font-size: 0.85rem; line-height: 1.4;">
                                ${step.description}
                            </p>
                        </div>
                    </div>
                `;
            });
            
            timelineHtml += '</div>';
            return timelineHtml;
        }

        // Function to toggle delivery items visibility
        function toggleDeliveryItems(deliveryId) {
            const itemsDiv = document.getElementById(`items-${deliveryId}`);
            const icon = document.getElementById(`icon-${deliveryId}`);
            const text = document.getElementById(`text-${deliveryId}`);
            
            if (itemsDiv.style.display === 'none') {
                // Show items
                itemsDiv.style.display = 'block';
                icon.className = 'bi bi-chevron-up';
                text.textContent = 'ซ่อนรายละเอียดสินค้า';
                
                // Add smooth animation
                itemsDiv.style.opacity = '0';
                itemsDiv.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    itemsDiv.style.transition = 'all 0.3s ease';
                    itemsDiv.style.opacity = '1';
                    itemsDiv.style.transform = 'translateY(0)';
                }, 10);
            } else {
                // Hide items
                itemsDiv.style.transition = 'all 0.3s ease';
                itemsDiv.style.opacity = '0';
                itemsDiv.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    itemsDiv.style.display = 'none';
                    icon.className = 'bi bi-chevron-down';
                    text.textContent = 'ดูรายละเอียดสินค้า';
                }, 300);
            }
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';
            
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }
    </script>
</body>

</html>