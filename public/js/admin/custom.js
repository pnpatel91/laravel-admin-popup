$(document).ready(function () {
    $('body').on('click', '#popup-modal-button', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            dataType: 'html',
            success: function(response) {
                $('#popup-modal-body').html(response);
            },
            error: function (data){
                    console.log(data);
            }
        });

        $('#popup-modal').modal('show');
    });
});

function alert_message(message) {
    if(typeof(message.success) != "undefined" && message.success !== null) {
        var messageHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success: </strong> '+ message.success +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    } else if(typeof(message.error) != "undefined" && message.error !== null){
        var messageHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> '+message.error+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }
    $('#message').html(messageHtml);
}

$(document).ready(function () {
    $(document).on('submit','#popup-form',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $("#pageloader").fadeIn();
        $.ajax({
            method: "POST",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: $(this).serialize(),
            success: function(message){
                $("#popup-modal").modal('hide');
                alert_message(message);
                setTimeout(function() {   //calls click event after a certain time
                    datatables();
                    $("#pageloader").hide();
                }, 1000);
            },
        });
    }); 
});

$(document).ready(function () {
    $(document).on('submit','.delete-form',function(e){
        e.preventDefault();
        var confirm_delete = confirm("Are you sure want to delete it?");
        if (confirm_delete == true) {
            $("#pageloader").fadeIn();
            var url = $(this).attr('action');
            $.ajax({
                method: "POST",
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: $(this).serialize(),
                success: function(message){
                    alert_message(message);
                    setTimeout(function() {   //calls click event after a certain time
                        datatables();
                        $("#pageloader").hide();
                    }, 1000);
                },
            });
        }
        return confirm_delete;
    }); 
});
