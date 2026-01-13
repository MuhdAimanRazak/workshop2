<?php
include("../page/header.php");
?>

<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Building Selection</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Choose Building</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-building me-1"></i>
                Select Building
            </div>

            <div class="card-body">

                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="building" class="form-label">Choose Building:</label>
                        <select name="building" id="building" class="form-select" required>
                            <option value="" selected disabled>-- Select Building --</option>

                            <option value="lestari_men">Lestari (Man)</option>
                            <option value="lestari_women">Lestari (Woman)</option>
                            <option value="kasturi">Kasturi</option>
                            <option value="jebat">Jebat</option>
                            <option value="tuah">Tuah</option>
                            <option value="lekir">Likir</option>
                            <option value="lekiu">Lekiu</option>

                            <option value="aljazari_a">Al-Jazari Blok A</option>
                            <option value="aljazari_b">Al-Jazari Blok B</option>
                            <option value="aljazari_c">Al-Jazari Blok C</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Proceed</button>
                </form>

            </div>
        </div>
    </div>
</main>

<?php
include("../page/footer.php");
?>
