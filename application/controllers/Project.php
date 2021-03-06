<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {

	public function do_upload($id)
    {
        $config['upload_path']          = './uploads/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 12288;
        $config['file_name']			= $id.'-'.date('Ymdhis');
        //$config['max_width']            = 1024;
        //$config['max_height']           = 768;

        $this->form_validation->set_rules('Title','Title','required|max_length[50]');
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

        if ($this->form_validation->run())
        {
	        $this->load->library('upload', $config);

	        if ($this->upload->do_upload('userfile'))
	        {
	    		//$data['upload_data'] = $this->upload->data();

	            $image = $this->input->post();
	        	$image['FKProjectID'] = $id;
		        $image['FKCreatedByID'] = $this->session->userdata('PersonID');
		        $image['FileName'] = $this->upload->data('file_name');
				$this->load->model('PagesModel');
				$this->PagesModel->saveRecord($image,'Media');

				$this->session->set_flashdata('response','Image successfully saved.');

	            //$data['id'] = $id;
	         	//$data['error'] = "Image uploaded successfully!";
	        }
	        else
	        {
				$this->session->set_flashdata('response',$this->upload->display_errors());
	        }
        }

        return redirect("Project/images/{$id}");
    }

    public function updateImage($mediaID,$ProjectID,$mediaFileName)
    {
    	$config['upload_path']          = './uploads/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 12288;
        $config['file_name']			= $id.'-'.date('Ymdhis');
        //$config['max_width']            = 1024;
        //$config['max_height']           = 768;

        $this->form_validation->set_rules('Title','Title','required|max_length[50]');
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');

        if ($this->form_validation->run())
        {
        	unlink("uploads/".$mediaFileName);
	        $this->load->library('upload', $config);

	        if ($this->upload->do_upload('userfile'))
	        {
	            $image = $this->input->post();
	        	$image['FKProjectID'] = $id;
		        $image['FKCreatedByID'] = $this->session->userdata('PersonID');
		        $image['FileName'] = $this->upload->data('file_name');
				$this->load->model('PagesModel');
				$this->PagesModel->update($image,'Media',array('MediaID' => $mediaID));

				$this->session->set_flashdata('response','Image successfully updated.');
	        }
	        else
	        {
				$this->session->set_flashdata('response',$this->upload->display_errors());
	        }
        }
        return redirect("Project/images/{$ProjectID}");
    }

    public function images($id)
    {
    	$this->load->model('ProjectModel');
    	$data['images'] = $this->ProjectModel->viewProjectImages($id);
    	$data['ProjectName'] = $this->ProjectModel->getProjectName($id);

    	$this->load->view('project_image',$data);
    }

	public function new()
	{
		if($this->session->userdata('Role') != 6)
		{
			$this->load->model('PagesModel');
			$data['Regions'] = $this->PagesModel->getRegions();
			$data['Fields'] = $this->PagesModel->getFields();
			$data['Districts'] = $this->PagesModel->getRecords('districts');
			$data['Categories'] = $this->PagesModel->getCategory();
			$data['Coordinators'] = $this->PagesModel->getCoordinators();
			$data['Countries'] = $this->PagesModel->getRecords('countries');
			$this->load->view('project_new',$data);
		}
		else
		{
			$this->load->view('forbidden');
		}
	}

	public function list()
	{	
		//if($this->session->userdata('Username') != 'admin')
        //{
		$this->load->model('ProjectModel');
		$data['projects'] = $this->ProjectModel->getProjectsByUser($this->session->userdata('PersonID'));
		//}
		//else
		//{
		//	$this->load->model('PagesModel');
		//	$data['projects'] = $this->PagesModel->getRecords('projects');
		//}
		$this->load->view('project_list',$data);
	}

	public function view($id)
	{	
		$this->load->model('ProjectModel');
		$data['images'] = $this->ProjectModel->viewProjectImages($id);
		$data['project'] = $this->ProjectModel->viewProject($id);
		$data['projectCategories'] = $this->ProjectModel->getProjectCategories($id);
		$data['logs'] = $this->ProjectModel->getProjectLogs($id);
		$this->load->view('project_view',$data);
	}

	public function viewFromNotif($projID,$Logid)
	{	
		$this->load->model('ProjectModel');
		$this->ProjectModel->seen($Logid);
		return redirect("Project/view/{$projID}");
	}

	public function coordinator($id)
	{	
		$this->load->model('PagesModel');
		$data['Coordinators'] = $this->PagesModel->getCoordinators();
		$this->load->model('ProjectModel');
		$data['project'] = $this->ProjectModel->viewProject($id);
		$this->load->view('project_coordinator',$data);
	}

	public function edit($id)
	{	
		$this->load->model('ProjectModel');
		$data['projectCategories'] = $this->ProjectModel->getProjectCategories($id);
		
		$this->load->model('PagesModel');
		$data['project'] = $this->PagesModel->viewRecord('projects','ProjectID',$id);
		$data['Regions'] = $this->PagesModel->getRegions();
		$data['Countries'] = $this->PagesModel->getRecords('countries');
		$data['Fields'] = $this->PagesModel->getFields();
		$data['Categories'] = $this->PagesModel->getCategory();
		$data['Coordinators'] = $this->PagesModel->getCoordinators();
		$data['Districts'] = $this->PagesModel->getRecords('districts');
		$error ="";
		$this->load->view('project_edit',$data);
	}

	public function delete($id)
	{	
		$this->load->model('PagesModel');
		$data['Deleted'] = '1';
		$data['DateDeleted'] = date('Y-m-d H:i:s');
		$condition = array('ProjectID' => $id);

		if($this->PagesModel->update($data,'projects',$condition))
        {
            $this->session->set_flashdata('response','Project successfully deleted.');
        }
        else
        {
			$this->session->set_flashdata('response','Project was not deleted.');
        }

		return redirect("Project/list");
	}

	public function update($id)
	{	
		$this->form_validation->set_rules('ProjectName','Project Name','required');
		$this->form_validation->set_rules('VisionObjective','Vision','required');
		$this->form_validation->set_rules('Description','Description','required');
		//$this->form_validation->set_rules('FKCategoryID[]','Category','required');
		/*$this->form_validation->set_rules('FKRegionID','Region','required|min_length[1]');
		$this->form_validation->set_rules('FKFieldID','Field','required|min_length[1]');
		$this->form_validation->set_rules('FKDistrictID','District','requiredmin_length[1]');
		$this->form_validation->set_rules('EstimatedCost','Estimated Cost','required|decimal|min_length[1]');
		$this->form_validation->set_rules('RequestedProjectFunds','Requested Project Funds','required|decimal|min_length[1]');
		$this->form_validation->set_rules('Country','Country','required|min_length[1]');
		$this->form_validation->set_rules('IndividualCostPerDay','Individual CostPer Day','decimal|min_length[1]');
		$this->form_validation->set_rules('City','City','required');
		$this->form_validation->set_rules('FKSiteCoordinatorID','Coordinator','required|min_length[1]');*/
		$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
		$this->load->model('PagesModel');

		if ($this->form_validation->run())
        {
        	$data = $this->input->post();
        	if(!isset($data['YouthTeamsAccepted']))
			{
				$data['YouthTeamsAccepted'] = 0;
			}
	        unset($data['FKCategoryID']);
	        $data['ModifiedDate'] = date('Y-m-d H:i:s');

	        $condition = array('ProjectID' => $id);

        	if($this->PagesModel->update($data,'projects',$condition))
            {
            	//foreach ($_POST['FKCategoryID'] as $cat)
            	//{
            	//	$categories['FKCategoryID'] = $cat;
            	//	$this->PagesModel->saveRecord($categories,'mmprojectcategory');
            	//}

                $this->session->set_flashdata('response','Project successfully updated.');
            }
            else
            {
				$this->session->set_flashdata('response','Project was not updated.');
            }
        }
        return redirect("Project/view/{$id}");
	}

	public function save()
	{
		if($this->session->userdata('Role') == 1 or $this->session->userdata('Role') == 2)
		{
			$this->form_validation->set_rules('ProjectName','Project Name','required');
			$this->form_validation->set_rules('VisionObjective','Vision','required');
			$this->form_validation->set_rules('Description','Description','required');
			$this->form_validation->set_rules('FKCategoryID[]','Category','required');
			/*$this->form_validation->set_rules('FKRegionID','Region','required|min_length[1]');
			$this->form_validation->set_rules('FKFieldID','Field','required|min_length[1]');
			$this->form_validation->set_rules('FKDistrictID','District','requiredmin_length[1]');
			$this->form_validation->set_rules('EstimatedCost','Estimated Cost','required|decimal|min_length[1]');
			$this->form_validation->set_rules('RequestedProjectFunds','Requested Project Funds','required|decimal|min_length[1]');
			$this->form_validation->set_rules('Country','Country','required|min_length[1]');
			$this->form_validation->set_rules('IndividualCostPerDay','Individual CostPer Day','decimal|min_length[1]');
			$this->form_validation->set_rules('City','City','required');
			$this->form_validation->set_rules('FKSiteCoordinatorID','Coordinator','required|min_length[1]');*/
			$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
			$this->load->model('PagesModel');

			if ($this->form_validation->run())
	        {
	        	$data = $this->input->post();
		        unset($data['FKCategoryID']);
		        $data['FKCreatedByID'] = $this->session->userdata('PersonID');

	        	if($this->PagesModel->saveRecord($data,'projects'))
	            {
	            	$categories['FKProjectID'] = $this->db->insert_id();
	            	foreach ($_POST['FKCategoryID'] as $cat)
	            	{
	            		$categories['FKCategoryID'] = $cat;
	            		$this->PagesModel->saveRecord($categories,'mmprojectcategory');
	            	}

	                $this->session->set_flashdata('response','Project successfully saved.');
	            }
	            else
	            {
					$this->session->set_flashdata('response','Project was not saved.');
	            }
	        }

			return redirect("Project/new/{$categoryList}");
		}
		else
		{
			$this->load->view('forbidden');
		}
	}

	public function ChangeStatus($id,$status)
	{
		$this->load->model('ProjectModel');
		$this->ProjectModel->ChangeStatus($id,$status);

		$this->load->model('PagesModel');
		$data = $this->input->post();
		$data['FKCreatedBy'] = $this->session->userdata('PersonID');
		$data['FKProjectID'] = $id;
		$data['FKStatusID'] = $status;
		$this->PagesModel->saveRecord($data,'projectlogs');

		return redirect("Project/view/{$id}");
	}

	public function setThumbnail($mediaID,$ProjectID)
	{
		$this->load->model('ProjectModel');
		$this->ProjectModel->ChangeThumbnail($mediaID,$ProjectID);
		return redirect("Project/images/{$ProjectID}");
	}

	public function deleteImage($mediaID,$ProjectID,$mediaFileName)
	{
		$this->load->model('PagesModel');
		if($this->PagesModel->delete('Media',array('MediaID' => $mediaID)))
		{
			unlink("uploads/".$mediaFileName);
			$this->session->set_flashdata('response','Image successfully deleted.');
		}
		else
		{
			$this->session->set_flashdata('response','Image failed to be deleted.');
		}
		return redirect("Project/images/{$ProjectID}");
	}

	public function SaveCoordinator($ProjectID)
	{
		$this->load->model('ProjectModel');
		$CoordinatorID = $_POST['FKSiteCoordinatorID'];
		$this->ProjectModel->UpdateCoordinator($ProjectID,$CoordinatorID);
		return redirect("Project/view/{$ProjectID}");
	}

	public function editImage($mediaID)
	{
		$this->load->model('ProjectModel');
		$data['image'] = $this->ProjectModel->viewImage($mediaID);
		$this->load->view('project_image_edit',$data);
	}

	public function showNotifications()
	{	
		$this->load->model('ProjectModel');
		$projects = $this->ProjectModel->getProjectsByUser($this->session->userdata('PersonID'));
		$arrayProjectIDs = array();
			foreach($projects as $project)
            {
               array_push($arrayProjectIDs,$project->ProjectID);
            }
		$projects = $this->ProjectModel->getProjectLogsByUser($arrayProjectIDs);
		$index = 0;
		foreach($projects as $project)
        {
        	$ArrayTimeDate = [];
        	$ArrayTimeDate = explode(' ', $this->elapsed_time($project['elapsedTime']), 3);
        	$timeDate = $ArrayTimeDate[0] . ' ' . $ArrayTimeDate[1];
        	$projects[$index]['timedate'] = $timeDate;
        	$index++;
            //$data['projects'] = array_merge($data['projects'],array('timedate' => $this->elapsed_time($project->elapsedTime)));
        }
		echo json_encode($projects);
	}

	public function elapsed_time($timestamp, $precision = 2) {
		$time = time() - $timestamp;
		$a = array('dec' => 315576000, 'year' => 31557600, 'mon' => 2629800, 'week' => 604800, 'day' => 86400, 'hr' => 3600, 'min' => 60, 'sec' => 1);
		$i = 0;
		foreach($a as $k => $v) {
			$$k = floor($time/$v);
				if ($$k) $i++;
				$time = $i >= $precision ? 0 : $time - $$k * $v;
				$s = $$k > 1 ? 's' : '';
				$$k = $$k ? $$k.' '.$k.$s.' ' : '';
				@$result .= $$k;
			}
		return $result ? $result.'ago' : '1 sec to go';
	}

}
