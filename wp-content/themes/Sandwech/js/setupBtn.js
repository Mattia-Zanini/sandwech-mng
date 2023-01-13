function SetupButtons() {
    var btnRow = $(".dt-buttons");
    btnRow.addClass("btn-group");
    btnRow.addClass("flex-wrap");
    //var classList = $(".dt-buttons").attr("class");
    //console.log(classList);

    var btnCreate = $(".buttons-create");
    btnCreate.addClass("btn");
    btnCreate.addClass("btn-secondary");
    btnCreate.removeClass("dt-button");

    var btnCreate = $(".buttons-edit");
    btnCreate.addClass("btn");
    btnCreate.addClass("btn-secondary");
    btnCreate.removeClass("dt-button");

    var btnRemove = $(".buttons-remove");
    btnRemove.addClass("btn");
    btnRemove.addClass("btn-secondary");
    btnRemove.removeClass("dt-button");

    console.log("Buttons setupped");
}

/*
$(window).on('load', function () {
    var btnRow = $(".dt-buttons");
    btnRow.addClass("btn-group");
    btnRow.addClass("flex-wrap");
    //var classList = $(".dt-buttons").attr("class");
    //console.log(classList);

    var btnCreate = $(".buttons-create");
    btnCreate.addClass("btn");
    btnCreate.addClass("btn-secondary");
    btnCreate.removeClass("dt-button");

    var btnCreate = $(".buttons-edit");
    btnCreate.addClass("btn");
    btnCreate.addClass("btn-secondary");
    btnCreate.removeClass("dt-button");

    var btnCreate = $(".buttons-remove");
    btnCreate.addClass("btn");
    btnCreate.addClass("btn-secondary");
    btnCreate.removeClass("dt-button");
});
*/

/*
<div>
    //bootstrap
    <div class="dt-buttons btn-group flex-wrap">
        <button class="btn btn-secondary buttons-create" tabindex="0" aria-controls="example" type="button">
            <span>New</span>
        </button>
        <button class="btn btn-secondary buttons-selected buttons-edit disabled" tabindex="0" aria-controls="example"
            type="button" disabled="">
            <span>Edit</span>
        </button>
        <button class="btn btn-secondary buttons-selected buttons-remove disabled" tabindex="0" aria-controls="example"
            type="button" disabled="">
            <span>Delete</span>
        </button>
    </div>

    //scrauso
    <div class="dt-buttons">
        <button class="dt-button buttons-create" tabindex="0" aria-controls="table" type="button">
            <span>New</span>
        </button>
        <button class="dt-button buttons-selected buttons-edit disabled" tabindex="0" aria-controls="table" type="button"
            disabled="">
            <span>Edit</span>
        </button>
        <button class="dt-button buttons-selected buttons-remove disabled" tabindex="0" aria-controls="table" type="button"
            disabled="">
            <span>Delete</span>
        </button>
    </div>

</div>
*/