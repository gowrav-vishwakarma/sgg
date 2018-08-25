$.widget("ui.xtooltip", $.ui.tooltip, {
    options: {
        content: function () {
            return $(this).prop('title');
        },
        position: { my: "center top+15", at: "center center" }
    }
});