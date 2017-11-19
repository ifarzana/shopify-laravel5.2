(function() {
    $(function() {

        var client = $('.typeahead');
        var client_id = $('input[name=client_id]');
        var clear_btn = $('#clear-client-btn');

        var client_selected_div = $('.client-selected-div');

        var save_btn = $('#save-btn');

        var domain_client_search_fg = $('.domain-client-search-fg');
        var contact_fg = $('#contact_fg');
        var contact_id = $('#contact_id');

        disableClientTemplate();

        client.typeahead({
            ajax: '/clients/search-ajax',
            triggerLength: 1,
            highlight: true,
            minLength: 1,
            limit: 10,
            displayField: 'top',


            highlighter: function (item) {
                var parts = item.split('_#_'),
                    html = '<div class="typeahead">';
                html += '<div class="pull-left margin-small">';
                html += '<div class="text-left" ><strong data-display = '+ parts[0] +'>' + parts[0] + '</strong></div>';
                html += '<small class="text-left">' + parts[1] + '</small>';
                html += '<small class="text-left text-primary" style="display: block;">' + parts[2] + '</small>';
                html += '</div>';
                html += '<div class="clearfix"></div>';
                html += '</div>';
                return html;
            },


            onSelect: function(item) {
                client_id.val(item.value);

                domain_client_search_fg.removeClass('margin');

                client.prop('readonly', true);
                disableClientTemplate();
                enableClientTemplate();

                return false;
            }
        });

        clear_btn.on('click', function() {

            client.prop('readonly', false);
            client.val('');
            client_id.val('');

            disableClientTemplate();

            return true;
        });

        function disableClientTemplate() {

            client_selected_div.hide();

            /*Clear contacts*/
            contact_fg.hide();
            contact_id.val('');
            contact_id.empty();
        }

        function enableClientTemplate() {

            client_selected_div.show();
            contact_fg.show();


            contact_id.val('');
            contact_id.empty();

            $.ajax({
                'url':'/clients/get-ajax',
                'method':'GET',
                'data':{ id: client_id.val() },
                'async': true,
                'success':function(data) {

                    contact_id.select2({data: data.contacts}).trigger('change.select2');

                    save_btn.show();

                }
            });

        }

        if(client_id.val() != '') {

            client.prop('readonly', true);
            disableClientTemplate();
            enableClientTemplate();
            client_selected_div.show();
        }

    });
})();
