<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/customer.png" alt="" /> <?php echo $heading_title; ?></h1>
      <form action="<?php echo $importExcel; ?>" method="post" enctype="multipart/form-data" style="position: absolute;padding-top: 10px;margin-left: 140px;">
        <label for="file"><?php echo $button_importExcel; ?>：</label>
        <input type="file" name="file" id="file">   
        <input type="submit" name="submit" value="<?php echo $button_submit; ?>" style="position: absolute;top: 10px;left: 230px;"/>
      </form>
      <div class="buttons">
        <a href="<?php echo $insert; ?>" class="button"><?php echo $button_insert; ?></a>
        <a onclick="document.getElementById('form').submit();" class="button"><?php echo $button_delete; ?></a>
      </div>
    </div>
    <div class="content">
     
    </div>
  </div>
</div>
<?php echo $footer; ?>