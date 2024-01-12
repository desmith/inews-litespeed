<div id="message">
    <p></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>
</div>
<form method="post" id="download_form" action="">
    <input type="submit" name="ils_pre_download_csv" class="button-primary" value="Download CSV" />
</form>
<style>
table.dataTable tbody th, table.dataTable tbody td {
    padding: 8px 10px;
    border-bottom: 1px solid #e1e1e1;
    padding: 12px 16px !important;
}
#wpbody-content{
    padding-top:20px;
}
#get_in_touch_length{
    margin-bottom: 20px;
}
#get_in_touch_filter{
    padding-right: 10px;
}
</style>
<div>
    <h2>Newsletter Requests</h2>
    <table class="" data-order='[[ 0, "desc" ]]' id="get_in_touch">
    <thead style="background:#000;color:#fff;text-align: left;">
        <tr>
			<th>Sl No.</th>
            <th>Email ID</th>
            <th>IP Address</th>        
            <th>Requested On</th>
            <th>Action</th>
        </tr>
    </thead>
    <tfoot style="background:#000;color:#fff;text-align: left;">
        <tr>
			<th>Sl No.</th>
            <th>Email ID</th>
            <th>IP Address</th>        
            <th>Requested On</th>
            <th>Action</th>
        </tr>
    </tfoot>
    <tbody>
         <?php if(!empty($data)):
            foreach ($data as $key => $value) { 
            $ip_addr = !empty($value['ip_addr'])?$value['ip_addr'] :'NULL';  
        ?>
            <tr class="single-row">
			<td><?= $value['id'] ?></td>
            <td><?php echo $value['gt_email']; ?></td>
            <td><?php echo $ip_addr ?></td>
            <td><?php echo date('d-m-Y',strtotime($value['added_on'])); ?></td>
            <?php 
            $query = 'admin.php';
            $link = add_query_arg( array(
                'page' => 'ils_pre_view',
                'id' => $value['id']
            ), $query );
            ?>
            <td><a href="<?=$link?>">View</a>
               &nbsp;/&nbsp;
               <a href="javascript:void(0)" onClick="show_alert(this)">Remove</a>
                <div class="alert" data-id="<?= $value['id'] ?>">
                    <p class="ques">Are you sure you want to delete this record</b>?</p>
                    <div class="alert-opt">
                        <a href="javascript:void(0)" class="btn-yes" onclick="confirm_delete(this)">Yes Please</a>
                        <a href="javascript:void(0)" class="btn-no" onclick="hide_alert(this)">No Thanks</a>
                    </div>
                </div>
            </td>
            </tr>
        <?php } ?> 
    <?php endif; ?>
    </tbody>
    </table>
    <!-- show message-->
    <div id="message"><p></p></div>
</div>