<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard >')}} 
            <x-nav-link :href="route('edi-title.list')">
                {{ __('EDI Title List') }}
            </x-nav-link>
            @if(!empty($row->id))
            {{ __('> Edit EDI Status') }}
            @else
            {{ __('> Add EDI Status') }}
            @endif
        </h2> 
    </x-slot>

    <div class="row d-flex justify-content-center">
        <!--Grid column-->
        <div class="col-md-6">
            <div class="container w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
                <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">
<!--                    <x-auth-validation-errors class="mb-4" :errors="$errors" /> -->
                    @if(!empty($row->id))
                    <form method="POST" action="{{ url('edi-title/update/'.$row->id) }}"> 
                        @else
                        <form method="POST" action="{{ route('edi-title.add') }}">
                            @endif
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">EDI Title:</label>
                                        <div class="col-sm-9">
                                            <x-input class="block mt-1 w-full" type="text" name="title" :value="old('title', isset($row->title)? $row->title : '' )" required autofocus placeholder="Enter EDI Title"/>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Status Code:</label>
                                        <div class="col-sm-9">
                                            <x-input class="block mt-1 w-full" type="number" name="status_code" :value="old('status_code', isset($row->status_code)? $row->status_code : '' )" required autofocus placeholder="Enter EDI Status Code"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Status:</label>
                                    <div class="col-sm-9">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input name="status" type="checkbox" value="1"  class="form-check-input" @if(old('status',isset($row->status)? $row->status : '')== 1) checked @endif >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"></label>
                                    <div class="col-sm-9">
                                        <x-button class="ml-4">
                                            {{ __('Save') }}
                                        </x-button>
                                        &nbsp;&nbsp;
                                        <x-button type="reset" class="ml-4">
                                            {{ __('Reset') }}
                                        </x-button>
                                        &nbsp;&nbsp;
                                        <x-nav-link :href="route('edi-title.list')">
                                            {{ __('Cancel') }}
                                            </x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
        </div>
    </div>
</div>
</div>
</x-app-layout>
