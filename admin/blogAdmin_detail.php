<?php
// include '../BE/db_blogAdmin_detail.php';
$blog_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($blog_id) {
    $stmt = $conn->prepare("SELECT title, content, status, author, category, image_path, description FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="../Css/blogAdmin_detail.css">
    <title>Blog_detail</title>
    <style>
    </style>
</head>

<body>
    <?php include('menu.php'); ?>
    <header class="Text_blog">
        <div class="backround_blog">
            <a href="../admin/BlogAdmin.php"><span class="icon">&#8592;</span>Bài viết</a>
            <h1>Đăng bài viết mới</h1>
        </div>

        <div class="editor-container" id="data-form">
            <input type="text" id="id" name="id" style="display: none;"
                value="<?php echo $blog_id ? $blog_id : '' ?>" />
            <input type="text" id="old_image" name="id" style="display: none;"
                value="<?php echo isset($blog['image_path']) ? htmlspecialchars($blog['image_path']) : '' ?>" />

            <label for="title">Tiêu Đề</label>
            <input type="text" value="<?php echo isset($blog['title']) ? htmlspecialchars($blog['title']) : '' ?>"
                id="title" placeholder="Nhập tiêu đề bài viết">

            <label for="content">Nội Dung Bài Viết</label>
            <div class="toolbar">
                <div class="dropdown-container">
                    <div class="font-dropdown">
                        <select id="font-name" name="font-name">
                            <option value="Arial">Arial</option>
                            <option value="Verdana">Verdana</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Tahoma">Tahoma</option>
                            <option value="Abadi">Abadi</option>
                        </select>
                    </div>

                    <div class="custom-select">
                        <select id="font-size" name="font-size"
                            style="padding: 8px; border: 1px solid #ddd; border-radius: 5px; width: 150px;">
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="14">14</option>
                            <option value="16">16</option>
                            <option value="18">18</option>
                            <option value="20">20</option>
                            <option value="22">22</option>
                            <option value="24">24</option>
                            <option value="26">26</option>
                            <option value="28">28</option>
                            <option value="36">36</option>
                            <option value="48">48</option>
                            <option value="72">72</option>
                        </select>
                    </div>

                    <div class="custom-select">
                        <select>
                            <option>Định dạng</option>
                            <option>Normal</option>
                            <option>Heading 1</option>
                            <option>Heading 2</option>
                            <option>Heading 3</option>
                        </select>
                    </div>
                </div>

                <button id="text-color-button"><span class="material-icons-outlined">format_color_text</span></button>
                <button id="bg-color-button"><span class="material-icons-outlined">format_color_fill</span></button>
                <button id="line-height-button"><span class="material-icons-outlined">line_weight</span> Line
                    Height</button>


                <div id="text-color-picker" class="color-picker" style="display:none;">
                    <div class="color-option" style="background-color: transparent;" data-color="inherit">None
                    </div>
                    <div class="color-option" style="background-color: yellow;" data-color="yellow"></div>
                    <div class="color-option" style="background-color: green;" data-color="green"></div>
                    <div class="color-option" style="background-color: cyan;" data-color="cyan"></div>
                    <div class="color-option" style="background-color: red;" data-color="red"></div>
                    <div class="color-option" style="background-color: blue;" data-color="blue"></div>
                    <div class="color-option" style="background-color: black;" data-color="black"></div>
                    <div class="color-option" style="background-color: orange;" data-color="orange"></div>
                    <div class="color-option" style="background-color: purple;" data-color="purple"></div>
                    <div class="color-option" style="background-color: pink;" data-color="pink"></div>
                    <div class="color-option" style="background-color: brown;" data-color="brown"></div>
                    <div class="color-option" style="background-color: gray;" data-color="gray"></div>
                </div>


                <div id="bg-color-picker" class="color-picker" style="display:none;">
                    <div class="color-option" style="background-color: transparent;" data-color="inherit">None</div>
                    <div class="color-option" style="background-color: yellow;" data-color="yellow"></div>
                    <div class="color-option" style="background-color: green;" data-color="green"></div>
                    <div class="color-option" style="background-color: cyan;" data-color="cyan"></div>
                    <div class="color-option" style="background-color: red;" data-color="red"></div>
                    <div class="color-option" style="background-color: blue;" data-color="blue"></div>
                    <div class="color-option" style="background-color: black;" data-color="black"></div>
                    <div class="color-option" style="background-color: orange;" data-color="orange"></div>
                    <div class="color-option" style="background-color: purple;" data-color="purple"></div>
                    <div class="color-option" style="background-color: pink;" data-color="pink"></div>
                    <div class="color-option" style="background-color: brown;" data-color="brown"></div>
                    <div class="color-option" style="background-color: gray;" data-color="gray"></div>
                </div>

                <div id="line-height-options" class="line-height-picker" style="display:none;">
                    <button class="line-option" data-line-height="1">1.0</button>
                    <button class="line-option" data-line-height="1.5">1.5</button>
                    <button class="line-option" data-line-height="2">2.0</button>
                    <button class="line-option" data-line-height="2.5">2.5</button>
                    <button class="line-option" data-line-height="3">3.0</button>
                    <button class="line-option" data-line-height="3.5">3.5</button>
                    <button class="line-option" data-line-height="4">4.0</button>
                    <button class="line-option" data-line-height="4.5">4.5</button>
                    <button class="line-option" data-line-height="5">5.0</button>
                </div>

                <div class="custom-dropdown">
                    <button id="bullet-dropdown-button">
                        <span class="material-icons-outlined">format_list_bulleted</span>
                        <span class="arrow-down">▼</span>
                    </button>

                    <div id="bullet-dropdown-menu" class="dropdown-content">
                        <h3>Bullet Library</h3>
                        <div class="bullet-options">
                            <button class="bullet-option" data-bullet="none">None</button>
                            <button class="bullet-option" data-bullet="circle">●</button>
                            <button class="bullet-option" data-bullet="hollow-circle">○</button>
                            <button class="bullet-option" data-bullet="square">■</button>
                            <button class="bullet-option" data-bullet="cross">✚</button>
                            <button class="bullet-option" data-bullet="diamond">◆</button>
                            <button class="bullet-option" data-bullet="arrow">➤</button>
                            <button class="bullet-option" data-bullet="check">✔</button>
                        </div>
                    </div>
                </div>

                <div class="custom-dropdown">
                    <button id="numbered-dropdown-button">
                        <span class="material-icons-outlined">format_list_numbered</span>
                        <span class="arrow-down">▼</span>
                    </button>
                    <div id="numbered-dropdown-menu" class="dropdown-content">
                        <h3>Numbering Library</h3>
                        <div class="numbering-options">
                            <div class="numbering-option" data-numbering="none">None</div>
                            <div class="numbering-option" data-numbering="1.">1.<br>2.<br>3.</div>
                            <div class="numbering-option" data-numbering="1)">1)<br>2)<br>3)</div>
                            <div class="numbering-option" data-numbering="I.">I.<br>II.<br>III.</div>
                            <div class="numbering-option" data-numbering="A.">A.<br>B.<br>C.</div>
                            <div class="numbering-option" data-numbering="a)">a)<br>b)<br>c)</div>
                            <div class="numbering-option" data-numbering="a.">a.<br>b.<br>c.</div>
                            <div class="numbering-option" data-numbering="i.">i.<br>ii.<br>iii.</div>
                        </div>
                    </div>
                </div>


                <div class="button-container">
                    <button><span class="material-icons-outlined">format_bold</span></button>
                    <button><span class="material-icons-outlined">format_italic</span></button>
                    <button><span class="material-icons-outlined">format_underlined</span></button>

                    <button id="insert-link-btn">
                        <span class="material-icons-outlined">insert_link</span>
                    </button>

                    <button id="insert-image-btn">
                        <span class="material-icons-outlined">image</span>
                    </button>

                    <div id="link-input-container" style="display: none;">
                        <input type="text" id="link-input" placeholder="Dán link vào đây" />
                        <button id="add-link-btn">Thêm liên kết</button>
                    </div>

                    <div id="image-input-container" style="display: none;">
                        <input type="file" id="image-input" accept="image/*" />
                        <button id="add-image-btn">Thêm hình ảnh</button>
                    </div>
                    <button><span class="material-icons-outlined">format_align_left</span></button>
                    <button><span class="material-icons-outlined">format_align_center</span></button>
                    <button><span class="material-icons-outlined">format_align_right</span></button>
                </div>
            </div>
            <textarea id="content"
                placeholder="Nội dung bài viết"><?php echo isset($blog['content']) ? htmlspecialchars($blog['content']) : ''; ?></textarea>
        </div>


        <div class="summary-container">
            <div class="summary">
                <h3>Tóm tắt</h3>
                <label for="image_caption">Thêm tóm tắt ngắn để hiển thị trên blog của bạn:</label>
                <input type="text" id="image_caption" name="image_caption"
                    value="<?php echo isset($blog['description']) ? htmlspecialchars($blog['description']) : '' ?>"
                    placeholder="Nhập mô tả ảnh">
            </div>

            <div class="summary">
                <h3>Xem trước kết quả tìm kiếm</h3>
                <p>Xin hãy nhập Tiêu đề và Mô tả để xem trước kết quả tìm kiếm của bài viết này. <a href="#"
                        class="sumary_KQ">Tùy chỉnh
                        SEO</a></p>
            </div>
        </div>

        <!-- cach -->
        <div class="containers">
            <div class="form-group">
                <label>Trạng thái:</label>
                <input type="radio" name="status" value="Hiển thị" checked> Hiển thị
                <input type="radio" name="status" value="Ẩn"> Ẩn
            </div>


            <div class="form-group">
                <label>Ảnh bài viết:</label>
                <img src="<?php echo htmlspecialchars($blog['image_path']); ?>" alt="">
                <div style="margin-top: 25px;" class="image_path"
                    onclick="document.getElementById('image_path').click()">
                    Upload ảnh
                    <input type="file" id="image_path" accept="image/*" required>
                </div>
            </div>

            <div class="form-group">
                <label for="author">Tác giả:</label>
                <input type="text" id="author"
                    value="<?php echo isset($blog['author']) ? htmlspecialchars($blog['author']) : '' ?>" name="author"
                    placeholder="Nhập tên tác giả">
            </div>

            <div class="form-group">
                <label for="category">Danh mục:</label>
                <select id="category" name="category">
                    <option value="Tin tức"
                        <?php echo isset($blog['category']) && $blog['category'] == 'Tin tức'  ? 'selected' : ''; ?>>Tin
                        tức</option>
                    <option value="Sự kiện"
                        <?php echo isset($blog['category']) && $blog['category'] == 'Sự kiện'  ? 'selected' : ''; ?>>Sự
                        kiện</option>
                    <option value="Khuyến mãi"
                        <?php echo isset($blog['category']) && $blog['category'] == 'Khuyến mãi'  ? 'selected' : ''; ?>>
                        Khuyến mãi</option>
                </select>
            </div>

            <div class="buttons">
                <button class="cancel-btn" data-form="editor-form" onclick="cancelForm()">Hủy</button>
                <button class="save-btn" data-form="editor-form" onclick="saveForm()">Lưu</button>
            </div>
        </div>
    </header>
    <script src="../Js/blogAdmin_detail.js"></script>
</body>

</html>