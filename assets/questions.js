import "./base"
import "./styles/questions.scss"

let searching = 0
let search = async function () {
    let query = $("#search input").val()
    let sId = ++searching
    let dimmer = $("#dimmer")

    if (!dimmer.hasClass("active")) {
        dimmer.addClass("active")
    }

    let questions = await fetch(`/questions/search/${encodeURIComponent(query)}?html`).then(res => res.json())

    if (sId !== searching) {
        return
    }

    $("#questions").html(questions.join(""))

    if (dimmer.hasClass("active")) {
        dimmer.removeClass("active")
    }
}

$("#search i.icon").click(search)
$("#search input").keyup(function (e) {
    if (e.keyCode === 13) {
        search()
    }
})

search()