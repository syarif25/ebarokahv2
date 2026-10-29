@extends('layouts.app')
@section('title', 'Rekap Kehadiran Satpam - ' . ($periode->lembaga->nama_lembaga ?? 'Satpam'))

@section('page_title')
<span class="text-danger font-weight-bold">Kehadiran Satpam</span> / Input Rekap Kehadiran
@endsection

@push('styles')
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
    .swal2-container {
        z-index: 99999 !important;
    }
    .rekap-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
    }

    #tabel_rek tbody tr {
        border-bottom: 1px solid #cbd5e1;
        transition: background-color 0.15s ease;
    }
    #tabel_rek tbody tr:hover {
        background-color: #f1f5f9;
    }
    #tabel_rek thead th {
        border-bottom: 2px solid #94a3b8;
        color: #000000 !important;
        font-size: 14px;
        font-weight: 700 !important;
        padding: 14px 12px;
        vertical-align: middle;
        background-color: #f8fafc;
    }

    .form-control-presence {
        border: 2px solid #94a3b8 !important;
        border-radius: 8px !important;
        text-align: center;
        font-weight: 800;
        font-size: 15px;
        color: #000000 !important;
        height: 38px;
    }
    .form-control-presence:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 161, 93, 0.25) !important;
        border-color: #00a15d !important;
    }

    .upload-box {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.2s ease;
    }
    .upload-box:hover {
        border-color: #00a15d;
        background: #f0fdf4;
    }

    .btn-save-rekap {
        background-color: #00a15d !important;
        color: white !important;
        border-radius: 30px;
        padding: 12px 35px;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 161, 93, 0.25);
        transition: all 0.2s ease;
    }
    .btn-save-rekap:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }
    </style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 15px; margin-top: 10px;">
        <div class="card-body py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-normal">
                <strong class="text-danger">Kehadiran Satpam</strong> 
                <span class="text-muted">/ Rekap Bulan {{ $periode->bulan }} {{ $periode->tahun }}</span>
            </h5>
            <a href="{{ url('/kehadiran-satpam') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card rekap-card">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title text-dark font-weight-bold mb-0 me-3">
                            <i class="fas fa-shield-alt text-success me-2"></i>{{ $periode->lembaga->nama_lembaga ?? 'Satpam' }}
                        </h5>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            Total Personil: <strong>{{ count($satpam) }} Orang</strong>
                        </span>
                    </div>
                    <div class="search-box" style="min-width: 260px;">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-color: #00a15d; border-radius: 20px 0 0 20px; color: #00a15d;">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="search_nama_satpam" class="form-control border-start-0" placeholder="Cari nama satpam..." style="border-color: #00a15d; border-radius: 0 20px 20px 0; font-size: 13px; padding-left: 0;">
                        </div>
                    </div>
                </div>

                <form id="formRekap" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_kehadiran_lembaga" value="{{ $periode->id_kehadiran_lembaga }}">
                    <input type="hidden" name="bulan" value="{{ $periode->bulan }}">
                    <input type="hidden" name="tahun" value="{{ $periode->tahun }}">

                    <div class="card-body p-4">
                        <div class="table-responsive mb-4">
                            <table id="tabel_rek" class="table table-hover align-middle mb-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th>Nama Lengkap</th>
                                        <th class="text-center" style="width: 150px;">Hadir Hari</th>
                                        <th class="text-center" style="width: 150px;">Hadir Shift</th>
                                        <th class="text-center" style="width: 150px;">Hadir Dini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($satpam as $key)
                                        @php
                                            $exist = $existingKehadiran->get($key->id_satpam);
                                            $hVal = $exist ? $exist->jumlah_hari : '';
                                            $sVal = $exist ? $exist->jumlah_shift : '';
                                            $dVal = $exist ? $exist->jumlah_dinihari : '';
                                            $namaLengkap = trim(($key->gelar_depan ?? '') . ' ' . $key->nama_lengkap . ' ' . ($key->gelar_belakang ?? ''));
                                        @endphp
                                        <tr>
                                            <td class="text-center text-muted fw-bold">
                                                {{ $no++ }}
                                                <input type="hidden" name="id_satpam[]" value="{{ $key->id_satpam }}">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark d-inline-block">{{ $namaLengkap }}</div>
                                                @if($key->is_danru == '1')
                                                    <span class="badge bg-info text-white ms-1">Danru</span>
                                                @endif
                                            </td>
                                            <td>
                                                <input type="number" min="0" max="31" name="jumlah_hari[]" 
                                                       value="{{ $hVal }}" class="form-control form-control-presence input-satpam" 
                                                       placeholder="0" onchange="cekInputFile()">
                                            </td>
                                            <td>
                                                <input type="number" min="0" name="jumlah_shift[]" 
                                                       value="{{ $sVal }}" class="form-control form-control-presence input-satpam" 
                                                       placeholder="0" onchange="cekInputFile()">
                                            </td>
                                            <td>
                                                <input type="number" min="0" name="jumlah_dini[]" 
                                                       value="{{ $dVal }}" class="form-control form-control-presence input-satpam" 
                                                       placeholder="0" onchange="cekInputFile()">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Upload File Absensi PDF -->
                        <div class="upload-box mb-4">
                            <h5 class="text-danger font-weight-bold mb-2">
                                <i class="fas fa-file-pdf me-2"></i>Upload File Absensi Lembaga (file PDF) <span class="text-danger">*</span>
                            </h5>
                            <p class="text-muted small mb-3">Unggah dokumen pindaian / rekap absensi resmi berformat PDF.</p>
                            
                            @if(!empty($periode->file))
                                <div class="alert alert-info py-2 px-3 d-inline-flex align-items-center mb-3">
                                    <i class="fas fa-check-circle me-2 text-success"></i> File saat ini: 
                                    <a href="{{ asset('upload/' . $periode->file) }}" target="_blank" class="ms-1 fw-bold text-decoration-underline">
                                        {{ $periode->file }}
                                    </a>
                                </div>
                            @endif

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <input type="file" name="file" id="file-input" class="form-control" accept="application/pdf" onchange="cekInputFile()">
                                    <span class="help-block text-danger small mt-1 d-block" id="file-error"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox Konfirmasi -->
                        <div class="form-check mb-4 p-3 bg-light rounded-3 border">
                            <input class="form-check-input ms-1" type="checkbox" id="cek" onchange="cekInputFile()" style="cursor: pointer; width: 1.2em; height: 1.2em;">
                            <label class="form-check-label text-dark fw-bold ms-2" for="cek" style="cursor: pointer;">
                                Sudah Lengkap (Total Presensi + Upload File Absensi PDF)
                            </label>
                        </div>

                        <!-- Tombol Simpan -->
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-save-rekap" id="btnSave" onclick="simpan_kehadiran()">
                                <i class="fas fa-save me-2"></i> Simpan Rekap Kehadiran
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<script>
    // Live search filter nama satpam
    $('#search_nama_satpam').on('keyup change', function() {
        var query = $(this).val().toLowerCase().trim();
        $('#tabel_rek tbody tr').each(function() {
            var nama = $(this).find('td:nth-child(2)').text().toLowerCase();
            if (nama.indexOf(query) !== -1 || query === '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    function cekInputFile() {
        // Optional live status indicator
    }

    function simpan_kehadiran() {
        var fileInput = document.getElementById('file-input');
        var hasExistingFile = "{{ !empty($periode->file) ? '1' : '0' }}";

        if ((!fileInput || !fileInput.files || fileInput.files.length === 0) && hasExistingFile !== '1') {
            Swal.fire({
                icon: 'warning',
                title: 'File belum dipilih',
                text: 'File absensi (PDF) wajib diunggah!'
            });
            return;
        }

        var isChecked = $('#cek').is(':checked');
        if (!isChecked) {
            Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi Kelengkapan',
                text: 'Silakan centang persetujuan "Sudah Lengkap" terlebih dahulu.'
            });
            return;
        }

        $('#btnSave').html('<i class="fas fa-spinner fa-spin me-2"></i> Proses menyimpan...').prop('disabled', true);

        var form = document.getElementById('formRekap');
        var formData = new FormData(form);

        $.ajax({
            url: "{{ url('/kehadiran-satpam/rekap') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                $('#btnSave').html('<i class="fas fa-save me-2"></i> Simpan Rekap Kehadiran').prop('disabled', false);

                if (data && data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan',
                        text: data.message || 'Rekap kehadiran berhasil disimpan.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ url('/kehadiran-satpam') }}";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menyimpan',
                        text: (data && data.message) ? data.message : 'Periksa kembali isian dan file PDF.'
                    });
                }
            },
            error: function(xhr) {
                $('#btnSave').html('<i class="fas fa-save me-2"></i> Simpan Rekap Kehadiran').prop('disabled', false);
                var msg = 'Gagal menghubungi server.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Server',
                    text: msg
                });
            }
        });
    }
</script>
@endpush
