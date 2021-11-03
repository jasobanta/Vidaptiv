<x-app-layout>
    <x-slot name="header">
        <div class="float-left">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard > Email History') }}
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
                            <th data-priority="1">{{ __('From') }}</th>
                            <th data-priority="2">{{ __('To') }}</th>
                            <th data-priority="3">{{ __('CC') }}</th>
                            <th data-priority="4">{{ __('BCC') }}</th>
                            <th data-priority="5">{{ __('Subject') }}</th>
                            <th data-priority="6">{{ __('Message') }}</th>
                            <th data-priority="7">{{ __('Module') }}</th>
                            <th data-priority="8">{{ __('Sent By') }}</th>
                            <th data-priority="8">{{ __('Sent On') }}</th>
                            <th data-priority="9">{{ __('Actions') }}</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                        <tr>
                            <td>{{$row->email_from}}</td>
                            <td>{{$row->email_to}}</td>
                            <td>{{$row->email_cc}}</td>
                            <td>{{$row->email_bcc}}</td>
                            <td>{{$row->subject}}</td>
                            <td>{{substr($row->message,0,30)}}</td>
                            <td>{{$row->module}}</td>
                            <td>
                                @if(isset($row->user))
                                    {{$row->user->name}}
                                @else
                                    System
                                @endif          
                            </td>
                            <td>{{ $row->created_at->format('d M Y H:i:s') }}</td>
                            <td>
                                <div class="row">

                                    @if (isset($row->attachment->id))
                                        <div class="col-6">
                                            <a href="{{ url('download-email-attachment?path='.$row->attachment->file_path) }}">
                                                <i class="material-icons" style="vertical-align:middle;" title="Download Attachment">attachment</i>
                                            </a>
                                        </div>
                                         <div class="col-6">
                                    @else
                                        <div class="col-12">
                                    @endif    
                                        <a href="{{ url('email/history/formstate/'.$row->id) }}" target="_blank">
                                            <i class="material-icons" style="vertical-align:middle;" title="View FormState">summarize</i>
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
        var table = $('#emailhistory').DataTable({
            "responsive": true,
            "stateSave": true,
        })
    });
</script>
