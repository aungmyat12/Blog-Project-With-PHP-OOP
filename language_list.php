<?php

require_once "inc/header.php";

?>

<div class="card card-dark">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>id</th>
                <th>Name</th>
                <th>Control</th>
            </tr>
            </thead>
            <tbody class="output">

            </tbody>
        </table>
    </div>
</div>


<?php
require_once "inc/footer.php";

?>

<script>
    let output = document.querySelector(".output");
    function showList() {
        axios.get("api.php?language_list")
            .then(res => output.innerHTML = res.data);
    }
    showList();
</script>



