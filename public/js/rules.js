$(document).ready(function () {
    $("#rules_list tbody").sortable({
        start: function (e, ui) {
            // creates a temporary attribute on the element with the old index
            $(this).attr('data-previndex', ui.item.index());
        },
        update: function (e, ui) {
            // gets the new and old index then removes the temporary attribute
            var new_priority = ui.item.index();
            var old_priority = $(this).attr('data-previndex');
            var rule_id = ui.item.attr('rule_id');

            if (new_priority > old_priority) {
                new_priority = new_priority + 2;
            }

            //set rows in priority orders 
            var set_priority = 1;
            $(".rules_priority").each(function (index) {
                $(this).html(set_priority++);
            });
            //$(this).removeAttr('data-previndex');            

            $.ajax({
                url: base_url + "/rule/update/priority/" + rule_id + "/" + new_priority,
                method: "GET",
                dataType: "json",
                success: function (response, status, xhr) {
                    if (response.success === true) {
                        //
                    }
                },
                error: function (textStatus, errorThrown) {

                }
            });
        }
    });

    $(document).on('change', '.select_all_default_rules', function (e) {
        if ($(this).is(':checked')) {
            $('.default_rules').prop('checked', true);
        } else {
            $('.default_rules').prop('checked', false);
        }
    });

    $(document).on('change', '.select_all_ignore_rules', function (e) {
        if ($(this).is(':checked')) {
            $('.ignore_rules').prop('checked', true);
        } else {
            $('.ignore_rules').prop('checked', false);
        }
    });

    $(document).on('change', '#segment_rule', function (e) {
        var option = $('option:selected', this);
        var fields = option.attr('fields');
        var items = fields.split(',');

        $("#rule_fields_button").hide();
        $("#rule_fields_inputs").html("");

        if ($(this).val() != '') {
            $.each(items, function (key, value) {
                var key = key + 1;
                if (value == "") {
                    value = "item1";
                } else {
                    value = "item" + key;
                }

                $("#rule_fields_inputs").append(value + ": <input type='checkbox' name='rule_fields[]' value='" + key + "'>&nbsp;&nbsp;&nbsp;");
            });

            $("#rule_fields_button").show();
        }

    });

});       