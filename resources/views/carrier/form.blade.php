<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard >')}} 
            <x-nav-link :href="route('carrier.list')">
                {{ __('Carrier List') }}
            </x-nav-link>
            @if(!empty($row->id))
            {{ __('> Edit Carrier') }}
            @else
            {{ __('> Add Carrier') }}
            @endif
        </h2> 
    </x-slot>

    <div class="container w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
        <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">
            <x-auth-validation-errors class="mb-4" :errors="$errors" />  
            @if(!empty($row->id))
            <form method="POST" action="{{ url('carrier/update/'.$row->id) }}"> 
                @else
                <form method="POST" action="{{ route('carrier.add') }}">
                    @endif
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Carrier Name:</label>
                                <div class="col-sm-8">
                                    <x-input class="block mt-1 w-full" type="text" name="carrier_name" :value="old('carrier_name', isset($row->carrier_name)? $row->carrier_name : '' )" required autofocus placeholder="Enter carrier name"/>
                            </div>
                        </div>
                    </div>


                    <div class="col">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">IS FTP:</label>
                            <div class="col-sm-8">
                                <input name="is_ftp" type="checkbox" value="1"  class="form-check-input" @if(old('is_ftp',isset($row->is_ftp)? $row->is_ftp : '')== 1) checked @endif >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Carrier Email Contact:</label>
                            <div class="col-sm-8">
                                <x-input class="block mt-1 w-full" type="text" name="carrier_email" :value="old('carrier_email', isset($row->carrier_email)? $row->carrier_email : '' )" autofocus placeholder="Enter email contact"/>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">FTP Location:</label>
                        <div class="col-sm-8">
                            <x-input class="block mt-1 w-full" type="text" name="ftp_location" :value="old('ftp_location', isset($row->ftp_location)? $row->ftp_location : '' )" autofocus placeholder="Enter ftp location"/>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">BDP Owner(s):</label>
                    <div class="col-sm-8">
                        <x-input class="block mt-1 w-full" type="text" name="bdp_owner" :value="old('bdp_owner', isset($row->bdp_owner)? $row->bdp_owner : '' )" autofocus placeholder="Enter BDP owner"/>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">FTP userid:</label>
                <div class="col-sm-8">
                    <x-input class="block mt-1 w-full" type="text" name="ftp_userid" :value="old('ftp_userid', isset($row->ftp_userid)? $row->ftp_userid : '' )" autofocus placeholder="Enter ftp userid"/>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Carrier SCAC:</label>
            <div class="col-sm-8">
                <x-input class="block mt-1 w-full" type="text" name="carrier_scac" :value="old('carrier_scac', isset($row->carrier_scac)? $row->carrier_scac : '' )" autofocus placeholder="Enter carrier SCAC"/>
        </div>
    </div>
</div>


<div class="col">
    <div class="form-group row">
        <label class="col-sm-4 col-form-label">FTP password:</label>
        <div class="col-sm-8">
            <x-input class="block mt-1 w-full" type="text" name="ftp_password" :value="old('ftp_password', isset($row->ftp_password)? $row->ftp_password : '' )" autofocus placeholder="Enter ftp password"/>
    </div>
</div>
</div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Active:</label>
            <div class="col-sm-8"> 
                <input name="status" type="checkbox" value="1"  class="form-check-input" @if(old('status',isset($row->status)? $row->status : '')== 1) checked @endif >
            </div>
        </div>
    </div>

    <div class="col">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Message Type:</label>
            <div class="col-sm-8">

                <div class="radio">
                    <label><input type="radio" name="folder_type" value="IN" @if(old('folder_type',isset($row->folder_type)? $row->folder_type : '')== 'IN') checked @endif> IN</label>
                    &nbsp;&nbsp;
                    <label><input type="radio" name="folder_type" value="OUT" @if(old('folder_type',isset($row->folder_type)? $row->folder_type : '')== 'OUT') checked @endif> OUT</label>
                </div>

            </div>
        </div>
    </div>

</div>


<div class="row">
    <div class="col">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Reply via Email:</label>
            <div class="col-sm-8">
                <input name="reply_via_email" type="checkbox" value="1"  class="form-check-input" @if(old('reply_via_email',isset($row->reply_via_email)? $row->reply_via_email : '')== 1) checked @endif >

            </div>
        </div>
    </div>
    <div class="col">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">FTP Folder Location:</label>
            <div class="col-sm-8">
                <x-input class="block mt-1 w-full" type="text" name="folder_location" :value="old('folder_location', isset($row->folder_location)? $row->folder_location : '' )" autofocus placeholder="Enter ftp folder location"/>
                         <div>
                        You can set other folder dir like /site/wwwroot/XYZ <br/>
                        Default location will be Carrier <b>SCAC Code</b>
                    </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label"></label>
            <div class="col-sm-8">
                <x-button class="ml-4">
                    {{ __('Save') }}
                </x-button>
                &nbsp;&nbsp;
                <x-button type="reset" class="ml-4">
                    {{ __('Reset') }}
                </x-button>

                &nbsp;&nbsp;
                <x-nav-link :href="route('carrier.list')">
                    {{ __('Cancel') }}
                    </x-button>
            </div>
        </div>
    </div>
</div>

</form>
</div>
</div>
</x-app-layout>
