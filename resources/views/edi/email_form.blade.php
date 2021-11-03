<?php
//checkbox code
//(old('email_type', '')== 'carrier') 
?>
<div class="modal-header">
    <h2 class="modal-title text-dark fw-bold ">Send Email</h2>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="material-icons" style="vertical-align:middle;">close</i></button>
</div>

<form id="send_email_form"> 
    @csrf

    <input type="hidden" name="edi_data_id" value="{{$row->id}}" id="edi_data_id" />
    <input type="hidden" name="email_attachment_path" value="0" id="edi_email_attachment_path"  />
    <input type="hidden" name="email_attachment_name" value="0" id="edi_email_attachment_name"  />
    <input type="hidden" name="email_attachment_mimetype" value="0" id="edi_email_attachment_mimetype"  />

    <div class="modal-body">
        <div class="row">

            <div class="col-sm-2"></div>
            <div class="col-sm-7">

                <div class="radio">
                    <label><input type="radio" name="email_type" class="email_type" value="carrier"  checked> Carrier</label>
                    &nbsp;&nbsp;
                    <label><input type="radio" name="email_type"  class="email_type"  value="owner"> Owner</label>
                    &nbsp;&nbsp;
                    <label><input type="radio" name="email_type"  class="email_type"  value="other"> Other</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-7">
                <select name="email_template_id" id="email_template_id" class="block mt-1 w-full" edi_id='{{$row->id}}' onchange="toggle_rejection_msg()">
                    <option data-show="0,1,2" value="0">Select Template </option>
                    @if(!empty($template_types))
                    @foreach($template_types as $key =>$val )
                    <option data-show="<?php echo $template_rules[$key]; ?>" value="{{$key}}" {{ $key === old('type_id', 1) ? 'selected' : '' }}>{{$val}}</option>
                    @endforeach
                    @endif
                </select>
            </div>
        </div>



        <div class="row">
            <div class="col-sm-2">
                To:
            </div>
            <div class="col-sm-9">
                <x-input  autocomplete="off" class="block mt-1 w-full" type="email" name="email_to" id="email_to" :value="old('email_to', '' )" required autofocus placeholder="Enter Email TO here"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2">
                CC:
            </div>
            <div class="col-sm-9">
                <x-input autocomplete="off" id="email_cc" class="block mt-1 w-full" type="email" name="email_cc" :value="old('email_cc', '')" autofocus placeholder="Enter Email CC here" />
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2">
                Subject:
            </div>
            <div class="col-sm-9">
                <x-input autocomplete="off" class="block mt-1 w-full" type="text" name="subject" id="subject" :value="old('subject', '' )" required autofocus placeholder="Enter Email Subject"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <textarea  autocomplete="off" rows="5" class="block mt-1 w-full rounded" type="text" name="message" id="message"  placeholder="Enter Email Body">{{{ old('message', '') }}}</textarea>
                <span class="mt-3 list-disc list-inside text-sm text-red-600 text-center" id="edi_error"></span>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <textarea  autocomplete="off" rows="3" class="block mt-1 w-full rounded" type="text" name="signature" id="signature"  placeholder="Enter Email Signature">{{{ old('signature', '') }}}</textarea> 
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                &nbsp;
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="align-middle form-group col-sm-12">
                        <label class="align-middle form-group col-sm-3 font-weight-bold">Send Attachment</label>
                        <div class="col-sm-4 form-check form-check-inline"><input type="checkbox" name="attachmentEnabled" class="appearance-none checked:bg-blue-600 checked:border-transparent" id="attachmentStatus" checked value="1"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="align-middle form-group col-sm-12">
                        <label class="align-middle form-group col-sm-3 font-weight-bold">Attachment Type :</label>
                        <div class="col-sm-4 form-check form-check-inline"><input type="radio" name="downloadtype" id="downloadtypepdf" value="pdf" checked class="form-check-input" ><label for="downloadtypepdf" class="form-check-label"> PDF </label></div>
                        <div class="col-sm-4 form-check form-check-inline"><input type="radio" name="downloadtype" id="downloadtypecsv" value="csv" class="form-check-input" ><label for="downloadtypecsv" class="form-check-label"> Excel </label></div>

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
        </div>
    </div>
</div>

<div class="">
    @if(!empty($metaFormated_carrier_reject))
    <div id="carrier_reject" data-bs-spy="scroll" data-bs-target="#navbar-example2" data-bs-offset="0" class="scrollspy-example d-none" tabindex="0" style="height: 200px; margin: 1rem; overflow: auto; width:95%;">
        <div class="table-responsive" style="height: 200px;">
            <table id="EdiDiffereence" class="table table-bordered" style="border:1px;">
                <thead>
                    <tr class="table-primary">
                        <th style="width:45%"> BDP SI Outgoing
                            (@php echo basename($edidata['edi']->data); @endphp)</th>
                        <th style="width:45%"> Carrier BL Incoming
                            (@php echo basename($edidata['compare_to_data']->data)@endphp)</th>
                        <th style="width:10%">Comments</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($edidata['rules'] as $rule) 
                    @if(in_array($rule['id'], $metaFormated_carrier_reject, true))
                    <tr>
                        <th colspan="3" class="table-secondary" >
                            @php echo str_replace('+','::',$rule['rules']); @endphp ({{ $rule['name'] }}) (Compare Elements: {{!empty($rule['default_compare_elements']) ? $rule['default_compare_elements'] : "ALL"}})
                        </th>
                    </tr>

                    <tr class="table-light">
                        <td style="width:45%">
                            @php
                            $outgoing_data = isset($diff_rows['outgoing']['data'][$rule['id']]) ? $diff_rows['outgoing']['data'][$rule['id']] : [];

                            if (!empty($outgoing_data)) {
                            foreach ($outgoing_data as $key => $data) {
                            echo $data;
                            echo '<br/>';
                            }
                            }
                            @endphp
                        </td>
                        <td style="width:45%">
                            @php
                            $incoming_data = isset($diff_rows['incoming']['data'][$rule['id']]) ? $diff_rows['incoming']['data'][$rule['id']] : [];

                            if (!empty($incoming_data)) {
                            foreach ($incoming_data as $key => $data) {
                            echo $data;
                            echo '<br/>';
                            }
                            }
                            @endphp
                        </td>
                        <td style="width:10%">
                            @foreach($metas as $meta)
                            @if( ($rule['id'] == $meta['rules_id']) && ($meta['is_carrier_reject']==1) )
                            {{ $meta['carrier_reject_msg'] }}
                            @endif
                            @endforeach
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    @else
    <div id="carrier_reject" data-bs-spy="scroll" data-bs-target="#navbar-example2" data-bs-offset="0" class="scrollspy-example d-none" tabindex="0" style=" margin: 1rem; overflow: auto; width:95%;">
        <p>There is no rejection message.</p>
    </div>
    @endif

    @if(!empty($metaFormated_carrier_reject) || !empty($metaFormated_bdp_reject))
    <div id="bdp_reject" data-bs-spy="scroll" data-bs-target="#navbar-example2" data-bs-offset="0" class="scrollspy-example d-none" tabindex="0" style="height: 200px; margin: 1rem; overflow: auto; width:95%;">
        <div class="table-responsive" style="height:200px;">
            <table id="EdiDiffereence" class="table table-bordered" style="border:1px;">
                <thead>
                    <tr class="table-primary">
                        <th style="width:40%"> BDP SI Outgoing
                            (@php echo basename($edidata['edi']->data); @endphp)</th>
                        <th style="width:40%"> Carrier BL Incoming
                            (@php echo basename($edidata['compare_to_data']->data)@endphp)</th>
                        <th style="width:10%">Carrier Reject Comments</th>
                        <th style="width:10%">BDP Reject Comments</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($edidata['rules'] as $rule)
                    @if(((in_array($rule['id'], $metaFormated_carrier_reject, true)) || (in_array($rule['id'], $metaFormated_bdp_reject, true)))) 
                    <tr>
                        <th colspan="4" class="table-secondary" >
                            @php echo str_replace('+','::',$rule['rules']); @endphp ({{ $rule['name'] }}) (Compare Elements: {{!empty($rule['default_compare_elements']) ? $rule['default_compare_elements'] : "ALL"}})
                        </th>
                    </tr>

                    <tr class="table-light">
                        <td style="width:40%">
                            @php
                            $outgoing_data = isset($diff_rows['outgoing']['data'][$rule['id']]) ? $diff_rows['outgoing']['data'][$rule['id']] : [];

                            if (!empty($outgoing_data)) {
                            foreach ($outgoing_data as $key => $data) {
                            echo $data;
                            echo '<br/>';
                            }
                            }
                            @endphp
                        </td>
                        <td style="width:40%">
                            @php
                            $incoming_data = isset($diff_rows['incoming']['data'][$rule['id']]) ? $diff_rows['incoming']['data'][$rule['id']] : [];

                            if (!empty($incoming_data)) {
                            foreach ($incoming_data as $key => $data) {
                            echo $data;
                            echo '<br/>';
                            }
                            }
                            @endphp
                        </td>
                        <td style="width:10%">
                            @foreach($metas as $meta)
                            @if( ($rule['id'] == $meta['rules_id']) && ($meta['is_carrier_reject']==1) )
                            {{ $meta['carrier_reject_msg'] }}
                            @endif
                            @endforeach
                        </td>
                        <td style="width:10%">
                            @foreach($metas as $meta)
                            @if( ($rule['id'] == $meta['rules_id']) && ($meta['is_bdp_reject']==1) )
                            {{ $meta['bdp_reject_msg'] }}
                            @endif
                            @endforeach
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    @else
    <div id="bdp_reject" data-bs-spy="scroll" data-bs-target="#navbar-example2" data-bs-offset="0" class="scrollspy-example d-none" tabindex="0" style="margin: 1rem; overflow: auto; width:95%;">
        <p>There is no rejection message.</p>
    </div>	
    @endif
    <div class="modal-footer">
        <span id="send_email_btn" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-4">Send</span>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Discard</button>
    </div>
    <input type="hidden" name="edi_title_id" id="edi_title_id" value="0" />
</form>
<script type="text/javascript">
    $(document).ready(function () {
        $("#email_template_id")[0].selectedIndex = 0;
        setTimeout(function () {
            $('input:radio[name=email_type]').filter('[value=carrier]').click();
            $("#email_template_id").trigger("change");
        }, 500);

        $('input:radio[name=email_type]').click(function () {
            $("#email_template_id")[0].selectedIndex = 0;
                    let
            user = $(this).attr('value');
                    switch (user) {
                case 'carrier':
                    user = 0;
                    break;
                case 'owner':
                    user = 1;
                    break;
                case 'other':
                    user = 2;
                    break;
                default:
                    user = 0;
                    break;
            }

            $("#email_template_id option").each(function (){
                let show_tpl = $(this).attr('data-show');
                        let
                el = this;
                        if (!show_tpl.includes(user)){
                $(el).hide();
                } else{
                $(el).show();
                        let opt = $(el).html();
                        opt = opt.toLowerCase();

                if (opt.includes('reject')){
                let opt_index = $(el).index();
                        $("#email_template_id")[0].selectedIndex = opt_index;
                $("#email_template_id").trigger("change");
                }
            }
            });
        });
        });
                function toggle_rejection_msg() {
                    let
                    msg = $("#email_template_id option:selected").html();
                            msg = msg.toLowerCase();
                    let
                    result = msg.search('rejection');
                            if (result === -1) {
                        $("#carrier_reject,#bdp_reject").hide();
                    } else {
                        $("#carrier_reject,#bdp_reject").show();
                    }
                }
</script>