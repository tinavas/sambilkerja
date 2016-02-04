<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jobs extends CI_Controller {

	private $username = null; 
	private $mem_id = null;
	private $mem_type = null;
	private $fullname = null;

	public function __construct() {
		parent::__construct();
		$this->load->model('Company');
		$this->load->model('Job');
		$this->load->model('Skill');
		$this->load->model('Location');

		if ($this->session->userdata('logged') != true) {
			$sess_data = array('last_page' => current_url());
			$this->session->set_userdata($sess_data);
		} else {
			$this->username = $this->session->userdata('logged');
			$this->mem_id = $this->session->userdata('mem_id');
			$this->mem_type = $this->session->userdata('mem_type');
		}
	}

	public function index()
	{
		redirect('Jobs/lists','refresh');
	}

	public function detail($id_post,$post_name)
	{
		$post_data = $this->Job->get_post($id_post);
		$req_skill = $this->Job->get_req_skill($id_post);
		$poster_data = $this->Job->get_poster($id_post);
		foreach ($poster_data as $poster) {}
		foreach ($post_data as $post) {}
		$basic_data = $this->Company->get('id_company',$poster->id_company);
		$ident_data = $this->Company->get_ident($poster->id_company);
		$loc_data = $this->Company->get_loc($poster->id_company);
		$post_count = $this->Job->get_per_comp_count($poster->id_company);

		$data = array(
			'title' => $post->post_title." | SambilKerja.com",
			'post_data' => $post_data,
			'req_skill' => $req_skill,
			'basic_data' => $basic_data,
			'ident_data' => $ident_data,
			'loc_data' => $loc_data,
			'post_count' => $post_count
			);
		$this->load->view('html_head', $data);
		// $this->load->view('content/modal', $data);
		$this->load->view('header', $data);
		$this->load->view('content/job-detail', $data);
		$this->load->view('footer', $data);		
	}

	public function lists()
	{
		//PAGINATION SETUP
		$config = array();
        $config["base_url"] = base_url()."Jobs/lists/";
        $config["total_rows"] = $this->Job->record_count();
        $config['per_page'] = "10";
        $config["uri_segment"] = 3;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;

        //PAGINATION VIEW
        $config['full_tag_open'] = '<ul class="pagination text-center">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['next_link'] = false;
        $config['prev_link'] = false;
        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $order_by = '';
        $sort = '';
        
        if (!empty($this->input->post('sort_by'))) {
        	$order_by = $this->input->post('sort_by');
        	$sort = $this->input->post('sort_method');
        	$sess_array = array('order_by' => $order_by, 'sort' => $sort);
        	$this->session->set_userdata($sess_array);
        }
    
        $cat_data = $this->Job->get_all_cats();
		$prov_data = $this->Location->get_all_prov(); 

    	$data = array(
    		'title' => "Lowongan kerja yang tersedia | SambilKerja.com",
    		'job_data' => $this->Job->get_all($config["per_page"], $page, $order_by, $sort),
    		'links' => $this->pagination->create_links(),
    		'cat_data' => $cat_data,
    		'prov_data' => $prov_data
    		);
		$this->load->view('html_head', $data);
		$this->load->view('header', $data);
		$this->load->view('content/job-list', $data);
		$this->load->view('footer', $data);
	}

	public function search()
	{
		$keyword = ($this->input->post('search')) ? $this->input->post('search') : "NIL";

		$keyword = ($this->uri->segment(3)) ? $this->uri->segment(3) : $keyword;

		//PAGINATION SETUP
		// $config = array();
  //       $config["base_url"] = site_url()."Jobs/search/".$keyword;
  //       $config["total_rows"] = $this->Job->search_record_count($keyword);
  //       $config['per_page'] = "1";
  //       $config["uri_segment"] = 3;
  //       $config['use_page_numbers'] = TRUE;
  //       $config['page_query_string'] = TRUE;
        // $config['enable_query_strings'] = TRUE;

        $config['base_url'] = site_url("Jobs/search/$keyword/");
        $config['total_rows'] = $this->Job->search_record_count($keyword);
        $config['per_page'] = "1";
        $config["uri_segment"] = 4;
        $choice = $config["total_rows"]/$config["per_page"];
        $config["num_links"] = floor($choice);
        $config['use_page_numbers'] = true;
        $config['page_query_string'] = false;
        $config['enable_query_strings'] = false;



        //PAGINATION VIEW
        $config['full_tag_open'] = '<ul class="pagination text-center">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['next_link'] = false;
        $config['prev_link'] = false;
        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        // $config["first_url"] = base_url()."Jobs/search/page/1";

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        $order_by = '';
        $sort = '';
        
        if (!empty($this->input->post('sort_by'))) {
        	$order_by = $this->input->post('sort_by');
        	$sort = $this->input->post('sort_method');
        	$sess_array = array('order_by' => $order_by, 'sort' => $sort);
        	$this->session->set_userdata($sess_array);
        }

        $data = array(
    		'title' => "Lowongan kerja yang tersedia | SambilKerja.com",
    		'job_data' => $this->Job->search_all($config["per_page"], $page, $keyword, $order_by, $sort),
    		'links' => $this->pagination->create_links(),
    		'keyword' => $keyword
    		);

		$this->load->view('html_head', $data);
		$this->load->view('header', $data);
		$this->load->view('content/job-list', $data);
		$this->load->view('footer', $data);
	}

	public function refine_search() {
		// $keyword = ($this->input->post('search')) ? $this->input->post('search') : "NIL";

		// $keyword = ($this->uri->segment(3)) ? $this->uri->segment(3) : $keyword;

		// //PAGINATION SETUP
		// $config = array();
		// // $config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-\=';
  //       $config["base_url"] = base_url()."Jobs/search/";
  //       $config["total_rows"] = $this->Job->search_record_count($keyword);
  //       $config['per_page'] = "8";
  //       $config["uri_segment"] = 3;
  //       $config['use_page_numbers'] = TRUE;

  //       //PAGINATION VIEW
  //       $config['full_tag_open'] = '<ul class="pagination text-center">';
  //       $config['full_tag_close'] = '</ul>';
  //       $config['first_link'] = false;
  //       $config['last_link'] = false;
  //       $config['next_link'] = false;
  //       $config['prev_link'] = false;
  //       $config['cur_tag_open'] = '<li class="active"><a href="">';
  //       $config['cur_tag_close'] = '</a></li>';
  //       $config['num_tag_open'] = '<li>';
  //       $config['num_tag_close'] = '</li>';

  //       $this->pagination->initialize($config);
  //       $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

  //       $order_by = '';
  //       $sort = '';
        
  //       if (!empty($this->input->post('sort_by'))) {
  //       	$order_by = $this->input->post('sort_by');
  //       	$sort = $this->input->post('sort_method');
  //       }

  //       $data = array(
  //   		'title' => "Lowongan kerja yang tersedia | SambilKerja.com",
  //   		'job_data' => $this->Job->search_all($config["per_page"], $page, $keyword, $order_by, $sort),
  //   		'links' => $this->pagination->create_links(),
  //   		'order_by' => $order_by,
  //   		'sort' => $sort
  //   		);
		// $this->load->view('html_head', $data);
		// $this->load->view('header', $data);
		// $this->load->view('content/job-list', $data);
		// $this->load->view('footer', $data);
	}

	public function new_job()
	{
		if ($this->session->userdata('logged') != false && $this->mem_type == 'C') { //IF USER COMPANY LOGIN
			$cat_data = $this->Job->get_all_cats();
			$skill_sets = $this->Skill->get_all();
			$prov_data = $this->Location->get_all_prov(); 
			$edit = false; 

			$data = array(
				'title' => "Buka Lowongan baru | SambilKerja.com",
				'sub_title' => 'Buka Lowongan Baru',
				'sub_subtitle' => 'Mulai Lowongan Baru',
				'cat_data' => $cat_data,
				'skill_sets' => $skill_sets,
				'prov_data' => $prov_data,
				'edit' => $edit
				);
			$this->load->view('html_head', $data);
			$this->load->view('header', $data);
			$this->load->view('content/new-job', $data);
			$this->load->view('footer', $data);	
		}
		else {
			redirect('errors/Page_not_found','refresh');
		}
	}

	public function edit_job($id_post) 
	{
		if ($this->session->userdata('logged') != false && $this->mem_type == 'C') { //IF USER COMPANY LOGIN
			$cat_data = $this->Job->get_all_cats();
			$skill_sets = $this->Skill->get_all();
			$prov_data = $this->Location->get_all_prov(); 
			
			$post_data = $this->Job->get_post($id_post);
			$req_skill_data = $this->Job->get_req_skill($id_post);
			$edit = true;

			$data = array(
				'title' => "Edit Lowongan | SambilKerja.com",
				'sub_title' => "Edit Lowongan",
				'sub_subtitle' => "Perbarui Lowongan Lama",
				'cat_data' => $cat_data,
				'skill_sets' => $skill_sets,
				'prov_data' => $prov_data,
				'post_data' => $post_data,
				'req_skill_data' => $req_skill_data,
				'edit' => $edit,
				'id_post' => $id_post
				);

			$this->load->view('html_head', $data);
			$this->load->view('header', $data);
			$this->load->view('content/new-job', $data);
			$this->load->view('footer', $data);	
		}
		else {
			redirect('errors/Page_not_found','refresh');
		}
	}

	function updating($id_post)
	{
		if (!empty($this->mem_id) && $this->mem_type == 'C' && $this->input->post('ins_job') !== false) {
			if ($_FILES['file']['size'] == 0) {
				$file_name = $this->input->post('cur_file');	
			} else {
				$config['upload_path'] = './files/loker/';
				$new_name = $this->username.' - '.$this->input->post('post_title');
				$config['file_name'] = $new_name;
				$config['allowed_types'] = 'pdf|jpg|ppt|pptx|doc|docx';
				$config['overwrite'] = FALSE;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				$upload = $this->upload->do_upload('file');	
				$upload_data = $this->upload->data(); //UPLOAD DATA AFTER UPLOADING
				$file_name = $upload_data['file_name']; //RETRIEVING FILE NAME
			} 

			$data = array( //ARRAY FOR INPUTS FROM FORM
				'post_title' => $this->input->post('post_title'),
				'id_job_category' => $this->input->post('category'),
				'description' => $this->input->post('jobdesc'),
				'file' => $file_name,
				'file_desc' => $this->input->post('file_desc'),
				'deadline' => $this->input->post('deadline'),
				'salary' => $this->input->post('salary'),
				'id_location' => $this->input->post('location'),
			 	);
			$update = $this->Job->update($data,$id_post); // UPDATING

			$cleaning = $this->Job->delete_skill($id_post);
			if (false !== $this->input->post('skills')) {
				foreach ($this->input->post('skills') as $row ) {
					$data = array(
						'id_post' => $id_post,
						'id_skill' => $row
						 );
					$ins_skill = $this->Job->insert_skill($data); 
				}
			}

			$this->session->set_flashdata(
					'msg', 
					'<b>Lowongan pekerjaan</b> berhasil diperbarui!'
					);
			redirect('Members/'.$this->username);
		}		
	}

	function inserting() {
		if (!empty($this->mem_id) && $this->mem_type == 'C' && $this->input->post('ins_job') !== false) {
			$config['upload_path'] = './files/loker/';
			$new_name = $this->username.' - '.$this->input->post('post_title');
			$config['file_name'] = $new_name;
			$config['overwrite'] = FALSE;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			$upload = $this->upload->do_upload('file');	
			$upload_data = $this->upload->data(); //UPLOAD DATA AFTER UPLOADING
			$file_name = $upload_data['file_name']; //RETRIEVING FILE NAME

			$data = array( //ARRAY FOR INPUTS FROM FORM
				'id_company' => $this->mem_id,
				'post_title' => $this->input->post('post_title'),
				'id_job_category' => $this->input->post('category'),
				'description' => $this->input->post('jobdesc'),
				'file' => $file_name,
				'file_desc' => $this->input->post('file_desc'),
				'deadline' => $this->input->post('deadline'),
				'salary' => $this->input->post('salary'),
				'id_location' => $this->input->post('location'),
			 	);
			$insert = $this->Job->insert($data); // INSERTING INTO DATABASE

			if (false !== $this->input->post('skills')) {
				foreach ($this->input->post('skills') as $row ) {
					$data = array(
						'id_post' => $insert,
						'id_skill' => $row
						 );
					$ins_skill = $this->Job->insert_skill($data); 
				}
			}

			$this->session->set_flashdata(
					'msg', 
					'<b>Lowongan pekerjaan</b> berhasil dibuat!'
					);
			redirect('Members/'.$this->username);
		}
	}

	function removing($id_post) {
		$poster = $this->Job->get_poster($id_post);
		foreach ($poster as $poster ) {}
		if ($this->mem_id == $poster->id_company) {
			$rem_skill = $this->Job->delete_skill($id_post);
			$rem_post = $this->Job->delete($id_post);
			
			$this->session->set_flashdata(
						'msg', 
						'<b>Lowongan pekerjaan</b> berhasil dihapus!'
						);
			redirect('Members/'.$this->username);
		}
	}

	function removing_file($file,$id_post) {
		$data = array('file' => '', 'file_desc' => '');
		$remove = $this->Job->update($data,$id_post);
		unlink("./files/loker/".$file);
		$this->session->set_flashdata(
					'msg', 
					'<b>File pendukung</b> berhasil dihapus!'
					);
		redirect('Jobs/edit_job/'.$id_post,'refresh');
	}

}