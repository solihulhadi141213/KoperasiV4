<div class="row mb-3">
                        <div class="col-md-3">
                            <label for="akses">Nama Entitias</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="akses" id="akses">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="keterangan">Keterangan</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="keterangan" id="keterangan">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <td align="center" colspan="4"><b>PENGATURAN IJIN AKSES FITUR</b></td>
                                        </tr>
                                        <tr>
                                            <td align="center"><b>No</b></td>
                                            <td colspan="2" align="center"><b>Kategori/Fitur</b></td>
                                            <td align="center"><b>Check</b></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            //Tampilkan Kategori Ijin Akses
                                            $jml_data = mysqli_num_rows(mysqli_query($Conn, "SELECT*FROM akses_fitur"));
                                            if(empty($jml_data)){
                                                echo '<tr colspan="4">';
                                                echo '  <td class="text-center text-danger">Belum ada data fitur aplikasi, silahkan tambahkan fitur aplikasi terlebih dulu</td>';
                                                echo '</tr>';
                                            }else{
                                                $no_kategori=1;
                                                $QryKategoriFitur = mysqli_query($Conn, "SELECT DISTINCT kategori FROM akses_fitur ORDER BY kategori ASC");
                                                while ($DataKategori = mysqli_fetch_array($QryKategoriFitur)) {
                                                    $kategori= $DataKategori['kategori'];
                                                    echo '<tr>';
                                                    echo '  <td align="center"><b>'.$no_kategori.'</b></td>';
                                                    echo '  <td align="left" colspan="2"><label for="IdKategori'.$no_kategori.'"><b>'.$kategori.'</b></label></td>';
                                                    echo '  <td align="center">';
                                                    echo '      <input type="checkbox" class="KelasKategori" id="IdKategori'.$no_kategori.'" value="'.$no_kategori.'">';
                                                    echo '  </td>';
                                                    echo '</tr>';
                                                    $no_fitur=1;
                                                    $QryFitur = mysqli_query($Conn, "SELECT * FROM akses_fitur WHERE kategori='$kategori' ORDER BY nama ASC");
                                                    while ($DataFitur = mysqli_fetch_array($QryFitur)) {
                                                        $id_akses_fitur= $DataFitur['id_akses_fitur'];
                                                        $nama= $DataFitur['nama'];
                                                        $keterangan= $DataFitur['keterangan'];
                                                        $kode= $DataFitur['kode'];
                                                        echo '<tr>';
                                                        echo '  <td align="center"></td>';
                                                        echo '  <td align="center"><label for="IdFitur'.$id_akses_fitur.'">'.$no_kategori.'.'.$no_fitur.'</label></td>';
                                                        echo '  <td align="left"><label for="IdFitur'.$id_akses_fitur.'">'.$nama.'</label><br><code class="text text-grayish">'.$keterangan.'</code></td>';
                                                        echo '  <td align="center">';
                                                        echo '      <input type="checkbox" name="rules[]" class="ListFitur" kategori="'.$no_kategori.'" id="IdFitur'.$id_akses_fitur.'" value="'.$id_akses_fitur.'">';
                                                        echo '  </td>';
                                                        echo '</tr>';
                                                        $no_fitur++;
                                                    }
                                                    $no_kategori++;
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>