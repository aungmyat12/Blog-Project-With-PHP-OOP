<?php

require_once "inc/header.php";

?>
<div class="card card-dark">
    <div class="card-header">
        <h3>Create New Category</h3>
    </div>
    <div class="card-body">
        <div class="alert rounded d-none alertBox"></div>
        <form method="post" id="createCategory">
            <div class="form-group">
                <label for="" class="text-white">Enter Category</label>
                <input type="text" name="category" class="form-control" id="categoryInput"
                       placeholder="Enter category">
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
    axios.get("api.php?user_auth")
    .then(res => {
        if(res.data === "success") {
            let createCategory = document.getElementById("createCategory");
            // let categoryInput = document.querySelector(".category");
            let categoryInput = document.getElementById("categoryInput");
            let alertBox = document.querySelector(".alertBox");

            function alert(text) {
                alertBox.classList.add("d-block");
                alertBox.innerHTML = text;
                setTimeout(() => {
                    alertBox.classList.remove("d-block");
                    alertBox.innerHTML = "";
                },2000)
            }
            createCategory.addEventListener("submit",function (e) {
                e.preventDefault();
                let input = categoryInput.value;
                let data = new FormData();
                data.append("category",input);
                axios.post("api.php",data)
                    .then(res => {
                        if(res.data[0] === "Category field is required") {
                            alertBox.classList.add('alert-danger');
                            alert("Category field is required")
                        } else {
                            categoryInput.value = "";
                            alertBox.classList.add('alert-success');
                            alert("Category added successfully");
                            showAllCategory("all_category");
                        }
                    })
            })
            function showAllCategory(table) {
                axios.get("api.php?" + table)
                    .then(res => {
                        document.querySelector(".all_category").innerHTML = res.data;
                    })
            }
        }
    })
</script>

