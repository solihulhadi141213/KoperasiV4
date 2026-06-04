<aside id="sidebar" class="sidebar menu_background">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?php if($Page==""){echo "";}else{echo "collapsed";} ?>" href="index.php">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <hr>
        
        <!-- Master Data -->
        <li class="nav-heading">Master Data</li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="Anggota"){echo "collapsed";} ?>" href="index.php?Page=Anggota">
                <i class="bi bi-people"></i>
                <span>Anggota</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="JenisSimpanan"){echo "collapsed";} ?>" href="index.php?Page=JenisSimpanan">
                <i class="bi bi-wallet"></i>
                <span>Jenis Simpanan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="JenisPinjaman"){echo "collapsed";} ?>" href="index.php?Page=JenisPinjaman">
                <i class="bi bi-bank"></i>
                <span>Jenis Pinjaman</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="Supplier"){echo "collapsed";} ?>" href="index.php?Page=Supplier">
                <i class="bi bi-truck"></i>
                <span>Supplier</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link 
                <?php 
                    if(
                        $Page == "KategoriHarga"||
                        $Page == "Barang"||
                        $Page == "BarangExpired"||
                        $Page == "Diskon"||
                        $Page == "StockOpename"
                    ){echo "";}else{echo "collapsed";} 
                ?>
            " data-bs-target="#icons2-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-box-seam"></i>
                <span>Barang</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="icons2-nav" class="nav-content collapse <?php if($Page=="KategoriHarga"||$Page=="Barang"||$Page=="BarangExpired"||$Page=="StockOpename"||$Page=="Supplier"){echo "show";} ?>" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=KategoriHarga" class="<?php if($Page=="KategoriHarga"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Kategori Harga</span>
                    </a>
                </li>
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
                    <a href="index.php?Page=Diskon" class="<?php if($Page=="Diskon"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Diskon</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=StockOpename" class="<?php if($Page=="StockOpename"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Stock Opename</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="BiayaOperasional"){echo "collapsed";} ?>" href="index.php?Page=BiayaOperasional">
                <i class="bi bi-car-front"></i>
                <span>Biaya Operasional</span>
            </a>
        </li>

        <hr>

        <!-- Transaksi -->
        <li class="nav-heading">Transaksi</li>
        
        <li class="nav-item">
            <a class="nav-link 
                <?php 
                    if(
                        $Page == "SimpananPokok"||
                        $Page == "SimpananWajib"||
                        $Page == "SimpananSukarela"||
                        $Page == "SimpananLainnya"
                    ){echo "";}else{echo "collapsed";} 
                ?>" 
                data-bs-target="#simpanan-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-wallet"></i> <span>Simpanan</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="simpanan-nav" class="nav-content collapse 
                <?php 
                    if(
                        $Page == "SimpananPokok"||
                        $Page == "SimpananWajib"||
                        $Page == "SimpananSukarela"||
                        $Page == "SimpananLainnya" ||
                        $Page == "PenarikanSimpanan" 
                    ){echo "show";} 
                ?>
            " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=SimpananPokok" class="<?php if($Page=="SimpananPokok"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Simpanan Pokok</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=SimpananWajib" class="<?php if($Page=="SimpananWajib"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Simpanan Wajib</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=SimpananSukarela" class="<?php if($Page=="SimpananSukarela"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Simpanan Sukarela</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=SimpananLainnya" class="<?php if($Page=="SimpananLainnya"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Simpanan Lainnya</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=PenarikanSimpanan" class="<?php if($Page=="PenarikanSimpanan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Penarikan Simpanan</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link 
                <?php 
                    if(
                        $Page == "Pinjaman"||
                        $Page == "Tagihan"||
                        $Page == "Angsuran"
                    ){echo "";}else{echo "collapsed";} 
                ?>
            " data-bs-target="#pinjaman-nav" data-bs-toggle="collapse" href="javascript:void(0);">
                <i class="bi bi-bank"></i>
                <span>Pinjaman</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="pinjaman-nav" class="nav-content collapse 
            <?php 
                if(
                    $Page == "Pinjaman"||
                    $Page == "Tagihan"||
                    $Page == "Angsuran"
                ){echo "show";} 
            ?>
            " data-bs-parent="#sidebar-nav">
                <li>
                    <a href="index.php?Page=Pinjaman" class="<?php if($Page=="Pinjaman"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Pinjaman</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Tagihan" class="<?php if($Page=="Tagihan"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Tagihan</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?Page=Angsuran" class="<?php if($Page=="Angsuran"){echo "active";} ?>">
                        <i class="bi bi-circle"></i><span>Angsuran</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="Pembelianbarang"){echo "collapsed";} ?>" href="index.php?Page=Pembelianbarang">
                <i class="bi bi-cart-plus"></i>
                <span>Pembelian Barang</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="Pengeluaran"){echo "collapsed";} ?>" href="index.php?Page=Pengeluaran">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Pengeluaran</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="Penjualan"){echo "collapsed";} ?>" href="index.php?Page=Penjualan">
                <i class="bi bi-pc-display-horizontal"></i>
                <span>Penjualan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="UtangPiutang"){echo "collapsed";} ?>" href="index.php?Page=UtangPiutang">
                <i class="bi bi-cart-check"></i>
                <span>Utang - Piutang</span>
            </a>
        </li>

        <hr>

        <li class="nav-heading">Aksesibilitas</li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="AksesFitur"){echo "collapsed";} ?>" href="index.php?Page=AksesFitur">
                <i class="bi bi-app"></i>
                <span>Fitur Aplikasi</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="AksesEntitas"){echo "collapsed";} ?>" href="index.php?Page=AksesEntitas">
                <i class="bi bi-layers"></i>
                <span>Level/Entitas</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="Akses"){echo "collapsed";} ?>" href="index.php?Page=Akses">
                <i class="bi bi-person-circle"></i>
                <span>Akses Pengguna</span>
            </a>
        </li>

        <hr>

        <li class="nav-heading">Pengaturan</li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="SettingGeneral"){echo "collapsed";} ?>" href="index.php?Page=SettingGeneral">
                <i class="bi bi-gear"></i>
                <span>Pengaturan Umum</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="SettingEmailGateway"){echo "collapsed";} ?>" href="index.php?Page=SettingEmailGateway">
                <i class="bi bi-envelope"></i>
                <span>Email Gateway</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php if($Page!=="SettingWhatsapp"){echo "collapsed";} ?>" href="index.php?Page=SettingWhatsapp">
                <i class="bi bi-whatsapp"></i>
                <span>Whatsapp Gateway</span>
            </a>
        </li>

        <hr>

        <li class="nav-heading">Utility</li>
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
        <hr>
        <li class="nav-item">
            <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ModalLogout">
                <i class="bi bi-box-arrow-in-left"></i>
                <span>Keluar</span>
            </a>
        </li>
    </ul>
</aside> 