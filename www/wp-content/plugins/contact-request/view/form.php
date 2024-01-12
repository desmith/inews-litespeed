<form action="javascript:void(0);" class="allForm contactFrm" id="rhl_contact_form" novalidate="">
    <div class="form-element">
        <label class="form-label">First Name </label>
        <input name="rhl_cr_fname" id="rhl_cr_fname" type="text" class="form-field" onkeypress="return ((event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 8 || event.charCode == 32 || (event.charCode >= 48 && event.charCode <= 57));">
    </div>
    <div class="form-element">
        <label class="form-label">Last Name </label>
        <input name="rhl_cr_lname" id="rhl_cr_lname" type="text" class="form-field" onkeypress="return ((event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || event.charCode == 8 || event.charCode == 32 || (event.charCode >= 48 && event.charCode <= 57));">
    </div>
    <div class="form-element">
        <label class="form-label">Email Address</label>
        <input name="rhl_cr_email" id="rhl_cr_email" type="email" class="form-field">
    </div>
    <div class="form-element">
        <label class="form-label">Phone Number</label>
        <input name="rhl_cr_phone" id="rhl_cr_phone" class="form-field">
    </div>
    <div class="form-element form-textarea">
        <label class="form-label">Message </label>
            <textarea class="form-field" name="rhl_cr_message" id="rhl_cr_message"></textarea>
    </div>

    <p class="desc">
        <button type="submit" class="btn btn-submit submit hvr:outline bg--yellow c--white hvr:bg--transparent hvr:c--yellow" id="rhl_cr_sbmt_btn"><span>Submit</span></button>
    </p>
</form>