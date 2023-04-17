import "./base"
import "./styles/question.scss"

// create a get request to /question/{id}/upvote when the user clicks .vote > i.up.arrow.icon and increment .vote > i.up.arrow.icon + span by 1
// create a get request to /question/{id}/downvote when the user clicks .vote > i.down.arrow.icon and decrement .vote > i.down.arrow.icon + span by 1

$(".vote > i.up.arrow.icon").click(function() {
    fetch("/questions/" + $(this).attr("comment-id") + "/upvote", {
        method: "GET",
    }).then((response) => {
        if (response.ok) {
            $(this).next().text(parseInt($(this).next().text()) + 1);
        }
    });
});

$(".vote > i.down.arrow.icon").click(function() {
    fetch("/questions/" + $(this).attr("comment-id") + "/downvote", {
        method: "GET",
    }).then((response) => {
        if (response.ok) {
            $(this).prev().text(parseInt($(this).prev().text()) - 1);
        }
    });
});
