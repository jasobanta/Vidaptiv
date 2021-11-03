<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($type=='diff')
            {{ __('Dashboard > Edi Diff') }}
            @elseif($type=='outgoing')
            {{ __('Dashboard > Sent Edi Content') }}
            @else
            {{ __('Dashboard > Received Edi Content') }}
            @endif
        </h2>
        @php
        $all = 0;
        $allDiffsCnt = count( $diff_rows['all_rule_id']);
        $allMetasCnt = count(array_keys($metas));

        if ($allDiffsCnt == $allMetasCnt)
        $all = 1;
        $style = "style='pointer-events: none'";
        @endphp
        @if($type=='diff')
        <div class="flex items-end float-right -my-5">
            <span class="pl-2 pr-2 m-1"><input data="{{$edi->id}}" {{$edi->is_locked ? 'checked' : '' }} {{$edi->is_locked ? 'disabled' : '' }} role="button" type="checkbox" id="docstatus" data-toggle="switchbutton"  data-onlabel="Closed" data-offlabel="Close" data-onstyle="danger" data-offstyle="dark" data-style="android" data-is-accepted-all={{$all}}></span>
            <button data="{{$edi->id}}" type="button" data-bs-toggle="modal" data-bs-target="#downloadAction" id="loadDownloadAction" class="btn btn-light  pl-2 pr-2 m-1">
                <i class="material-icons" style="vertical-align:middle;">save_alt</i> Download
            </button>
            <button data="{{$edi->id}}" class="btn btn-light pl-2 pr-2 m-1" type="button" data-bs-toggle="modal"
                    data-bs-target="#ediEmailModal" id="loadEdiEmail">
                <i class="material-icons" id="hjh" style="vertical-align:middle;">send</i>Send
            </button>
        </div>
        @endif
    </x-slot>

    <div class="row m-3" id='edidata'>
        <!--Container-->
        <div class="col-sm-12 table-responsive bg-white">
            <div class="row mb-3 bg-white">
                <div class="col-sm-9 p-3 ">
                    <div>    
                        <span class="m-3 fw-bold">Owner : {{$edi->owner_name}}</span>
                        <span class="m-3 fw-bold">Order : {{$edi->ff_no}}</span>
                        <span class="m-3 fw-bold">Carrier : {{$edi->carrier}}</span>
                        <span class="m-3 fw-bold">Booking No : {{$edi->booking_no}}</span>
                    </div>
                    <!--div>
                        <span class="m-3 fw-bold">Status : {{ $edi_status[$edi->status] }}</span>
                        <span class="m-2 fw-bold">Email Notification : <?php echo ($edi->sent_emails_count > 0) ? "<a href='email/history/".$edi->id."/".$hash."' target='_blank'>".$edi->sent_emails_count."</a>" : 0; ?></span>
                    </div-->
                    <div>
                        <span class="m-3 fw-bold">
                            <span class="material-icons edi_country_documents_btn" style="vertical-align:middle;" title="List of documents required documents">help_outline</span>
                            <span class="edi_country_documents d-none">{{$country_documents['country_name']}} documents: {{$country_documents['documents']}}</span>
                        </span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <span class="m-2 float-right">
                        <input role="button" type="checkbox" data-bs-toggle="collapse" data-bs-target=".rld" aria-expanded="false" aria-controls="" id="show-all-diff-only" data-toggle="switchbutton" checked data-onlabel="Show All" data-offlabel="Show Diff " data-onstyle="success" data-offstyle="warning" data-style="android"></span>
                    <span class="m-2 float-right">
                        @php 
                        $all = 0;
                        $allDiffsCnt = count( $diff_rows['diff_rule_id']);
                        $allMetasCnt = count(array_keys($metas));

                        if ($allDiffsCnt == $allMetasCnt)
                        $all = 1;
                        @endphp
                        <button type="button" data-bs-toggle="customaction" class="btn rounded {{($all) ?'btn-warning' :'btn-outline-primary'}}" {{($all) ?'disabled' :''}} data="{{$edi->id}},'All',1" data-accept-btn-type="all">
                            <span class="d-none spinner spinner-border spinner-border-sm p-2" role="status" aria-hidden="true"></span> {{($all) ?'Accepted All' :'Accept All'}} 
                        </button>
                    </span>
                </div>
            </div>

            <table id="EdiDiffereence" class="table table-bordered border-primary">
                <thead>
                    <tr class="table-primary">
                        <th class="w-50"><i class="material-icons" style="vertical-align:middle;">input</i> BDP SI Outgoing
                            (@php echo basename($edi->data); @endphp)</th>
                        <th class="w-50"><i class="material-icons" style="vertical-align:middle; transform:rotate(180deg);">input</i> Carrier BL Incoming
                            (@php echo basename($compare_to_data->data)@endphp)</th>
                    </tr>
                </thead>
                <tbody> 
                    @php $is_warning_present = 0; @endphp
                    @foreach ($rules as $rule)
                    <tr class="{{ in_array($rule['id'] , $diff_rows['diff_rule_id'])  ?'' :'collapse rld'}}">
                        <th colspan="2" class="table-secondary border-end border-2 boarder-info">

                            <i class="material-icons"
                               style="vertical-align:middle;">label</i><span id="title-{{$rule['id']}}">@php echo str_replace('+','::',$rule['rules']); @endphp ({{ $rule['name'] }}) (Compare Elements: {{!empty($rule['default_compare_elements']) ? $rule['default_compare_elements'] : "ALL"}})</span> 
                            @if(in_array($rule['id'], $diff_rows['diff_rule_id']))
                            @php
                            $is_accepted = '';
                            $is_bdp_reject = '';
                            $is_carrier_reject = '';
                            foreach ($metas as $meta) {
                            if ($rule['id'] == $meta['rules_id'] && $meta['is_accepted']) {
                            $is_accepted = 1;
                            }
                            if ($rule['id'] == $meta['rules_id'] && $meta['is_bdp_reject']) {
                            $is_bdp_reject = 1;
                            $is_warning_present = 1;
                            }
                            if ($rule['id'] == $meta['rules_id'] && $meta['is_carrier_reject']) {
                            $is_carrier_reject = 1;
                            $is_warning_present = 1;
                            }
                            }
                            @endphp

                <div class="btn-grp float-right">
                    <button data-bs-toggle="customaction" type="button" class="btn single_accept_btn {{($is_accepted)?'btn-warning':'btn-outline-primary'}}"
                            data="{{$edi->id}},{{$rule['id']}},1" {{($is_accepted)?'disabled':''}} data-accept-val={{($is_accepted)? 1: 0}} data-srow-is-warning-present="{{($is_carrier_reject == true || $is_bdp_reject == true)? 1:0}}" data-accept-btn-type="single">
                        <span class="d-none spinner spinner-border spinner-border-sm p-2" role="status" aria-hidden="true"></span>
                        {{($is_accepted)?'Accepted':'Accept'}}
                    </button>
                    <button type="button" class="btn {{($is_carrier_reject)?'btn-outline-warning':'btn-outline-primary'}} carrier_reject_btn"
                            data="{{$edi->id}},{{$rule['id']}},2" {{($is_accepted)?'disabled':''}} data-bs-toggle="modal"
                            data-bs-target="#actionModal">Carrier Reject
                    </button>
                    <button type="button" class="btn {{($is_bdp_reject)?'btn-outline-warning':'btn-outline-primary'}} bdp_reject_btn"
                            data="{{$edi->id}},{{$rule['id']}},3" {{($is_accepted)?'disabled':''}} data-bs-toggle="modal"
                            data-bs-target="#actionModal">BDP Reject
                    </button>
                </div>
                @endif
                </th>
                </tr>

                <tr class="{{ in_array($rule['id'] , $diff_rows['diff_rule_id']) ?'' :'collapse rld'}}">
                    <td class="w-50 h-25">
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
                    <td class="w-50 h-25"> 
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
                </tr>
                @endforeach 

                </tbody>
            </table>
            <span id="is_car_bdp_err_present" data-val="{{($is_warning_present)? 1:0}}"></span>
        </div>
    </div>
    <!-- Modal for email popup-->
    <div class="modal fade" id="ediEmailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="ediEmailHtml">
                <div class="m-5 position-relative p-5">
                    <div class="spinner-grow text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-secondary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-success" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-danger" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-light" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-dark" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Modal for each segment buttons -->
    <div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="actionHtml">
                <div class="m-5 position-relative p-5 align-middle">
                    <div class="spinner-grow text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-secondary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-success" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-danger" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-light" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-dark" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for each segment buttons -->
    <div class="modal fade" id="downloadAction" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="downloadActionHtml">
                <div class="m-5 position-relative p-5 align-middle">
                    <div class="spinner-grow text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-secondary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-success" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-danger" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-light" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-dark" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/container-->
</div>
<style>
    table#EdiDiffereence thead tr {
        border: 2px 0;
        border-color: #ffffff;
    }

    table#EdiDiffereence tbody tr {
        border: 2px 0;
        border-color: #cfefff;
    }

    .btn-grp .btn {
        margin-right: 5px;
    }
    .switch.ios, .switch-on.ios, .switch-off.ios { border-radius: 20rem; }
    .switch.ios .switch-handle { border-radius: 20rem; }
</style>
<script>
 $(document).ready(function(){

    $('.edi_country_documents_btn').on('click', function(){
    if ($(".edi_country_documents").is(":visible")){
    $(".edi_country_documents").addClass('d-none');
    } else{
    $(".edi_country_documents").removeClass('d-none');
    }
    });

    var accept_btn_count = $(".single_accept_btn").length;
    console.log(accept_btn_count);
    var has_accepted_records_count = 0;
    if(accept_btn_count > 0)
    {
        $.each($('.single_accept_btn'), function(index, value)
        {
            if($(value).attr('data-accept-val') == 1)
                has_accepted_records_count++;
        });
        if(accept_btn_count == has_accepted_records_count)
        {
            $('[data-bs-toggle="customaction"]').prop('disabled', true).addClass('btn-warning').removeClass('btn-outline-primary').text('Accepted').first().text('Accepted All');
            $("#docstatus").attr('data-is-accepted-all', 1);
        }
        console.log(has_accepted_records_count);
    }

            $('[data-bs-toggle="customaction"]').on('click', function(){
            let accept_btn_type = $(this).attr('data-accept-btn-type');
            if(accept_btn_type == "all") {
                let has_error = $("#is_car_bdp_err_present").attr('data-val');
                if(has_error == 1){
                    alert("Please clear reject messages and then click accept");
                    return false;
                }
            }
            else{
                let has_error = $(this).attr('data-srow-is-warning-present');
                if(has_error == 1){
                    alert("Please clear reject messages and then click accept");
                    return false;
                }
            }    
            let thisel = $(this);
            let params = $(this).attr('data').split(',');
            let ediId = params[0];
            let rules = params[1];
            let is_accepted = params[2];
            let rulesToChange = [];
            if (isNaN(rules) && is_accepted == 1){
    $.each($('[data-bs-toggle="customaction"]'), function(index, value){
    var param = $(value).attr('data').split(',');
            if (!isNaN(param[1]))
            rulesToChange.push(param[1]);
    });
            console.log('Accept ALL');
            var option = confirm('Are you sure, you want to  Accepted All?');
    }

    if (!isNaN(rules) && is_accepted == 1){
    console.log('Accept for (' + rules + ') now accepted');
            rulesToChange.push(rules);
            var option = true;
    }



    if (option) {
    $(this).prop('disabled', true);
            $(this).find('span.spinner').removeClass('d-none');
            $.ajax({
            url: base_url + "/save-accept-action",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    method: "POST",
                    data: {ediId: ediId, rules: rulesToChange},
                    beforeSend: function () {
                    //  console.log(this.data)
                    },
                    success: function (res) {
                    console.info(rulesToChange.length);
                            //thisel.prop('disabled',false);
                            thisel.find('span.spinner').addClass('d-none');
                            thisel.addClass('btn-warning').removeClass('btn-outline-primary');
                            if (rulesToChange.length == 1){
                            thisel.text('Accepted');
                            thisel.next().prop('disabled', true);
                            thisel.next().next().prop('disabled', true);

                            var accept_btn_count = $(".single_accept_btn").length;
                            var has_accepted_records_count = 0;
                            thisel.attr('data-accept-val', 1);
                            $.each($('.single_accept_btn'), function(index, value)
                            {
                                if($(value).attr('data-accept-val') == 1)
                                    has_accepted_records_count++;
                            });
                            if(accept_btn_count == has_accepted_records_count)
                            {
                                $('[data-bs-toggle="customaction"]').prop('disabled', true).addClass('btn-warning').removeClass('btn-outline-primary').text('Accepted').first().text('Accepted All');
                                $("#docstatus").attr('data-is-accepted-all', 1);
                            }

                    } else{
                    $('[data-bs-toggle="customaction"]').prop('disabled', true).addClass('btn-warning').removeClass('btn-outline-primary').text('Accepted').first().text('Accepted All');
                            $(".carrier_reject_btn").prop('disabled', true);
                            $(".bdp_reject_btn").prop('disabled', true);
                            $("#docstatus").attr('data-is-accepted-all', 1);
                    }

                    }
            });
    } else{
    //
    }
    });
            $('#show-all-diff-only').change(function(){

    //console.log($(this).prop('checked'));
    if ($(this).prop('checked') == true){
    $('.rld').removeClass('collapsing show').addClass('collapse');
    } else{
    $('.rld').removeClass('collapse').addClass('collapsing show');
    }

    });
            $('#docstatus').change(function(){
                let is_accpeted_all = $(this).attr('data-is-accepted-all');
                if(is_accpeted_all != 1)
                {
                    alert("Please accept all then you can close the file!");
                    $(this).parent().removeClass('btn-danger').addClass('btn-dark').addClass('off');
                    return false;
                }
    var option = confirm('Are you sure, you want to Close?');
            if (option) {
    var id = $(this).attr('data');
            var thisel = $(this);
            if ($(this).prop('checked') == true){
    $.ajax({
    url: base_url + "/save-document-status",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            data: {id: id},
            beforeSend: function () {
            //  console.log(this.data)
            },
            success: function (res) {
            console.info(res);
                    thisel.prop('disabled', true);
                    //alert("Success");
                    //location.reload();
            }
    });
    }
    } else{
    $(this).parent().removeClass('btn-primary off');
            $(this).parent().addClass('btn-danger off');
    }

    });
    });
</script>
</x-app-layout>
