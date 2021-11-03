<x-app-layout>
    <x-slot name="header">
        <div class="float-left">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
                <x-nav-link :href="route('rule.list')">
                    {{ __('> Rule List') }}
                </x-nav-link> 
                {{ __('> Segment List') }}
            </h2>
        </div> 
        <div class="float-right">

        </div> <br/>
    </x-slot>

    <div class="w-full">
        <div class="container w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
            <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white"> 

                <div class="row">
                    <div class="col-sm-12"><br/> 
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        @if(Session::has('success_message'))
                        <p class="alert alert-success">{{ Session::get('success_message') }}</p>
                        @elseif(Session::has('error_message'))
                        <p class="alert alert-danger">{{ Session::get('error_message') }}</p>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ url('rule/segment/add') }}"> 
                    @csrf
                    <div class="row m-3">
                        <div class="col-sm-12">
                            <select name="rule_id" id="segment_rule">
                                <option value="" fields="" > Select Rule </option>
                                @if(!empty($rules_data))
                                @foreach($rules_data as $key =>$rule)
                                <option value="{{$key}}" fields="{{$rule['rule_fields']}}" 
                                        {{ old('rule_id')== $key ? 'selected' : '' }}>{{$rule['rules']}}: {{$rule['name']}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row m-3">
                        <div class="col-sm-12">
                            <span id="rule_fields_inputs"></span>

                            <x-button id="rule_fields_button" class="ml-4" style="display:none;">
                                {{ __('Add In Segment Rules List') }}
                            </x-button> 
                        </div>
                    </div>
                </form>

                <div class="row m-3">
                    <div class="col-sm-12"><b>Segment Rules List</b></div>
                </div>
                <div class="">
                    @foreach($rows as $row)
                    <div class="row m-3 row-striped">
                        <div class="col-sm-8"> 
                            {{ $rules_data[$row['id']]['rules']}}: {{ $rules_data[$row['id']]['name']}} 
                        </div> 
                        <div class="col-sm-4"> : 
                            @foreach($row['get_rule_segments'] as $segment)
                            item{{$segment['segment_field_id']}}, 
                            @endforeach
                            @if(!empty($row['get_rule_segments']))
                            <a href="{{ url('/rule/segment/delete/'.$row['id']) }}">
                                <i class="material-icons" style="vertical-align:middle;">delete_forever</i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div> 
    </div>

</x-app-layout>
<script>
    $(document).ready(function () {
        var table = $('#rule_carrierlist').DataTable({
            "order": [[0, "asc"]],
            "stateSave": true,
        })

        $('#segment_rule').trigger('change');
    });
</script>
<input type="checkbox" name="rule_fields[]" value="">
