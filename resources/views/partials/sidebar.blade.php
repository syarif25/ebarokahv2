<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
            <li><a href="{{ url('/dashboard') }}" aria-expanded="false">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-database"></i>
                    <span class="nav-text">Master Data</span>
                </a>
                <ul aria-expanded="false">
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
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-text">Manajemen Umana'</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('/manajemen-umana/penempatan') }}">Penempatan Umana'</a></li>
                    <li><a href="{{ url('/manajemen-umana/potongan-umana') }}">Potongan Barokah</a></li>
                    <li><a href="{{ url('/manajemen-umana/tunjangan-beban-kerja') }}">Tunjangan Beban Kerja</a></li>
                </ul>
            </li>
            
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span class="nav-text">Manajemen Pengajar</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('/Penempatan/pengajar') }}">Penempatan Guru/Dosen</a></li>
                    <li><a href="{{ url('/Potongan_pengajar') }}">Potongan Barokah</a></li>
                    <li><a href="{{ url('/Barokah_tambahan') }}">Barokah Tambahan</a></li>
                </ul>
            </li>
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-user-shield"></i>
                    <span class="nav-text">Manajemen Satpam</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('/manajemen-satpam/penempatan') }}">Penempatan Satpam</a></li>
                    <li><a href="{{ url('/manajemen-satpam/potongan-satpam') }}">Potongan Barokah</a></li>
                </ul>
            </li>
            <li class="{{ Request::is('kehadiran*') ? 'mm-active' : '' }}">
                <a class="has-arrow {{ Request::is('kehadiran*') ? 'mm-active' : '' }}" href="javascript:void()" aria-expanded="{{ Request::is('kehadiran*') ? 'true' : 'false' }}">
                    <i class="fas fa-user-check"></i>
                    <span class="nav-text">Kehadiran</span>
                </a>
                <ul aria-expanded="{{ Request::is('kehadiran*') ? 'true' : 'false' }}" class="{{ Request::is('kehadiran*') ? 'mm-show' : '' }}">
                    <li class="{{ Request::is('kehadiran_struktural*') || Request::is('kehadiran-struktural*') ? 'mm-active' : '' }}"><a href="{{ url('/kehadiran_struktural') }}">Struktural</a></li>
                    <li class="{{ Request::is('kehadiran_pengajar*') || Request::is('kehadiran-pengajar*') ? 'mm-active' : '' }}"><a href="{{ url('/kehadiran_pengajar') }}">Pengajar</a></li>
                    <li class="{{ Request::is('kehadiran_satpam*') || Request::is('kehadiran-satpam*') ? 'mm-active' : '' }}"><a href="{{ url('/kehadiran-satpam') }}">Satpam</a></li>
                </ul>
            </li>

            <li class="{{ (Request::is('validasi') || Request::is('Validasi')) && !Request::is('validasi/audit*') ? 'mm-active' : '' }}">
                <a class="{{ (Request::is('validasi') || Request::is('Validasi')) && !Request::is('validasi/audit*') ? 'mm-active' : '' }}" href="{{ url('/validasi') }}" aria-expanded="false">
                    <i class="fas fa-list-alt"></i>
                    <span class="nav-text">Validasi Barokah</span>
                </a>
            </li>

            <li class="{{ Request::is('validasi/audit-tunjangan*') || Request::is('audit-tunjangan*') ? 'mm-active' : '' }}">
                <a class="{{ Request::is('validasi/audit-tunjangan*') || Request::is('audit-tunjangan*') ? 'mm-active' : '' }}" href="{{ url('/validasi/audit-tunjangan') }}" aria-expanded="false">
                    <i class="fas fa-user-shield"></i>
                    <span class="nav-text">Audit Tunjangan Ganda</span>
                </a>
            </li>

            <li class="{{ Request::is('payroll*') || Request::is('Payroll*') ? 'mm-active' : '' }}">
                <a class="{{ Request::is('payroll*') || Request::is('Payroll*') ? 'mm-active' : '' }}" href="{{ url('/payroll') }}" aria-expanded="false">
                    <i class="fas fa-cash-register"></i>
                    <span class="nav-text">PayRoll</span>
                </a>
            </li>
        
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
                    <li><a href="{{ url('/laporan/satpam') }}">Laporan Satpam</a></li>
                    <li><a href="{{ url('/laporan/per-umana') }}">Per Umana</a></li>
                </ul>
            </li>
            <li><a href="#" aria-expanded="false">
                    <i class="fas fa-cog"></i>
                    <span class="nav-text">Pengaturan</span>
                </a>
            </li>
        </ul>
        <div class="plus-box text-center">
            <h4 class="fs-18 font-w600 mb-4">Butuh Bantuan?</h4>
            <a href="#" class="btn btn-primary btn-rounded">Hubungi Support</a>
        </div>
        <div class="copyright">
            <p><strong>E-Barokah Payroll</strong> © 2026 All Rights Reserved</p>
        </div>
    </div>
</div>
