<div class="modal-header">
    <h5 class="modal-title">Edit Lowongan</h5>
</div>

<form id="formEdit">
    @csrf
    @method('PUT')

    <div class="modal-body">
        <input type="text" name="posisi" value="{{ $loker->posisi }}" class="form-control mb-2" required>
        <input type="text" name="nama_perusahaan" value="{{ $loker->nama_perusahaan }}" class="form-control mb-2" required>
        <input type="text" name="lokasi" value="{{ $loker->lokasi }}" class="form-control mb-2">
        <input type="text" name="gaji" value="{{ $loker->gaji }}" class="form-control mb-2">

        <textarea name="deskripsi" class="form-control mb-2">{{ $loker->deskripsi }}</textarea>

        <!-- 🔥 FIX TANGGAL -->
        <input type="date" name="batas_lamaran" id="batas_lamaran"
            value="{{ $loker->batas_lamaran }}" class="form-control mb-2">

        <!-- 🔥 FIX KONTAK -->
        <input type="text" name="kontak" id="kontak"
            value="{{ $loker->kontak }}"
            class="form-control mb-2"
            placeholder="Contoh: 08123456789"
            pattern="08[0-9]{8,12}"
            required>

        <input type="text" name="link_lamaran" value="{{ $loker->link_lamaran }}" class="form-control mb-2">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary">Update</button>
    </div>
</form>

<script>
// =====================
// SUBMIT EDIT
// =====================
$('#formEdit').submit(function(e){
    e.preventDefault();

    $.ajax({
        url: "/admin/loker/{{ $loker->id }}/update",
        type: "PUT",
        data: $(this).serialize(),
        success: function(res){
            $('#myModal').modal('hide');
            $('#table-loker').DataTable().ajax.reload();

            Swal.fire('Berhasil', 'Lowongan berhasil diupdate', 'success');
        },
        error: function(xhr){
            Swal.fire('Error', xhr.responseJSON?.message ?? 'Gagal update', 'error');
        }
    });
});

// =====================
// VALIDASI KONTAK REALTIME
// =====================
$('#kontak').on('input', function() {
    let value = $(this).val();
    let regex = /^(08)[0-9]{0,12}$/;

    if (!regex.test(value)) {
        $(this).addClass('is-invalid');
    } else {
        $(this).removeClass('is-invalid');
    }
});

// =====================
// BATAS TANGGAL (TIDAK BOLEH MASA LALU)
// =====================
document.addEventListener("DOMContentLoaded", function () {
    let today = new Date().toISOString().split('T')[0];
    document.getElementById("batas_lamaran").setAttribute("min", today);
});
</script>