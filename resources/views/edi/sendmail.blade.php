<div class="row m-3" id='edidata'>
    <!--Container-->
    <div class="col-sm-9 m-2" style="margin-top:25px;margin-bottom:25px;">{!! nl2br($AllReqData['message']) !!}</div>
    @php
    $diff_rows = $AllReqData['diff_rows'];
    @endphp

    @if($AllReqData['intent']=='carrier')
    @if(!empty($AllReqData['metaFormated_carrier_reject']))
    <div class="table-responsive">
        <table id="EdiDiffereence" class="table table-bordered" style="border:2px solid #17a2b8!important;">
            <thead>
                <tr class="table-primary" style="background-color:#007bff!important;">
                    <th style="width:45%">BDP SI Outgoing (@php echo basename($AllReqData['edi']->data); @endphp)</th>
                    <th style="width:45%">Carrier BL Incoming (@php echo basename($AllReqData['compare_to_data']->data)@endphp)</th>
                    <th style="width:10%">Comments</th>
                </tr>
            </thead>
            <tbody style="border:2px solid #17a2b8!important;">
                @foreach ($AllReqData['rules'] as $rule)
                @if(in_array($rule['id'], $AllReqData['metaFormated_carrier_reject'], true))
                <tr>
                    <th colspan="3" class="table-secondary" style="background-color:#17a2b8!important;">
                        @php echo str_replace('+','::',$rule['rules']); @endphp ({{ $rule['name'] }})
                    </th>
                </tr>

                <tr class="table-light" style="background-color:#f8f9fa!important;" >
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
                        @foreach($AllReqData['metas'] as $meta)
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
    @endif
    @endif

    @if($AllReqData['intent']=='owner' || $AllReqData['intent']=='other')
    @if(!empty($AllReqData['metaFormated_carrier_reject']) || !empty($AllReqData['metaFormated_bdp_reject']) )
    <div class="table-responsive">
        <table id="EdiDiffereence" class="table table-bordered" style="border:2px solid #17a2b8!important;">
            <thead>
                <tr class="table-primary" style="background-color:#007bff!important;">
                    <th style="width:40%">BDP SI Outgoing (@php echo basename($AllReqData['edi']->data); @endphp)</th>
                    <th style="width:40%">Carrier BL Incoming (@php echo basename($AllReqData['compare_to_data']->data)@endphp)</th>
                    <th style="width:10%">Carrier Reject Comments</th>
                    <th style="width:10%">BDP Reject Comments</th>
                </tr>
            </thead>
            <tbody>
                @foreach($AllReqData['rules'] as $rule)
                @if(in_array($rule['id'], $AllReqData['metaFormated_carrier_reject'], true) || in_array($rule['id'], $AllReqData['metaFormated_bdp_reject'], true) )
                <tr style="border:2px solid #17a2b8!important;">
                    <th colspan="4" class="table-secondary" style="background-color:#17a2b8!important;">
                        @php echo str_replace('+','::',$rule['rules']); @endphp ({{ $rule['name'] }})
                    </th>
                </tr>

                <tr class="table-light" style="background-color:#f8f9fa!important; border:2px solid #17a2b8!important;" >
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
                    <td style="width:40%">@php
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
                        @foreach($AllReqData['metas'] as $meta)
                        @if( ($rule['id'] == $meta['rules_id']) && ($meta['is_carrier_reject']==1) )
                        {{ $meta['carrier_reject_msg'] }}
                        @endif
                        @endforeach
                    </td>                        
                    <td style="width:10%">
                        @foreach($AllReqData['metas'] as $meta)
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
    @endif
    @endif
    <div>
        {!! nl2br($AllReqData['signature']) !!}
    </div>
</div>