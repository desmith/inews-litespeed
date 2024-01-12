<?php 
    $query = 'admin.php';
    $link = add_query_arg( array(
            'page' => 'rhl_cr_listing',

        ), $query );
?>
<div>
<h2>Contact Request Details</h2>
<a href="<?= $link ?>" class="button">Back</a>
<?php 
$ip_addr  = !empty($data['ip_addr']) ? $data['ip_addr'] :   'N/A';
?>
<div class="row quote-page" id="quote-page">
    <div class="col-md-8 ">
        <table class="table table-striped dp-view-table">
            <tr>
                <td><b>Subscribed On :</b></td>
                <td><?= date('d-m-Y',strtotime($data['added_on'])) ?></td>
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
                                <td><b>First Name :</b> </td>
                                <td><?php echo $data['rhl_cr_fname'] ?></td>
                            </tr>
                            <tr>
                                <td><b>Last Name :</b> </td>
                                <td><?php echo $data['rhl_cr_lname'] ?></td>
                            </tr>
                            <tr>
                                <td><b>Email Address :</b> </td>
                                <td><?php echo $data['rhl_cr_email']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Phone Number :</b> </td>
                                <td><?php echo $data['rhl_cr_phone']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Message :</b> </td>
                                <td><?php echo $data['rhl_cr_message']; ?></td>
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