$(document).ready(function () {
    var exTime = 2000;

    $('[data-toggle="tooltip"]').tooltip();

    $(".faq-expand").click(function () {
        var dataAttr = "data-action-toggle";
        var action = $(this).attr(dataAttr);
        console.log('action is', action);
        if (action === "show") {
            $(".fa-minus-circle").removeClass("fa-minus-circle").addClass("fa-plus-circle");
            $(".faq-expand").attr(dataAttr, "show");
            $(".answer").hide();
            $(this).attr(dataAttr, "hide");
            $(this).children().first().removeClass("fa-plus-circle").addClass("fa-minus-circle");
            $(this).parent().next().show();
        } else {
            $(this).attr(dataAttr, "show");
            $(this).children().first().removeClass("fa-minus-circle").addClass("fa-plus-circle");
            $(this).parent().next().hide();
        }
    })
});
