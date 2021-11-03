<x-app-layout>
    <x-slot name="header">
        <div class="float-left">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard > Users List') }}
            </h2>
        </div> 
        <div class="float-right">
            <x-nav-link :href="route('user.add')">
                {{ __('Add New') }}
            </x-nav-link>
        </div> <br/>
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
                <table id="userlist" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                    <thead>
                        <tr>
                            <th data-priority="1" style="width: 30%;">{{ __('Name') }}</th>
                            <th data-priority="2" style="width: 40%;">{{ __('Email') }}</th>
                            <th data-priority="2" style="width: 40%;">{{ __('Country') }}</th>
                            <th data-priority="3" style="width: 10%;">{{ __('All Carrier') }}</th>
                            <th data-priority="3" style="width: 5%;">{{ __('Active') }}</th>
                            <th data-priority="4" style="width: 5%;">{{ __('Admin') }}</th>
                            <th style="width: 10%;" nowrap>{{ __('Actions Icons') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                        <tr>
                            <td>{{$row->name}}</td>
                            <td>{{$row->email}}</td>
                            <td>{{$row->country_code}}</td>
                            <td>{{$row->is_all_carriers_title}}</td>
                            <td>{{$row->active_title}}</td>
                            <td>{{$row->is_admin_title}}</td> 
                            <td>
                                <div class="row">
                                    <div class="col-6">
                                        <a href="{{ url('user/edit/'.$row->id) }}">
                                            <img src="{{ asset('img/edit.svg') }}" class="action_icons" title="edit" alt="edit"/>
                                        </a>
                                    </div>
                                   <!-- <div class="col-6">
                                        <a href="{{ url('user/delete/'.$row->id) }}" onclick="return confirm('Are you sure you want to DELETE?')">
                                            <img src="{{ asset('img/trash.svg') }}" class="action_icons" title="delete" alt="delete"/> 
                                        </a>
                                    </div> -->
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
        var table = $('#userlist').DataTable({
            "stateSave": true,
            //	responsive: true
        })
    });
</script>
