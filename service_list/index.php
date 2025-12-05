<?php
require_once "../../connection.php";
session_start();
include "../sidebar.php";

// ========================================
// CEK LOGIN
// ========================================
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// ========================================
// AMBIL DATA USER
// ========================================
$id = $_SESSION['user_id'];
$query = mysqli_query($connection, "SELECT * FROM account WHERE id='$id'");
$user = mysqli_fetch_assoc($query);

// ========================================
// AMBIL RIWAYAT ORDER USER
// ========================================
$order_query = mysqli_query($connection, "
    SELECT 
        id,
        order_code,
        service_type,
        weight,
        total_amount,
        status,
        payment_status,
        received_date,
        due_date,
        va_id,
        created_at
    FROM orders 
    WHERE account_id='$id'
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Service List - Laundryin</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            background: #f5f7ff;
            min-height: 100vh;
            color: #333;
        }

        .main {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .top-bar h1 {
            font-size: 28px;
            font-weight: 700;
            color: #007bff;
        }

        .user-info span {
            font-size: 16px;
            color: #555;
        }

        /* Service Grid */
        .service-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .service-card {
            background: white;
            border-radius: 15px;
            padding: 25px 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .service-card:hover {
            transform: translateY(-5px);
            border-color: #007bff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .service-card i {
            font-size: 40px;
            color: #007bff;
            margin-bottom: 15px;
        }

        .service-card h4 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #333;
        }

        .service-card p {
            font-size: 13px;
            color: #777;
        }

        .price {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            background: #eaf4ff;
            color: #007bff;
            font-weight: 600;
            border-radius: 12px;
            font-size: 13px;
        }

        /* TABEL RIWAYAT */
        .order-section h2 {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background: #007bff;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        tr:hover {
            background: #f8faff;
        }

        /* BADGE STATUS */
        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-block;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.process {
            background: #cce5ff;
            color: #004085;
        }

        .status-badge.done {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.cancel {
            background: #f8d7da;
            color: #721c24;
        }

        /* PAYMENT */
        .unpaid {
            background: #f8d7da;
            color: #721c24;
        }

        .paid {
            background: #d1ecf1;
            color: #0c5460;
        }

        .clickable-badge:hover {
            opacity: 0.75;
            cursor: pointer;
        }

        /* ACTION BUTTONS */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            border: none;
            padding: 8px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }

        .detail-btn {
            background: #17a2b8;
            color: white;
        }

        .edit-btn {
            background: #ffc107;
            color: white;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .detail-btn:hover {
            background: #138496;
        }

        .edit-btn:hover {
            background: #e0a800;
        }

        .delete-btn:hover {
            background: #c82333;
        }
    </style>
</head>

<body>

    <div class="main">

        <div class="top-bar">
            <h1>Choose Your Laundry Service</h1>
            <div class="user-info">
                <span>👋 Hi, <?= htmlspecialchars($user['nama']); ?></span>
            </div>
        </div>

        <!-- SERVICE LIST -->
        <div class="service-container">
            <div class="service-card" onclick="chooseService('baju', 8000)">
                <i class="fa-solid fa-shirt"></i>
                <h4>Baju</h4>
                <p>Cuci, setrika, dan lipat rapi.</p>
                <span class="price">Rp8.000 / Kg</span>
            </div>

            <div class="service-card" onclick="chooseService('celana', 9000)">
                <i class="fa-solid fa-user"></i>
                <h4>Celana</h4>
                <p>Perawatan khusus untuk celana favoritmu.</p>
                <span class="price">Rp9.000 / Kg</span>
            </div>

            <div class="service-card" onclick="chooseService('jaket', 12000)">
                <i class="fa-solid fa-vest"></i>
                <h4>Jaket</h4>
                <p>Cuci bersih tanpa merusak bahan.</p>
                <span class="price">Rp12.000 / Kg</span>
            </div>

            <div class="service-card" onclick="chooseService('selimut', 10000)">
                <i class="fa-solid fa-bed"></i>
                <h4>Selimut</h4>
                <p>Segar, bersih, dan bebas tungau.</p>
                <span class="price">Rp10.000 / Kg</span>
            </div>

            <div class="service-card" onclick="chooseService('sepatu', 15000, 'pasang')">
                <i class="fa-solid fa-shoe-prints"></i>
                <h4>Sepatu</h4>
                <p>Pembersihan profesional tanpa merusak bentuk.</p>
                <span class="price">Rp15.000 / Pasang</span>
            </div>

            <div class="service-card" onclick="chooseService('karpet', 20000)">
                <i class="fa-solid fa-rug"></i>
                <h4>Karpet</h4>
                <p>Cuci karpet dengan mesin khusus.</p>
                <span class="price">Rp20.000 / Kg</span>
            </div>
        </div>

        <!-- ORDER HISTORY -->
        <div class="order-section">
            <h2><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Order Anda</h2>

            <?php if (mysqli_num_rows($order_query) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Kode Order</th>
                            <th>Service</th>
                            <th>Berat</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($order_query)): ?>
                            <tr>
                                <td><?= $row['order_code']; ?></td>
                                <td><?= $row['service_type']; ?></td>
                                <td><?= $row['weight']; ?> Kg</td>

                                <td>Rp<?= number_format($row['total_amount'], 0, ',', '.'); ?></td>

                                <td><span class="status-badge <?= $row['status']; ?>"><?= $row['status']; ?></span></td>

                                <td>
                                    <?php if ($row['payment_status'] === 'unpaid'): ?>
                                        <a href="../payment/pay.php?id=<?= $row['va_id']; ?>&order=<?= $row['id']; ?>"
                                           class="status-badge unpaid clickable-badge"
                                           style="text-decoration:none;">unpaid</a>
                                    <?php else: ?>
                                        <span class="status-badge paid">paid</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= $row['created_at']; ?></td>

                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn detail-btn" data-id="<?= $row['id']; ?>"><i class="fa-solid fa-eye"></i></button>
                                        <button class="action-btn edit-btn" data-id="<?= $row['id']; ?>"><i class="fa-solid fa-pen"></i></button>
                                        <button class="action-btn delete-btn" data-id="<?= $row['id']; ?>"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <p style="color:#777;">Belum ada order yang dibuat.</p>
            <?php endif; ?>
        </div>

    </div>

    <script>
        // POPUP SELECT SERVICE
        function chooseService(service, price, unit = 'kg') {
            Swal.fire({
                title: 'Pilih Jenis Service',
                html: `
                    <p>Kamu memilih <b>${service.toUpperCase()}</b> - Rp${price.toLocaleString()} / ${unit.toUpperCase()}</p>
                    <select id="serviceTypeSelect" class="swal2-select" style="margin-top:15px;">
                        <option value="">-- Pilih Jenis Service --</option>
                        <option value="kiloan">Kiloan</option>
                        <option value="satuan">Satuan</option>
                        <option value="dry_clean">Dry Clean</option>
                        <option value="express">Express</option>
                    </select>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan Order',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#007bff',
                preConfirm: () => {
                    const type = document.getElementById('serviceTypeSelect').value;
                    if (!type) {
                        Swal.showValidationMessage('Pilih jenis service dulu!');
                        return false;
                    }
                    return type;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const type = result.value;
                    window.location.href = `insert.php?service=${service}&price=${price}&type=${type}&unit=${unit}`;
                }
            });
        }

        // ACTION BUTTON HANDLER
        document.querySelectorAll('.detail-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                window.location.href = '../details_order/index.php?id=' + this.dataset.id;
            });
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                window.location.href = '../list_order/update.php?id=' + this.dataset.id;
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Hapus Order?',
                    text: "Yakin ingin menghapus order ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '../list_order/delete_order.php?id=' + this.dataset.id;
                    }
                })
            });
        });
    </script>

</body>
</html>
