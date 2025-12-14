


<?php 
include("../page/header.php");
?>

<main>

<style>

/* --- PAGE WRAPPER (Grey section) --- */
.page-wrapper {
    max-width: 1100px; /* SAME WIDTH AS FOOTER */
    margin: 0 auto;
    padding-top: 1.5rem;
}

/* --- BACK BUTTON --- */
.back-btn {
    display: inline-block;
    margin-bottom: 1.2rem;
    background: #eef2ff;
    padding: 0.45rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    color: #3f3dff;
    text-decoration: none;
    border: 1px solid #cdd0ff;
    transition: 0.2s;
}
.back-btn:hover {
    background: #dfe3ff;
}

/* --- WHITE FORM BOX --- */
.form-box {
    max-width: 1050px; /* WIDER LIKE FOOTER */
    margin: 0 auto 2.5rem auto;
    background: #ffffff;
    padding: 2rem 2.5rem;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.04);
}

/* --- HEADER: Logo + Title --- */
.header-flex {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.6rem;
}

.header-logo img {
    width: 150px;
    height: auto;
}

.header-title {
    flex: 1;
    text-align: center;
}

.header-title h2 {
    margin: 0;
    font-size: 1rem;
    font-weight: 800;
}

.header-title p {
    margin: 4px 0 0 0;
    font-size: 0.86rem;
    color: #444;
}

/* --- STUDENT INFO GRID --- */
.header-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem 2rem;
    margin-top: 0.6rem;
}

.label-bold {
    font-weight: 800;
    color:#15406a;
    font-size: 0.85rem;
}

.info-value {
    margin-bottom: 10px;
    color:#111;
}

/* --- TABLE STYLING --- */
.item-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
    font-size: 0.95rem;
}

.item-table th, .item-table td {
    padding: 0.6rem 0;
    border-bottom: 1px dashed #dcdcdc;
}

.item-table th {
    font-weight: 800;
    font-size: 0.9rem;
    color: #222;
}

.center { text-align:center; }

.checkmark {
    color:#6b46ff;
    font-weight:900;
}

.qty-text {
    display:inline-block;
    min-width:30px;
    text-align:center;
}

/* --- BRAND --- */
.brand {
    color:#333;
}

/* --- NOTE --- */
.note {
    margin-top: 1rem;
    padding-top: 0.8rem;
    border-top: 1px dashed #ccc;
    font-size: 0.85rem;
    color:#444;
}

</style>


<!-- ========== PAGE WRAPPER (controls center & width) ========== -->
<div class="page-wrapper">

    <!-- BACK BUTTON (on the grey area, top-left) -->
    <a href="student.php" class="back-btn">← Back</a>

    <!-- WHITE CONTENT BOX -->
    <div class="form-box">

        <!-- Header with logo + centered title -->
        <div class="header-flex">

            <div class="header-logo">
                <img src="../utem-logo.png" alt="UTeM Logo">
            </div>

            <div class="header-title">
                <h2>BORANG PENDAFTARAN PERALATAN ELEKTRIK PERSENDIRIAN PELAJAR</h2>
                <p>Kolej Kediaman UTeM — Pejabat Hal Ehwal Pelajar</p>
            </div>

            <div style="width:80px; visibility:hidden;"></div>
        </div>

        <!-- Student Info -->
        <div class="header-grid">

            <div>
                <div class="label-bold">Nama</div>
                <div class="info-value">Nurain Farahin Syazmin</div>

                <div class="label-bold">Tahun / Sesi</div>
                <div class="info-value">2025 / 2</div>

                <div class="label-bold">No. Telefon</div>
                <div class="info-value">0149288458</div>
            </div>

            <div>
                <div class="label-bold">No. Matrik</div>
                <div class="info-value">B032310690</div>

                <div class="label-bold">Bangunan / Bilik</div>
                <div class="info-value">LEKIU, SATRIA — SQ-12-4B</div>

                <div class="label-bold">Email</div>
                <div class="info-value">nurain.farahin@example.com</div>
            </div>
        </div>

        <h4 style="margin:10px 0;">Senarai Peralatan:</h4>

        <!-- Equipment Table -->
        <table class="item-table">
            <tr>
                <th>Alatan</th>
                <th class="center">✔</th>
                <th class="center">Kuantiti</th>
                <th>Jenama</th>
            </tr>

            <tr><td>Komputer (Desktop)</td><td class="center">—</td><td class="center">—</td><td class="brand">Dell</td></tr>
            <tr><td>Laptop</td><td class="center"><span class="checkmark">✔</span></td><td class="center">1</td><td class="brand">HP Pavilion</td></tr>
            <tr><td>Printer</td><td class="center">—</td><td class="center">—</td><td class="brand">Canon Pixma</td></tr>
            <tr><td>Scanner</td><td class="center">—</td><td class="center">—</td><td class="brand">Epson ScanMate</td></tr>
            <tr><td>Pengecas Bateri</td><td class="center">—</td><td class="center">—</td><td class="brand">Panasonic</td></tr>
            <tr><td>Kipas Meja (RM5)</td><td class="center"><span class="checkmark">✔</span></td><td class="center">1</td><td class="brand">Khind</td></tr>
            <tr><td>Lampu Belajar (RM5)</td><td class="center">—</td><td class="center">—</td><td class="brand">Philips LED</td></tr>
            <tr><td>Cerek / Jug Elektrik (RM5)</td><td class="center"><span class="checkmark">✔</span></td><td class="center">1</td><td class="brand">Pensonic</td></tr>
            <tr><td>Pengering Rambut (RM10)</td><td class="center">—</td><td class="center">—</td><td class="brand">Panasonic Ionity</td></tr>
            <tr><td>Peti Ais Mini (RM10)</td><td class="center">—</td><td class="center">—</td><td class="brand">Hisense MiniCool</td></tr>
        </table>

        <div class="note">
            <strong>Nota:</strong> Peralatan di atas adalah digunakan di kolej kediaman sahaja mengikut peraturan.
        </div>

    </div><!-- end form box -->

</div><!-- end page wrapper -->

</main>

<?php include("../page/footer.php"); ?>
