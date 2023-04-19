import "./base"
import "./styles/profile.scss"

$('.menu .item').tab()

// Input class
class Input {
    constructor(name, label, defaultValue = "", placeholder = "", type = "text", minLength = 1, maxLength = 100, required = true) {
        this.name = name
        this.defaultValue = defaultValue
        this.placeholder = placeholder
        this.type = type
        this.label = label
        this.minLength = minLength
        this.maxLength = maxLength
        this.required = required
    }

    getInput() {
        return `<input name="${this.name}" type="${this.type}" minlength="${this.minLength}" maxlength="${this.maxLength}" placeholder="${this.placeholder}" value="${this.defaultValue}" ${this.required ? 'required' : ''}>`
    }

    getField() {
        return `<div class="field"><label>${this.label}</label>${this.getInput()}</div>`
    }
}

// Custom Modal Template
$.fn.modal.settings.templates.settingChange = function (title, inputs, onConfirm = () => true, onCancel = () => true) {
    let inputString = ""

    for (let id in inputs) {
        let input = inputs[id]
        inputString += input.getField()
    }

    return {
        title: title,
        closeIcon: true,
        closable: false,
        content: `<form class='ui form'>${inputString}</form>`,
        transition: "fade up",
        actions: [
            {
                text: 'Confirm',
                class: 'violet',
                click: onConfirm
            },
            {
                text: 'Cancel',
                class: 'pink',
                click: onCancel
            }
        ],
        onHidden: () => {
            window.location.reload()
        },
        onShow: function () {
            $(this).find("form").submit((e) => {
                e.preventDefault()
                $(this).find(".violet.button").click()
            })

            let modal = $(this)

            $(this).find("input").keyup(function(e) {
                let maxLength = $(this).attr("maxlength")
                let currentLength = $(this).val().length
                let minLength = $(this).attr("minlength")
                let field = $(this).parent()
                let value = $(this).val().trim()
                let error = ""

                if (currentLength > maxLength) {
                    error = `Maximum length is ${maxLength}`
                }

                if (currentLength < minLength) {
                    error = `Minimum length is ${minLength}`
                }

                if (error) {
                    if (!field.hasClass("error")) {
                        field.addClass("error")
                        field.append(`<div class="ui pointing fluid inverted centered red basic label">${error}</div>`)
                    }

                    modal.find(".violet.button").addClass("disabled")
                } else {
                    field.removeClass("error")
                    field.find(".ui.pointing.red.basic.label").remove()
                    modal.find(".violet.button").removeClass("disabled")
                }
            })
        }
    }
}

$(".editable [property]").click(function (e) {
    let prop = $(this).attr("property")
    let text = $(this).text()

    if (prop === "pfp" ) {
        return
    }

    let inputs = {
        password: [
            new Input("password", "New Password", "", "New password", "password", 6, 32),
            new Input("confirm_password", "Confirm New Password", "", "Confirm new password", "password", 6, 32),
            new Input("old_password", "Confirm Old Password", "", "Current password", "password", 6, 32)
        ],
        name: [
            new Input("fname", "First Name", text.split(" ")[0] ?? "", "First Name", "text", 1, 30, true),
            new Input("lname", "Last Name", text.split(" ")[1] ?? "", "Last Name", "text", 1, 30, true)
        ],
        email: [
            new Input("email", "Email", text, "Email", "email", 1, 100)
        ],
        username: [
            new Input("username", "Username", text, "Username", "text", 3, 50, true)
        ]
    }

    let endpoint = `${window.location.pathname}/${prop}`

    $.modal(
        "settingChange",
        `Change ${prop}`,
        inputs[prop],
        function () {
            let formData = new FormData($(this).find("form").get(0))
            let modal = $(this)

            modal.dimmer({
                closable: false,
                displayLoader: true,
                loaderVariation: 'slow pink medium elastic',
                loaderText: 'Processing...'
            }).dimmer("show")

            $.ajax({
                url: endpoint,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $(`[property=${prop}]`).text(data[prop])
                    modal.dimmer("hide")
                    modal.modal("hide")
                },
                error: function (xhr) {
                    let errors = xhr?.responseJSON?.errors ?? []

                    $.toast({
                        title: `Failed to edit ${prop}`,
                        message: errors.join(", "),
                        class: "error"
                    })

                    modal.dimmer("hide")
                }
            })

            return false
        }
    )
})
