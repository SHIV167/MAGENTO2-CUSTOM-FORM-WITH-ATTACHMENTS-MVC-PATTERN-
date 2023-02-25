define(['jquery'], function($){
    "use strict";
    return function myscript()
    {
        $(window).load(function() {
            // this is the id of the form
            $("#non").submit(function(e) {
                e.preventDefault(); // avoid to execute the actual submit of the form.

                var form = $(this);
                var url = "rest/V1/UserFormManagement/saveUser";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    success: function(data)
                    {
                        alert(data); // show response from the php script.
                    }
                });

            });
        });
    }
});
