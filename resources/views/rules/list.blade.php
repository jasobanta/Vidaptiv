<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard > List of Rules') }}
                </h2>
            </div>
            <div>
                <h2>
                    <a href="{{route('rule.carrier.list')}}" class="btn btn-secondary">{{ __('Carrier Rules') }}</a>
                    <!-- <a href="{{route('rule.segment.list')}}" class="btn btn-secondary">{{ __('Segment Rules') }}</a> -->

                </h2>
            </div>
        </div>
    </x-slot> 
    <div class="w-full">
        <!--Container-->
        <div class="container w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
            <!--Card-->
            <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">

                <table id="rules_list" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                    <thead>
                        <tr>
                            <th data-priority="1">Name</th>
                            <th data-priority="4">Rules</th>
                            <th data-priority="4">Compare Elements</th>
                            <th data-priority="2" style="width: 10%">{{ __('Active') }}</th>
                            <th data-priority="3">Type</th>
                            <th data-priority="5">Priority</th>
                            <th>{{ __('Actions Icons') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                        <tr rule_id='{{$row->id}}'>
                            <td>{{$row->name}}</td>
                            <td>{{$row->rules}}</td>
                            <td>{{$row->default_compare_elements}}</td>
                            <td>{{$row->status_title}}</td>
                            <td>{{$row->rule_type_id}}</td>

                            <td><span class="rules_priority">{{$row->priority}}</span></td>
                            <td>
                                <div class="row"> &nbsp;&nbsp;&nbsp;
                                    <a href="{{ url('rule/edit/'.$row->id) }}">
                                        <img src="{{ asset('img/edit.svg') }}" class="action_icons" title="edit" alt="edit"/>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
            <!--/Card-->

        </div>
        <!--/container-->
    </div>
</x-app-layout>
<script>
    $(document).ready(function () {
        var table = $('#rules_list').DataTable({
            "order": [[4, "asc"]],
            "stateSave": true,
        });

        $("#rules_list tbody").disableSelection();
    });
</script>
