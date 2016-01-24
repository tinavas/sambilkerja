<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jobs extends CI_Controller {

	public function index()
	{
    $data = array('title' => "Lowongan kerja yang tersedia | SambilKerja.com");
		$this->load->view('html_head', $data);
		$this->load->view('header', $data);
		$this->load->view('content/job-list', $data);
		$this->load->view('footer', $data);
	}

	function inserting() {
		$data = array( //ARRAY FOR INPUTS FROM FORM
			'company_name' => $this->input->post('company_name'),
			'username' => $this->input->post('username'),
			'email' => $this->input->post('email'),
			'password' => md5($this->input->post('password')) //MD5 ENCRYPTED
		 	);

		if (null !== $this->input->post('ins_job')) {
			$insert = $this->Job->insert($data); // INSERTING INTO DATABASE

			$sess_array = array('company_name' => $this->input->post('company_name'));
			$this->session->set_userdata($sess_array); //SESSION-ING THE FULLNAME REGRISTRATOR
			if ($this->session->userdata('company_name') !== false) {
				redirect('Main/regristration_success');
			}
		}
	}

}
