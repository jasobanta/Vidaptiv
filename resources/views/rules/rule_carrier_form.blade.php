<?php /* <x-app-layout>
  <x-slot name="header">
  <h2 class="font-semibold text-xl text-gray-800 leading-tight">
  {{ __('Dashboard >')}}
  <x-nav-link :href="route('rule.list')">
  {{ __('Rule List >') }}
  </x-nav-link>
  <x-nav-link :href="route('rule.carrier.list')">
  {{ __('Rule Carrier List >') }}
  </x-nav-link>
  {{ __('Assign Carrier Rules') }}
  </h2>
  </x-slot>

  <div class="container w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
  <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">

  <div class="d-flex justify-content-center p-1 rounded shadow bg-white">
  <b>{{$row->carrier_scac}} : {{$row->carrier_name}}</b>
  </div>

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

  <div class="row">
  <div class="col-sm-6"><b>Default Rule List</b> </div>
  <div class=""><b>Ignore Rules</b> </div>
  </div>
  <div class="row m-3">
  <div class="col-sm-6">
  <form method="POST" action="{{ url('rule/carrier/add-ignore/'.$row->id) }}">
  @csrf
  <ul>
  <li>
  <input type="checkbox" value="1" name='select_all_default_rules'  class="select_all_default_rules form-check-input" @if(old('select_all_default_rules','')== 1) checked @endif /> Select All

  <x-button class="ml-4">
  {{ __('Add In Ignore List ->') }}
  </x-button>
  </li>
  @if(!empty($rules))
  @foreach($rules as $rule)
  <li>
  <input name="rule_ids[]" type="checkbox" value="{{$rule['id']}}"  class="default_rules form-check-input"  {{ in_array($rule['id'], old('rule_ids',  []  ?? [] ) ) ? 'checked' : '' }} >
  {{$rule['rules']}}: {{$rule['name']}}
  </li>
  @endforeach
  @endif
  </ul>
  </form>
  </div>

  <div class="col-sm-6">
  <form method="POST" action="{{ url('rule/carrier/delete-ignore/'.$row->id) }}">
  @csrf
  <ul>
  <li>
  <input type="checkbox" value="1" name='select_all_ignore_rules'  class="select_all_ignore_rules form-check-input" @if(old('select_all_default_rules','')== 1) checked @endif /> Select All

  <x-button class="ml-4">
  {{ __('<- Add In Default List') }}
  </x-button>
  </li>
  @if(!empty($ignore_rules))
  @foreach($ignore_rules as $rule)
  <li>
  <input name="rule_ids[]" type="checkbox" value="{{$rule['id']}}"  class="ignore_rules form-check-input"  {{ in_array($rule['id'], old('rule_ids',  []  ?? [] ) ) ? 'checked' : '' }} >
  {{$rule['rules']}}: {{$rule['name']}}
  </li>
  @endforeach
  @endif
  </ul>
  </form>
  </div>
  </div>

  </div>
  </div>
  </x-app-layout> */ ?>
<?php
$list = [];
$ignore_list = [];

foreach ($rules as $rule) {
    $list[$rule['id']] = '';
}

$free_text_compare_list = [];

foreach ($all_rules as $record) {
    if ($record['is_ignore'] == 1) {
        $ignore_list[] = $record['rule_id'];
    }
    if (!is_null($record['compare_elements'])) {
        $list[$record['rule_id']] = $record['compare_elements'];
    }
    $free_text_compare_list[$record['rule_id']] = $record['is_free_text_compare'];
}
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard >')}} 
            <x-nav-link :href="route('rule.list')">
                {{ __('Rule List >') }}
            </x-nav-link> 
            <x-nav-link :href="route('rule.carrier.list')">
                {{ __('Rule Carrier List >') }}
            </x-nav-link> 
            {{ __('Assign Carrier Rules') }}
        </h2> 
    </x-slot>

    <div class="container w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
        <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">

            <div class="d-flex justify-content-center p-1 rounded shadow bg-white"> 
                <b>{{$row->carrier_scac}} : {{$row->carrier_name}}</b> 
            </div>

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

            <div class="row">
                <div class="col-sm-5"><b>Ignore Rules</b> </div>
                <div class="col-sm-5"><b>Override Element Rules</b><div>Put comma separated values like 1,3,4,6</div> </div>
                <div class="col-sm-2"><b>Free Text Compare</b> </div>
            </div>
            <div class="row m-2">
                <div class="col-sm-12">
                    <form method="POST" action="{{ url('rule/carrier/update/'.$row->id) }}"> 
                        @csrf

                        @if(!empty($rules))
                        @foreach($rules as $rule)

                        <div class="row mb-3">
                            <div class="col-sm-5">
                                <input data-rule="{{$rule['rules']}}" name="is_ignore[{{$rule['id']}}][]" type="checkbox" value="{{$rule['id']}}" id="{{$rule['id']}}" class="is_ignore_rules form-check-input"  {{ in_array($rule['id'], $ignore_list) ? 'checked' : '' }} >
                                       {{$rule['rules']}}: {{$rule['name']}}
                            </div>
                            <div class="col-sm-5">
                                <input type="text" name="compare_element[{{$rule['id']}}][]" id="{{$rule['id']}}_val" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-900 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full" value="{{$list[$rule['id']]}}" />
                            </div>
                            <div class="col-sm-2">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input name="is_free_text_compare[{{$rule['id']}}]" type="checkbox" value="1"  class="form-check-input" @if(old('is_free_text_compare', !empty($free_text_compare_list[$rule['id']]) )? 1 : '')== 1) checked @endif >
                            </div>
                        </div>    
                        @endforeach
                        @endif
                        <div class="text-center mt-5">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Save
                            </button> 
                            <button type="reset" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-3">
                                Reset
                            </button>
                            <x-nav-link :href="route('rule.carrier.list')" class="ml-3">
                                Cancel
                            </x-nav-link>
                        </div>
                    </form>
                </div>


            </div>

        </div>
    </div>
</x-app-layout>
<script type="text/javascript">

    $(".is_ignore_rules").on("click", function(e){
    toggleInputBox(e, this);
    });
            function toggleInputBox(ev, el){

            let id = $(el).attr("id");
                    let rule = $(el).attr("data-rule");
                    let val = "#" + id + "_val";
                    let original = "#" + id + "_original";
                    if ($(val).prop('disabled')){
            $(val).removeAttr("disabled");
                    $(original).prop("checked", true);
            } else{
            let txtInput = $(val).val();
                    if (txtInput != ''){
            let result = confirm("Do you want to clear the override rule for " + rule + " ?");
                    if (result === true){
            $(val).val('');
                    $(val).attr("disabled", "disabled");
                    return;
            } else{
            ev.preventDefault();
                    return;
            }

            }

            $(val).attr("disabled", "disabled");
                    $(original).prop("checked", false);
            }
            }

    function goBack(){

    }


</script>
