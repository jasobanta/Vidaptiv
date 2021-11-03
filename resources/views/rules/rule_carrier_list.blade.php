<x-app-layout>
    <x-slot name="header">
        <div class="float-left">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
                <x-nav-link :href="route('rule.list')">
                    {{ __('> Rule List') }}
                </x-nav-link> 
                {{ __('> Carrier List') }}
            </h2>
        </div> 
        <div class="float-right">

        </div> <br/>
    </x-slot>

    <div class="w-full"> 
        <div class="w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
            <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">
                @if(Session::has('add_message'))
                <p class="alert alert-success">{{ Session::get('add_message') }}</p>
                @elseif(Session::has('update_message'))
                <p class="alert alert-success">{{ Session::get('update_message') }}</p>
                @elseif(Session::has('delete_message'))
                <p class="alert alert-success">{{ Session::get('delete_message') }}</p>
                @elseif(Session::has('delete_error'))
                <p class="alert alert-danger">{{ Session::get('delete_error') }}</p>
                @endif
                <table id="rule_carrierlist" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                    <thead>
                        <tr> 
                            <th width="15%" >{{ __('Carrier Name') }}</th>
                            <th width="15%" >{{ __('SCAC Code') }}</th>
                            <th width="30%" >{{ __('Ignore Rules') }}</th>
                            <th width="30%" >{{ __('Compare Element Rules') }}</th>
                            <th width="10%" nowrap>{{ __('Assign Rules') }}</th>     
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)

                        <tr>
                            <td>{{$row['carrier_name']}}</td>
                            <td>{{$row['carrier_scac']}}</td>
                            <td>
                                @if($row['get_carrier_ignore_rules'])
                                <?php $i = 0; $max = count($row['get_carrier_ignore_rules']); ?>
                                @foreach($row['get_carrier_ignore_rules'] as $rule_data)
                                <?php $i++; ?>
                                {{ !empty($rules_data[$rule_data['rule_id']]['rules']) ?  $rules_data[$rule_data['rule_id']]['rules'] : "" }}
                                <?php if($i < $max){
                                    echo ", ";
                                } ?>
                                @endforeach
                                @endif

                            </td>
                            <td>
                                @if($row['get_carrier_compare_element_rules'])
                                <?php $i = 0; $max = count($row['get_carrier_compare_element_rules']); ?>
                                @foreach($row['get_carrier_compare_element_rules'] as $rule_data)
                                <?php $i++; ?> 
                                {{ !empty($rules_data[$rule_data['rule_id']]['rules']) ?  $rules_data[$rule_data['rule_id']]['rules'] : "" }}
                                <?php if($i < $max){
                                    echo ", ";
                                } ?>
                                @endforeach
                                @endif

                            </td>
                            <td>
                                <div class="row">
                                    <div class="col-6">
                                        <a href="{{ url('rule/carrier/edit/'.$row['id']) }}">
                                            <img src="{{ asset('img/edit.svg') }}" class="action_icons" title="edit" alt="edit"/>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
        <!--/container-->
    </div>
</x-app-layout>
<script>
    $(document).ready(function () {
        var table = $('#rule_carrierlist').DataTable({
            "order": [[0, "asc"]],
            "stateSave": true,
        })
    });
</script>
