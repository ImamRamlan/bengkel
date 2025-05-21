<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link" style="display: flex; align-items: center; justify-content: center; background-color: #343a40; color: #ffffff; padding: 10px; text-decoration: none; border-radius: 5px;">
        <span class="brand-text font-weight-light" style="font-size: 20px;">Bengkel Rinus</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php
        // Include database connection
        include 'koneksi.php';

        // Ambil data pegawai berdasarkan username yang disimpan di session
        $username = $_SESSION['username'] ?? '';
        $role = $_SESSION['role'] ?? '';

        if ($username) {
            $stmt = $koneksi->prepare("SELECT nama_lengkap, role FROM pegawai WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                // Cek apakah data ditemukan
                if ($result && $result->num_rows > 0) {
                    $pegawai = $result->fetch_assoc();
                    $namaLengkap = htmlspecialchars($pegawai['nama_lengkap']);
                    $role = htmlspecialchars($pegawai['role']);
                } else {
                    $namaLengkap = "User";
                    $role = "Guest";
                }

                $stmt->close();
            } else {
                echo "Error preparing statement: " . $koneksi->error;
                $namaLengkap = "User";
                $role = "Guest";
            }
        } else {
            $namaLengkap = "User";
            $role = "Guest"; // Default role jika tidak ada session
        }

        // Function to set 'active' class on the current menu item
        function setActive($page)
        {
            return basename($_SERVER['PHP_SELF']) == $page ? 'active' : '';
        }
        ?>

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo $namaLengkap; ?></a>
                <small class="d-block text-muted"><?php echo htmlspecialchars($role); ?></small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo setActive('index.php'); ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="data_pegawai.php" class="nav-link <?php echo setActive('data_pegawai.php'); ?>">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Data Pegawai</p>
                    </a>
                </li>
                <!-- Menu Data Pemasok -->
                <li class="nav-item">
                    <a href="data_pemasok.php" class="nav-link <?php echo setActive('data_pemasok.php'); ?>">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Data Pemasok</p>
                    </a>
                </li>

                <!-- Menu Data Suku Cadang -->
                <li class="nav-item">
                    <a href="data_suku_cadang.php" class="nav-link <?php echo setActive('data_suku_cadang.php'); ?>">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Data Suku Cadang</p>
                    </a>
                </li>

                <!-- Menu Data Pembelian -->


                <li class="nav-header">LAINNYA</li>
                <!-- Menu Generate Laporan -->
                <li class="nav-item">
                    <a href="data_pembelian.php" class="nav-link <?php echo setActive('data_pembelian.php'); ?>">
                        <i class="nav-icon fas fa-wrench"></i>
                        <p>Data Pembelian</p>
                    </a>
                </li>

                <!-- Menu Data Penjualan -->
                <li class="nav-item">
                    <a href="data_penjualan.php" class="nav-link <?php echo setActive('data_penjualan.php'); ?>">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Data Penjualan</p>
                    </a>
                </li>

                <!-- Menu Logout -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link" data-toggle="modal" data-target="#logoutModal">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>