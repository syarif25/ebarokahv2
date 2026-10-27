@php
    $user = Auth::user();
    $isSuperAdmin = $user && $user->isSuperAdmin();
    $isEvaluasi   = $user && $user->isEvaluasi();
    $isAdminLembaga = $user && $user->isAdminLembaga();
    $isSDM        = $user && $user->isSDM();
    $isKasir      = $user && $user->isKasir();

    $canStruktural = $user && $user->canAccessKehadiranStruktural();
    $canPengajar   = $user && $user->canAccessKehadiranPengajar();
    $canSatpam     = $user && $user->canAccessKehadiranSatpam();
    $hasKehadiranMenu = $canStruktural || $canPengajar || $canSatpam;
@endphp

<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
            <!-- Dashboard (Semua Akun) -->
            <li class="{{ Request::is('dashboard*') ? 'mm-active' : '' }}">
                <a href="{{ url('/dashboard') }}" aria-expanded="false">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- MENU KHUSUS SDM & KASIR (Sementara Menu Kosong) --}}
            @if($isSDM || $isKasir)
                <li class="mt-3">
                    <div class="px-4 py-3 mx-3 text-center" style="background: rgba(255, 171, 0, 0.1); border-radius: 12px; border: 1px dashed #ffab00;">
                        <i class="fas fa-lock text-warning fs-20 mb-2"></i>
                        <h6 class="font-weight-bold text-dark mb-1" style="font-size: 13px;">Menu Belum Tersedia</h6>
                        <p class="text-muted mb-0" style="font-size: 11px;">Akun {{ $user->jabatan }} saat ini belum memiliki akses modul operasional.</p>
                    </div>
                </li>
            @endif

            {{-- MASTER DATA (Khusus SuperAdmin) --}}
            @if($isSuperAdmin)
            <li class="{{ Request::is('master-data*') ? 'mm-active' : '' }}">
                <a class="has-arrow " href="javascript:void()" aria-expanded="{{ Request::is('master-data*') ? 'true' : 'false' }}">
                    <i class="fas fa-database"></i>
                    <span class="nav-text">Master Data</span>
                </a>
                <ul aria-expanded="false" class="{{ Request::is('master-data*') ? 'mm-show' : '' }}">
                    <li><a href="{{ url('/master-data/lembaga') }}">Lembaga</a></li>
                    <li><a href="{{ url('/master-data/prodi') }}">Prodi</a></li>
                    <li><a href="{{ url('/master-data/pengguna') }}">Pengguna</a></li>
                    <li><a href="{{ url('/master-data/umana') }}">Umana</a></li>
                    <li><a href="{{ url('/master-data/tahun-acuan') }}">Tahun Acuan</a></li>
                    <li><a href="{{ url('/master-data/ketentuan-barokah') }}">Ketentuan Barokah</a></li>
                    <li><a href="{{ url('/master-data/libur-pesantren') }}">Libur Pesantren</a></li>
                    <li><a href="{{ url('/master-data/jenis-potongan') }}">Jenis Potongan</a></li>
                    <li><a href="{{ url('/master-data/tunkel') }}">Tunjangan Keluarga</a></li>
                    <li><a href="{{ url('/master-data/tunj-anak') }}">Tunjangan Anak</a></li>
                    <li><a href="{{ url('/master-data/transport') }}">Transport</a></li>
                    <li><a href="{{ url('/master-data/barokah-pengajar') }}">Barokah Pengajar</a></li>
                    <li><a href="{{ url('/master-data/barokah-kehormatan') }}">Tunjangan Kehormatan Struktural</a></li>
                    <li><a href="{{ url('/master-data/barokah-kehormatan-pengajar') }}">Tunjangan Kehormatan Pengajar</a></li>
                    <li><a href="{{ url('/master-data/bank') }}">Biaya Admin Bank</a></li>
                </ul>
            </li>

            {{-- MANAJEMEN UMANA (Khusus SuperAdmin) --}}
            <li class="{{ Request::is('manajemen-umana*') ? 'mm-active' : '' }}">
                <a class="has-arrow " href="javascript:void()" aria-expanded="{{ Request::is('manajemen-umana*') ? 'true' : 'false' }}">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-text">Manajemen Umana'</span>
                </a>
                <ul aria-expanded="false" class="{{ Request::is('manajemen-umana*') ? 'mm-show' : '' }}">
                    <li><a href="{{ url('/manajemen-umana/penempatan') }}">Penempatan Umana'</a></li>
                    <li><a href="{{ url('/manajemen-umana/potongan-umana') }}">Potongan Barokah</a></li>
                    <li><a href="{{ url('/manajemen-umana/tunjangan-beban-kerja') }}">Tunjangan Beban Kerja</a></li>
                </ul>
            </li>
            
            {{-- MANAJEMEN PENGAJAR (Khusus SuperAdmin) --}}
            <li class="{{ Request::is('Penempatan/pengajar*') || Request::is('Potongan_pengajar*') || Request::is('Barokah_tambahan*') ? 'mm-active' : '' }}">
                <a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span class="nav-text">Manajemen Pengajar</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('/Penempatan/pengajar') }}">Penempatan Guru/Dosen</a></li>
                    <li><a href="{{ url('/Potongan_pengajar') }}">Potongan Barokah</a></li>
                    <li><a href="{{ url('/Barokah_tambahan') }}">Barokah Tambahan</a></li>
                </ul>
            </li>

            {{-- MANAJEMEN SATPAM (Khusus SuperAdmin) --}}
            <li class="{{ Request::is('manajemen-satpam*') || Request::is('Penempatan_satpam*') || Request::is('Potongan_satpam*') ? 'mm-active' : '' }}">
                <a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-user-shield"></i>
                    <span class="nav-text">Manajemen Satpam</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('/manajemen-satpam/penempatan') }}">Penempatan Satpam</a></li>
                    <li><a href="{{ url('/manajemen-satpam/potongan-satpam') }}">Potongan Barokah</a></li>
                </ul>
            </li>
            @endif

            {{-- KEHADIRAN (SuperAdmin, Evaluasi, AdminLembaga Berhak) --}}
            @if($hasKehadiranMenu)
            <li class="{{ Request::is('kehadiran*') ? 'mm-active' : '' }}">
                <a class="has-arrow {{ Request::is('kehadiran*') ? 'mm-active' : '' }}" href="javascript:void()" aria-expanded="{{ Request::is('kehadiran*') ? 'true' : 'false' }}">
                    <i class="fas fa-user-check"></i>
                    <span class="nav-text">Kehadiran</span>
                </a>
                <ul aria-expanded="{{ Request::is('kehadiran*') ? 'true' : 'false' }}" class="{{ Request::is('kehadiran*') ? 'mm-show' : '' }}">
                    @if($canStruktural)
                        <li class="{{ Request::is('kehadiran_struktural*') || Request::is('kehadiran-struktural*') ? 'mm-active' : '' }}"><a href="{{ url('/kehadiran_struktural') }}">Struktural</a></li>
                    @endif
                    @if($canPengajar)
                        <li class="{{ Request::is('kehadiran_pengajar*') || Request::is('kehadiran-pengajar*') ? 'mm-active' : '' }}"><a href="{{ url('/kehadiran_pengajar') }}">Pengajar</a></li>
                    @endif
                    @if($canSatpam)
                        <li class="{{ Request::is('kehadiran_satpam*') || Request::is('kehadiran-satpam*') ? 'mm-active' : '' }}"><a href="{{ url('/kehadiran-satpam') }}">Satpam</a></li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- POTONGAN (SuperAdmin & AdminLembaga) --}}
            @if($isSuperAdmin || $isAdminLembaga)
            @php
                $isPotonganActive = Request::is('manajemen-umana/potongan-umana*') || Request::is('Potongan_pengajar*') || Request::is('potongan*');
            @endphp
            <li class="{{ $isPotonganActive ? 'mm-active' : '' }}">
                <a class="has-arrow {{ $isPotonganActive ? 'mm-active' : '' }}" href="javascript:void()" aria-expanded="{{ $isPotonganActive ? 'true' : 'false' }}">
                    <i class="fas fa-user-check"></i>
                    <span class="nav-text">Potongan</span>
                </a>
                <ul aria-expanded="{{ $isPotonganActive ? 'true' : 'false' }}" class="{{ $isPotonganActive ? 'mm-show' : '' }}">
                    @if($canStruktural || $isSuperAdmin)
                        <li class="{{ Request::is('manajemen-umana/potongan-umana*') || Request::is('potongan/struktural*') ? 'mm-active' : '' }}">
                            <a href="{{ url('/manajemen-umana/potongan-umana') }}">Struktural</a>
                        </li>
                    @endif
                    @if($canPengajar || $isSuperAdmin)
                        <li class="{{ Request::is('Potongan_pengajar*') || Request::is('potongan/pengajar*') ? 'mm-active' : '' }}">
                            <a href="{{ url('/Potongan_pengajar') }}">Pengajar</a>
                        </li>
                    @endif
                </ul>
            </li>
            @endif

            {{-- VALIDASI BAROKAH & AUDIT (SuperAdmin & Evaluasi) --}}
            @if($isSuperAdmin || $isEvaluasi)
            <li class="{{ (Request::is('validasi') || Request::is('Validasi')) && !Request::is('validasi/audit*') ? 'mm-active' : '' }}">
                <a class="{{ (Request::is('validasi') || Request::is('Validasi')) && !Request::is('validasi/audit*') ? 'mm-active' : '' }}" href="{{ url('/validasi') }}" aria-expanded="false">
                    <i class="fas fa-list-alt"></i>
                    <span class="nav-text">Validasi Barokah</span>
                </a>
            </li>

            <li class="{{ Request::is('validasi/audit-tunjangan*') || Request::is('audit-tunjangan*') ? 'mm-active' : '' }}">
                <a class="{{ Request::is('validasi/audit-tunjangan*') || Request::is('audit-tunjangan*') ? 'mm-active' : '' }}" href="{{ url('/validasi/audit-tunjangan') }}" aria-expanded="false">
                    <i class="fas fa-balance-scale"></i>
                    <span class="nav-text">Audit Tunjangan Ganda</span>
                </a>
            </li>
            @endif

            {{-- PAYROLL (Khusus SuperAdmin) --}}
            @if($isSuperAdmin)
            <li class="{{ Request::is('payroll*') || Request::is('Payroll*') ? 'mm-active' : '' }}">
                <a class="{{ Request::is('payroll*') || Request::is('Payroll*') ? 'mm-active' : '' }}" href="{{ url('/payroll') }}" aria-expanded="false">
                    <i class="fas fa-cash-register"></i>
                    <span class="nav-text">PayRoll</span>
                </a>
            </li>
            @endif
        
            {{-- LAPORAN (SuperAdmin & Evaluasi) --}}
            @if($isSuperAdmin || $isEvaluasi)
            <li class="{{ Request::is('laporan*') || Request::is('Laporan*') ? 'mm-active' : '' }}">
                <a class="has-arrow {{ Request::is('laporan*') || Request::is('Laporan*') ? 'mm-active' : '' }}" href="javascript:void()" aria-expanded="{{ Request::is('laporan*') || Request::is('Laporan*') ? 'true' : 'false' }}">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-text">Laporan</span>
                </a>
                <ul aria-expanded="false" class="{{ Request::is('laporan*') || Request::is('Laporan*') ? 'mm-show' : '' }}">
                    <li class="{{ Request::is('laporan/per-lembaga*') || Request::is('Laporan_lembaga*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/laporan/per-lembaga') }}">Per Lembaga</a>
                    </li>
                    <li class="{{ Request::is('laporan/rekap-kehadiran-pengajar*') || Request::is('Rekap_kehadiran_pengajar*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/laporan/rekap-kehadiran-pengajar') }}">Rekap Kehadiran Pengajar</a>
                    </li>
                    <li class="{{ Request::is('laporan/satpam*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/laporan/satpam') }}">Laporan Satpam</a>
                    </li>
                    <li class="{{ Request::is('laporan/per-umana*') || Request::is('Laporan/per_umana*') || Request::is('Laporan_v2/per_umana*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/laporan/per-umana') }}">Per Umana</a>
                    </li>
                    <li class="{{ Request::is('laporan/waktu-pengajuan*') || Request::is('Laporan_waktu*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/laporan/waktu-pengajuan') }}">Waktu Pengajuan</a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- LOG RIWAYAT (Khusus SuperAdmin) --}}
            @if($isSuperAdmin)
            <li class="{{ Request::is('log*') || Request::is('Log*') ? 'mm-active' : '' }}">
                <a class="has-arrow {{ Request::is('log*') || Request::is('Log*') ? 'mm-active' : '' }}" href="javascript:void()" aria-expanded="{{ Request::is('log*') || Request::is('Log*') ? 'true' : 'false' }}">
                    <i class="fas fa-history"></i>
                    <span class="nav-text">Log</span>
                </a>
                <ul aria-expanded="false" class="{{ Request::is('log*') || Request::is('Log*') ? 'mm-show' : '' }}">
                    <li class="{{ Request::is('log/kehadiran-lembaga*') || Request::is('Log_kehadiran_lemb*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/log/kehadiran-lembaga') }}">Kehadiran Lembaga</a>
                    </li>
                    <li class="{{ Request::is('log/kehadiran-struktural*') || Request::is('Kehadiran/kehadiran_log*') || Request::is('kehadiran/kehadiran_log*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/log/kehadiran-struktural') }}">Kehadiran Struktural</a>
                    </li>
                    <li class="{{ Request::is('log/kehadiran-pengajar*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/log/kehadiran-pengajar') }}">Kehadiran Pengajar</a>
                    </li>
                    <li class="{{ Request::is('log/potongan-umana*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/log/potongan-umana') }}">Potongan Umana</a>
                    </li>
                    <li class="{{ Request::is('log/potongan-pengajar*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/log/potongan-pengajar') }}">Potongan Pengajar</a>
                    </li>
                    <li class="{{ Request::is('log/potongan-satpam*') ? 'mm-active' : '' }}">
                        <a href="{{ url('/log/potongan-satpam') }}">Potongan Satpam</a>
                    </li>
                </ul>
            </li>

            <li><a href="#" aria-expanded="false">
                    <i class="fas fa-cog"></i>
                    <span class="nav-text">Pengaturan</span>
                </a>
            </li>
            @endif
        </ul>
        <div class="copyright">
            <p><strong>E-Barokah P2S3</strong> © 2026 All Rights Reserved</p>
        </div>
    </div>
</div>
