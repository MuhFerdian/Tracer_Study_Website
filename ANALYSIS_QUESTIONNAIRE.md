# ANALISIS STRUKTUR QUESTIONNAIRE PROJECT

## 1. KONDISI SEKARANG

### A. Sistem Lama (Masih Ada di Database)
```
Table: pertanyaan (PertanyaanModel)
  - pertanyaan_id (PK)
  - kode_soal
  - question_text
  - type
  - options (JSON array)
  - urutan
  - timestamps

Table: jawaban (JawabanSurveiModel)
  - jawaban_id (PK)
  - pertanyaan_id (FK)
  - alumni_id
  - jawaban (text)
  - timestamps
```

**Status**: Hanya ada 4 pertanyaan sederhana untuk survei kompetensi.

### B. Sistem Baru (Baru di Migration, Belum Ada UI)
```
Table: questions (Question.php)
  - id (PK)
  - kode
  - pertanyaan
  - type (single, multiple, text, scale)
  - is_required
  - urutan
  - timestamps

Table: question_options (QuestionOption.php)
  - id (PK)
  - question_id (FK)
  - label
  - value
  - urutan
  - timestamps

Table: answers (Answer.php)
  - id (PK)
  - alumni_id (FK)
  - question_id (FK)
  - timestamps

Table: answer_details (AnswerDetail.php)
  - id (PK)
  - answer_id (FK)
  - option_id (FK, nullable)
  - value (text, nullable)
  - timestamps
```

**Status**: Infrastructure ada, tapi belum ada view/controller, dan database kosong.

### C. Controllers Yang Ada
1. **SurveiController** - untuk sistem lama (pertanyaan/jawaban)
2. **PertanyaanController** - untuk admin manage questions (pertanyaan)
3. **ManajemenAlumniController** - untuk manage alumni data
4. **ExportController** - untuk export data alumni

**Masalah**: Tidak ada controller untuk alumni mengisi questionnaire baru!

### D. Views Yang Ada
- Admin: `layoutAdmin/pertanyaan/` (manage questions)
- Alumni: `layoutAlumni/index.blade.php` (hanya satu view kosong)
- Export: `layoutAdmin/rekap/` (hanya untuk export alumni)

**Masalah**: Tidak ada view untuk alumni mengisi atau melihat questions!

---

## 2. PERBANDINGAN DENGAN PDF

| Aspek | PDF Panduan Form | Database Sekarang |
|-------|------------------|-------------------|
| **Jumlah Pertanyaan** | 21 pertanyaan wajib | Hanya 4 pertanyaan |
| **Tipe Input** | Single, Multiple, Text, Matrix, Date | Text, Scale (sederhana) |
| **Struktur Data** | Terstruktur dengan kode (f8, f502, dll) | Tidak terstruktur |
| **Field Alumni** | 9 field (identitas lengkap) | 12 field (sudah puas) |
| **Field Pekerjaan** | 10+ field detail | 3 field (sederhana) |
| **View Alumni Mengisi** | ❌ TIDAK ADA | ❌ TIDAK ADA |
| **View Lihat Jawaban** | ❌ TIDAK ADA | ❌ TIDAK ADA |

---

## 3. FLOW YANG DIPERLUKAN

### A. Admin Flow
```
1. Admin login → Dashboard
2. Admin bisa manage questions (sudah ada PertanyaanController)
3. Admin bisa lihat alumni yang sudah/belum mengisi
4. Admin bisa export hasil survey
```

### B. Alumni Flow (YANG BELUM ADA!)
```
1. Alumni login
2. Lihat status: "Sudah Mengisi" atau "Belum Mengisi"
3. Klik "Isi Questionnaire"
4. Tampilkan 21 pertanyaan dari PDF (dinamis dari database)
5. Alumni jawab 1 per 1 atau semua di halaman
6. Submit jawaban
7. Alumni bisa lihat kembali jawaban yang sudah diisi
```

### C. Detail Jawaban Alumni (YANG BELUM ADA!)
```
1. Admin → Alumni Detail
2. Lihat informasi alumni (nama, nim, tahun lulus, dll)
3. Tab "Jawaban Questionnaire"
4. Lihat semua 21 pertanyaan beserta jawaban alumni
5. Bisa filter/search pertanyaan tertentu
```

---

## 4. REKOMENDASI SOLUSI

### Step 1: Unified Question System
✅ Gunakan sistem **Question/QuestionOption/Answer/AnswerDetail** yang baru
❌ Jangan pakai sistem PertanyaanModel lama (deprecated)

**Action**:
- Migrate all questions dari file PDF ke table `questions` & `question_options` (sudah ada seeder)
- Hapus/deprecated table `pertanyaan` & `jawaban` (lama)

### Step 2: Create Alumni Survey Interface
📝 Buat view & controller agar alumni bisa mengisi survey:

**Files Perlu Dibuat**:
- `SurveyController.php` - controller untuk alumni mengisi survey
- `resources/views/layoutAlumni/survey/form.blade.php` - form questionnaire
- `resources/views/layoutAlumni/survey/view.blade.php` - lihat jawaban yang sudah diisi

### Step 3: Create Answer Detail View
📊 Buat view agar admin bisa lihat detail jawaban alumni:

**Files Perlu Dibuat**:
- `resources/views/layoutAdmin/manajemenAlumni/detail_answers.blade.php` - detail jawaban alumni

### Step 4: Update Routes
🔀 Tambah routes untuk survey alumni:
```
Route::group(['prefix' => 'alumni', 'middleware' => 'auth:alumni'], function () {
    Route::get('/survey', [SurveyController::class, 'showForm']);
    Route::post('/survey', [SurveyController::class, 'store']);
    Route::get('/survey/view', [SurveyController::class, 'viewAnswers']);
});
```

---

## 5. DATABASE YANG DIPERLUKAN (Sudah Disiapkan)

Saya sudah buat 3 migration + 3 seeder:

✅ **Migrations**:
1. `2026_05_05_000001_expand_alumni_table.php` - tambah kolom alumni
2. `2026_05_05_000002_create_support_tables.php` - tabel provinsi, kota, jenis_instansi
3. `2026_05_05_000003_enhance_questions_table.php` - enhance questions table

✅ **Seeders**:
1. `QuestionSeeder.php` - 21 pertanyaan + options sesuai PDF
2. `ProvinsiSeeder.php` - 37 provinsi + kota Indonesia
3. `JenisInstansiSeeder.php` - 7 jenis instansi kerja

✅ **Models Baru**:
1. `Provinsi.php`
2. `KotaKabupaten.php`
3. `JenisInstansi.php`

---

## 6. STRUKTUR FINAL YANG AKAN TERJADI

```
alumni (student) login
    ↓
layoutAlumni/survey/form.blade.php
    ↓
Display 21 questions dari questions table
    ↓
Alumni jawab → SurveyController@store
    ↓
Data disimpan di tables: answers + answer_details
    ↓
Alumni bisa lihat di: layoutAlumni/survey/view.blade.php

---

admin login
    ↓
Dashboard → lihat daftar alumni
    ↓
Klik alumni → manajemenAlumni/detail_answers.blade.php
    ↓
Lihat semua jawaban alumni (21 pertanyaan + jawabannya)
    ↓
Bisa export Excel dengan data lengkap
```

---

## 7. NEXT STEPS

### Phase 1: Database Ready ✅ (Done)
- Migrations untuk expand alumni & create support tables
- Seeder untuk 21 questions dari PDF
- Models untuk Provinsi, KotaKabupaten, JenisInstansi

### Phase 2: Alumni Survey Interface (TODO)
- SurveyController untuk show form & store answers
- View form untuk alumni isi questionnaire
- View untuk alumni lihat jawaban yang sudah diisi
- AJAX untuk submit smooth (opsional)

### Phase 3: Admin Detail Answers (TODO)
- Enhance ManajemenAlumniController untuk show detail answers
- View detail jawaban alumni per question
- Styling & UX improvements

### Phase 4: Export & Reporting (TODO)
- Enhance ExportController untuk export semua jawaban survey
- Report/Analytics untuk answers

---

## KESIMPULAN

✅ Database structure sudah OK (Question/QuestionOption/Answer/AnswerDetail)
✅ 21 pertanyaan dari PDF sudah disiapkan via seeder
✅ Alumni table sudah expanded dengan kolom yang perlu

❌ Alumni UI untuk mengisi survey BELUM ADA
❌ Alumni UI untuk lihat jawaban BELUM ADA
❌ Admin UI untuk lihat detail jawaban BELUM ADA
❌ Routes untuk survey BELUM ADA

**Solusi**: Kita perlu buat 3 views + 1 controller + update routes untuk membuat flow lengkap.

Apakah Anda ingin saya lanjut ke Phase 2 (Alumni Survey Interface)?
