<div id="message">
    <button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>
</div>
<form method="post" id="download_form" action="">
    <input type="submit" name="ief_download_csv" class="button-primary" value="Download CSV" />
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
#contact_request_length{
    margin-bottom: 20px;
}
#contact_request_filter{
    padding-right: 10px;
}
</style>
<div>
    <h2>Story Submit Form Requests</h2>
    <table class="" data-order='[[ 0, "desc" ]]' id="contact_request">
    <thead style="background:#000;color:#fff;text-align: left;">
        <tr>
			<th>Sl No.</th>
            <th>Name</th>
            <th>Phone Number</th>
            <th>Email</th>            
			<th>Location</th>
            <th>IP Address</th>        
            <th>Requested On</th>
            <th>Action</th>
        </tr>
    </thead>
    <tfoot style="background:#000;color:#fff;text-align: left;">
        <tr>
			<th>Sl No.</th>
            <th>Name</th>
            <th>Phone Number</th>
            <th>Email</th>            
			<th>Location</th>
            <th>IP Address</th>        
            <th>Requested On</th>
            <th>Action</th>
        </tr>
    </tfoot>
    <tbody>
         <?php if(!empty($data)):
            foreach ($data as $key => $value) { 
            $ip_addr = !empty($value['ip_addr'])?$value['ip_addr'] :'NULL'; 
			$path = (isset($value['cv']) && !empty($value['cv']) ) ? site_url().'/wp-content/uploads/doc/'.$value['cv'] : 'javascript:void(0)';
        ?>
            <tr class="single-row">
			<td><?= $value['id'] ?></td>
            <td><?php echo $value['wb_fullname'] ?></td>
            <td><?php echo $value['wb_phone']; ?></td>
			<td><?php echo $value['wb_email']; ?></td>
            <td><?php echo $value['wb_location']; ?></td>
            <td><?php echo $ip_addr ?></td>
            <td><?php echo date('d-m-Y',strtotime($value['added_on'])); ?></td>

            <?php 
            $query = 'admin.php';
            $link = add_query_arg( array(
                'page' => 'ief_view',
                'id' => $value['id']
            ), $query );
            ?>
            <td><a href="<?=$link?>">View</a>&nbsp;/&nbsp;
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

