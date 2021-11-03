@php
$email_type = isset($AllReqData['email_type']) ? $AllReqData['email_type'] : '';
$carrier_width = $email_type=='carrier' ? '40%' : '15%';
@endphp
<div class="row m-3" id='edidata'>
    @if($AllReqData['feedback']=='without')
    <div class="table-responsive">
        <table id="EdiDiffereence" class="table table-bordered" style="border:2px solid #17a2b8!important;">
            <thead>
                <tr class="table-primary" style="background-color:#007bff!important;">
                    <th style="width:45%">BDP SI Outgoing (@php echo basename($AllReqData['edi']->data); @endphp)</th>
                    <th style="width:45%">Carrier BL Incoming (@php echo basename($AllReqData['compare_to_data']->data)@endphp)</th>
                </tr>
            </thead>
            <tbody style="border:2px solid #17a2b8!important;">
                @foreach ($AllReqData['rules'] as $rule)
                <tr>
                    @if($AllReqData['downloadtype'] == 'pdf')    
                    <th colspan="2" class="table-secondary" style="background-color:#17a2b8!important;">
                        @else
                    <th colspan="2" class="table-secondary" style="background-color:#FFFF00;color:#000000;font-weight:bold;">
                        @endif

                        @php echo str_replace('+','::',$rule['rules']); @endphp ({{ $rule['name'] }}) (Compare Elements: {{!empty($rule['default_compare_elements']) ? $rule['default_compare_elements'] : "ALL"}})
                    </th>
                </tr>

                <tr class="table-light" style="background-color:#f8f9fa!important;" >
                    <td style="width:50%;vertical-align: top;"> 
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
                    <td style="width:50%; vertical-align: top; border-left:2px solid #17a2b8!important;">
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
    </div>
    @endif

    @if($AllReqData['feedback']=='with')

    <div class="table-responsive">
        <table id="EdiDiffereence" class="table table-bordered" style="border:2px solid #17a2b8!important;">
            <thead>
                <tr class="table-primary" style="background-color:#007bff!important;">
                    <th style="width:30%">BDP SI Outgoing (@php echo basename($AllReqData['edi']->data); @endphp)</th>
                    <th style="width:30%">Carrier BL Incoming (@php echo basename($AllReqData['compare_to_data']->data)@endphp)</th>
                    <th style="width:{{$carrier_width}}">Carrier Reject Comments</th>
                    @if($email_type!='carrier')
                    <th style="width:15%">BDP Reject Comments</th>
                    @endif
                </tr>
            </thead>
            <tbody> 
                @foreach($AllReqData['rules'] as $rule)
                <tr style="border:2px solid #17a2b8!important;">

                    @if($AllReqData['downloadtype'] == 'pdf')    
                    <th colspan="4" class="table-secondary" style="background-color:#17a2b8 !important;">
                        @else
                    <th colspan="4" class="table-secondary" style="background-color:#FFFF00;color:#000000;font-weight:bold;">
                        @endif
                        @php echo str_replace('+','::',$rule['rules']); @endphp ({{ $rule['name'] }}) (Compare Elements: {{!empty($rule['default_compare_elements']) ? $rule['default_compare_elements'] : "ALL"}})
                    </th>
                </tr>

                <tr class="table-light" style="background-color:#f8f9fa!important; border:2px solid #17a2b8!important;" >
                    <td style="width:30%; vertical-align: top;"> 
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
                    <td style="width:30%;vertical-align: top; border-left:2px solid #17a2b8!important;">
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
                    <td style="width:{{$carrier_width}}; vertical-align: top; border-left:2px solid #ff00aa!important;">            
                        @foreach($AllReqData['metas'] as $meta) 
                        @if($rule['id'] == $meta['rules_id'] && $meta['is_carrier_reject']==1)
                        {{ $meta['carrier_reject_msg'] }}
                        @endif
                        @endforeach
                    </td>          


                    @if($email_type!='carrier') 
                    <td style="width:15%; vertical-align: top; border-left:2px solid #17a2b8!important;">
                        @foreach($AllReqData['metas'] as $meta)
                        @if($rule['id'] == $meta['rules_id'] && $meta['is_bdp_reject']==1)
                        {{ $meta['bdp_reject_msg'] }}
                        @endif
                        @endforeach
                    </td>
                    @endif
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    @endif
</div>
