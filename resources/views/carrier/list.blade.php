<x-app-layout>
    <x-slot name="header">
        <div class="float-left">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard > Carrier List') }}
            </h2>
        </div> 
        <div class="float-right">
            <x-nav-link :href="route('carrier.add')">
                {{ __('Add New') }}
            </x-nav-link>
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
                <table id="carrierlist" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                    <thead>
                        <tr>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Carrier Name') }}</th>
                            <th>{{ __('Carrier Email') }}</th>
                            <th>{{ __('SCAC Code') }}</th>
                            <th>{{ __('BDP Owner') }}</th>
                            <th>{{ __('FTP location') }}</th> 
                            <th>{{ __('Reply') }}</th>
                            <th>{{ __('Active') }}</th>
                            <th>{{ __('Actions Icons') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                        <tr>
                            <td class="text-uppercase">{{ !empty($row->folder_type) ? $row->folder_type : "IN"}}</td>
                            <td>{{$row->carrier_name}}</td>
                            <td>{{$row->carrier_email}}</td>
                            <td>{{$row->carrier_scac}}</td>
                            <td>{{$row->bdp_owner}}</td>
                            <td>{{$row->ftp_location}}</td> 
                            <td>{{$row->reply_via_email_title}}</td>
                            <td>{{$row->status_title}}</td>
                            <td>
                                <div class="row">
                                    <div class="col-6">
                                        <a href="{{ url('carrier/edit/'.$row->id) }}">
                                            <img src="{{ asset('img/edit.svg') }}" class="action_icons" title="edit" alt="edit"/>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ url('carrier/delete/'.$row->id) }}" onclick="return confirm('Are you sure you want to DELETE?')">
                                            <img src="{{ asset('img/trash.svg') }}" class="action_icons" title="delete" alt="delete"/> 
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
        var table = $('#carrierlist').DataTable({
            //	responsive: true
            "order": [[1, "asc"]],
            "stateSave": true,
        })
    });
</script>
