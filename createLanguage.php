<?php

require_once "inc/header.php";

?>
<div class="card card-dark">
    <div class="card-header">
        <h3>Create New Language</h3>
    </div>
    <div class="card-body">
        <div class="alert rounded d-none alertBox"></div>
        <form method="post" id="createLanguage">
            <div class="form-group">
                <label for="" class="text-white">Enter Language</label>
                <input type="text" name="language" class="form-control" id="languageInput"
                       placeholder="Enter Language">
            </div>
            <input type="submit" value="Create"
                   class="btn  btn-outline-warning">
        </form>
    </div>
</div>


<?php
require_once "inc/footer.php";

?>

<script>
    let createLanguage = document.getElementById("createLanguage");
    let languageInput = document.querySelector("#languageInput");
    let alertBox = document.querySelector(".alertBox");

    function alert(text) {
        alertBox.classList.add("d-block");
        alertBox.innerHTML = text;
        setTimeout(() => {
            alertBox.classList.remove("d-block");
            alertBox.innerHTML = "";
        },2000)
    }
    createLanguage.addEventListener("submit",function (e) {
        e.preventDefault();
        console.log(languageInput.value)
        let data = new FormData();
        data.append("language",languageInput.value);
        axios.post("api.php",data)
        .then(res => {
            if(res.data[0] === "Language field is required") {
                alertBox.classList.add('alert-danger');
                alert("Language field is required")
            } else {
                languageInput.value = "";
                alertBox.classList.add('alert-success');
                alert("Language added successfully");
                showAllLanguage("all_language");
            }
        })
    })
    function showAllLanguage(table) {
        axios.get("api.php?" + table)
            .then(res => {
                document.querySelector(".all_language").innerHTML = res.data;
            })
    }

</script>

