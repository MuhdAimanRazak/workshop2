<?php
include("../page/header.php");
?>

<style>
    /* ===============================
   FORCE FULL WHITE BACKGROUND
================================ */
html, body {
    background: #ffffff !important;
    margin: 0;
    padding: 0;
}
/* ===============================
   PAGE LAYOUT
================================ */
.add-student-wrapper {
    padding: 3rem;
}

.add-student-white {
    background: #ffffff;
    border-radius: 28px;
    padding: 5.5rem 4rem;
    max-width: 1100px;
    margin: 0 auto;
    box-shadow: 0 18px 45px rgba(0,0,0,.15);
    text-align: center;
}

/* ===============================
   TITLE
================================ */
.add-student-title {
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 3.5rem;
    color: #2a2a8c;
}

/* ===============================
   CSV UPLOAD AREA
================================ */
.csv-upload-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
}

/* Hide ugly file input */
.csv-upload-box input[type="file"] {
    display: none;
}

/* Fake button */
.csv-select-btn {
    background: #8894ff;
    color: #fff;
    border-radius: 14px;
    padding: 1.2rem 3.5rem;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s ease;
}

.csv-select-btn:hover {
    background: #6f7cff;
}

.csv-hint {
    font-size: .9rem;
    color: #555;
}

/* ===============================
   DOWNLOAD TEMPLATE
================================ */
.template-text {
    margin-top: 3.5rem;
    font-size: .9rem;
    color: #555;
}

.template-btn {
    display: inline-block;
    margin-top: .75rem;
    background: #8894ff;
    color: #fff;
    padding: .6rem 1.6rem;
    border-radius: 10px;
    font-size: .9rem;
    text-decoration: none;
}

.template-btn:hover {
    background: #6f7cff;
}

/* ===============================
   LOADING
================================ */
#loadingBox {
    display: none;
    margin-top: 1.5rem;
    font-weight: 600;
    color: #555;
}

.spinner {
    width: 18px;
    height: 18px;
    border: 3px solid #ddd;
    border-top: 3px solid #4f7cff;
    border-radius: 50%;
    display: inline-block;
    vertical-align: middle;
    animation: spin .8s linear infinite;
}

@keyframes spin {
    100% { transform: rotate(360deg); }
}
</style>

<main>
<div class="add-student-wrapper">
<div class="add-student-white">

    <!-- BACK -->
    <a href="add_student_choice.php" class="back-link" style="float:left;">← Back to Add Choice</a>
    <div style="clear:both;"></div>

    <div class="add-student-title">
        Import Student via CSV
    </div>

    <!-- FORM -->
    <form class="csv-form"
          action="student_import_process.php"
          method="post"
          enctype="multipart/form-data"
          id="csvForm">

        <!-- UPLOAD STATE -->
        <div class="csv-upload-box" id="uploadBox">

            <label for="csv_file" class="csv-select-btn">
                Select CSV file
            </label>

            <input type="file" name="csv_file" id="csv_file" accept=".csv" required>

            <div class="csv-hint">
                or drop your file here
            </div>

        </div>

        <!-- PREVIEW STATE -->
        <div class="csv-upload-box" id="previewBox" style="display:none;">

            <div style="
                background:#f4f6ff;
                padding:1rem 1.5rem;
                border-radius:14px;
                font-weight:600;
                margin-bottom:1.5rem;
            ">
                📄 <span id="fileName"></span>
            </div>

            <div style="display:flex;justify-content:center;gap:1rem;">
                <button type="submit" class="template-btn">
                    Import CSV
                </button>

                <button type="button"
                        onclick="resetUpload()"
                        style="
                            width:42px;
                            height:42px;
                            border-radius:50%;
                            border:none;
                            font-size:1.3rem;
                            cursor:pointer;
                        ">
                    +
                </button>
            </div>

            <!-- LOADING -->
            <div id="loadingBox">
                <span class="spinner"></span>
                <span style="margin-left:.5rem;">Importing, please wait…</span>
            </div>

        </div>

    </form>

    <!-- TEMPLATE DOWNLOAD -->
    <div class="template-text">
        Download student csv template here
    </div>

    <a href="student_template.php" class="template-btn">
        Download template here
    </a>

</div>
</div>
</main>

<script>
const fileInput  = document.getElementById("csv_file");
const uploadBox  = document.getElementById("uploadBox");
const previewBox = document.getElementById("previewBox");
const fileName   = document.getElementById("fileName");

fileInput.addEventListener("change", function () {
    if (this.files.length > 0) {
        fileName.innerText = this.files[0].name;
        uploadBox.style.display = "none";
        previewBox.style.display = "flex";
    }
});

function resetUpload() {
    fileInput.value = "";
    previewBox.style.display = "none";
    uploadBox.style.display = "flex";
}

document.getElementById("csvForm").addEventListener("submit", function () {
    document.getElementById("loadingBox").style.display = "block";
});
</script>

<?php
include("../page/footer.php");
?>
