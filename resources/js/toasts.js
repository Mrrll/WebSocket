$(function () {
    $(".toast").toast("show");
});

$(".toast").on("shown.bs.toast", function () {

    let dt = new Date();
    let time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
    $(this).children(".toast-header").children("#date_toasts").html(time);

    let progress_bar = {
        "animation-name": "progress_toast",
        "animation-fill-mode": "forwards",
        "animation-duration": $(this).data("bs-delay") + "ms",
        "animation-timing-function": "ease-out",
        "animation-iteration-count": "1",
    };

    if ($(this).data("bs-autohide")) {
        $(this).children(".progress").children("div").css(progress_bar);
    }
});

$("#close_toats").on("click", function () {
    $(this).closest(".toast").addClass("close-toast");
    setTimeout(() => {
        $(this).closest(".toast").toast("hide");
    }, 2500);
});

$(".toast").on("hidden.bs.toast", function () {
    $(this).addClass("close-toast");
});

function CreateToast(
    title,
    message,
    type = "info",
    delay = 10000,
    autohide = "true",
    icon = true,
    close = true,
    position = "top-0 end-0"
) {

    $.ajax({
        url: "/toasts/ajax",
        method: "get",
        dataType: "json",
        data: {
            title : title,
            message : message,
            type : type,
            delay : delay,
            autohide : autohide,
            icon : icon,
            close : close,
            position : position
        },
    }).done(function (data) {

        $(".toast-container").append(data);


        $(".toast").on("show.bs.toast", function () {
            let dt = new Date();
            let time =
                dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
            $(".toast:last").children(".toast-header").find("small").html(time);
            $(".toast:last").children(".toast-body").html(message);
        });

        $(".toast").on("shown.bs.toast", function () {

            let progress_bar = {
                "animation-name": "progress_toast",
                "animation-fill-mode": "forwards",
                "animation-duration": $(".toast:last").data("bs-delay") + "ms",
                "animation-timing-function": "ease-out",
                "animation-iteration-count": "1",
            };

            if ($(".toast:last").data("bs-autohide")) {
                $(".toast:last")
                    .children(".progress")
                    .children("div")
                    .css(progress_bar);
            }
        });

        $(".toast").on("hide.bs.toast", function () {
            $(this).addClass("close-toast");
        });
        $(".toast").on("hidden.bs.toast", function () {
            $(this).remove();
            if ($(".toast:last").length === 0) {
                $(".toast-container").find("svg").remove()
            }
        });
        $(".toast:last").toast("show");

        $(".btn-close").on("click", function () {
            $(this).closest(".toast").addClass("close-toast");
            setTimeout(() => {
                $(this).closest(".toast").toast("hide");
            }, 2500);
        });
    });
}
window.CreateToast = CreateToast;
