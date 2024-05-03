<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('changeDateFormat')) {
    function changeDateFormat($format = 'd-m-Y', $givenDate = null)
    {
        return date($format, strtotime($givenDate));
    }
}

class C_API_Pelanggan extends CI_Controller
{
    public function ApiPelanggan()
    {
        $request = $this->M_Pelanggan->DataPelanggan_API();
        echo json_encode($request);
    }
}
