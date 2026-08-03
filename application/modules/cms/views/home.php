<style>
    body {
        margin :0px 0px;
    }
</style>
<?php 
if(isset($_GET['defaultpage']) && $_GET['defaultpage'] == 1) {
?>
<img width='100%' src="<?php echo site_url();?>/assets/uploads/design_11_ad.png"/>
<?php } else { ?>
<img width='100%' src="<?php echo site_url();?>/assets/uploads/design_11.png" onclick='window.location.href="<?php echo site_url();?>?defaultpage=1"' />
<?php } ?>
