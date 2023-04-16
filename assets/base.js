import "../node_modules/fomantic-ui/dist/semantic.min.css"
import "../node_modules/fomantic-ui/dist/semantic.min.js"
import "./styles/base.scss"
import "./styles/parts/nav.scss"
import "./styles/parts/footer.scss"

$("#nav-search input").on("keydown", function (e) {
    if (e.originalEvent.keyCode !== 13) {
        return;
    }

    window.location.href = `/questions?q=${$(this).val()}`;
})

$("#nav-search i.icon").on("click", function () {
    window.location.href = `/questions?q=${$("#nav-search input").val()}`;
})
