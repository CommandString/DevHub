import "./base"
import "./styles/question.scss"

$(".vote > i.up.arrow.icon").click(function() {
    fetch("/questions/" + $(this).attr("comment-id") + "/upvote", {
        method: "POST",
    }).then((response) => {
        if (response.ok) {
            $(this).next().text(parseInt($(this).next().text()) + 1);
        }
    });
});

$(".vote > i.down.arrow.icon").click(function() {
    fetch("/questions/" + $(this).attr("comment-id") + "/downvote", {
        method: "POST",
    }).then((response) => {
        if (response.ok) {
            $(this).prev().text(parseInt($(this).prev().text()) - 1);
        }
    });
});

$("#submit-comment").click(function() {
    var formData = new FormData();
    formData.append("comment", $("textarea").val());

    fetch("/questions/" + $(this).attr("comment-id") + "/comments", {
        method: "POST",
        body: formData,
    }).then(async (response) => {
        let body = await response.json();

        if (body.success) {
            location.reload();
            return;
        }

        $.toast({
            title: "Failed to post comment",
            message: body.errors.join(", "),
            class: 'error'
        });
    });
});
