<x-app-layout> 
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard >')}} 
            <x-nav-link :href="route('users')">
                {{ __('User List') }}
            </x-nav-link>
            @if(!empty($row->id))
            {{ __('> Edit User') }}
            @else
            {{ __('> Add User') }}
            @endif
        </h2> 
    </x-slot>
    <div class="container w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
        <!--Card-->
        <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">
            <div class="row">

                <div class="col-sm-1"></div>
                <div class="col-sm-10">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    @if(!empty($row->id))
                    <form method="POST" action="{{ url('user/update/'.$row->id) }}"> 
                        @else
                        <form method="POST" action="{{ route('user.add') }}">
                            @endif
                            @csrf

                            <div class="row">
                                <div class="col-sm-3">
                                    Active:
                                </div>
                                <div class="col-sm-9">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input name="active" type="checkbox" value="1"  class="form-check-input" @if(old('active',isset($row->active)? $row->active : '')== 1) checked @endif >
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    Name:
                                </div>
                                <div class="col-sm-9">
                                    <x-input id="subject" class="block mt-1 w-full" type="text" name="name" :value="old('name', isset($row->name)? $row->name : '' )" required autofocus placeholder="Enter Name"/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                Email:
                            </div>
                            <div class="col-sm-9">
                                <x-input class="block mt-1 w-full" type="email" name="email" :value="old('email', isset($row->email)? $row->email : '')" required autofocus  placeholder="Enter Email here" />
                        </div>
                    </div>

                    

                    <div class="row">
                        <div class="col-sm-3">
                            Password:
                        </div>
                        <div class="col-sm-9">
                            <x-input class="block mt-1 w-full" type="text" name="password" :value="old('password', '')" autofocus placeholder="Enter Password here" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            Confirm Password:
                        </div>
                        <div class="col-sm-9">
                            <x-input class="block mt-1 w-full" type="text" name="password_confirmation" :value="old('password_confirmation', '')" autofocus placeholder="Enter Confirm Password here"/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            Country:
                        </div>
                        <div class="col-sm-9">
                            <select name="country_id" id="" class="form-control">
                                <option value="">Select</option>
                                @foreach($all_countries as $single_country)
                                    <option value="{{$single_country['id']}}" @if (isset($row->country_id) && $row->country_id == $single_country['id']) selected='selected' @endif>{{$single_country['country_code']. " - ".$single_country['country_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-3">
                            Is Admin:
                        </div>
                        <div class="col-sm-9">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input name="is_admin" type="checkbox" value="1" id="is_admin" class=" form-check-input" @if(old('is_admin',isset($row->is_admin)? $row->is_admin : '')== 1) checked @endif >
                        </div>
                    </div>
                    

                    <div class="row carriers_access_div" style="margin-top: 5px;">
                        <div class="col-sm-3">
                            Select carriers for access:
                        </div>
                        <div class="col-sm-2"> 
                            <lable style="margin-right: 30px"> All: </lable>
                            <input name="is_all_carriers" type="checkbox" value="1" id="is_all_carriers"  class="form-check-input" @if(old('is_all_carriers',isset($row->is_all_carriers)? $row->is_all_carriers : '')== 1) checked @endif >
                        </div>
                        <div class="col-sm-7 user_carrier_ids_div" @if(!empty($row->is_all_carriers)) ? style="display: none" : "" @endif>
                             OR Select Carrier: 
                             <select name="user_carrier_ids[]" id="user_carriers" class="block mt-1 w-full d-none" multiple="multiple">
                                @if(!empty($carriers))
                                @foreach($carriers as $carrier )
                                <option value="{{$carrier['id']}}" {{ in_array($carrier['id'], old('user_carrier_ids',  $user_carrier_ids  ?? [] ) ) ? 'selected' : '' }}  >{{$carrier['carrier_name']}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div> 

                    <div class="row">
                        <div class="col-sm-3"> </div>
                        <div class="col-sm-9"><br/>
                            <x-button class="ml-4">
                                {{ __('Save') }}
                            </x-button>
                            &nbsp;&nbsp;
                            <x-button type="reset" class="ml-4 reset_user">
                                {{ __('Reset') }}
                            </x-button>

                            &nbsp;&nbsp;
                            <x-nav-link :href="route('users')">
                                {{ __('Cancel') }}
                                </x-button>
                        </div>
                    </div>

                </form>
        </div>
        <div class="col-sm-1"> </div>  
    </div>
</div>
</div>
<script type="text/javascript">
    function showhideCarriersAccessDiv() {
        if ($("#is_admin").is(':checked')) {
            $(".carriers_access_div").hide();
        } else {
            $(".carriers_access_div").show();
        }
    }
    
    $(document).ready(function () {
        $('#user_carriers').multiselect({
            numberDisplayed: 4,
            maxHeight: 250,
            includeSelectAllOption: true
        });

        $(document).on('click', '#is_admin', function (e) {
            showhideCarriersAccessDiv();
        });

        $(document).on('click', '.reset_user', function (e) {
            setTimeout(function () {
                showhideCarriersAccessDiv();
            }, 100);

        });


        $(document).on('click', '#is_all_carriers', function (e) {
            if ($(this).is(':checked')) {
                $(".user_carrier_ids_div").hide();
            } else {
                $(".user_carrier_ids_div").show();
            }
        });

        if ($("#is_admin").is(':checked')) {
            $(".carriers_access_div").hide();

        }

    });
</script>
</x-app-layout>
