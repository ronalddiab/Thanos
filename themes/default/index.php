<?php
    $ci = $this->_ci;
    require_once 'header.php';
?>

<div class="content-wrap">
    <div id="messages">
    <?php echo $ci->theme->message(); ?>
    </div>
    <!-- ajax message -->
    <div style="display: none;" id="ajax-messages"> 
        <div id="msg_content" class="alert alert-success">
            <span id="ajax_msg"></span>	
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>							
        </div>
    </div>
    <!-- ajax message -->

    <script type="text/javascript">
        var site_base_url = '<?php echo base_url(); ?>';
    </script>
    <?php echo $this->content(); ?>
</div>

<?php require_once 'footer.php'; ?>