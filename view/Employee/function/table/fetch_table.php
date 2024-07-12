<?php
                // Calculate total unique bill numbers
                $total_bills_query = "SELECT COUNT(DISTINCT bill_number) as total FROM tb_header WHERE bill_status = 1";
                $total_bills_result = $conn->query($total_bills_query);
                $total_bills = $total_bills_result->fetch_assoc()['total'];

                $bills_per_page = 30;
                $total_pages = ceil($total_bills / $bills_per_page);
                $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

                $offset = ($current_page - 1) * $bills_per_page;

                // Your SQL query
                $sql = "SELECT DISTINCT tb_header.bill_number, tb_header.bill_customer_name,  tb_header.bill_weight,
                                    tb_line.item_code, tb_line.item_desc, tb_line.item_quantity, 
                                    tb_line.item_unit, tb_line.item_price, tb_line.line_total, tb_line.item_sequence
                                    FROM tb_header
                                    INNER JOIN tb_line ON TRIM(tb_header.bill_number) = TRIM(tb_line.line_bill_number)
                                    WHERE tb_header.bill_status = 1 AND tb_line.line_status = 1
                                    LIMIT $bills_per_page OFFSET $offset";

                $result = $conn->query($sql);

                $merged_rows = [];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $bill_number = $row["bill_number"];
                        if (!isset($merged_rows[$bill_number])) {
                            $merged_rows[$bill_number] = [
                                "bill_number" => $bill_number,
                                "bill_customer_name" => $row["bill_customer_name"],
                                "bill_weight" => $row["bill_weight"],
                                "item_details" => []
                            ];
                        }
                        // Add item details to the item_details array
                        $merged_rows[$bill_number]["item_details"][] = [
                            "item_sequence" => $row["item_sequence"],
                            "item_code" => $row["item_code"],
                            "item_desc" => $row["item_desc"],
                            "item_quantity" => $row["item_quantity"],
                            "item_unit" => $row["item_unit"],
                            "item_price" => $row["item_price"],
                            "line_total" => $row["line_total"],
                        ];
                    }
                }
                ?>