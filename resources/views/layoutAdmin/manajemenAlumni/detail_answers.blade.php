@extends('layoutAdmin.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Jawaban Alumni</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/admin/alumni') }}">Alumni</a></li>
        <li class="breadcrumb-item active">Detail Jawaban</li>
    </ol>

    <!-- Info Alumni Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-user me-1"></i> Informasi Alumni</span>
            <a href="{{ url('/admin/alumni') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama:</strong> {{ $alumni->nama }}</p>
                    <p><strong>NIM:</strong> {{ $alumni->nim }}</p>
                    <p><strong>Program Studi:</strong> {{ $alumni->prodi }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Angkatan:</strong> {{ $alumni->angkatan }}</p>
                    <p><strong>Tahun Lulus:</strong> {{ $alumni->tahun_lulus }}</p>
                    <p><strong>No HP:</strong> {{ $alumni->no_hp ?? '-' }}</p>
                </div>
            </div>
            @if($alumni->email)
                <p><strong>Email:</strong> {{ $alumni->email }}</p>
            @endif
            @if($alumni->alamat)
                <p><strong>Alamat:</strong> {{ $alumni->alamat }}</p>
            @endif
        </div>
    </div>

    <!-- Answers Section -->
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-check-circle me-1"></i> Jawaban Questionnaire</span>
            <span class="badge bg-info ms-2">
                {{ count($answers) }} / {{ count($questions) }} Pertanyaan Terjawab
            </span>
        </div>
        <div class="card-body">
            @if(count($questions) == 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> Tidak ada pertanyaan yang tersedia.
                </div>
            @else
                <div class="accordion" id="accordionAnswers">
                    @foreach($questions as $index => $question)
                        @php
                            $answer = $answers->get($question->id);
                            $hasAnswered = $answer ? true : false;
                            $statusBadge = $hasAnswered ? 
                                '<span class="badge bg-success ms-2">Terjawab</span>' : 
                                '<span class="badge bg-danger ms-2">Belum Terjawab</span>';
                        @endphp
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $question->id }}">
                                <button 
                                    class="accordion-button {{ !$hasAnswered ? 'collapsed' : '' }}" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $question->id }}" 
                                    aria-expanded="{{ $hasAnswered ? 'true' : 'false' }}" 
                                    aria-controls="collapse{{ $question->id }}">
                                    
                                    <strong>{{ $index + 1 }}. </strong>
                                    <span class="ms-2">{{ $question->pertanyaan }}</span>
                                    {!! $statusBadge !!}
                                </button>
                            </h2>
                            <div 
                                id="collapse{{ $question->id }}" 
                                class="accordion-collapse collapse {{ $hasAnswered ? 'show' : '' }}" 
                                aria-labelledby="heading{{ $question->id }}" 
                                data-bs-parent="#accordionAnswers">
                                
                                <div class="accordion-body">
                                    @if($hasAnswered)
                                        @php
                                            $answerDetails = $answer->answerDetails;
                                        @endphp
                                        
                                        @if($question->type == 'text')
                                            {{-- Text Answer --}}
                                            <p class="mb-0">
                                                <strong>Jawaban:</strong><br>
                                                <span class="text-muted">{{ $answerDetails->first()->value ?? '-' }}</span>
                                            </p>
                                        
                                        @elseif($question->type == 'single')
                                            {{-- Single Option Answer --}}
                                            @php
                                                $option = $answerDetails->first()?->option;
                                            @endphp
                                            <p class="mb-0">
                                                <strong>Jawaban:</strong><br>
                                                <span class="badge bg-primary">
                                                    {{ $option->label ?? '-' }}
                                                </span>
                                            </p>
                                        
                                        @elseif($question->type == 'multiple')
                                            {{-- Multiple Options Answer --}}
                                            <p class="mb-2">
                                                <strong>Jawaban (Pilih Lebih Dari Satu):</strong>
                                            </p>
                                            <div>
                                                @forelse($answerDetails as $detail)
                                                    @if($detail->option)
                                                        <span class="badge bg-primary mb-2">
                                                            {{ $detail->option->label }}
                                                        </span>
                                                    @endif
                                                @empty
                                                    <span class="text-muted">-</span>
                                                @endforelse
                                            </div>
                                        
                                        @elseif($question->type == 'scale')
                                            {{-- Scale Answer (1-5) --}}
                                            @php
                                                $scaleValue = $answerDetails->first()->value ?? '-';
                                                $scaleLabels = [
                                                    '1' => 'Sangat Rendah / Tidak Puas',
                                                    '2' => 'Rendah / Kurang Puas',
                                                    '3' => 'Sedang / Cukup',
                                                    '4' => 'Tinggi / Puas',
                                                    '5' => 'Sangat Tinggi / Sangat Puas',
                                                ];
                                            @endphp
                                            <p class="mb-0">
                                                <strong>Jawaban:</strong><br>
                                                <span class="badge bg-warning text-dark">
                                                    {{ $scaleValue }} - {{ $scaleLabels[$scaleValue] ?? 'N/A' }}
                                                </span>
                                            </p>
                                        
                                        @else
                                            <p class="text-muted mb-0">Format jawaban tidak dikenali</p>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">Alumni belum menjawab pertanyaan ini</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Summary Stats -->
                @php
                    $answeredCount = count($answers);
                    $totalCount = count($questions);
                    $percentage = $totalCount > 0 ? ($answeredCount / $totalCount) * 100 : 0;
                @endphp
                <div class="mt-4 pt-3 border-top">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="alert alert-info mb-0">
                                <strong>Total Pertanyaan:</strong> {{ $totalCount }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-success mb-0">
                                <strong>Sudah Terjawab:</strong> {{ $answeredCount }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-warning mb-0">
                                <strong>Persentase:</strong> {{ round($percentage, 2) }}%
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #000;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .text-muted {
        font-size: 1rem;
    }
</style>
@endpush
