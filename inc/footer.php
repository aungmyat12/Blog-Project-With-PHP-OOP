
</div>

</div>

</div>


</div>




<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js"
        integrity="sha384-fA23ZRQ3G/J53mElWqVJEGJzU0sTs+SvzG8fXVWP+kJQ1lwFAOkcUOysnlKJC33U"
        crossorigin="anonymous"></script>
<script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js"
        integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.0/axios.min.js"></script>
<script>$(document).ready(function () { $('body').bootstrapMaterialDesign(); });</script>
<script>
    axios.get("api.php?user_auth")
    .then(res => {
        console.log(res.data);
        if(res.data === "success") {
            let category_list = document.getElementById("category_list");
            let all_category = document.querySelector(".all_category")
            let language_list = document.getElementById("language_list");
            let all_language = document.querySelector(".all_language");
            let result = document.querySelector(".result");

            category_list.addEventListener("click",function (e) {
                e.preventDefault();
                showList("category_list")
            })

            language_list.addEventListener("click",function (e) {
                e.preventDefault();
                showList("language_list")
            })
            $(".result").delegate(".delCate","click",function () {
                let currentId = $(this).attr('data-id');
                axios.get("api.php?delete_category="+ currentId)
                    .then(res => {
                        showAllCategory("all_category");
                        showList("category_list");
                    })
            })
            $(".result").delegate(".delLan","click",function () {
                let currentId = $(this).attr('data-id');
                axios.get("api.php?delete_language="+ currentId)
                    .then(res => {
                        console.log(res.data);
                        showAllLanguage("all_language");
                        showList("language_list");
                    })
            })
            // $(".result").delegate(".delLan","click",function () {
            //     let currentId = $(this).attr('data-id');
            //     axios.get("api.php?delete_language="+ currentId)
            //         .then(res => {
            //
            //         })
            // })
            $(".result").delegate(".editCate","click",function () {
                let currentId = $(this).attr('data-id');
                axios.get("api.php?edit_category="+ currentId)
                    .then(res => {
                        if(res.data) {
                            $(".category").val(res.data.name);
                            document.querySelector(".edit_category_form").classList.add("d-block");
                            $("#updateId").val(res.data.id);
                            result.innerHTML = "";
                        }
                    })
            })
            $(".result").delegate(".editLan","click",function () {
                let currentId = $(this).attr('data-id');
                axios.get("api.php?edit_language="+ currentId)
                    .then(res => {
                        if(res.data) {
                            $(".language").val(res.data.name);
                            document.querySelector(".edit_language_form").classList.add("d-block");
                            $("#updateLanId").val(res.data.id);
                            result.innerHTML = "";
                        }
                    })
            })
            document.getElementById("updateCategory").addEventListener("submit",function (e) {
                e.preventDefault();
                updateData()
            })

            document.getElementById("updateLanguage").addEventListener("submit",function (e) {
                e.preventDefault();
                updateLanData()
            })

            function updateData() {
                let data = new FormData();
                let updateId = $("#updateId").val();
                let categoryValue = $(".category").val();
                data.append("updateId",updateId);
                data.append("updateCategory",categoryValue);
                axios.post("api.php",data).then(res => {
                    if(res.data = "success") {
                        showAllCategory("all_category");
                        location.href = "index.php";
                    }
                });
            }
            function updateLanData() {
                let data = new FormData();
                let updateId = $("#updateLanId").val();
                let languageValue = $(".language").val();
                data.append("updateId",updateId);
                data.append("updateLanguage",languageValue);
                axios.post("api.php",data).then(res => {
                    if(res.data = "success") {
                        showAllLanguage("all_language");
                        location.href = "index.php";
                    }
                });
            }

            function showList(table) {
                axios.get("api.php?"+ table)
                    .then(res => {
                        result.innerHTML = res.data;
                    })
            }

            function showAllLanguage(table) {
                axios.get("api.php?" + table)
                    .then(res => {
                        all_language.innerHTML = res.data;
                    })
            }
            showAllLanguage("all_language");
            function showAllCategory(table) {
                axios.get("api.php?" + table)
                    .then(res => {
                        all_category.innerHTML = res.data;
                    })
            }
            showAllCategory("all_category");
        } else {

        }
    });

</script>
</body>

</html>

