<x-app-layout>
    <x-slot name="header">
        <div class="float-left">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard > File Logs') }}
            </h2>
        </div> 
    </x-slot>

    <div class="w-full">
        <!--Container-->
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
                <table id="emailhistory" class="display responsive nowrap stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                    <thead>
                        <tr>
                            <th data-priority="1">{{ __('Carrier Name') }}</th>
                            <th data-priority="2">{{ __('Total Count') }}</th>
                            <th data-priority="3">{{ __('Last 24hrs Count') }}</th>
                            <th data-priority="4">{{ __('Actions') }}</th>
                            <!--th data-priority="5">{{ __('Booking Number') }}</th>
                            <th data-priority="6">{{ __('FF Number') }}</th>
                            <th data-priority="7">{{ __('Actions') }}</th-->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                        <tr>
                            <td>{{ $row['carrier'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ $row['count_in_24hrs'] }}</td>
                            <td><a href="<?php echo route('filelog.carrier.list',$row['scac']); ?>" title="View {{ $row['scac'] }} records"><i class='material-icons' style='vertical-align:middle;'>view_list</i></a></td>
                            <!--td><input type="text" /></td>
                            <td><input type="text" /></td>
                            <td>
                                <div class="row">

                                        <div class="col-6">
                                            <a href="{{ url('download-email-attachment?path=') }}">
                                                <i class="material-icons" style="vertical-align:middle;" title="Download Attachment">attachment</i>
                                            </a>
                                        </div>
                                         <div class="col-6">
                                        <div class="col-12">
                                        <a href="{{ url('email/history/formstate/') }}" target="_blank">
                                            <i class="material-icons" style="vertical-align:middle;" title="View FormState">summarize</i>
</a>
                                    </div>
                                </div>
                                
                            </td-->
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
        var table = $('#emailhistory').DataTable({
            "responsive": true,
            "stateSave": true,
        })
    });
</script>
