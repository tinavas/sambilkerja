<?php
	class Job extends CI_model {

		function __construct() {
			parent::__construct();
		}

		function insert($data) {
			$this->db->insert('job_post', $data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}

		function insert_skill($data) {
			$this->db->insert('job_req_skill', $data);
		}

		function record_count() {
			return $this->db->count_all('job_post');
		}

		function get_all($limit,$start) {
			$this->db->select('
				p.id_post as id_post, 
				p.post_title as post_title,
				p.id_company as id_company,
				ci.avatar as avatar,
				c.company_name as company_name,
				p.id_job_category as id_job_category,
				s.sub_category_name as sub_category_name,
				ca.category_name as category_name,
				p.description as description,
				p.salary as salary,
				p.file as file,
				p.file_desc as file_desc,
				p.created_time as created_time,
				p.deadline as deadline
				');
			$this->db->from('job_post as p');
			$this->db->join('company as c', 'p.id_company = c.id_company');
			$this->db->join('job_sub_categories as s', 'p.id_job_category = s.id_sub_category');
			$this->db->join('job_categories as ca', 's.id_category = ca.id_category');
			$this->db->join('c_identity as ci', 'c.id_company = ci.id_company');

			$this->db->limit($limit,$start);
			$query = $this->db->get();
		    if ($query->num_rows() > 0) {
		        return $query->result();
		    }
		    else {
		    	return false;
		    }
		}

		function get_req_skill($id_post) {
			$this->db->select('
				j.id_post as id_post,
				j.id_skill as id_skill,
				s.skill_name as skill_name
				');
			$this->db->from('job_req_skill as j');
			$this->db->join('skills_set as s','j.id_skill = s.id_skill');
			$this->db->where('id_post',$id_post);

			$query = $this->db->get();
		    if ($query->num_rows() > 0) {
		        return $query->result();
		    }
		    else {
		    	return false;
		    }
		}

		function get_all_cat_w_sub() {
			$this->db->select('
				s.id_sub_category as id_sub_category,
				s.sub_category_name as sub_category_name,
				s.id_category as id_category,
				c.category_name as category_name
				');
			$this->db->from('job_sub_categories as s');
			$this->db->join('job_categories as c', 's.id_category = c.id_category');

			$query = $this->db->get();
		    if ($query->num_rows() > 0) {
		        return $query->result();
		    }
		    else {
		    	return false;
		    }
		}

		function get_all_cats() {
			$this->db->select('*');
			$this->db->from('job_categories');

			$query = $this->db->get();
		    if ($query->num_rows() > 0) {
		        return $query->result();
		    }
		    else {
		    	return false;
		    }
		}

		function get_subs($cur_id) {
			$this->db->select('
				s.id_sub_category as id_sub_category, 
				s.sub_category_name as sub_category_name');
			$this->db->from('job_sub_categories as s');
			$this->db->join('job_categories as c', 's.id_category = c.id_category');
			$this->db->where('s.id_category',$cur_id);

			$query = $this->db->get();
		    if ($query->num_rows() > 0) {
		        return $query->result();
		    }
		    else {
		    	return false;
		    }
		}
	}
?>