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
        <select name="kode_soal" id="kode_soal" class="form-control" required>
          <option value="">-- Pilih Kode Soal --</option>
          <option value="f8">f8 - Status Alumni</option>
          <option value="f502">f502 - Nama Perusahaan</option>
          <option value="f505">f505 - Gaji</option>
          <option value="f510">f510 - Skill</option>
        </select>
        <small id="error-kode_soal" class="text-danger"></small>
      </div>

      <div class="mb-3">
        <label for="question_text" class="form-label">Pertanyaan</label>
        <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
        <small id="error-question_text" class="text-danger"></small>
      </div>

      <div class="mb-3">
        <label for="type" class="form-label">Tipe</label>
        <select name="type" id="type" class="form-control" required>
          <option value="text">Text</option>
          <option value="radio">Radio</option>
          <option value="checkbox">Checkbox</option>
          <option value="number">Number</option>
        </select>
        <small id="error-type" class="text-danger"></small>
      </div>

      <div class="mb-3">
        <label for="options" class="form-label">Format penulisan (untuk radio/checkbox)</label>
        <textarea name="options" id="options" class="form-control" placeholder='["Bekerja","Belum bekerja","Wirausaha"]'></textarea>
        <small id="error-options" class="text-danger"></small>
      </div>

      <div class="mb-3">
        <label for="urutan" class="form-label">Urutan pertanyaan</label>
        <input type="number" name="urutan" id="urutan" class="form-control" value="0" min="0">
        <small id="error-urutan" class="text-danger"></small>
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
  function closeModalCreate() {
    $('#modalCreate').modal('hide');
    setTimeout(function () {
      $('#modalCreate').remove();
      $('.modal-backdrop').remove();
    }, 300);
  }

  $(function () {
    $('#formCreatePertanyaan').on('submit', function (e) {
      e.preventDefault();

      $('#formCreatePertanyaan .text-danger').text('');
      $('#formCreatePertanyaan .form-control').removeClass('is-invalid');

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
