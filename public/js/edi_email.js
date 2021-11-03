$(document).ready(function () {
	// this would open up the Email sending form

    $('#ediEmailModal').on('show.bs.modal', function (e) {
        let id = $(e.relatedTarget).attr('data');
        $.ajax({
            url: base_url + "/edi/email-form/" + id,
            method: "GET",
            success: function (data) {
                $("#ediEmailHtml").html(data);
            },
        })
    });
	// action inside Email Sending form
    $(document).on('click', '.email_type', function () {
        //$("#email_to").val($(this).attr('email'));
        if ($(this).val() == 'carrier') {
            $('#carrier_reject').removeClass('d-none')
            $('#bdp_reject').addClass('d-none')
        }
        if ($(this).val() == 'owner' || $(this).val() == 'other') {
            $('#carrier_reject').addClass('d-none')
            $('#bdp_reject').removeClass('d-none')
        }
    });

    $(document).on('click', '#send_email_btn', function (e) {
        e.preventDefault();
        $("#edi_error").html("");
        $("#send_email_btn").hide();
        let attachmentStatus = $("#attachmentStatus").is(":checked");

        if(attachmentStatus){
            $.ajax({
                url: base_url + "/save-email-attachment",
                method: "POST",
                data: {
                    "downloadtype": $("[name='downloadtype']:checked").val(),
                    "allordiffonly": $("[name='allordiffonly']:checked").val(),
                    "withcomments": $("[name='withcomments']:checked").val(),
                    "email_type": $("[name='email_type']:checked").val(),
                    "id": $("#edi_data_id").val()
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    console.log(this.data);
                },
                success: function (response, status, xhr) {
                  
                    if (response.status == false) {
                        var error_msg = '';
                        $.each(response.data, function (index, value) {
                            if (value != '') {
                                error_msg += '<div>' + value + '<div/>';
                            }
                        });

                        $("#edi_error").html(error_msg);

                    } else {
                       
                        $("#edi_email_attachment_path").val(response.file_path);
                        $("#edi_email_attachment_name").val(response.file_name);
                        $("#edi_email_attachment_mimetype").val(response.mime_type);
                       
                        sendEmail();
                    }
                   
                },
                error: function (textStatus, errorThrown) {
                    $("#send_email_btn").show();
                }
            });
        }else{
           sendEmail();
        }
    });
	// This will open up the Action modals for each Segment
    $('#actionModal').on('show.bs.modal', function (e) {

        var formdata = $(e.relatedTarget).attr('data').split(',');
        var labletxt = $('#title-'+formdata[1]).text();
        $.ajax({
            url: base_url + "/edi/action-form",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            data: {ediId: formdata[0], ruleId: formdata[1], actionId: formdata[2], label:labletxt},
            beforeSend: function () {
                console.log(this.data)
            },
            success: function (data) {
                $('#actionHtml').html(data);
            }
        });
    });

    $(document).on('click', '#saveAction', function (event) {
        event.preventDefault();
        var confirm_option = true;
        //meta_data_id old_data_msg
        var meta_data_id = $("#meta_data_id").val();
        var new_data_msg = $(".new_data_msg").val();
        var old_data_msg = $("#old_data_msg").val();
        
        if(meta_data_id>0 && new_data_msg=="" ){
             var confirm_option = confirm('Are you sure, you want to delete comment?');
        }
        
        if(confirm_option){
            $.ajax({
                url: base_url + "/save-edi-action",
                method: "POST",
                data: $("#save_edi_action").serialize(),
                dataType: "json",
                beforeSend: function(){},
                success: function(response) {
                    if (response.status == false) {
                        var error_msg = '';
                        $.each(response.data, function (index, value) {
                            $('#' + index).addClass('is-invalid');

                        });
                    } else {
                         location.reload(true);
                        $("#actionHtml").html('<p>Data saved</p>');
                        $("#actionModal").modal("hide");
                        $(event.relatedTarget).addClass('btn-warning').removeClass('btn-outline-primary');
                       }
                }
            });
        }
    });
    // Download action modal
	$('#downloadAction').on('show.bs.modal', function (e) {
		console.log('opened the download window');
		let id = $(e.relatedTarget).attr('data');
		$.ajax({
			url: base_url + "/edi/download-form",
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			method: "POST",
			data: {id:id},
			beforeSend:function(){
			console.log(this.data);
			},
			success: function (data) {
				$("#downloadActionHtml").html(data);
			},
		})
	});


    /*  $(document).on('click', '.email_type', function () {
     $("#email_to").val($(this).attr('email'));
     if ($(this).val() == 'carrier') {
     $('#carrier_reject').removeClass('d-none')
     $('#bdp_reject').addClass('d-none')
     }
     if ($(this).val() == 'owner' || $(this).val() == 'other') {
     $('#carrier_reject').addClass('d-none')
     $('#bdp_reject').removeClass('d-none')
     }
     }); */

    $(document).on('change', '#email_template_id', function (e) {
        e.preventDefault();

        var edi_id = $(this).attr('edi_id');
        var template_id = $(this).val();


        if (template_id == 1 || template_id == 5) { //carrier
            $('#carrier_reject').removeClass('d-none')
            $('#bdp_reject').addClass('d-none')
        } else {
            $('#carrier_reject').addClass('d-none')
            $('#bdp_reject').removeClass('d-none')
        }

        $.ajax({
            url: base_url + "/edi/email-template/" + edi_id + "/" + template_id,
            method: "GET",
            dataType: "json",
            beforeSend: function () {
                $("#send_email_form #email_to").val('');
                $("#send_email_form #email_cc").val('');
                $("#send_email_form #email_bcc").val('');
                $("#send_email_form #subject").val('');
                $("#send_email_form #message").val('');
                $("#send_email_form #signature").val('');
            },
            success: function (response) {
                if (response.status == true) {
                    $("#send_email_form #email_to").val(response.data.email_to);
                    $("#send_email_form #email_cc").val(response.data.email_cc);
                    $("#send_email_form #email_bcc").val(response.data.email_bcc);
                    $("#send_email_form #subject").val(response.data.subject);
                    $("#send_email_form #message").val(response.data.message);
                    $("#send_email_form #signature").val(response.data.signature);
                    $("#edi_title_id").val(response.data.edi_title_id);
                }
            },
            error: function (textStatus, errorThrown) {
                console.log(textStatus);

            }
        });
    });

});

function sendEmail(){
    $.ajax({
        url: base_url + "/edi/send-email",
        method: "POST",
        data: $("#send_email_form").serialize(),
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            console.log(this.data);
        },
        success: function (response, status, xhr) {
            if (response.status == false) {
                var error_msg = '';
                $.each(response.data, function (index, value) {
                    if (value != '') {
                        error_msg += '<div>' + value + '<div/>';
                    }
                });

                $("#edi_error").html(error_msg);
            } else {
                $("#ediEmailModal").modal("hide");
                //$("#ediEmailHtml").html(data);
                //$("#send_email_form").reset();

                alert('Email successfully sent.');
                window.location.reload(true)
            }
            $("#send_email_btn").show();
        },
        error: function (textStatus, errorThrown) {
            $("#send_email_btn").show();
        }
    });
}
