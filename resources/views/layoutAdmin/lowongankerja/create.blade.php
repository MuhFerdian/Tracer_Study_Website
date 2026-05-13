<div class="modal-header">
    <h5 class="modal-title">Tambah Lowongan</h5>
</div>

<form id="formTambah">
    @csrf

    <div class="modal-body">
        <input type="text" name="posisi" class="form-control mb-2" placeholder="Posisi" required>
        <input type="text" name="nama_perusahaan" class="form-control mb-2" placeholder="Perusahaan" required>
        <input type="text" name="lokasi" class="form-control mb-2" placeholder="Lokasi">
        <input type="text" name="gaji" class="form-control mb-2" placeholder="Gaji">
        
        <textarea name="deskripsi" class="form-control mb-2" placeholder="Deskripsi"></textarea>

        <!-- 🔥 FIX TANGGAL -->
        <input type="date" name="batas_lamaran" id="batas_lamaran" class="form-control mb-2">

        <!-- 🔥 FIX KONTAK -->
        <input type="text" name="kontak" id="kontak"
            class="form-control mb-2"
            placeholder="Contoh: 08123456789"
            pattern="08[0-9]{8,12}"
            required>

        <input type="text" name="link_lamaran" class="form-control mb-2" placeholder="Link Lamaran">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
// =====================
// SUBMIT TAMBAH
// =====================
$('#formTambah').submit(function(e){
    e.preventDefault();

    $.post("/admin/loker/store", $(this).serialize(), function(res){
        $('#myModal').modal('hide');
        $('#table-loker').DataTable().ajax.reload();

        Swal.fire('Berhasil', res.message, 'success');
    }).fail(function(xhr){
        Swal.fire('Error', xhr.responseJSON?.message ?? 'Gagal tambah data', 'error');
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
// BATAS TANGGAL
// =====================
document.addEventListener("DOMContentLoaded", function () {
    let today = new Date().toISOString().split('T')[0];
    document.getElementById("batas_lamaran").setAttribute("min", today);
});
</script>