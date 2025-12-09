<?php
include("../page/header.php");
?>

<main>
    <style>
  
.edit-page {
    max-width: 1100px; /* increased width to match footer */
    margin: 3.25rem auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.06);
    padding: 2rem; /* slightly increased for better spacing */
}

.edit-page h3 {
    margin-bottom: 0.75rem;
    font-weight: 700;
}

.edit-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 0.6rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    font-size: 0.92rem;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 0.5rem 0.65rem;
    border-radius: 8px;
    border: 1px solid #e1e6f3;
    font-size: 0.95rem;
}

.full-row { grid-column: 1 / -1; }

.btn-row {
    display:flex;
    gap: .6rem;
    justify-content: flex-end;
    margin-top: .9rem;
}

.btn-row .btn {
    border-radius: 999px;
}

/* small responsive */
@media (max-width: 640px) {
    .edit-row {
        grid-template-columns: 1fr;
    }
    .btn-row {
        justify-content: center;
    }
}
    </style>

    <div class="container">
        <div class="edit-page">

            <?php
            // Optional: get id from query string for later DB usage
            // $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            // For now we use static demo values (no DB).
            ?>

            <h3>Edit Student Profile</h3>
            <p style="color:#666; margin-bottom:1rem;">(UI only — Save button disabled)</p>

            <form id="editStudentForm" onsubmit="return false;">
                <div class="edit-row">

                    <!-- LEFT COLUMN (top -> down) -->
                    <div class="form-group">
                        <label>Matric Number</label>
                        <input type="text" id="matric" value="B032310690" />
                    </div>

                    <!-- RIGHT COLUMN: Gender -->
                    <div class="form-group">
                        <label>Gender</label>
                        <select id="gender">
                            <option value="Female" selected>Female</option>
                            <option value="Male">Male</option>
                        </select>
                    </div>

                    <!-- LEFT: Phone Number -->
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" id="phone" value="0149288458" />
                    </div>

                    <!-- RIGHT: IC Number -->
                    <div class="form-group">
                        <label>IC Number</label>
                        <input type="text" id="ic" value="990101-01-1234" />
                    </div>

                    <!-- LEFT: Emergency Contact Relation -->
                    <div class="form-group">
                        <label>Emergency Contact Relation</label>
                        <input type="text" id="emergency_rel" value="Mother" />
                    </div>

                    <!-- RIGHT: Guardian's Number -->
                    <div class="form-group">
                        <label>Guardian's Number</label>
                        <input type="text" id="guardian" value="012345678" />
                    </div>

                    <!-- LEFT: Building -->
                    <div class="form-group">
                        <label>Building</label>
                        <select id="building">
                            <option value="" data-gender="both" selected>Select Building</option>

                            <!-- SATRIA -->
                            <optgroup label="Satria">
                                <option value="Satria - Lekiu" data-gender="female">Satria - Lekiu</option>
                                <option value="Satria - Lekir" data-gender="female">Satria - Lekir</option>
                                <option value="Satria - Tuah" data-gender="male">Satria - Tuah</option>
                                <option value="Satria - Jebat" data-gender="male">Satria - Jebat</option>
                                <option value="Satria - Kasturi" data-gender="male">Satria - Kasturi</option>
                            </optgroup>

                            <!-- LESTARI -->
                            <optgroup label="Lestari">
                                <option value="Lestari - Block A (Female)" data-gender="female">Lestari - Block A (Female)</option>
                                <option value="Lestari - Block B (Male)" data-gender="male">Lestari - Block B (Male)</option>
                            </optgroup>

                            <!-- AL-JAZARI -->
                            <optgroup label="Al-Jazari">
                                <option value="Al-Jazari - Block A" data-gender="male">Al-Jazari - Block A</option>
                                <option value="Al-Jazari - Block B" data-gender="male">Al-Jazari - Block B</option>
                                <option value="Al-Jazari - Block C" data-gender="male">Al-Jazari - Block C</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- RIGHT: Room -->
                    <div class="form-group">
                        <label>Room</label>
                        <input type="text" id="room" value="SQ-12-4B" />
                    </div>

                    <!-- LEFT: Programme -->
                    <div class="form-group">
                        <label>Programme</label>
                        <select id="programme">
                            <optgroup label="FTMK – Fakulti Teknologi Maklumat & Komunikasi">
                                <option>Bachelor of Computer Science (Artificial Intelligence)</option>
                                <option>Bachelor of Computer Science (Data Engineering)</option>
                                <option>Bachelor of Computer Science (Software Development)</option>
                                <option>Bachelor of Computer Science (Cybersecurity)</option>
                                <option>Bachelor of Information Technology (Information Security)</option>
                                <option>Bachelor of Information Technology (Software Engineering)</option>
                                <option>Diploma in Computer Science</option>
                                <option>Diploma in Information Technology</option>
                            </optgroup>

                            <optgroup label="FKE – Fakulti Kejuruteraan Elektrik">
                                <option>Bachelor of Electrical Engineering</option>
                                <option>Bachelor of Electronics Engineering</option>
                                <option>Bachelor of Mechatronics Engineering</option>
                                <option>Diploma in Electrical Engineering</option>
                                <option>Diploma in Electronics Engineering</option>
                            </optgroup>

                            <optgroup label="FKM – Fakulti Kejuruteraan Mekanikal">
                                <option>Bachelor of Mechanical Engineering</option>
                                <option>Bachelor of Manufacturing Engineering</option>
                                <option>Bachelor of Industrial Engineering</option>
                                <option>Bachelor of Automotive Engineering</option>
                                <option>Diploma in Mechanical Engineering</option>
                                <option>Diploma in Industrial Engineering</option>
                            </optgroup>

                            <optgroup label="FKP – Fakulti Kejuruteraan Pembuatan">
                                <option>Bachelor of Manufacturing Engineering</option>
                                <option>Bachelor of Product Design Engineering</option>
                                <option>Bachelor of Materials Engineering</option>
                                <option>Diploma in Manufacturing Engineering</option>
                            </optgroup>

                            <optgroup label="FKEKK – Fakulti Kejuruteraan Elektronik & Kejuruteraan Komputer">
                                <option>Bachelor of Computer Engineering</option>
                                <option>Bachelor of Electronics Engineering (Computer Engineering)</option>
                                <option>Bachelor of Telecommunication Engineering</option>
                                <option>Diploma in Computer Engineering</option>
                                <option>Diploma in Electronics Engineering</option>
                            </optgroup>

                            <optgroup label="FTKMP – Fakulti Kejuruteraan Teknologi">
                                <option>Bachelor of Technology in Automotive Technology</option>
                                <option>Bachelor of Technology in Manufacturing Systems</option>
                                <option>Bachelor of Technology in Welding</option>
                                <option>Bachelor of Technology in Industrial Automation</option>
                                <option>Bachelor of Technology in Robotics & Automation</option>
                                <option>Diploma in Technology (Automotive / Electronic / Manufacturing)</option>
                            </optgroup>

                            <optgroup label="FPTT – Fakulti Pengurusan Teknologi & Teknousahawanan">
                                <option>Bachelor of Technology Management</option>
                                <option>Bachelor of Technology Entrepreneurship</option>
                                <option>Diploma in Technology Management</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- RIGHT: Year / Semester -->
                    <div class="form-group">
                        <label>Year / Semester</label>
                        <select id="year_semester">
                            <option selected disabled>Select Year & Semester</option>

                            <optgroup label="Year 1">
                                <option>Year 1 / Semester 1</option>
                                <option>Year 1 / Semester 2</option>
                            </optgroup>

                            <optgroup label="Year 2">
                                <option>Year 2 / Semester 3</option>
                                <option>Year 2 / Semester 4</option>
                            </optgroup>

                            <optgroup label="Year 3">
                                <option>Year 3 / Semester 5</option>
                                <option>Year 3 / Semester 6</option>
                            </optgroup>

                            <optgroup label="Year 4">
                                <option>Year 4 / Semester 7</option>
                                <option>Year 4 / Semester 8</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- RIGHT (bottom): Email -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="email" value="nurain.farahin@example.com" />
                    </div>

                </div> <!-- end edit-row -->

                <div class="btn-row">
                    <button type="button" class="btn btn-outline-secondary btn-disabled">Cancel</button>
                    <button type="button" class="btn btn-success btn-disabled">Save (disabled)</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Filter building options depending on selected gender.
        (function() {
            const genderSelect = document.getElementById('gender');
            const buildingSelect = document.getElementById('building');

            function applyBuildingFilter() {
                const gender = genderSelect.value; // "Female" or "Male"
                const opts = Array.from(buildingSelect.querySelectorAll('option'));
                let currentValue = buildingSelect.value;

                // Show/hide options based on data-gender attribute.
                opts.forEach(opt => {
                    // always allow placeholder (data-gender="both") to remain visible
                    const allowed = opt.dataset.gender === 'both' || opt.dataset.gender === gender.toLowerCase();
                    opt.hidden = !allowed;
                    opt.disabled = !allowed;
                });

                // If current value is not allowed, reset to placeholder
                const currentOpt = buildingSelect.querySelector('option[value="' + currentValue + '"]');
                if (!currentOpt || currentOpt.hidden) {
                    buildingSelect.value = "";
                }
            }

            // run on load
            document.addEventListener('DOMContentLoaded', applyBuildingFilter);
            // run when gender changes
            genderSelect.addEventListener('change', applyBuildingFilter);
        })();
    </script>

</main>

<?php
include("../page/footer.php");
?>
