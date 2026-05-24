<aside id="sidebar" class="sidebar menu_background">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?php if($Page==""){echo "";}else{echo "collapsed";} ?>" href="index.php">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page=="AksesFitur"||$Page=="AksesEntitas"||$Page=="Akses"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#akses-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-person"></i>
                <span>Akses</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="akses-nav" class="nav-content collapse <?php if($Page=="AksesFitur"||$Page=="AksesEntitas"||$Page=="Akses"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=AksesFitur" class="<?php if($Page=="AksesFitur"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Fitur Aplikasi</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=AksesEntitas" class="<?php if($Page=="AksesEntitas"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Level/Entitas</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Akses" class="<?php if($Page=="Akses"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Akses Pengguna</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page=="Anggota"||$Page=="AnggotaKeluarMasuk"||$Page=="RekapAnggota"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#anggota-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-people"></i>
                <span>Anggota</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="anggota-nav" class="nav-content collapse <?php if($Page=="Anggota"||$Page=="AnggotaKeluarMasuk"||$Page=="RekapAnggota"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=Anggota" class="<?php if($Page=="Anggota"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Anggota</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=AnggotaKeluarMasuk" class="<?php if($Page=="AnggotaKeluarMasuk"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Keluar & Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=RekapAnggota" class="<?php if($Page=="RekapAnggota"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Rekap Anggota</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page=="JenisSimpanan"||$Page=="SimpananWajib"||$Page=="Tabungan"||$Page=="RekapSimpanan"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#simpanan-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-wallet"></i>
                <span>Simpanan</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="simpanan-nav" class="nav-content collapse <?php if($Page=="JenisSimpanan"||$Page=="SimpananWajib"||$Page=="Tabungan"||$Page=="RekapSimpanan"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=JenisSimpanan" class="<?php if($Page=="JenisSimpanan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Jenis Simpanan</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=SimpananWajib" class="<?php if($Page=="SimpananWajib"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Simpanan</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Tabungan" class="<?php if($Page=="Tabungan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Log Simpanan & Penarikan</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=RekapSimpanan" class="<?php if($Page=="RekapSimpanan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Rekap Simpanan</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page=="JenisPinjaman"||$Page=="Pinjaman"||$Page=="Tagihan"||$Page=="RekapPinjaman"||$Page=="PotonganAnggota"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#pinjaman-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-bank"></i>
                <span>Pinjaman</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="pinjaman-nav" class="nav-content collapse <?php if($Page=="JenisPinjaman"||$Page=="Pinjaman"||$Page=="Tagihan"||$Page=="RekapPinjaman"||$Page=="PotonganAnggota"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=JenisPinjaman" class="<?php if($Page=="JenisPinjaman"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Jenis Pinjaman</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Pinjaman" class="<?php if($Page=="Pinjaman"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Sesi Pinjaman</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Tagihan" class="<?php if($Page=="Tagihan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Tagihan/Tunggakan</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=RekapPinjaman" class="<?php if($Page=="RekapPinjaman"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Rekap Pinjaman</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=PotonganAnggota" class="<?php if($Page=="PotonganAnggota"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Potongan Anggota</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page=="Barang"||$Page=="BarangExpired"||$Page=="StockOpename"||$Page=="Supplier"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#icons2-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-box-seam"></i>
                <span>Barang</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="icons2-nav" class="nav-content collapse <?php if($Page=="Barang"||$Page=="BarangExpired"||$Page=="StockOpename"||$Page=="Supplier"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=Barang" class="<?php if($Page=="Barang"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Master Barang</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=BarangExpired" class="<?php if($Page=="BarangExpired"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Batch & Expired</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=StockOpename" class="<?php if($Page=="StockOpename"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Stock Opename</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Supplier" class="<?php if($Page=="Supplier"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Supplier</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page=="JenisTransaksi"||$Page=="RekapTransaksi"||$Page=="Transaksi"||$Page=="TransaksiJualBeli"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#transaksi-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-cash-coin"></i>
                <span>Transaksi Operasional</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="transaksi-nav" class="nav-content collapse <?php if($Page=="JenisTransaksi"||$Page=="RekapTransaksi"||$Page=="Transaksi"||$Page=="TransaksiJualBeli"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=JenisTransaksi" class="<?php if($Page=="JenisTransaksi"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Kategori Operasional</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Transaksi" class="<?php if($Page=="Transaksi"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Transaksi Operasional</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=RekapTransaksi" class="<?php if($Page=="RekapTransaksi"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Rekap Transaksi</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page=="Penjualan"||$Page=="Pembelian"||$Page=="RekapJualBeli"||$Page=="UtangPiutang"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#transaksi_jual_beli-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-cart-check"></i>
                <span>Transaksi Jual Beli</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="transaksi_jual_beli-nav" class="nav-content collapse <?php if($Page=="Penjualan"||$Page=="Pembelian"||$Page=="RekapJualBeli"||$Page=="UtangPiutang"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=Penjualan" class="<?php if($Page=="Penjualan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Transaksi Penjualan</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Pembelian" class="<?php if($Page=="Pembelian"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Transaksi Pembelian</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=RekapJualBeli" class="<?php if($Page=="RekapJualBeli"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Rekap Jual/Beli</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=UtangPiutang" class="<?php if($Page=="UtangPiutang"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Utang/Piutang</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page=="Pembayaran"||$Page=="AkunPerkiraan"||$Page=="Jurnal"||$Page=="BagiHasil"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#keuangan-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-gem"></i>
                <span>Keuangan</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="keuangan-nav" class="nav-content collapse <?php if($Page=="Pembayaran"||$Page=="AkunPerkiraan"||$Page=="Jurnal"||$Page=="BagiHasil"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=BagiHasil" class="<?php if($Page=="BagiHasil"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Bagi Hasil (SHU)</span>
                    </a>
                </li>    
                <li>
                    <a href="index.php?Page=AkunPerkiraan" class="<?php if($Page=="AkunPerkiraan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Akun Perkiraan</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Jurnal" class="<?php if($Page=="Jurnal"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Jurnal Keuangan</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page=="SimpanPinjam"||$Page=="BukuBesar"||$Page=="NeracaSaldo"||$Page=="LabaRugi"||$Page=="RiwayatSimpanPinjam"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-bar-chart"></i><span>Laporan</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="charts-nav" class="nav-content collapse <?php if($Page=="SimpanPinjam"||$Page=="BukuBesar"||$Page=="NeracaSaldo"||$Page=="LabaRugi"||$Page=="RiwayatSimpanPinjam"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=SimpanPinjam" class="<?php if($Page=="SimpanPinjam"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Simpan-Pinjam</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=BukuBesar" class="<?php if($Page=="BukuBesar"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Buku Besar</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=NeracaSaldo" class="<?php if($Page=="NeracaSaldo"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Neraca saldo</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=LabaRugi" class="<?php if($Page=="LabaRugi"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Laba Rugi</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=RiwayatSimpanPinjam" class="<?php if($Page=="RiwayatSimpanPinjam"){echo "active";} ?>">
                    <i class="bi bi-circle"></i><span>Riwayat Anggota</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page=="SettingGeneral"||$Page=="SettingEmail"||$Page=="AutoJurnal"){echo "";}else{echo "collapsed";} ?>" data-bs-target="#components-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-gear"></i>
                    <span>Pengaturan</span><i class="bi bi-chevron-down ms-auto">
                </i>
            </a>
            <ul id="components-nav" class="nav-content collapse <?php if($Page=="SettingGeneral"||$Page=="SettingEmail"||$Page=="AutoJurnal"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=SettingGeneral" class="<?php if($Page=="SettingGeneral"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Pengaturan Umum</span>
                    </a>
                </li> 
                <li>
                    <a href="index.php?Page=AutoJurnal" class="<?php if($Page=="AutoJurnal"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Auto Jurnal</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=SettingEmail" class="<?php if($Page=="SettingEmail"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Email Gateway</span>
                    </a>
                </li> 
            </ul>
        </li>
        <li class="nav-heading">Fitur Lainnya</li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="Aktivitas"){echo "collapsed";} ?>" href="index.php?Page=Aktivitas&Sub=AktivitasUmum">
                <i class="bi bi-circle"></i>
                <span>Log Aktivitas</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="Help"){echo "collapsed";} ?>" href="index.php?Page=Help&Sub=HelpData">
                <i class="bi bi-question"></i>
                <span>Dokumentasi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalLogout">
                <i class="bi bi-box-arrow-in-left"></i>
                <span>Keluar</span>
            </a>
        </li>
    </ul>
</aside> 