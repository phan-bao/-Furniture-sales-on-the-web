// -------------------tìm kiếm-------------------
function searchTable() {
    let input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase(); // Chuyển thành chữ hoa để so sánh không phân biệt chữ hoa, chữ thường
    table = document.getElementById("blogTable");
    tr = table.getElementsByTagName("tr"); // Lấy tất cả các hàng trong bảng

    // Duyệt qua tất cả các hàng trong bảng, bỏ qua hàng đầu tiên (tiêu đề cột)
    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none"; // Mặc định ẩn hàng

        td = tr[i].getElementsByTagName("td"); // Lấy tất cả các ô trong hàng
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText; // Lấy giá trị của ô (text hoặc innerText)

                // Kiểm tra nếu từ khóa tìm kiếm có trong ô hiện tại
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // Hiển thị hàng nếu có ít nhất một cột khớp
                    break; // Dừng kiểm tra nếu đã tìm thấy khớp
                }
            }
        }
    }
}

// ------------------chọn all check box------------------------
function toggleSelectAll() {
    let selectAllCheckbox = document.getElementById("selectAll"); // Lấy checkbox chọn tất cả
    let checkboxes = document.getElementsByClassName("row-checkbox"); // Lấy tất cả các checkbox trong bảng

    // Duyệt qua tất cả các checkbox và thiết lập trạng thái giống như checkbox "selectAll"
    for (let i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = selectAllCheckbox.checked;
    }
}

//  ----------------xóa nhiều bài viết -----------------------
function deleteAllPosts() {
    let checkboxes = document.querySelectorAll('.row-checkbox:checked');

    if (checkboxes.length === 0) {
        alert("Vui lòng chọn ít nhất một bài viết để xóa.");
        return;
    }

    let postIds = [];
    checkboxes.forEach(function(checkbox) {
        let postId = checkbox.getAttribute('data-post-id');
        postIds.push(postId);
    });

    if (confirm("Bạn có chắc chắn muốn xóa tất cả bài viết đã chọn?")) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "../BE/delete_posts.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    checkboxes.forEach(function(checkbox) {
                        let row = checkbox.closest('tr');
                        row.style.display = "none";
                    });
                } else {
                    alert("Xóa tất cả bài viết thất bại.");
                }
            }
        };

        xhr.send("postIds=" + JSON.stringify(postIds));
    }
}

// Kiểm tra nếu có ít nhất một checkbox được chọn
function isCheckboxChecked() {
    let checkboxes = document.querySelectorAll('.row-checkbox');
    for (let checkbox of checkboxes) {
        if (checkbox.checked) {
            return true;
        }
    }
    return false;
}

// Sửa bài viết
function editPost() {
    // Kiểm tra xem có checkbox nào được chọn không
    if (!isCheckboxChecked()) {
        alert("Bạn phải chọn bài viết trước khi sửa.");
        return; // Dừng lại nếu không có checkbox nào được chọn
    }

    // Lấy id của bài viết đã chọn
    let selectedCheckbox = document.querySelector('.row-checkbox:checked');
    let postId = selectedCheckbox.getAttribute('data-post-id');
    
    // Chuyển hướng tới trang sửa bài viết với id đã chọn
    window.location.href = 'blogAdmin_detail.php?id=' + postId;
}

// Xóa bài viết
function deletePost() {
    // Kiểm tra xem có checkbox nào được chọn không
    if (!isCheckboxChecked()) {
        alert("Bạn phải chọn bài viết trước khi xóa.");
        return; // Dừng lại nếu không có checkbox nào được chọn
    }

    // Lấy id của bài viết đã chọn
    let selectedCheckbox = document.querySelector('.row-checkbox:checked');
    let postId = selectedCheckbox.getAttribute('data-post-id');
    
    // Xác nhận xóa
    if (confirm("Bạn có chắc chắn muốn xóa bài viết này?")) {
        // Gửi yêu cầu xóa bài viết này qua AJAX
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "../BE/delete_post.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                let response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Nếu xóa thành công, ẩn dòng trong bảng
                    let row = selectedCheckbox.closest('tr');
                    row.style.display = "none";
                } else {
                    alert("Xóa bài viết thất bại.");
                }
            }
        };
        xhr.send("postId=" + postId); // Gửi ID bài viết cần xóa
    }
}

