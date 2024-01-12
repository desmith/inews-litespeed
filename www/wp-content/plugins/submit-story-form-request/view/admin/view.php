<?php 
    $query = 'admin.php';
    $link = add_query_arg( array(
            'page' => 'ief_listing',

        ), $query );
?>
<div>
<h2>Story Details</h2>
<a href="<?= $link ?>" class="button">Back</a>
<?php 
$ip_addr  = !empty($data['ip_addr']) ? $data['ip_addr'] :   'N/A';
?>
<div class="row quote-page" id="quote-page">
    <div class="col-md-8 ">
        <table class="table table-striped dp-view-table">
            <tr>
                <td><b>Applying On :</b></td>
                <td><?= date('d-m-Y',strtotime($data['added_on'])) ?></td>
				<td>
                    <?php if(isset($data['cv']) && !empty($data['cv']) ){ 
                        $excs = explode(',', $data['cv']);
                        //print_r($excs);die();
                        foreach ($excs as $exc) { ?>
                            <a href="<?= site_url().'/wp-content/uploads/doc/'.$exc; ?>" target="_blank">Download</a>
                        <?php } ?>
                    <?php } ?>
                    
                </td>
            </tr>
        </table>    
        <table class="table table-striped dp-view-table" >
            <tr>
                <td>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2">General Info:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Name :</b> </td>
                                <td><?php echo $data['wb_fullname'] ?></td>
                            </tr>
                            
                            <tr>
                                <td><b>Phone Number :</b> </td>
                                <td><?php echo $data['wb_phone']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Email Address :</b> </td>
                                <td><?php echo $data['wb_email']; ?></td>
                            </tr>
							<tr>
                                <td><b>Location :</b> </td>
                                <td><?php echo $data['wb_location']; ?></td>
                            </tr>
							<tr>
                                <td><b>Message :</b> </td>
                                <td><?php echo $data['wb_message']; ?></td>
                            </tr>
                            <tr>
                                <td><b>IP Address :</b> </td>
                                <td><?= $ip_addr ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>  
    </div>
</div>