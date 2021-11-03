<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard >')}} 
            <x-nav-link :href="route('carrier.list')">
                {{ __('Rules List') }}
            </x-nav-link>
            @if(!empty($row->id))
            {{ __('> Edit Rules') }}
            @else
            {{ __('> Add Rules') }}
            @endif
        </h2> 
    </x-slot>

    <div class="row d-flex justify-content-center">
        <!--Grid column-->
        <div class="col-md-8">

            <div class="container w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
                <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />  
                    @if(!empty($row->id))
                    <form method="POST" action="{{ url('rule/update/'.$row->id) }}"> 
                        @else
                        <form method="POST" action="{{ route('rule.add') }}">
                            @endif
                            @csrf
                            <div class="row"> 
                                <label class="col-sm-3 col-form-label">Active:</label>
                                <div class="col-sm-9"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input name="status" type="checkbox" value="1"  class="form-check-input" @if(old('status',isset($row->status)? $row->status : '')== 1) checked @endif >
                                </div>
                            </div>
                            <div class="row"> 
                                <label class="col-sm-3 col-form-label">Free Text Compare:</label>
                                <div class="col-sm-9"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input name="is_free_text_compare" type="checkbox" value="1"  class="form-check-input" @if(old('is_free_text_compare',isset($row->is_free_text_compare)? $row->is_free_text_compare : '')== 1) checked @endif >
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Rule Name:</label>
                                <div class="col-sm-9">
                                    <x-input class="block mt-1 w-full" type="text" name="name" :value="old('name', isset($row->name)? $row->name : '' )" autofocus placeholder="Fule Title Name"/>  
                            </div>
                        </div>

                        <div class="row">
                            <label class="col-sm-3 col-form-label">Rules:</label>
                            <div class="col-sm-9">
                                {{isset($row->rules)? $row->rules : ''}}
                            </div>
                        </div>


                        <div class="row">
                            <label class="col-sm-3 col-form-label">Default Compare Elements:</label>
                            <div class="col-sm-9">
                                <x-input class="block mt-1 w-full" type="text" name="default_compare_elements" :value="old('default_compare_elements', isset($row->default_compare_elements)? $row->default_compare_elements : '' )" autofocus placeholder="Default Compare Elements"/> 
                                         <div>Put with comma separated like 1, 2.2, 2.4, 5,6.1, 6.4, 6.2</div>
                            </div>
                        </div>
                        <br/> 
                        <div class="row">
                            <label class="col-sm-3 col-form-label">Priority:</label>
                            <div class="col-sm-3">
                                <x-input class="block mt-1 w-full" type="number" name="priority" :value="old('priority', isset($row->priority)? $row->priority : '' )" required autofocus placeholder="Set Priority"/> 
                        </div>

                    </div>
                    <br/> 

                    <div class="row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-9">
                            <x-button>
                                {{ __('Save') }}
                            </x-button>
                            &nbsp;&nbsp;
                            <x-button type="reset">
                                {{ __('Reset') }}
                            </x-button>

                            &nbsp;&nbsp;
                            <x-nav-link :href="route('rule.list')">
                                {{ __('Cancel') }}
                                </x-button>
                        </div>

                    </div>
                </form>
        </div>
    </div>
</div>
</div>
</x-app-layout>
