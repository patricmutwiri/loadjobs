<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-loadjobs" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" data-id="form-createjob" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="textarea-loadjobs_limit_field"><?php echo $entry_limit; ?></label>
            <div class="col-sm-10">
              <input type="number" required name="loadjobs_limit_field" id="input-limit" class="form-control" value="<?php echo $loadjobs_limit_field ?>">
            </div>
          </div>
          <div class="job-body jobs">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="textarea-loadjobs_text_field"><?php echo $entry_code; ?></label>
              <div class="col-sm-10">
                <input type="text" name="loadjobs_text_field" data-id="input-reference" class="form-control" value="REF:<?php echo date('y/m') ?>" />
                <?php if ($error_code) { ?>
                <div class="text-danger"><?php echo $error_code; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-sm-2 control-label" for="textarea-loadjobs_business_field"><?php echo $entry_business; ?></label>
              <div class="col-sm-10">
                <input type="text" name="loadjobs_business_field" data-id="input-business" class="form-control" value="" />
                <?php if ($error_code) { ?>
                <div class="text-danger"><?php echo $error_code; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-sm-2 control-label" for="textarea-loadjobs_position_field"><?php echo $entry_position; ?></label>
              <div class="col-sm-10">
                <input type="text" name="loadjobs_position_field" data-id="input-position" class="form-control" value="" />
                <?php if ($error_code) { ?>
                <div class="text-danger"><?php echo $error_code; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-sm-2 control-label" for="textarea-loadjobs_description_field"><?php echo $entry_description; ?></label>
              <div class="col-sm-10">
                <input type="text" name="loadjobs_description_field" data-id="input-description" class="form-control" value="" />
                <?php if ($error_code) { ?>
                <div class="text-danger"><?php echo $error_code; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group required">
              <label class="col-sm-2 control-label" for="textarea-loadjobs_requirements_field"><?php echo $entry_requirements; ?></label>
              <div class="col-sm-10">
                <input type="text" name="loadjobs_requirements_field" data-id="input-requirements" class="form-control" value="" />
                <?php if ($error_code) { ?>
                <div class="text-danger"><?php echo $error_code; ?></div>
                <?php } ?>
              </div>
            </div>

            <div class="form-group required">
              <label class="col-sm-2 control-label" for="textarea-loadjobs_deadline_field"><?php echo $entry_deadline; ?></label>
              <div class="col-sm-10">
                <input type="date" name="loadjobs_deadline_field" data-id="input-deadline" class="form-control" value="" placeholder="<?php echo date('d-m-y', time()) ?>" />
                <?php if ($error_code) { ?>
                <div class="text-danger"><?php echo $error_code; ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
              <div class="col-sm-10">
                <select name="loadjobs_status" data-id="input-status" class="form-control">
                  <?php if ($loadjobs_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group col-xs-12 add-new">
              <a href="#new" class="btn btn-primary"> Add Job</a>
              <hr/>
            </div>
          </div>
        </form> 
      </div>
	</div>
  </div>
</div>

<?php echo $footer; ?>