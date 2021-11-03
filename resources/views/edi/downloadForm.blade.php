<div class="modal-header bg-gray-100">
    <h2 class="modal-title text-center font-weight-bold"> Download Options</h2>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="material-icons" style="vertical-align:middle;">close</i></button>

</div>

<form id="edi_download_action" action="{{route('diff.download')}}" method="POST">
    @csrf

    <div class="modal-body">
        <div class="row">
            <div class="align-middle form-group col-sm-12">
			<label class="align-middle form-group col-sm-3 font-weight-bold">Download Type :</label>
                <div class="col-sm-4 form-check form-check-inline"><input type="radio" name="downloadtype" id="downloadtypepdf" value="pdf" checked class="form-check-input" ><label for="downloadtypepdf" class="form-check-label"> Download PDF </label></div>
                <div class="col-sm-4 form-check form-check-inline"><input type="radio" name="downloadtype" id="downloadtypecsv" value="csv" class="form-check-input" ><label for="downloadtypecsv" class="form-check-label"> Download Excel </label></div>
                
            </div>
        </div>
        <div class="row">
            <div class="align-middle form-group col-sm-12">
			<label class="align-middle form-group col-sm-3 font-weight-bold">Segment Coverage:</label>
                <div class="col-sm-4 form-check form-check-inline"><input type="radio" name="allordiffonly" id="all" value="all" checked class="form-check-input" ><label for="allordiffonly" class="form-check-label"> All Segment</label></div>
                <div class="col-sm-4 form-check form-check-inline"><input type="radio" name="allordiffonly" id="diffonly" value="diffonly" class="form-check-input" ><label for="allordiffonly" class="form-check-label"> Diff Only </label></div>
            </div>
        </div>
        <div class="row">
            <div class="align-middle form-group col-sm-12">
			<label class="align-middle form-group col-sm-3 font-weight-bold">Feedback Coverage:</label>
                <div class="col-sm-4 form-check form-check-inline"><input type="radio" name="withcomments" id="withcomments" value="with" checked class="form-check-input" ><label for="withcomments" class="form-check-label"> With Feedback</label></div>
                <div class="col-sm-4 form-check form-check-inline"><input type="radio" name="withcomments" id="withoutcomments" value="without" class="form-check-input" ><label for="withoutcomments" class="form-check-label"> Without Feedback </label></div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="id" value="{{ $edi_id }}">    
    <div class="modal-footer bg-gray-100">
        <button type="submit" id="downloadActionBtn" class="btn btn-primary"><span style="display:none;" class="spinner spinner-border spinner-border-sm p-2" role="status" aria-hidden="true"></span>Download</button>
        <button type="button" id="downloadActionClose" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    </div>
</form>
<script>
$('#downloadActionBtn').on('click', function(e){
		e.preventDefault();
		$('span.spinner').show(800);
		$(this).closest('form').submit();
		setTimeout(function(){
		$('#downloadActionClose').delay(3000).trigger('click');
		},3000);
		
	});
</script>
