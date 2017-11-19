(function() {
    $(function() {

        var context_menu_items_fg = $('#context-menu-items-fg');
        var schedule_context_menu_items_input = $('#schedule_context_menu_items');
        var schedule_enable_context_menu_input = $('#schedule_enable_context_menu');

        function updateItems(schedule_enable_context_menu_value)
        {
            if(schedule_enable_context_menu_value === '' || schedule_enable_context_menu_value === '0') {
                hideContextMenuItems();
            }else {
                showContextMenuItems();
            }

            return true;
        }

        function showContextMenuItems() {
            context_menu_items_fg.show();
            schedule_context_menu_items_input.prop('required', true);

            return true;
        }

        function hideContextMenuItems() {
            context_menu_items_fg.hide();
            schedule_context_menu_items_input.prop('required', false);

            return true;
        }

        /*Change*/
        schedule_enable_context_menu_input.change(function(){
            updateItems($(this).val());
        });

        updateItems(
            schedule_enable_context_menu_input.find(":selected").val()
        )

    });
})();
