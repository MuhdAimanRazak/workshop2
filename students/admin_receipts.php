<?php
// admin_receipts.php
// Simple admin page: list files from uploads/receipts and allow approve/reject
// Place this file in your project root (e.g. htdocs/hostel_system/admin_receipts.php)
$uploadsDir = __DIR__ . '/uploads/receipts/';
$statusFile = __DIR__ . '/uploads/receipts/receipts_status.json';

// ensure uploads dir exists
if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);

// load statuses (filename => status)
$statuses = [];
if (file_exists($statusFile)) {
    $raw = file_get_contents($statusFile);
    $statuses = json_decode($raw, true) ?: [];
}

// get files
$files = array_values(array_filter(scandir($uploadsDir), function($f) use($uploadsDir,$statusFile){
    return !in_array($f, ['.','..','receipts_status.json']) && is_file($uploadsDir . $f);
}));

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin - View Receipts</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Bootstrap 5 CDN (optional, but recommended) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .thumb { width:120px; height:90px; object-fit:cover; border-radius:6px; }
    .receipt-card { cursor:pointer; }
    .status-badge { min-width:90px; display:inline-block; text-align:center; }
    .no-files { color:#777; }
  </style>
</head>
<body>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Admin — Uploaded Receipts</h3>
    <small class="text-muted">Folder: <code>uploads/receipts/</code></small>
  </div>

  <div class="row">
    <div class="col-lg-5">
      <div class="list-group" id="receiptList">
        <?php if(empty($files)): ?>
          <div class="p-4 no-files">Tiada fail. Minta pelajar upload satu resit dulu (gunakan upload_receipt.php).</div>
        <?php else: foreach($files as $f): 
            $url = 'uploads/receipts/' . rawurlencode($f);
            $status = isset($statuses[$f]) ? $statuses[$f] : 'pending';
            $badgeClass = $status === 'pending' ? 'bg-warning text-dark' : ($status === 'approved' ? 'bg-success' : 'bg-danger');
        ?>
          <a href="#" class="list-group-item list-group-item-action receipt-card d-flex align-items-center" data-filename="<?php echo htmlspecialchars($f); ?>" data-url="<?php echo htmlspecialchars($url); ?>" data-mime="<?php echo mime_content_type($uploadsDir . $f); ?>">
            <img src="<?php echo htmlspecialchars($url); ?>" class="thumb me-3" alt="thumb" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=...></svg>';">
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between">
                <div><strong><?php echo htmlspecialchars($f); ?></strong><br><small class="text-muted">Uploaded file</small></div>
                <div>
                  <span class="badge status-badge <?php echo $badgeClass; ?>"><?php echo strtoupper($status); ?></span>
                </div>
              </div>
              <div class="mt-1 text-muted small">Click untuk lihat / manage</div>
            </div>
          </a>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card" id="detailCard">
        <div class="card-body">
          <h5 id="detailTitle">Pilih satu resit dari sebelah kiri</h5>
          <p id="detailFilename" class="text-muted">—</p>
          <div id="previewArea" class="border rounded p-2 mb-3" style="min-height:240px; display:flex; align-items:center; justify-content:center;">
            <div class="text-muted">Preview akan muncul di sini</div>
          </div>

          <div id="metaArea" style="display:none;">
            <dl class="row">
              <dt class="col-sm-4">Filename</dt><dd class="col-sm-8" id="metaFilename"></dd>
              <dt class="col-sm-4">MIME</dt><dd class="col-sm-8" id="metaMime"></dd>
              <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span id="metaStatus" class="badge"></span></dd>
            </dl>

            <div class="d-flex gap-2">
              <button id="btnApprove" class="btn btn-success" disabled>Approve</button>
              <button id="btnReject" class="btn btn-outline-danger" disabled>Reject</button>
              <a id="btnDownload" class="btn btn-primary" href="#" target="_blank" download>Download</a>
            </div>

            <hr>
            <h6>Audit / Notes</h6>
            <div id="notesArea" class="small text-muted">Tiada nota.</div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal for larger preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-2" id="previewModalBody" style="min-height:60vh; display:flex; align-items:center; justify-content:center;">
        <!-- content injected -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <a id="modalDownload" class="btn btn-primary" href="#" target="_blank" rel="noopener">Download</a>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
  const list = document.getElementById('receiptList');
  const previewArea = document.getElementById('previewArea');
  const detailTitle = document.getElementById('detailTitle');
  const detailFilename = document.getElementById('detailFilename');
  const metaArea = document.getElementById('metaArea');
  const metaFilename = document.getElementById('metaFilename');
  const metaMime = document.getElementById('metaMime');
  const metaStatus = document.getElementById('metaStatus');
  const btnApprove = document.getElementById('btnApprove');
  const btnReject = document.getElementById('btnReject');
  const btnDownload = document.getElementById('btnDownload');
  const notesArea = document.getElementById('notesArea');

  let current = null;

  function clearDetail(){
    detailTitle.textContent = 'Pilih satu resit dari sebelah kiri';
    detailFilename.textContent = '—';
    previewArea.innerHTML = '<div class="text-muted">Preview akan muncul di sini</div>';
    metaArea.style.display = 'none';
    btnApprove.disabled = true;
    btnReject.disabled = true;
    btnDownload.href = '#';
    notesArea.textContent = 'Tiada nota.';
    current = null;
  }

  function showFile(fileEl){
    const filename = fileEl.dataset.filename;
    const url = fileEl.dataset.url;
    const mime = fileEl.dataset.mime;
    // status badge text from element
    const badgeEl = fileEl.querySelector('.status-badge');
    const statusText = badgeEl ? badgeEl.textContent.trim().toLowerCase() : 'pending';

    current = { filename, url, mime, status: statusText };

    detailTitle.textContent = 'Preview';
    detailFilename.textContent = filename;
    metaFilename.textContent = filename;
    metaMime.textContent = mime;
    metaStatus.className = 'badge ' + (statusText === 'pending' ? 'bg-warning text-dark' : (statusText === 'approved' ? 'bg-success' : 'bg-danger'));
    metaStatus.textContent = statusText.toUpperCase();
    metaArea.style.display = '';
    btnApprove.disabled = (statusText !== 'pending');
    btnReject.disabled = (statusText !== 'pending');
    btnDownload.href = url;
    notesArea.textContent = 'Tiada nota.';

    // render preview (image or pdf)
    previewArea.innerHTML = '';
    if(mime === 'application/pdf' || url.toLowerCase().endsWith('.pdf')){
      const embed = document.createElement('embed');
      embed.src = url;
      embed.type = 'application/pdf';
      embed.style.width = '100%';
      embed.style.height = '60vh';
      previewArea.appendChild(embed);
    } else {
      const img = document.createElement('img');
      img.src = url;
      img.alt = filename;
      img.style.maxHeight = '60vh';
      img.style.objectFit = 'contain';
      img.className = 'img-fluid';
      previewArea.appendChild(img);
    }
  }

  // click handler
  list.querySelectorAll('.receipt-card').forEach(el=>{
    el.addEventListener('click', function(e){
      e.preventDefault();
      list.querySelectorAll('.receipt-card').forEach(x=>x.classList.remove('active'));
      this.classList.add('active');
      showFile(this);
    });

    // open modal when click thumb image
    const img = el.querySelector('img');
    img && img.addEventListener('click', function(e){
      e.stopPropagation();
      // build preview in modal
      const url = el.dataset.url;
      const mime = el.dataset.mime;
      const body = document.getElementById('previewModalBody');
      body.innerHTML = '';
      if(mime === 'application/pdf' || url.toLowerCase().endsWith('.pdf')){
        const embed = document.createElement('embed');
        embed.src = url;
        embed.type = 'application/pdf';
        embed.style.width = '100%';
        embed.style.height = '80vh';
        body.appendChild(embed);
      } else {
        const imgLarge = document.createElement('img');
        imgLarge.src = url;
        imgLarge.className = 'img-fluid';
        imgLarge.style.maxHeight = '80vh';
        body.appendChild(imgLarge);
      }
      document.getElementById('modalDownload').href = url;
      const modal = new bootstrap.Modal(document.getElementById('previewModal'));
      modal.show();
    });
  });

  // approve/reject ajax (calls handler)
  btnApprove.addEventListener('click', function(){
    if(!current) return;
    if(!confirm('Confirm approve "'+current.filename+'"?')) return;
    fetch('receipt_status_handler.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ action: 'approve', filename: current.filename, note: 'Approved by admin' })
    }).then(r=>r.json()).then(j=>{
      if(j.ok){
        alert('Approved');
        location.reload();
      } else alert('Error: '+(j.error||'unknown'));
    }).catch(e=>alert('Network error'));
  });

  btnReject.addEventListener('click', function(){
    if(!current) return;
    const reason = prompt('Reason for rejection (will be saved):','Wrong account / insufficient amount');
    if(reason === null) return;
    fetch('receipt_status_handler.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ action: 'reject', filename: current.filename, note: reason })
    }).then(r=>r.json()).then(j=>{
      if(j.ok){
        alert('Rejected');
        location.reload();
      } else alert('Error: '+(j.error||'unknown'));
    }).catch(e=>alert('Network error'));
  });

  // initial clear
  clearDetail();
})();
</script>

</body>
</html>
