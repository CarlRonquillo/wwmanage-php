<?php include('header.php'); ?>

<main>
    <div class="container-fluid">
    	<div class="card">
    		<h4 class="card-header blue-gradient white-text text-center py-3">Projects</h4>
  			<div class="card-body">
  				<?php 
					if($error = $this->session->flashdata('response')):
					{						
				?>
					<div class="alert alert-success">
						<span class ="glyphicon glyphicon-info-sign"></span>
						<?php echo $error; ?>
					</div>
				<?php 
					}
					endif
				?>
	  			<div class="table-responsive text-wrap table-hover table-striped">
	  			<div class="card-group">
				  <table class="table">
				    <thead>
				      <tr>
				        <th scope="col" class="font-weight-bold dark-grey-text">#</th>
				        <th scope="col" class="font-weight-bold dark-grey-text"></th>
				        <th scope="col" class="font-weight-bold dark-grey-text">Project Name</th>
				        <th scope="col" class="font-weight-bold dark-grey-text">Short Description</th>
				        <th scope="col" class="font-weight-bold dark-grey-text">Arrival City</th>
				        <th scope="col" class="font-weight-bold dark-grey-text">Est Cost</th>
				        <th scope="col" class="font-weight-bold dark-grey-text">Req Funds</th>
				        <th scope="col" class="font-weight-bold dark-grey-text">Created Date</th>
				        <th scope="col" class="font-weight-bold dark-grey-text">Exp Date</th>
				      </tr>
				    </thead>
				    <tbody>
				    <?php $i = 0;
				    	if(count($projects)):  ?>
				    	<?php foreach($projects as $project) { $StatusColor ='text-dark';?>
				    		<?php if($project->Status == 0)
						    {
						    	$StatusColor = "badge badge-default";
						    }
						    elseif($project->Status == 1)
						    {
						    	$StatusColor = "badge badge-secondary";
						    }
						    elseif($project->Status == 2)
						    {
						    	$StatusColor = "badge badge-warning";
						    }
						    elseif($project->Status == 3)
						    {
						    	$StatusColor = "badge badge-success";
						    }
						    elseif($project->Status == 4)
						    {
						    	$StatusColor = "badge badge-danger";
						    }
					     ?>
					      <tr class="text-danger">
					        <td scope="row"><?php echo $i += 1; ?></td>
					        <td scope="row"><img src="<?php echo base_url('uploads/'.((!isset($project->FileName)) ? 'alt_logo.jpg' : $project->FileName))?>" alt="thumbnail" class="img-thumbnail" style="width: 120px"></td>
					        <td style="max-width: 200px;"><?php echo anchor("project/view/{$project->ProjectID}",$project->ProjectName,["class"=>"text-info"]); ?><br>
					        	<i class="<?php echo $StatusColor; ?>"><?php echo  $project->Title ?></i>
					        </td>
					        <td class="text-justify" style="max-width: 250px;" ><?php echo substr($project->Description,0,120).((strlen($project->Description) < 120) ? '' : '...') ?></td>
					        <td><?php echo $project->ArrivalCity ?></td>
					        <td><?php echo "$".$project->EstimatedCost ?></td>
					        <td><?php echo "$".$project->RequestedProjectFunds ?></td>
					        <td><?php echo date("M j, Y", strtotime($project->CreatedDate)) ?></td>
					        <td><?php echo $project->ExpirationDate ?></td>
					      </tr>
					    <?php } else: ?>
					    <td>No record(s) Found!</td>
					<?php endif; ?>
				    </tbody>
				  </table>
				</div>
				</div>
  			</div>
  		</div>
  	</div>
</main>

<?php include('footer.php'); ?>