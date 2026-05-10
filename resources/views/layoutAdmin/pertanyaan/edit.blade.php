<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="{{ url('/admin/pertanyaan/' . $data->id . '/update_ajax') }}" method="POST" id="form-edit">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Pertanyaan</h5>
          <button type="button" class="btn-close" aria-label="Close" onclick="closeModalEdit()"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="kode_soal" class="form-label">Kode Soal</label>
            <input type="text" name="kode_soal" id="kode_soal" class="form-control" value="{{ $data->kode_soal ?? '' }}" placeholder="Contoh: f8, f502, f1761/f1762">
          <small id="error-kode_soal" class="text-danger"></small>
          </div>

          <div class="mb-3">
            <label for="question_text" class="form-label">Pertanyaan</label>
            <textarea name="question_text" id="question_text" class="form-control" placeholder='Jelaskan status Anda saat ini?' rows="3" required>{{ $data->pertanyaan }}</textarea>
            <small id="error-question_text" class="error-text form-text text-danger"></small>
          </div>

          <div class="mb-3">
            <label for="type" class="form-label">Tipe</label>
            <select name="type" id="type" class="form-control" required>
              <option value="">-- Pilih Tipe --</option>
              <option value="text" {{ $data->type == 'text' ? 'selected' : '' }}>Text (Input Teks)</option>
              <option value="single" {{ $data->type == 'single' ? 'selected' : '' }}>Single (Pilihan Tunggal)</option>
              <option value="multiple" {{ $data->type == 'multiple' ? 'selected' : '' }}>Multiple (Pilihan Ganda)</option>
              <option value="scale" {{ $data->type == 'scale' ? 'selected' : '' }}>Scale (Skala 1-5)</option>
              <option value="matrix" {{ $data->type == 'matrix' ? 'selected' : '' }}>Matrix (Tabel dengan Sub-Items)</option>
            </select>
            <small id="error-type" class="error-text form-text text-danger"></small>
          </div>

          <div class="mb-3" id="optionsGroup" style="display: none;">
            <label for="options" class="form-label">Options <span class="text-danger" id="optionsRequired">*</span></label>
            <textarea name="options" id="options" class="form-control" placeholder='["Bekerja","Wiraswasta","Melanjutkan Pendidikan"]' data-required="false">{{ !empty($data->options) && $data->options->isNotEmpty() ? json_encode($data->options->pluck('label')->toArray()) : '' }}</textarea>
            <small class="form-text text-muted">Format: ["Opsi 1","Opsi 2","Opsi 3"]</small>
            <small id="error-options" class="error-text form-text text-danger"></small>
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
            <small id="error-matrix_items" class="error-text form-text text-danger"></small>
          </div>
          <div class="mb-3">
            <label for="urutan" class="form-label">Urutan pertanyaan</label>
            <input type="number" name="urutan" id="urutan" class="form-control" min="1" value="{{ $data->urutan ?? 1 }}">
            <small class="form-text text-muted">Isi dengan angka unik. Kosongkan atau 1 untuk otomatis.</small>
            <small id="error-urutan" class="error-text form-text text-danger"></small>
            <small id="warning-urutan" class="text-warning" style="display: none;"></small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeModalEdit()">Batal</button>
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
    const optionsField = $('#options');
    const optionsRequired = $('#optionsRequired');
    const selectedType = typeSelect.val();

    // Tipe yang memerlukan options
    const typesWithOptions = ['single', 'multiple'];

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
        // Load dari details jika ada
        const details = {{ json_encode($data->details->pluck('item_label')->toArray() ?? []) }};
        if (details.length > 0) {
          details.forEach(label => addMatrixItemRow(label));
        } else {
          addMatrixItemRow();
        }
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
      const hiddenInput = '<input type="hidden" name="matrix_items" value="' + JSON.stringify(matrixItems).replace(/"/g, '&quot;') + '">';
      $('form#form-edit').append(hiddenInput);
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

  function closeModalEdit() {
    $('#modalEdit').modal('hide');
    setTimeout(function () {
      $('#modalEdit').remove(); // hapus dari DOM
      $('.modal-backdrop').remove();
    }, 300);
  }

  $(function () {
    // Setup change listener saat modal dibuka
    setupTypeChangeListener();
    // Trigger untuk set state awal sesuai type yang sudah dipilih
    toggleOptionsField();

    // Validasi urutan saat blur
    $('#urutan').on('blur', async function () {
      const urutan = $(this).val();
      const questionId = "{{ $data->id }}";
      const warningEl = $('#warning-urutan');
      const errorEl = $('#error-urutan');
      
      warningEl.hide().text('');
      errorEl.text('');
      $(this).removeClass('is-invalid');

      if (urutan && urutan != 0) {
        const response = await checkUrutanAvailability(urutan, questionId);
        if (!response.available) {
          warningEl.text('⚠️ Urutan ' + urutan + ' sudah digunakan. Silakan gunakan urutan yang berbeda.').show();
          $(this).addClass('is-invalid');
        }
      }
    });

    $('#form-edit').on('submit', function (e) {
      e.preventDefault();

      // Clear previous errors
      $('.error-text').text('');
      $('.form-control').removeClass('is-invalid');

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
        url: $(this).attr('action'),
        method: $(this).attr('method'),
        data: $(this).serialize(),
        dataType: 'json',
        success: function (response) {
          if (response.status) {
            closeModalEdit();
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message
            });
            $('#tablePertanyaan').DataTable().ajax.reload(null, false);
          } else {
            $('.error-text').text('');
            $('.form-control').removeClass('is-invalid');
            if (response.msgField) {
              $.each(response.msgField, function (field, msg) {
                $('#error-' + field).text(msg[0]);
                $('#' + field).addClass('is-invalid');
              });
            }
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: response.message
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: 'error',
            title: 'Kesalahan Server',
            text: 'Gagal memperbarui data. Silakan coba lagi.'
          });
        }
      });

      return false;
    });

    // Initialize form display based on current type
    setTimeout(() => {
      toggleOptionsField();
    }, 100);
  });
</script>
