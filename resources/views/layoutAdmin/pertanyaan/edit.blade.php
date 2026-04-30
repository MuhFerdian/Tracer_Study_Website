<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form action="{{ url('/admin/pertanyaan/' . $data->pertanyaan_id . '/update_ajax') }}" method="POST" id="form-edit">
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
            <select name="kode_soal" id="kode_soal" class="form-control" required>
              <option value="">-- Pilih Kode Soal --</option>
              <option value="f8" {{ $data->kode_soal == 'f8' ? 'selected' : '' }}>f8 - Status Alumni</option>
              <option value="f502" {{ $data->kode_soal == 'f502' ? 'selected' : '' }}>f502 - Nama Perusahaan</option>
              <option value="f505" {{ $data->kode_soal == 'f505' ? 'selected' : '' }}>f505 - Gaji</option>
              <option value="f510" {{ $data->kode_soal == 'f510' ? 'selected' : '' }}>f510 - Skill</option>
            </select>
            <small id="error-kode_soal" class="error-text form-text text-danger"></small>
          </div>

          <div class="mb-3">
            <label for="question_text" class="form-label">Pertanyaan</label>
            <textarea name="question_text" id="question_text" class="form-control" rows="3" required>{{ $data->question_text }}</textarea>
            <small id="error-question_text" class="error-text form-text text-danger"></small>
          </div>

          <div class="mb-3">
            <label for="type" class="form-label">Tipe</label>
            <select name="type" id="type" class="form-control" required>
              <option value="text" {{ $data->type == 'text' ? 'selected' : '' }}>Text</option>
              <option value="radio" {{ $data->type == 'radio' ? 'selected' : '' }}>Radio</option>
              <option value="checkbox" {{ $data->type == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
              <option value="number" {{ $data->type == 'number' ? 'selected' : '' }}>Number</option>
            </select>
            <small id="error-type" class="error-text form-text text-danger"></small>
          </div>

          <div class="mb-3">
            <label for="options" class="form-label">Format penulisan (untuk radio/checkbox)</label>
            <textarea name="options" id="options" class="form-control" placeholder='["Bekerja","Belum bekerja","Wirausaha"]'>{{ !empty($data->options) ? json_encode($data->options) : '' }}</textarea>
            <small id="error-options" class="error-text form-text text-danger"></small>
          </div>

          <div class="mb-3">
            <label for="urutan" class="form-label">Urutan pertanyaan</label>
            <input type="number" name="urutan" id="urutan" class="form-control" min="0" value="{{ $data->urutan ?? 0 }}">
            <small id="error-urutan" class="error-text form-text text-danger"></small>
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
  function closeModalEdit() {
    $('#modalEdit').modal('hide');
    setTimeout(function () {
      $('#modalEdit').remove(); // hapus dari DOM
      $('.modal-backdrop').remove();
    }, 300);
  }

  $(function () {
    $('#form-edit').validate({
      rules: {
        question_text: {
          required: true,
          minlength: 5,
          maxlength: 255
        },
        kode_soal: { required: true },
        type: { required: true }
      },
      submitHandler: function (form, event) {
        event.preventDefault();

        $.ajax({
          url: form.action,
          method: $(form).attr('method'),
          data: $(form).serialize(),
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
              $.each(response.msgField, function (field, msg) {
                $('#error-' + field).text(msg[0]);
                $('#' + field).addClass('is-invalid');
              });
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
      },
      errorElement: 'span',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function (element) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element) {
        $(element).removeClass('is-invalid');
      }
    });
  });
  
</script>
