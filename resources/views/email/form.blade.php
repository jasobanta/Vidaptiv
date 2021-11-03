@php
if(!empty($row->id)){
$template_for = explode(",",$row->template_types);
}
@endphp
<x-app-layout> 
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard >')}} 
            <x-nav-link :href="route('email.list')">
                {{ __('Email List') }}
            </x-nav-link>
            @if(!empty($row->id))
            {{ __('> Edit Email') }}
            @else
            {{ __('> Add Email') }}
            @endif
        </h2> 
    </x-slot>
    <div class="container w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
        <!--Card-->
        <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">
            <div class="row">

                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    @if(!empty($row->id))
                    <form method="POST" action="{{ url('email/update/'.$row->id) }}"> 
                        @else
                        <form method="POST" action="{{ route('email.add') }}">
                            @endif
                            @csrf

                            <div class="row">
                                <div class="col-sm-3">
                                    Active:
                                </div>
                                <div class="col-sm-9">
                                    <p class="ml-4"><input name="status" type="checkbox" value="1"  class="form-check-input" @if(old('status',isset($row->status)? $row->status : '')== 1) checked @endif ></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    Template Type:
                                </div>
                                <div class="col-sm-9">
                                    <select name="type_id" id="type_id" class="block mt-1 w-full">
                                        <option value="0">Select Template Type</option>
                                        @if(!empty($types))
                                        @foreach($types as $key =>$val )
                                        <option value="{{$key}}" {{ $key === old('type_id',  $row->type_id  ?? 0 ) ? 'selected' : '' }}>{{$val}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="row">&nbsp;</div>

                            <div class="row">
                                <div class="col-sm-3">
                                    Template For:
                                </div>
                                <div class="col-sm-9">
                                    <p class="ml-4"><input type="checkbox" class="form-check-input" value="0" name="template_types[]" @if(isset($template_for) && in_array("0", $template_for)) checked @endif> Carrier</p>
                                    <p class="ml-4"><input type="checkbox" class="form-check-input" value="1" name="template_types[]" @if(isset($template_for) && in_array("1", $template_for)) checked @endif> Owner</p>
                                    <p class="ml-4"><input type="checkbox" class="form-check-input" value="2" name="template_types[]" @if(isset($template_for) && in_array("2", $template_for)) checked @endif> Other</p>
                                </div>
                            </div>
                            <div class="row">&nbsp;</div>
                            <div class="row">
                                <div class="col-sm-3">
                                    Template Title:
                                </div>
                                <div class="col-sm-9">
                                    <x-input id="template_title" class="block mt-1 w-full" type="text" name="template_title" :value="old('template_title', isset($row->template_title)? $row->template_title : '' )" required autofocus placeholder="Enter Template Title"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                Email Subject:
                            </div>
                            <div class="col-sm-9">
                                <x-input id="subject" class="block mt-1 w-full" type="text" name="subject" :value="old('subject', isset($row->subject)? $row->subject : '' )" required autofocus placeholder="Enter Email Subject"/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            Email Body:
                        </div>
                        <div class="col-sm-9">
                            <textarea id="message" rows="5" class="block mt-1 w-full" type="text" name="message"  title="Enter Email Body"  placeholder="Enter Email Body">{{{ old('message', isset($row->message)? $row->message : '') }}}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            Email Signature:
                        </div>
                        <div class="col-sm-9">
                            <textarea id="signature" rows="5" class="block mt-1 w-full" type="text" name="signature"  title="Enter Email Signature"  placeholder="Enter Email Signature">{{{ old('signature', isset($row->signature)? $row->signature : '') }}}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            Email To:
                        </div>
                        <div class="col-sm-9">
                            <x-input id="email_to" class="block mt-1 w-full" type="text" name="email_to" :value="old('email_to', isset($row->email_to)? $row->email_to : '')" required autofocus  placeholder="Enter Email To here" />
                                     <p><small>TO email like ##owner_email##, abc@gmail.com, xyz@gmail.com </p>    

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            Email CC:
                        </div>
                        <div class="col-sm-9">
                            <x-input id="email_cc" class="block mt-1 w-full" type="text" name="email_cc" :value="old('email_cc', isset($row->email_cc)? $row->email_cc : '')" autofocus placeholder="Enter Email CC here" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3">
                        Email BCC:
                    </div>
                    <div class="col-sm-9">
                        <x-input id="email_bcc" class="block mt-1 w-full" type="text" name="email_bcc" :value="old('email_bcc', isset($row->email_bcc)? $row->email_bcc : '')" autofocus placeholder="Enter Email BCC here"/>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    EDI Status:
                </div>
                <div class="col-sm-9">
                    <select name="edi_title_id" id="edi_title_id" class="block mt-1 w-full">
                        <option value="0">Select Status</option>
                        @if(!empty($edi_titles))
                        @foreach($edi_titles as $key =>$val )
                        <option value="{{$key}}" {{ $key === old('edi_title_id',  $row->edi_title_id  ?? 0 ) ? 'selected' : '' }}>{{$val}}</option>
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
                    <x-button type="reset" class="ml-4">
                        {{ __('Reset') }}
                    </x-button>

                    &nbsp;&nbsp;
                    <x-nav-link :href="route('email.list')">
                        {{ __('Cancel') }}
                        </x-button>
                </div>
            </div>

        </form>
</div>
<div class="col-sm-2"> </div>  
</div>
<div class="row">
    <div class="col-sm-12">Short Codes: </div> 
    <div class="col-sm-12">Booking Number: ##booking_no##</div>
    <div class="col-sm-12">FF Number: ##ff_no##</div>
    <div class="col-sm-12">BL Number: ##bl_no##</div>
    <div class="col-sm-12">Carrier: ##carrier##</div>
    <div class="col-sm-12">Link: ##click_here##</div>
    <div class="col-sm-12">Url: ##url##</div>
    <div class="col-sm-12">Owner email: ##owner_email##</div>
    <div class="col-sm-12">Carrier email: ##carrier_email##</div>
</div>
</div>

</div>
</x-app-layout>
