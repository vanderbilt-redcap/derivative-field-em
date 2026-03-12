$( document ).ready(function() {
    insertButton(targetField, buttonHTML);
});
function populateResponse() {
    showProgress(1,0);
    $.ajax({
        method: 'POST',
        url: ajax_url,
        data: { action: "process"},
        dataType: 'json'
    })
    .done(function(data) {
        if (data.status != 1) {
            alert("Something went wrong!");
        } else {
            //typeWriterEffect(chatElement.querySelector("p"), data.message, 5); // Type into 'myDiv' with 50ms delay per character
            $("[name='"+targetField+"']").val(data.message);
        }
        showProgress(0,0);
    })
    .fail(function(data) {

    })
    .always(function(data) {

    });
}

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
