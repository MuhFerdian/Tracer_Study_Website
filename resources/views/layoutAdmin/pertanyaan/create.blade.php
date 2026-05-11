<div class="modal fade" id="modalCreate" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="formCreatePertanyaan">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="createModalLabel">Tambah Pertanyaan</h5>
            <button type="button" class="btn-close" aria-label="Close" onclick="closeModalCreate()"></button>
        </div>

        <div class="modal-body">
            <div class="mb-3">
        <label for="kode_soal" class="form-label">Kode Soal</label>
        <input type="text" name="kode_soal" id="kode_soal" class="form-control" placeholder="Contoh: f8, f502, f1761/f1762">
        <small id="error-kode_soal" class="text-danger"></small>
      </div>

      <div class="mb-3">
        <label for="question_text" class="form-label">Pertanyaan</label>
        <textarea class="form-control" id="question_text" name="question_text" placeholder='Contoh: Jelaskan status Anda saat ini?' rows="3" required></textarea>
        <small id="error-question_text" class="text-danger"></small>
      </div>

      <div class="mb-3">
        <label for="hint" class="form-label">Keterangan / Petunjuk <span class="text-muted">(opsional)</span></label>
        <textarea class="form-control" id="hint" name="hint" rows="2" placeholder="Contoh: Pilih satu status yang paling sesuai kondisi Anda saat ini setelah lulus."></textarea>
        <small class="form-text text-muted">Keterangan ini akan ditampilkan di bawah pertanyaan pada aplikasi mobile untuk membantu alumni memahami soal.</small>
        <small id="error-hint" class="text-danger"></small>
      </div>

      <div class="mb-3">
        <label for="type" class="form-label">Tipe</label>
        <select name="type" id="type" class="form-control" required>
          <option value="">-- Pilih Tipe --</option>
          <option value="text">Text (Input Teks)</option>
          <option value="single">Single (Pilihan Tunggal)</option>
          <option value="multiple">Multiple (Pilihan Ganda)</option>
          <option value="scale">Scale (Skala 1-5)</option>
          <option value="matrix">Matrix (Tabel dengan Sub-Items)</option>
        </select>
        <small id="error-type" class="text-danger"></small>
      </div>

      <div class="mb-3" id="optionsGroup" style="display: none;">
        <label for="options" class="form-label">Options <span class="text-danger" id="optionsRequired" style="display: none;">*</span></label>
        <textarea name="options" id="options" class="form-control" placeholder='["Bekerja","Wiraswasta","Melanjutkan Pendidikan"]' data-required="false"></textarea>
        <small class="form-text text-muted">Format: ["Opsi 1","Opsi 2","Opsi 3"]</small>
        <small id="error-options" class="text-danger"></small>
      </div>

      <div class="mb-3" id="scaleInfo" style="display: none;">
        <div class="alert alert-info" role="alert">
          <strong>ℹ️ Informasi Skala:</strong> Tipe Scale akan secara otomatis menggunakan skala <strong>1 hingga 5</strong>. Anda tidak perlu menambahkan options.
          <br>
          <small class="text-muted">Skala: 1 (Sangat Rendah) → 5 (Sangat Tinggi)</small>
        </div>
      </div>

      <div class="mb-3" id="matrixItemsGroup" style="display: none;">
        <label class="form-label">Matrix Items <span class="text-danger">*</span></label>
        <div id="matrixItemsList" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
          <!-- Matrix items akan ditambahkan di sini via JavaScript -->
        </div>
        <button type="button" class="btn btn-sm btn-success mt-2" id="addMatrixItemBtn" onclick="addMatrixItemRow()">+ Tambah Item</button>
        <small class="form-text text-muted d-block mt-2">Masukkan setiap sub-pertanyaan (item) untuk matrix. Contoh: "Etika", "Bahasa Inggris", dll</small>
        <small id="error-matrix_items" class="text-danger"></small>
      </div>

      <div class="mb-3" id="tipeDataGroup" style="display: none;">
        <label for="tipe_data" class="form-label">Tipe Data Input</label>
        <select name="tipe_data" id="tipe_data" class="form-control">
          <option value="text">Text (default)</option>
          <option value="number">Number (angka)</option>
          <option value="date">Date (tanggal)</option>
          <option value="year">Year (tahun)</option>
        </select>
        <small class="form-text text-muted">Hanya berlaku untuk tipe pertanyaan <strong>Text</strong>. Digunakan sebagai hint input di mobile.</small>
        <small id="error-tipe_data" class="text-danger"></small>
      </div>

      <div class="mb-3">
        <label for="urutan" class="form-label">Urutan pertanyaan</label>
        <input type="number" name="urutan" id="urutan" class="form-control" value="1" min="1">
        <small class="form-text text-muted">Isi dengan angka unik. Kosongkan atau 1 untuk otomatis.</small>
        <small id="error-urutan" class="text-danger"></small>
        <small id="warning-urutan" class="text-warning" style="display: none;"></small>
      </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModalCreate()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  // Function untuk cek ketersediaan urutan via AJAX
  function checkUrutanAvailability(urutan, excludeId = null) {
    return new Promise((resolve) => {
      if (!urutan || urutan == 0) {
        resolve({ available: true });
        return;
      }

      $.ajax({
        url: "{{ url('/admin/pertanyaan/check-urutan') }}",
        method: "GET",
        data: {
          urutan: urutan,
          excludeId: excludeId
        },
        dataType: "json",
        success: function (response) {
          resolve(response);
        },
        error: function () {
          resolve({ available: true });
        }
      });
    });
  }

  // Function untuk toggle visibility dan required status field options
  function toggleOptionsField() {
    const typeSelect = $('#type');
    const optionsGroup = $('#optionsGroup');
    const scaleInfo = $('#scaleInfo');
    const matrixItemsGroup = $('#matrixItemsGroup');
    const tipeDataGroup = $('#tipeDataGroup');
    const optionsField = $('#options');
    const optionsRequired = $('#optionsRequired');
    const selectedType = typeSelect.val();

    // Tipe yang memerlukan options
    const typesWithOptions = ['single', 'multiple'];

    // tipe_data hanya relevan untuk text
    if (selectedType === 'text') {
      tipeDataGroup.slideDown(300);
    } else {
      tipeDataGroup.slideUp(300);
    }

    if (typesWithOptions.includes(selectedType)) {
      // Tampilkan field options dan set sebagai required
      optionsGroup.slideDown(300);
      scaleInfo.slideUp(300);
      matrixItemsGroup.slideUp(300);
      optionsField.attr('required', 'required');
      optionsField.data('required', 'true');
      optionsRequired.show();
      optionsField.addClass('is-options-required');
    } else if (selectedType === 'scale') {
      // Untuk scale, tampilkan info dan hide options
      optionsGroup.slideUp(300);
      scaleInfo.slideDown(300);
      matrixItemsGroup.slideUp(300);
      optionsField.removeAttr('required');
      optionsField.data('required', 'false');
      optionsRequired.hide();
      optionsField.removeClass('is-options-required');
      optionsField.val('');
    } else if (selectedType === 'matrix') {
      // Untuk matrix, tampilkan matrix items dan hide options
      optionsGroup.slideUp(300);
      scaleInfo.slideUp(300);
      matrixItemsGroup.slideDown(300);
      optionsField.removeAttr('required');
      optionsField.data('required', 'false');
      optionsRequired.hide();
      optionsField.removeClass('is-options-required');
      optionsField.val('');
      // Initialize matrix items jika belum ada
      if ($('#matrixItemsList').children().length === 0) {
        addMatrixItemRow();
      }
    } else {
      // Untuk text, sembunyikan keduanya
      optionsGroup.slideUp(300);
      scaleInfo.slideUp(300);
      matrixItemsGroup.slideUp(300);
      optionsField.removeAttr('required');
      optionsField.data('required', 'false');
      optionsRequired.hide();
      optionsField.removeClass('is-options-required');
      optionsField.val('');
    }
  }

  // Function untuk tambah matrix item row
  function addMatrixItemRow(label = '') {
    const itemsList = $('#matrixItemsList');
    const itemCount = itemsList.children().length;
    const itemId = 'matrixItem_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    
    const html = `
      <div class="input-group mb-2" id="${itemId}">
        <input type="text" class="form-control matrix-item-input" placeholder="Contoh: Etika, Bahasa Inggris" value="${label}">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeMatrixItemRow('${itemId}')">Hapus</button>
      </div>
    `;
    
    itemsList.append(html);
  }

  // Function untuk hapus matrix item row
  function removeMatrixItemRow(itemId) {
    $(`#${itemId}`).remove();
  }

  // Function untuk get matrix items
  function getMatrixItems() {
    const items = [];
    $('#matrixItemsList .matrix-item-input').each(function(idx) {
      const label = $(this).val().trim();
      if (label) {
        items.push({
          label: label,
          urutan: idx + 1
        });
      }
    });
    return items;
  }

  // Event listener untuk perubahan type
  function setupTypeChangeListener() {
    $('#type').on('change', function () {
      toggleOptionsField();
      // Bersihkan error message jika ada
      $('#error-type').text('');
      $('#type').removeClass('is-invalid');
    });
  }

  // Validasi options sebelum submit
  function validateOptions() {
    const type = $('#type').val();
    const typesWithOptions = ['single', 'multiple'];

    if (typesWithOptions.includes(type)) {
      const options = $('#options').val().trim();
      if (!options) {
        $('#error-options').text('Options wajib diisi untuk tipe ' + type);
        $('#options').addClass('is-invalid');
        return false;
      }

      // Validasi format options
      const parsed = parseOptionsFormat(options);
      if (parsed.length < 2) {
        $('#error-options').text('Minimal harus ada 2 opsi untuk tipe ' + type);
        $('#options').addClass('is-invalid');
        return false;
      }
    }

    // Validasi matrix items
    if (type === 'matrix') {
      const matrixItems = getMatrixItems();
      if (matrixItems.length === 0) {
        $('#error-matrix_items').text('Minimal harus ada 1 item untuk tipe matrix');
        return false;
      }
      
      // Store matrix items ke hidden field untuk dikirim
      $('input[name="matrix_items"]').remove();
      $('form#formCreatePertanyaan').append(
        '<input type="hidden" name="matrix_items" value=\'' + JSON.stringify(matrixItems) + '\'>'
      );
    }

    return true;
  }

  // Function untuk parse options dari berbagai format
  function parseOptionsFormat(optionsInput) {
    try {
      const decoded = JSON.parse(optionsInput);
      if (Array.isArray(decoded)) {
        return decoded.filter(item => item.trim() !== '');
      }
    } catch (e) {
      // Bukan JSON, coba split dengan koma
      return optionsInput
        .split(',')
        .map(item => item.trim())
        .filter(item => item !== '');
    }
  }

  function closeModalCreate() {
    $('#modalCreate').modal('hide');
    setTimeout(function () {
      $('#modalCreate').remove();
      $('.modal-backdrop').remove();
    }, 300);
  }

  $(function () {
    // Setup change listener saat modal dibuka
    setupTypeChangeListener();

    // Validasi urutan saat blur
    $('#urutan').on('blur', async function () {
      const urutan = $(this).val();
      const warningEl = $('#warning-urutan');
      const errorEl = $('#error-urutan');
      
      warningEl.hide().text('');
      errorEl.text('');
      $(this).removeClass('is-invalid');

      if (urutan && urutan != 0) {
        const response = await checkUrutanAvailability(urutan);
        if (!response.available) {
          warningEl.text('⚠️ Urutan ' + urutan + ' sudah digunakan. Silakan gunakan urutan yang berbeda.').show();
          $(this).addClass('is-invalid');
        }
      }
    });

    $('#formCreatePertanyaan').on('submit', function (e) {
      e.preventDefault();

      // Clear previous errors
      $('#formCreatePertanyaan .text-danger').text('');
      $('#formCreatePertanyaan .form-control').removeClass('is-invalid');

      // Validasi options terlebih dahulu
      if (!validateOptions()) {
        return;
      }

      // Validasi urutan
      const urutan = $('#urutan').val();
      if (urutan && urutan != 0 && $('#urutan').hasClass('is-invalid')) {
        return;
      }

      $.ajax({
        url: "{{ url('/admin/pertanyaan/store') }}",
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (response) {
          if (response.status) {
            closeModalCreate();
            Swal.fire('Sukses', response.message, 'success');
            $('#tablePertanyaan').DataTable().ajax.reload(null, false);
          } else {
            Swal.fire('Gagal', response.message, 'error');
            if (response.msgField) {
              $.each(response.msgField, function (field, messages) {
                $('#error-' + field).text(messages[0]);
                $('#' + field).addClass('is-invalid');
              });
            }
          }
        },
        error: function () {
          Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error');
        }
      });
    });
  });
</script>
