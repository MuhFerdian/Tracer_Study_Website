<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="{{ url("/admin/pertanyaan/" . $data->id . "/update_ajax") }}" method="POST" id="form-edit">
      @csrf
      @method("PUT")
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Pertanyaan</h5>
          <button type="button" class="btn-close" aria-label="Close" onclick="closeModalEdit()"></button>
        </div>
        <div class="modal-body">

          {{-- Kode Soal --}}
          <div class="mb-3">
            <label class="form-label">Kode Soal</label>
            <input type="text" name="kode_soal" id="kode_soal" class="form-control"
                   value="{{ $data->kode_soal ?? '' }}" placeholder="Contoh: f8, f502">
            <small id="error-kode_soal" class="text-danger"></small>
          </div>

          {{-- Pertanyaan --}}
          <div class="mb-3">
            <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
            <textarea name="question_text" id="question_text" class="form-control" rows="3" required
                      placeholder="Contoh: Jelaskan status Anda saat ini?">{{ $data->pertanyaan }}</textarea>
            <small id="error-question_text" class="text-danger"></small>
          </div>

          {{-- Hint --}}
          <div class="mb-3">
            <label class="form-label">Keterangan / Petunjuk <span class="text-muted">(opsional)</span></label>
            <textarea name="hint" id="hint" class="form-control" rows="2"
                      placeholder="Keterangan untuk membantu alumni memahami soal">{{ $data->hint ?? '' }}</textarea>
            <small class="form-text text-muted">Ditampilkan di bawah pertanyaan pada aplikasi mobile.</small>
            <small id="error-hint" class="text-danger"></small>
          </div>

          {{-- Tipe --}}
          <div class="mb-3">
            <label class="form-label">Tipe <span class="text-danger">*</span></label>
            <select name="type" id="type" class="form-select" required>
              <option value="">-- Pilih Tipe --</option>
              <option value="text"     {{ $data->type == 'text'     ? 'selected' : '' }}>Text (Input Teks)</option>
              <option value="single"   {{ $data->type == 'single'   ? 'selected' : '' }}>Single (Pilihan Tunggal)</option>
              <option value="multiple" {{ $data->type == 'multiple' ? 'selected' : '' }}>Multiple (Pilihan Ganda)</option>
              <option value="scale"    {{ $data->type == 'scale'    ? 'selected' : '' }}>Scale (Skala 1-5)</option>
              <option value="matrix"   {{ $data->type == 'matrix'   ? 'selected' : '' }}>Matrix (Tabel Sub-Items)</option>
            </select>
            <small id="error-type" class="text-danger"></small>
          </div>

          {{-- Options (single/multiple) - tampil langsung jika tipe sesuai --}}
          <div class="mb-3" id="optionsGroup"
               @if(!in_array($data->type, ['single','multiple'])) style="display:none;" @endif>
            <label class="form-label">Options <span class="text-danger">*</span></label>
            <textarea name="options" id="options" class="form-control" rows="3"
                      placeholder='["Opsi 1","Opsi 2","Opsi 3"]'
                      @if(in_array($data->type, ['single','multiple'])) required @endif
            >{{ $data->options->isNotEmpty() ? json_encode($data->options->pluck('label')->toArray()) : '' }}</textarea>
            <small class="form-text text-muted">Format JSON: ["Opsi 1","Opsi 2"] atau pisahkan dengan koma</small>
            <small id="error-options" class="text-danger"></small>
          </div>

          {{-- Scale info --}}
          <div class="mb-3" id="scaleInfo"
               @if($data->type !== 'scale') style="display:none;" @endif>
            <div class="alert alert-info mb-0">
              <strong>&#8505;&#65039; Scale:</strong> Otomatis menggunakan skala 1&ndash;5.
              <br><small>1 = Sangat Rendah &nbsp;&rarr;&nbsp; 5 = Sangat Tinggi</small>
            </div>
          </div>

          {{-- Matrix items --}}
          <div class="mb-3" id="matrixItemsGroup"
               @if($data->type !== 'matrix') style="display:none;" @endif>
            <label class="form-label">Matrix Items <span class="text-danger">*</span></label>
            <div id="matrixItemsList" class="border rounded p-3" style="max-height:300px;overflow-y:auto;">
              @if($data->type === 'matrix')
                @foreach($data->details as $detail)
                  <div class="input-group mb-2" id="mi_blade_{{ $loop->index }}">
                    <input type="text" class="form-control matrix-item-input"
                           placeholder="Contoh: Etika" value="{{ $detail->item_label }}">
                    <button type="button" class="btn btn-danger btn-sm"
                            onclick="$(this).closest('.input-group').remove()">Hapus</button>
                  </div>
                @endforeach
              @endif
            </div>
            <button type="button" class="btn btn-sm btn-success mt-2" onclick="addMatrixItemRow()">+ Tambah Item</button>
            <small class="form-text text-muted d-block mt-1">Contoh: "Etika", "Bahasa Inggris", dll.</small>
            <small id="error-matrix_items" class="text-danger"></small>
          </div>

          {{-- Tipe Data (hanya untuk text) --}}
          <div class="mb-3" id="tipeDataGroup"
               @if($data->type !== 'text') style="display:none;" @endif>
            <label class="form-label">Tipe Data Input</label>
            <select name="tipe_data" id="tipe_data" class="form-select">
              <option value="text"   {{ ($data->tipe_data ?? 'text') == 'text'   ? 'selected' : '' }}>Text (default)</option>
              <option value="number" {{ ($data->tipe_data ?? '')     == 'number' ? 'selected' : '' }}>Number (angka)</option>
              <option value="date"   {{ ($data->tipe_data ?? '')     == 'date'   ? 'selected' : '' }}>Date (tanggal)</option>
              <option value="year"   {{ ($data->tipe_data ?? '')     == 'year'   ? 'selected' : '' }}>Year (tahun)</option>
            </select>
            <small class="form-text text-muted">Hint input di mobile. Hanya berlaku untuk tipe Text.</small>
            <small id="error-tipe_data" class="text-danger"></small>
          </div>

          {{-- Urutan --}}
          <div class="mb-3">
            <label class="form-label">Urutan Pertanyaan</label>
            <input type="number" name="urutan" id="urutan" class="form-control" min="1"
                   value="{{ $data->urutan ?: '' }}" placeholder="Kosongkan untuk otomatis">
            <small class="form-text text-muted">Angka unik. Kosongkan untuk otomatis.</small>
            <small id="error-urutan" class="text-danger"></small>
            <small id="warning-urutan" class="text-warning" style="display:none;"></small>
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
// =============================================
// TOGGLE FIELD BERDASARKAN TIPE (saat user ganti tipe)
// =============================================
function toggleOptionsField() {
  const type = $('#type').val();

  $('#optionsGroup').hide();
  $('#scaleInfo').hide();
  $('#matrixItemsGroup').hide();
  $('#tipeDataGroup').hide();
  $('#options').removeAttr('required');

  if (type === 'text') {
    $('#tipeDataGroup').show();

  } else if (type === 'single' || type === 'multiple') {
    $('#optionsGroup').show();
    $('#options').attr('required', true);

  } else if (type === 'scale') {
    $('#scaleInfo').show();

  } else if (type === 'matrix') {
    $('#matrixItemsGroup').show();
    if ($('#matrixItemsList').children().length === 0) {
      addMatrixItemRow();
    }
  }
}

// =============================================
// MATRIX ITEM ROWS
// =============================================
function addMatrixItemRow(label = '') {
  const id = 'mi_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
  $('#matrixItemsList').append(
    '<div class="input-group mb-2" id="' + id + '">' +
      '<input type="text" class="form-control matrix-item-input" placeholder="Contoh: Etika" value="' + label + '">' +
      '<button type="button" class="btn btn-danger btn-sm" onclick="$(\'#' + id + '\').remove()">Hapus</button>' +
    '</div>'
  );
}

function getMatrixItems() {
  const items = [];
  $('#matrixItemsList .matrix-item-input').each(function(i) {
    const v = $(this).val().trim();
    if (v) items.push({ label: v, urutan: i + 1 });
  });
  return items;
}

// =============================================
// VALIDASI
// =============================================
function validateEditForm() {
  const type = $('#type').val();
  $('.text-danger').text('');
  $('.form-control, .form-select').removeClass('is-invalid');
  $('input[name="matrix_items"]').remove();

  if (!type) {
    $('#error-type').text('Tipe wajib dipilih');
    return false;
  }

  if (type === 'single' || type === 'multiple') {
    const opts = $('#options').val().trim();
    if (!opts) {
      $('#error-options').text('Options wajib diisi');
      $('#options').addClass('is-invalid');
      return false;
    }
    const parsed = parseOpts(opts);
    if (parsed.length < 2) {
      $('#error-options').text('Minimal 2 opsi');
      $('#options').addClass('is-invalid');
      return false;
    }
  }

  if (type === 'matrix') {
    const items = getMatrixItems();
    if (items.length === 0) {
      $('#error-matrix_items').text('Minimal 1 item matrix');
      return false;
    }
    $('<input>').attr({ type: 'hidden', name: 'matrix_items', value: JSON.stringify(items) })
                .appendTo('#form-edit');
  }

  return true;
}

function parseOpts(input) {
  try {
    const d = JSON.parse(input);
    if (Array.isArray(d)) return d.filter(x => x.trim() !== '');
  } catch(e) {}
  return input.split(',').map(x => x.trim()).filter(x => x !== '');
}

// =============================================
// URUTAN CHECK
// =============================================
async function checkUrutanEdit(urutan) {
  if (!urutan || urutan == 0) return true;
  const res = await $.ajax({
    url: "{{ url('/admin/pertanyaan/check-urutan') }}",
    data: { urutan, excludeId: "{{ $data->id }}" }
  });
  return res.available;
}

// =============================================
// CLOSE MODAL
// =============================================
function closeModalEdit() {
  $('#modalEdit').modal('hide');
  setTimeout(() => { $('#modalEdit').remove(); $('.modal-backdrop').remove(); }, 300);
}

// =============================================
// INIT
// =============================================
$(function () {
  $('#type').on('change', function () {
    toggleOptionsField();
    $('#error-type').text('');
    $(this).removeClass('is-invalid');
  });

  $('#urutan').on('blur', async function () {
    const val = $(this).val();
    $('#warning-urutan').hide().text('');
    if (val && val > 0) {
      const ok = await checkUrutanEdit(val);
      if (!ok) {
        $('#warning-urutan').text('Urutan ' + val + ' sudah digunakan.').show();
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    }
  });

  $('#form-edit').on('submit', function (e) {
    e.preventDefault();
    if (!validateEditForm()) return;
    if ($('#urutan').hasClass('is-invalid')) return;

    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (res) {
        if (res.status) {
          closeModalEdit();
          Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message });
          $('#tablePertanyaan').DataTable().ajax.reload(null, false);
        } else {
          if (res.msgField) {
            $.each(res.msgField, function (field, msg) {
              $('#error-' + field).text(msg[0]);
              $('#' + field).addClass('is-invalid');
            });
          }
          Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
        }
      },
      error: function () {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memperbarui data.' });
      }
    });
  });
});
</script>
