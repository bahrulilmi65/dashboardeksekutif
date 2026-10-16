<!-- ============================================================
     FOOTER
============================================================ -->
</div><!-- /.page-wrapper -->

<footer class="main-footer">
    <div class="footer-content">
        <span>&copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong>. Dibuat untuk keperluan akademik.</span>
        <span class="footer-version">v<?= APP_VERSION ?></span>
    </div>
</footer>

</div><!-- /.app-wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- jsPDF + AutoTable (untuk export PDF) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.1/jspdf.plugin.autotable.min.js"></script>

<script>
// ============================================================
// Global Scripts
// ============================================================

// Tampilkan tanggal hari ini
function updateDate() {
    const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months = ['Januari','Februari','Maret','April','Mei','Juni',
                    'Juli','Agustus','September','Oktober','November','Desember'];
    const now    = new Date();
    const str    = days[now.getDay()] + ', ' + now.getDate() + ' ' +
                   months[now.getMonth()] + ' ' + now.getFullYear();
    const el     = document.getElementById('currentDate');
    if (el) el.textContent = str;
}
updateDate();

// Toggle sidebar
function toggleSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const appWrap  = document.getElementById('appWrapper');
    sidebar.classList.toggle('sidebar-collapsed');
    appWrap.classList.toggle('sidebar-collapsed');
    if (window.innerWidth < 992) {
        sidebar.classList.toggle('sidebar-open');
        overlay.classList.toggle('show');
    }
}
function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.remove('sidebar-open');
    overlay.classList.remove('show');
}

// Konfirmasi logout
function confirmLogout() {
    Swal.fire({
        title: 'Keluar dari Aplikasi?',
        text: 'Anda akan diarahkan ke halaman login.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-sign-out-alt me-1"></i> Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= BASE_URL ?>/controllers/AuthController.php?action=logout';
        }
    });
}

// Konfirmasi hapus
function confirmDelete(url, nama) {
    Swal.fire({
        title: 'Hapus Data?',
        html: 'Data <strong>' + nama + '</strong> akan dihapus permanen dan tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// Init DataTable default
function initDataTable(selector, options) {
    const defaults = {
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        responsive: true,
        pageLength: 10,
        dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6 text-end"f>>rtip',
    };
    return $(selector).DataTable(Object.assign(defaults, options || {}));
}

// Auto-dismiss flash alerts
document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
    setTimeout(function() {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        if (bsAlert) bsAlert.close();
    }, 5000);
});
</script>

<?php if (isset($extraScript)) echo $extraScript; ?>
</body>
</html>
