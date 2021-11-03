<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('File logs > ') }} {{ $carrier_scac }}
        </h2>
    </x-slot>
    <div class="w-full">
        <!--Container-->
        <div class=" w-full md:w-5/5 xl:w-5/5  mx-auto px-2">
            <!--Card-->
            <div id='recipients' class="p-4 mt-3 lg:mt-0 rounded shadow bg-white">
                    
                <form action="#" name="edi_search_form" id="edi_search_form">
                    
                <div class="flex items-end float-right -my-5" id="flex_container">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-4">Search</button>
                    <button type="reset" id="search_reset" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-4">Reset</button>
                </div>
                <table id="example">
                    <thead>
                        <tr>
                            <th>Received <br/> Date</th>
                            <th>Booking No</th>
                            <th>FF No</th>
                            <th>Bill of Lading</th>
                            <th>Carrier</th>
                            <th>Status</th>
                            <th class="notexport">Rec. EDI</th>
                        </tr>
                        <tr style="width: 100%;padding: 3px;box-sizing: border-box;">
                            <td><input type="hidden" name="search_all" value="search_all"><input type="text" class="form-control" style="" name="received_date" id="received_date"></td>
                            <td><input type="text" class="form-control" style="" name="booking_no"></td>
                            <td><input type="text" class="form-control" style="" name="ff_no"></td>
                            <td><input type="text" class="form-control" style="" name="bn_no"></td>
                            <td>
                                <select name="carrier" id="" class="form-control"  style="width:100px;">
                                    <option value="">Select</option>
                                    @foreach($carrier_data as $single_carrier)
                                        <option value="{{$single_carrier->carrier_scac}}">{{$single_carrier->carrier_scac}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="status" id="" class="form-control"  style="width:100px;">
                                    <option value="">Select</option>
                                    <option value="error">Error</option>
                                    <option value="pending">Pending</option>
                                    <option value="success">Success</option>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody id="load_receive_data_container">
                        <?php
                            
                        ?>
                    </tbody>
                </table>
                <input type="hidden" name="scac" id="scac" value="{{ $carrier_scac }}" />
                </form>
            </div>
            <!--/Card-->

        </div>
        <!--/container-->
    </div>
</x-app-layout>
<script src="https://cdn.datatables.net/plug-ins/1.10.25/pagination/input.js" type="text/javascript"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script>
    $(document).ready(function () {
        $('#edi_search_form').each(function() { this.reset() });
        var data_load_timer = 15 * 60 * 1000;
        var timerId = 0;
        let table = $('#example').DataTable({
            "bSortCellsTop": true,
            "scrollX":        true,
            //"stateSave": true,
            "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            'pagingType': 'input',
            'order': [[0, "desc"]],
            'oLanguage': {
                'sEmptyTable': 'No matching record found',
                //'sSearch': 'Global Search:',
                'sProcessing': '<div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>&nbsp;<div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>&nbsp;<div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>'
            },
    
            'searching': false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': <?php echo "'".route('filelog.datatable.ajax')."'"; ?> ,
                'headers': {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "data": function ( d ) {
                    return $.extend( {}, d, {
                    "scac": $("#scac").val()
                    });
                }
                
            },
            'columns': [
                { 'data': 'received_date' },
                { 'data': 'booking_no' },
                { 'data': 'ff_no'},
                { 'data': 'bn_no'},
                { 'data': 'carrier'},
                { 'data': 'status'},
                { 'data': 'received_edi'},
            ],
        });

        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'excel',
                    text: 'Export To Excel',
                    className: 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-4',
                    exportOptions: {
                        columns: ':not(.notexport)'
                    },
                    title: 'BLCompareReport_'+getDateTime()
                },
                {
                    extend: 'pdf',
                    text: 'Export To Pdf',
                    className: 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-4',
                    exportOptions: {
                        columns: ':not(.notexport)'
                    },
                    orientation : 'landscape',
                    title: 'BLCompareReport_'+getDateTime()
                }
            ],
        }).container().appendTo($('#flex_container'));

       
        timerId = setInterval(() => $("#edi_search_form").submit(), data_load_timer);
        
        $("#searchInput").on("input", function() {
            var value = $(this).val().toLowerCase();
            
            $("#example tbody tr").filter(function() {

                let text = $(this).text().toLowerCase();
                let arr = text.split(" ");
                text = arr.join(" ");

                $(this).toggle(text.indexOf(value) > -1);
            });

            var rec_count = $("#example tbody tr:visible").length;
            $("#example_info").text("Showing "+rec_count+" entries");
        });

        table.on( 'search.dt', function () {
            $('#searchInput').val("");
        });

        //$('input#range').daterangepicker();
        $('input#received_date').daterangepicker({
            singleDatePicker: true,
        }).val('');

        $('#received_date').on('cancel.daterangepicker', function(ev, picker) {
            //do something, like clearing an input
            $('#received_date').val('');
        });


        $("#edi_search_form").on("submit", function(evt){
            evt.preventDefault();
            clearInterval(timerId);
            timerId = setInterval(() => $("#edi_search_form").submit(), data_load_timer);
			$('#example').dataTable().fnDestroy();
			$("#load_receive_data_container").html("");
            var formData = $("#edi_search_form").serializeArray();
            //formData.push({'name':'op', 'value':'SEARCH_EDIT_DATA'}, {'name':'id', 'value':id});
            let table = $('#example').DataTable({
                "bSortCellsTop": true,
                scrollX:        true,
                //"stateSave": true,
                "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
                'pagingType': 'input',
                'order': [[0, "desc"]],
                'oLanguage': {
                    'sEmptyTable': 'No matching record found',
                    //'sSearch': 'Global Search:',
                    'sProcessing': '<div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>&nbsp;<div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>&nbsp;<div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>'
                },
                'searching': false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    data : formData,
                    'url': <?php echo "'".route('filelog.datatable.ajax')."'"; ?> ,
                    'headers': {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    
                },
                'columns': [
                    { 'data': 'received_date' },
                    { 'data': 'booking_no' },
                    { 'data': 'ff_no'},
                    { 'data': 'bn_no'},
                    { 'data': 'carrier'},
                    { 'data': 'status'},
                    { 'data': 'received_edi'},
                ],
            });
            var buttons = new $.fn.dataTable.Buttons(table, {
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Export To Excel',
                        className: 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-4',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        },
                        title: 'BLCompareReport_'+getDateTime()
                    },
                    {
                        extend: 'pdf',
                        text: 'Export To Pdf',
                        className: 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-4',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        },
                        orientation : 'landscape',
                        title: 'BLCompareReport_'+getDateTime()
                    }
                ],
            }).container().appendTo($('#flex_container'));
        });
    });

    $("#search_reset").click(function(){
        $('#edi_search_form').each(function() { this.reset() });
        location.reload();
    });

    function submit_form(el){
        event.preventDefault();
        el.closest('form').submit();
    }

    function getDateTime(){
        var dt = new Date();
        let dateTimeVal = dt.getFullYear().toString().padStart(4, '0') + (dt.getMonth()+1).toString().padStart(2, '0') + dt.getDate().toString().padStart(2, '0') + "_" + dt.getHours().toString().padStart(2, '0') + dt.getMinutes().toString().padStart(2, '0') + dt.getSeconds().toString().padStart(2, '0');
        return dateTimeVal;
    }
</script>