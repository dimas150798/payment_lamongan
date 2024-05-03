<?php
$months = array(1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember');

if (!function_exists('changeDateFormat')) {
    function changeDateFormat($format = 'd-m-Y', $givenDate = null)
    {
        return date($format, strtotime($givenDate));
    }
}

?>

<div id="layoutSidenav_content">
    <main>

        <div class="menuatas" id="cetakKwitansi">
            <div class="row align-items-center justify-content-between">
                <div class="col-xl-6">
                    <i class="fa fa-list"></i> <b class="textmenuatas">Kwitansi</b>
                </div>
                <div class="col-12 col-xl-auto mt-2">
                    <a class="btn bg-danger text-white" onclick="history.back()"><img src="<?php echo base_url(); ?>vendor/bootstrap-icons/icons/backspace-fill.svg" alt="Bootstrap" ...> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="container-fluid px-4">
            <div class="card mb-3 mt-3 mx-auto">

                <div class="card-body">
                    <?php foreach ($DataPelanggan as $data) : ?>
                        <div class="receipt">
                            <div class="header">
                                <div class="logo-container">
                                    <img src="<?php echo base_url(); ?>assets/img/logonew.png" alt="Logo" class="logo">
                                </div>
                                <p class="address">Jl. Hos Cokroaminoto No 72, <br>
                                    Kanigaran, Kec. Kanigaran, Kota Probolinggo, Jawa Timur - 67213, Indonesia
                                </p>
                                <hr>
                            </div>
                            <div class="content">
                                <p class="info">Nama : <?php echo $data['name'] ?></p>
                                <p class="info">Telepon : <?php echo $data['phone'] ?></p>
                                <p class="info">Id Pelanggan : <?php echo $data['code_client'] ?></p>
                                <p class="info">Kode Area : KBS</p>
                                <hr>
                                <p class="payment">RINCIAN TRANSAKSI</p>
                                <p class="info">Bulan : <?php echo date('F', strtotime('2024-' . $data['bulan_transaksi'] . '-01')); ?></p>
                                <p class="info">Paket : <?php echo $data['nama_paket'] ?></p>
                                <p class="info">Harga : Rp. <?php echo number_format($data['harga_paket'], 0, ',', '.') ?></p>
                                <p class="info">Admin Fee : Rp. <?php echo number_format($data['biaya_admin'], 0, ',', '.') ?></p>
                                <p class="info">Total + PPN : Rp. <?php echo number_format($data['harga_paket'] + $data['biaya_admin'], 0, ',', '.') ?></p>
                                <p class="info">Tgl Transaksi : <?php echo date('d-m-Y H:i:s', strtotime($data['created_at'])); ?></p>
                                <p class="info">Status : <b>Sudah Lunas</b></p>
                                <p class="info">Admin By : <?php echo $data['nama_admin'] ?></p>
                                <hr>
                                <p class="notice">Simpan struk ini sebagai bukti telah melakukan pembayaran.</p>
                                <p class="cs">Customer Service : WA 0838-655-666-35</p>
                            </div>
                            <div class="footer">
                                <p class="thank-you">Terima Kasih</p>
                            </div>
                            <div class="row mt-4">
                                <div class="col-sm-12 d-flex justify-content-center">
                                    <button onclick="window.print();" type="submit" id="cetakKwitansi" class="btn btn-warning mt-2 btn-lg justify-content-end"> PRINT</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>

            </div>
        </div>

    </main>