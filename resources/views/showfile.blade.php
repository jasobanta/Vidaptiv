<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($type=='diff')
            {{ __('Dashboard > Edi Diff') }}
            @elseif($type=='outgoing')
            {{ __('Dashboard > Sent Edi Content') }}
            @else
                @if($file==1)
                    {{ __('File Log > Received Edi Content') }}
                @else
                    {{ __('Dashboard > Received Edi Content') }}
                @endif
            @endif
        </h2>
        @if($type=='diff')
        <div class="flex items-end float-right -my-5" >
            <form method="POST" action="{{ route('diff.download') }}">
                @csrf
                <input type="hidden" value="{{$edi->id}}" name="id" />

                <x-dropdown-link :href="route('diff.download')" onclick="event.preventDefault();
                        this.closest('form').submit();">
                    <span class="material-icons-outlined"><img src="{{ asset('img/save_alt_black_24dp.svg') }}"></span>
                </x-dropdown-link>
            </form>

        </div>
        @endif 
    </x-slot>

    <div>
        <!--Container-->
        <div class="container w-full md:w-5/5 xl:w-5/5  mx-auto px-2">

            <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight"> @php echo basename($edi->data); @endphp </h2>
                <a href="{{ url('edi/download?path='. $edi->data) }}" download title="Download file" class="float-right -mt-7 btn btn-primary btn-sm"> <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
  <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
</svg></a>
                @if($type==='diff')
                @php
                echo html_entity_decode($contents)
                @endphp
                @else
                <div class="overflow-x-auto rounded text-white p-4 mt-3 lg:mg-0 border border-dark">
                    @php 
                    $contents =  preg_replace( "/\r|\n/", "" , $contents);
                    $contents =  str_replace("?'", "question_with_single_code", $contents);
                    $contents =  str_replace("'", "'\n", $contents);
                    $contents =  str_replace("question_with_single_code", "?'", $contents);
                    @endphp
                    <code>
                        <pre style="height: 300px;">{{ $contents }}</pre>
                    </code>
                </div>
                @endif
            </div>


        </div>
        <!--/container-->
    </div>
</x-app-layout>
