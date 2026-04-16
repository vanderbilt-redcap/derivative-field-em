$( document ).ready(function() {
    $(".evaluate-prompt-btn").click(function () {
        showProgress(1,0);
        var targetField = $(this).attr("this-target");
        var sourceField = $(this).attr("this-source");

        var params = {};
        params['action'] = 'process';
        params['num'] = $(this).attr("this-setup");

        if ($("#"+sourceField).length > 0) {
            params['sourceValue'] = $("#"+sourceField).val();
        } else {
            params['record'] = $(this).attr("this-record");
        }

        $.ajax({
            method: 'POST',
            url: ajax_url,
            data: params,
            dataType: 'json'
        })
        .done(function(data) {
            if (data.status != 1) {
                alert("Something went wrong!");
            } else {
                $("[name='"+targetField+"']").val(data.message);
            }
            showProgress(0,0);
        })
        .fail(function(data) {

        })
        .always(function(data) {

        });
    });

});

function insertButton(targetField, buttonHTML) {
    if ($('tr#'+targetField+'-tr').length > 0) { // Execute this script only if form contain that field
        if ($('tr#'+targetField+'-tr').find('td:first-child div:first').length > 0) {
            $('tr#'+targetField+'-tr').find('td:first-child div:first').append(buttonHTML);
        } else {
            $('tr#'+targetField+'-tr').find('td:nth-child(2) div:first').append(buttonHTML);
        }
    }
    //$('input[name="'+targetField+'"]').after(infoHTML);
}
