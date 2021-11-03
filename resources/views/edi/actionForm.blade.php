<div class="modal-header bg-gray-100">
    <h2 class="modal-title"> Are you about to <b>"{{($data['is_carrier_reject'] == 1?'Carrier Reject':($data['is_bdp_reject'] == 1?'BDP Reject':''))}}"</b> {{$data['label']}}</h2>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="material-icons" style="vertical-align:middle;">close</i></button>

</div>

<form id="save_edi_action" action="{{route('actionform.save')}}" method="POST">
    @csrf

    @php
    $data_msg = ($data['is_carrier_reject']?$data['carrier_reject_msg']:($data['is_bdp_reject']?$data['bdp_reject_msg']:''));

    @endphp
    <div class="modal-body">
        <div class="row">
            <div class="p-3 align-middle form-group col-sm-12">
                <label class="form-label"> Description </label>
                <textarea rows="5" class="block mt-1 w-full form-control form-control-lg rounded new_data_msg" id="{{($data['is_carrier_reject']?'carrier_reject_msg':($data['is_bdp_reject']?'bdp_reject_msg':''))}}" name="{{($data['is_carrier_reject']?'carrier_reject_msg':($data['is_bdp_reject']?'bdp_reject_msg':''))}}" {{ !empty($data->id) ? "" : "required" }}>{{$data_msg}}</textarea>
            </div>
        </div>
    </div>

    <input type="hidden" name="meta_data_id" id="meta_data_id" value="{{ !empty($data->id) ? $data->id : 0 }}">    
    <input type="hidden" name="old_data_msg" id="old_data_msg" value="{{ $data_msg }}">    
    <input type="hidden" name="edi_data_id" value="{{ $data['edi_data_id'] }}">    
    <input type="hidden" name="rules_id" value="{{ $data['rules_id'] }}" >
    @if($data['is_carrier_reject'])
    <input type="hidden" name="is_carrier_reject" value="{{ $data['is_carrier_reject'] }}">
    @endif
    @if($data['is_bdp_reject'])
    <input type="hidden" name="is_bdp_reject" value="{{ $data['is_bdp_reject'] }}">    
    @endif    
    <div class="modal-footer bg-gray-100">
        <button type="button" id="saveAction" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    </div>
</form>
