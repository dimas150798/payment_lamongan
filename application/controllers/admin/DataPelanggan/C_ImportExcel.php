<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

class C_ImportExcel extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('email') == null) {

            // Notifikasi Login Terlebih Dahulu
            $this->session->set_flashdata('BelumLogin_icon', 'error');
            $this->session->set_flashdata('BelumLogin_title', 'Login Terlebih Dahulu');

            redirect('C_FormLogin');
        }
    }

    public function index()
    {
        // Memanggil mysql dari model
        $data['DataPaket']      = $this->M_Paket->DataPaket();
        $data['DataArea']       = $this->M_Area->DataArea();
        $data['DataSales']      = $this->M_Sales->DataSales();
        $data['DataExcel']      = $this->M_ImportExcel->DataExcel();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebarAdmin', $data);
        $this->load->view('admin/DataPelanggan/V_ImportExcel', $data);
        $this->load->view('template/V_FooterImportExcel', $data);
    }

    public function  ImportExcel()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $upload_status = $this->uploadDoc();
            if ($upload_status != false) {
                $inputFileName = 'assets/uploads/imports/' . $upload_status;
                $inputTileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputTileType);
                $spreadsheet = $reader->load($inputFileName);
                $sheet = $spreadsheet->getSheet(0);
                $count_Rows = 0;

                $counter = 0;
                foreach ($sheet->getRowIterator(6) as $row) {
                    if (++$counter == 6) continue;
                    $Kode_Customer      = $spreadsheet->getActiveSheet()->getCell('B' . $row->getRowIndex());
                    $Phone_Customer     = $spreadsheet->getActiveSheet()->getCell('C' . $row->getRowIndex());
                    $Nama_Customer      = $spreadsheet->getActiveSheet()->getCell('D' . $row->getRowIndex());
                    $Nama_Paket         = $spreadsheet->getActiveSheet()->getCell('E' . $row->getRowIndex());
                    $Name_PPPOE         = $spreadsheet->getActiveSheet()->getCell('F' . $row->getRowIndex());
                    $Password_PPPOE     = $spreadsheet->getActiveSheet()->getCell('G' . $row->getRowIndex());
                    $Alamat_Customer    = $spreadsheet->getActiveSheet()->getCell('H' . $row->getRowIndex());
                    $Email_Customer     = $spreadsheet->getActiveSheet()->getCell('I' . $row->getRowIndex());
                    $Tanggal_Registrasi = $spreadsheet->getActiveSheet()->getCell('J' . $row->getRowIndex())->getFormattedValue();
                    $Tanggal_Terminated = $spreadsheet->getActiveSheet()->getCell('K' . $row->getRowIndex())->getFormattedValue();
                    $Nama_Area          = $spreadsheet->getActiveSheet()->getCell('L' . $row->getRowIndex());
                    $Nama_Sales         = $spreadsheet->getActiveSheet()->getCell('M' . $row->getRowIndex());

                    $this->session->set_userdata('name_pppoe', $Name_PPPOE);

                    $CheckDuplicate     = $this->M_Pelanggan->CheckDuplicatePelanggan($Name_PPPOE);

                    // Convert Date
                    $spreadsheet->getActiveSheet()->getStyle('K' . $row->getRowIndex())
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

                    $spreadsheet->getActiveSheet()->getStyle('L' . $row->getRowIndex())
                        ->getNumberFormat()
                        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

                    if ($CheckDuplicate->name_pppoe == $Name_PPPOE) {
                        // Notifikasi Gagal Import
                        $this->session->set_flashdata('ExcelGagal_icon', 'danger');
                        $this->session->set_flashdata('ExcelGagal_title', 'Terdapat Data Nama Yang Sama');

                        redirect('admin/DataPelanggan/C_ImportExcel');
                    } else {
                        if ($Tanggal_Terminated == NULL) {
                            $data = array(
                                'kode_customer'     => $Kode_Customer,
                                'phone_customer'    => $Phone_Customer,
                                'nama_customer'     => $Nama_Customer,
                                'nama_paket'        => $Nama_Paket,
                                'name_pppoe'        => $Name_PPPOE,
                                'password_pppoe'    => $Password_PPPOE,
                                'alamat_customer'   => $Alamat_Customer,
                                'email_customer'    => $Email_Customer,
                                'start_date'        => $Tanggal_Registrasi,
                                'nama_area'         => $Nama_Area,
                                'nama_sales'        => $Nama_Sales
                            );
                        } else {
                            $data = array(
                                'kode_customer'     => $Kode_Customer,
                                'phone_customer'    => $Phone_Customer,
                                'nama_customer'     => $Nama_Customer,
                                'nama_paket'        => $Nama_Paket,
                                'name_pppoe'        => $Name_PPPOE,
                                'password_pppoe'    => $Password_PPPOE,
                                'alamat_customer'   => $Alamat_Customer,
                                'email_customer'    => $Email_Customer,
                                'start_date'        => $Tanggal_Registrasi,
                                'stop_date'         => $Tanggal_Terminated,
                                'nama_area'         => $Nama_Area,
                                'nama_sales'        => $Nama_Sales
                            );
                        }
                        $this->db->insert('data_customer', $data);
                        $count_Rows++;

                        // Notifikasi Insert Data Berhasil
                        $this->session->set_flashdata('ExcelSuccess_icon', 'success');
                        $this->session->set_flashdata('ExcelSuccess_title', 'Insert Data Berhasil');

                        redirect('admin/DataPelanggan/C_DataPelanggan');
                    }
                }
            } else {
                // Notifikasi Insert Data Gagal
                $this->session->set_flashdata('ExcelGagal_icon', 'warning');
                $this->session->set_flashdata('ExcelGagal_title', 'Insert Data Gagal');

                redirect('admin/DataPelanggan/C_ImportExcel');
            }
        } else {
            // Notifikasi Insert Data Gagal
            $this->session->set_flashdata('ExcelGagal_icon', 'warning');
            $this->session->set_flashdata('ExcelGagal_title', 'Insert Data Gagal');

            redirect('admin/DataPelanggan/C_DataPelanggan');
        }
    }

    function uploadDoc()
    {
        $uploadPath = 'assets/uploads/imports/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, TRUE);
        }

        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = 'csv|xlsx|xls';
        $config['max_size'] = 1000000;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload('upload_excel')) {
            $fileData = $this->upload->data();
            $data['file_name'] = $fileData['file_name'];
            $this->db->insert('data_excel', $data);
            $insert_id = $this->db->insert_id();
            $_SESSION['lastid'] = $insert_id;

            return $fileData['file_name'];
        } else {
            return false;
        }
    }


    public function DeletePelanggan()
    {
        // Memanggil mysql dari model
        $data['DataPaket']      = $this->M_Paket->DataPaket();
        $data['DataArea']       = $this->M_Area->DataArea();
        $data['DataSales']      = $this->M_Sales->DataSales();
        $data['DataExcel']      = $this->M_ImportExcel->DataExcel_Delete();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebarAdmin', $data);
        $this->load->view('admin/DataPelanggan/V_ImportExcel_DeletePelanggan', $data);
        $this->load->view('template/V_FooterImportExcel', $data);
    }

    public function  ImportExcel_DeletePelanggan()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $upload_status = $this->uploadDoc_DeletePelanggan();
            if ($upload_status != false) {
                $inputFileName      = 'assets/uploads/imports/' . $upload_status;
                $inputTileType      = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                $reader             = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputTileType);
                $spreadsheet        = $reader->load($inputFileName);
                $sheetData          = $spreadsheet->getActiveSheet()->toArray();
            }

            for ($i = 5; $i < count($sheetData); $i++) {
                $code_client        = $sheetData[$i]['1'];
                $name               = $sheetData[$i]['2'];
                $phone              = $sheetData[$i]['3'];
                $nama_paket         = $sheetData[$i]['4'];
                $id_pppoe           = $sheetData[$i]['5'];
                $name_pppoe         = $sheetData[$i]['6'];
                $password_pppoe     = $sheetData[$i]['7'];
                $address            = $sheetData[$i]['8'];
                $email              = $sheetData[$i]['9'];
                $start_date         = $sheetData[$i]['10'];
                $nama_area          = $sheetData[$i]['11'];
                $nama_sales         = $sheetData[$i]['12'];
                $id_paket           = $sheetData[$i]['13'];
                $id_area            = $sheetData[$i]['14'];
                $id_sales           = $sheetData[$i]['15'];

                // Menyimpan data dalam array
                $data_customer = array(
                    'code_client'       => $code_client,
                    'phone'             => $phone,
                    'name'              => $name,
                    'id_paket'          => $id_paket,
                    'name_pppoe'        => $name_pppoe,
                    'password_pppoe'    => $password_pppoe,
                    'address'           => $address,
                    'email'             => $email,
                    'start_date'        => $start_date,
                    'id_area'           => $id_area,
                    'id_sales'          => $id_sales,
                );

                $api = connect();
                $api->comm('/ppp/secret/remove', [
                    ".id" => $id_pppoe,
                ]);
                $api->disconnect();

                // Kondisi delete menggunakan id_customer
                $idCustomer = array(
                    'name_pppoe'       => $name_pppoe
                );

                $this->M_CRUD->deleteData($idCustomer, 'client');


                echo "
                <script>history.go(-1);</script>
                ";
            }
        }
    }


    function uploadDoc_DeletePelanggan()
    {
        $uploadPath = 'assets/uploads/imports/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, TRUE);
        }

        $config['upload_path'] = $uploadPath;
        $config['allowed_types'] = 'csv|xlsx|xls';
        $config['max_size'] = 1000000;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload('upload_excel')) {
            $fileData = $this->upload->data();
            $data['file_name'] = $fileData['file_name'];
            $data['keterangan'] = 'Delete';

            $this->db->insert('data_excel', $data);

            $insert_id = $this->db->insert_id();
            $_SESSION['lastid'] = $insert_id;

            return $fileData['file_name'];
        } else {
            return false;
        }
    }
}
