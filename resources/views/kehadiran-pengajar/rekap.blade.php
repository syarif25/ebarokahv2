@extends('layouts.app')
@section('title', 'Rekap Kehadiran Pengajar - ' . $periode->lembaga->nama_lembaga)

@section('page_title')
<span class="text-danger font-weight-bold">Kehadiran Pengajar</span> / Input Rekap Kehadiran Guru / Dosen
@endsection

@push('styles')
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
    /* Clean Modern Fullscreen & Card Styling */
    .swal2-container {
        z-index: 99999 !important;
    }
    .rekap-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #fff;
    }

    /* Table Clean Styling */
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

    /* Stepper Input Styling */
    .stepper-box {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 130px;
        margin: 0 auto;
        border: 2px solid #94a3b8;
        border-radius: 25px;
        background: #fff;
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .stepper-box:focus-within {
        border-color: #00a15d;
        box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.25);
    }
    .stepper-btn {
        width: 34px;
        height: 34px;
        border: none;
        background: transparent;
        color: #000000;
        font-size: 17px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .stepper-btn:hover {
        background-color: #e2e8f0;
        color: #00a15d;
    }
    .stepper-btn:active {
        background-color: #cbd5e1;
    }
    .stepper-input {
        width: 58px;
        height: 34px;
        border: none;
        text-align: center;
        font-size: 15px;
        font-weight: 800;
        color: #000000 !important;
        background: transparent;
        outline: none;
        -moz-appearance: textfield;
    }
    .stepper-input::-webkit-outer-spin-button,
    .stepper-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* PDF Upload Box */
    .upload-box {
        border: 2px dashed #00a15d;
        background-color: #f0fdf4;
        border-radius: 14px;
        padding: 22px;
        text-align: center;
        transition: all 0.2s ease;
    }
    .upload-box:hover {
        background-color: #dcfce7;
    }

    /* Sticky Footer Bar */
    .sticky-submit-bar {
        position: sticky;
        bottom: 0;
        background: #ffffff;
        box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.05);
        border-top: 1px solid #eef2f6;
        z-index: 100;
        border-radius: 0 0 16px 16px;
    }

    .btn-simpan-rekap {
        background-color: #00a15d !important;
        border: none;
        color: white;
        border-radius: 25px;
        padding: 10px 30px;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 4px 10px rgba(0, 161, 93, 0.25);
        transition: all 0.2s ease;
    }
    .btn-simpan-rekap:hover:not(:disabled) {
        opacity: 0.95;
        transform: translateY(-1px);
    }
    .btn-simpan-rekap:disabled {
        background-color: #94a3b8 !important;
        box-shadow: none;
        cursor: not-allowed;
    }
    .btn-kembali {
        background-color: #fdeef3 !important;
        color: #d81b60 !important;
        border-radius: 25px;
        padding: 10px 25px;
        font-weight: 600;
        border: none;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <!-- HEADER BREADCRUMB STANDAR E-BAROKAH -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; margin-top: 5px;">
        <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 font-weight-bold text-dark">
                    {{ $periode->lembaga->nama_lembaga }}
                    <span class="text-muted font-weight-normal fs-14"> — Rekap Kehadiran Pengajar Bulan {{ $periode->bulan }} {{ $periode->tahun }}</span>
                </h5>
            </div>
            <a href="{{ url('/kehadiran_pengajar') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- FORM INPUT REKAP TABEL PENGAJAR -->
    <div class="card border-0 shadow-sm rekap-card mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="font-weight-bold text-dark mb-1">Daftar Kehadiran Guru / Dosen</h5>
                <p class="text-muted font-weight-normal mb-0" style="font-size: 13px;">
                    Isi kehadiran per Guru / Dosen untuk periode <strong>{{ $periode->bulan }} {{ $periode->tahun }}</strong>.
                </p>
            </div>
            <div class="search-box" style="min-width: 260px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-color: #00a15d; border-radius: 20px 0 0 20px; color: #00a15d;">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="search_nama_pengajar" class="form-control border-start-0" placeholder="Cari nama pengajar..." style="border-color: #00a15d; border-radius: 0 20px 20px 0; font-size: 13px; padding-left: 0;">
                </div>
            </div>
        </div>

        <form id="formRekap" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id_kehadiran_lembaga" value="{{ $periode->id_kehadiran_lembaga }}">
            <input type="hidden" name="bulan" value="{{ $periode->bulan }}">
            <input type="hidden" name="tahun" value="{{ $periode->tahun }}">

            <div class="card-body px-4 pt-2">
                <div class="table-responsive">
                    <table id="tabel_rek" class="table align-middle" style="width: 100%;">
                        <thead>
                            <tr style="border-bottom: 2px solid #64748b;">
                                <th style="width: 50px; color: #000 !important; font-weight: 800; font-size: 14px;" class="text-center">No</th>
                                <th style="color: #000 !important; font-weight: 800; font-size: 14px;">Nama Lengkap</th>
                                <th style="width: 90px; color: #000 !important; font-weight: 800; font-size: 14px;" class="text-center">SKS / Jam</th>
                                <th style="color: #000 !important; font-weight: 800; font-size: 14px;">Prodi</th>
                                <th style="width: 140px; color: #000 !important; font-weight: 800; font-size: 14px;" class="text-center">
                                    Hadir Normal<br><small class="text-dark font-weight-bold" style="font-size: 11px;">(sesuai domisili)</small>
                                </th>
                                <th style="width: 140px; color: #000 !important; font-weight: 800; font-size: 14px;" class="text-center">Hadir 15.000</th>
                                <th style="width: 140px; color: #000 !important; font-weight: 800; font-size: 14px;" class="text-center">Hadir 10.000</th>
                                <th style="width: 140px; color: #000 !important; font-weight: 800; font-size: 14px;" class="text-center">Jam Piket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajar as $idx => $p)
                            @php
                                $d = $existingKehadiran[$p->id_pengajar] ?? null;
                                $hadirNormal = $d ? $d->jumlah_hadir : 0;
                                $hadir15 = $d ? $d->jumlah_hadir_15 : 0;
                                $hadir10 = $d ? $d->jumlah_hadir_10 : 0;
                                $hadirPiket = $d ? $d->jumlah_hadir_piket : 0;

                                $namaGelar = trim(($p->gelar_depan ?? '') . ' ' . ucwords(strtolower($p->nama_lengkap ?? '')) . ' ' . ($p->gelar_belakang ?? ''));
                                
                                $badge = '';
                                if ($p->status === 'Cuti 50%') {
                                    $badge = ' <span class="badge bg-warning text-dark font-weight-bold" style="font-size: 11px;">Cuti 50%</span>';
                                } elseif ($p->status === 'Cuti 100%') {
                                    $badge = ' <span class="badge bg-danger font-weight-bold" style="font-size: 11px;">Cuti 100%</span>';
                                }
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold" style="color: #000; font-size: 14px;">{{ $idx + 1 }}</td>
                                <td>
                                    <span class="font-weight-bold d-block" style="color: #000; font-size: 14px;">{!! $namaGelar . $badge !!}</span>
                                    <input type="hidden" name="id_penempatan[]" value="{{ $p->id_pengajar }}">
                                    <input type="hidden" name="nik_umana[]" value="{{ $p->nik }}">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark font-weight-bold px-2 py-1" style="border: 1px solid #cbd5e1; font-size: 12px;">
                                        {{ $p->jumlah_sks ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @if(!empty($p->nama_prodi))
                                        <span class="font-weight-bold text-primary" style="font-size: 13px;">{{ $p->nama_prodi }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- HADIR NORMAL -->
                                <td class="text-center">
                                    <div class="stepper-box">
                                        <button type="button" class="stepper-btn" onclick="stepperChange(this, -1)">−</button>
                                        <input type="number" name="jumlah_kehadiran[]" class="stepper-input input-hadir" min="0" value="{{ $hadirNormal }}">
                                        <button type="button" class="stepper-btn" onclick="stepperChange(this, 1)">+</button>
                                    </div>
                                </td>

                                <!-- HADIR 15.000 -->
                                <td class="text-center">
                                    <div class="stepper-box">
                                        <button type="button" class="stepper-btn" onclick="stepperChange(this, -1)">−</button>
                                        <input type="number" name="jumlah_kehadiran_15[]" class="stepper-input input-hadir-15" min="0" value="{{ $hadir15 }}">
                                        <button type="button" class="stepper-btn" onclick="stepperChange(this, 1)">+</button>
                                    </div>
                                </td>

                                <!-- HADIR 10.000 -->
                                <td class="text-center">
                                    <div class="stepper-box">
                                        <button type="button" class="stepper-btn" onclick="stepperChange(this, -1)">−</button>
                                        <input type="number" name="jumlah_kehadiran_10[]" class="stepper-input input-hadir-10" min="0" value="{{ $hadir10 }}">
                                        <button type="button" class="stepper-btn" onclick="stepperChange(this, 1)">+</button>
                                    </div>
                                </td>

                                <!-- JAM PIKET -->
                                <td class="text-center">
                                    <div class="stepper-box">
                                        <button type="button" class="stepper-btn" onclick="stepperChange(this, -1)">−</button>
                                        <input type="number" name="jumlah_kehadiran_piket[]" class="stepper-input input-hadir-piket" min="0" value="{{ $hadirPiket }}">
                                        <button type="button" class="stepper-btn" onclick="stepperChange(this, 1)">+</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-3x mb-3 text-secondary d-block"></i>
                                    Tidak ada data Guru/Dosen aktif pada unit ini untuk periode {{ $periode->bulan }} {{ $periode->tahun }}.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- BOX UPLOAD PDF ABSENSI -->
                <div class="row mt-4 mb-3">
                    <div class="col-12">
                        <div class="upload-box">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3 text-start">
                                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #00a15d; color: white; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-dark mb-1">Upload Bukti Absensi Lembaga (Format PDF) <span class="text-danger">*</span></h6>
                                        <p class="text-muted mb-0" style="font-size: 13px;">
                                            Maks. 10MB.
                                            @if($periode->file)
                                                <span class="badge bg-success ms-2"><i class="fas fa-check"></i> File Sudah Ada: {{ $periode->file }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div style="min-width: 280px;">
                                    <input type="file" name="file" id="file_pdf" class="form-control" accept="application/pdf" style="border-radius: 10px; border-color: #00a15d;" onchange="validateFileSelected()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STICKY ACTION FOOTER BAR -->
            <div class="card-footer sticky-submit-bar py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="text-muted" style="font-size: 13px;">
                    <i class="fas fa-info-circle text-success me-1"></i> Pastikan seluruh data kehadiran dan file PDF sudah lengkap sebelum menyimpan.
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url('/kehadiran_pengajar') }}" class="btn btn-kembali">Batal</a>
                    <button type="submit" class="btn btn-simpan-rekap" id="btnSave">
                        <i class="fas fa-save me-1"></i> Simpan Rekap Kehadiran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<script>
    // Live search filter nama pengajar
    $('#search_nama_pengajar').on('keyup change', function() {
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

    // Fungsi Stepper + / -
    function stepperChange(btn, delta) {
        var input = $(btn).siblings('input.stepper-input');
        var val = parseInt(input.val()) || 0;
        var newVal = Math.max(0, val + delta);
        input.val(newVal).trigger('change');
    }

    // Input manual validation
    $(document).on('input', '.stepper-input', function() {
        var val = parseInt($(this).val()) || 0;
        if (val < 0) $(this).val(0);
    });

    // Check file uploaded
    function validateFileSelected() {
        var file = document.getElementById('file_pdf').files[0];
        if (file) {
            if (file.type !== 'application/pdf') {
                Swal.fire('Format Salah', 'File bukti absensi harus berformat PDF!', 'warning');
                document.getElementById('file_pdf').value = '';
                return false;
            }
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire('Ukuran Terlalu Besar', 'Maksimal ukuran file PDF adalah 10MB!', 'warning');
                document.getElementById('file_pdf').value = '';
                return false;
            }
        }
        return true;
    }

    // Submit form AJAX
    $('#formRekap').submit(function(e) {
        e.preventDefault();

        // Validasi file PDF jika belum pernah upload sebelumnya
        var hasExistingFile = "{{ !empty($periode->file) ? '1' : '0' }}";
        var fileInput = document.getElementById('file_pdf');

        if (hasExistingFile !== '1' && (!fileInput || !fileInput.files || fileInput.files.length === 0)) {
            Swal.fire({
                icon: 'warning',
                title: 'File PDF Belum Dipilih',
                text: 'File bukti absensi (PDF) wajib diunggah sebelum menyimpan rekap!'
            });
            return;
        }

        var formData = new FormData(this);
        var btn = $('#btnSave');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan Data...');

        $.ajax({
            url: '{{ url("/kehadiran_pengajar/saveRekap") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Rekap Kehadiran');
                if (res.status === 'success' || res.status === true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ url("/kehadiran_pengajar") }}';
                    });
                } else {
                    Swal.fire('Gagal', res.message || 'Gagal menyimpan rekap', 'error');
                }
            },
            error: function(err) {
                btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Rekap Kehadiran');
                var msg = 'Terjadi kesalahan sistem saat menyimpan data.';
                if (err.responseJSON && err.responseJSON.message) {
                    msg = err.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });
</script>
@endpush
