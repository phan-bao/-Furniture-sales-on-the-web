let textColorButton = document.getElementById("text-color-button");
let textColorPicker = document.getElementById("text-color-picker");
let bgColorButton = document.getElementById("bg-color-button");
let bgColorPicker = document.getElementById("bg-color-picker");
let lineHeightButton = document.getElementById("line-height-button");
let lineHeightOptions = document.getElementById("line-height-options");
let fontNameSelect = document.getElementById("font-name");
let fontSizeSelect = document.getElementById("font-size");
let contentArea = document.getElementById("content");

// Lưu thông tin bài viết
function saveForm() {
  const status = getStatus();
  const schedule = getSchedule();
  const author = getAuthor();
  const category = getCategory();
  const imageCaption = getImageCaption();

  alert(
    `Trạng thái: ${status}\nLịch hiển thị: ${schedule}\nTác giả: ${author}\nDanh mục: ${category}\nTóm tắt: ${imageCaption}`
  );
}

// Lấy trạng thái hiển thị
function getStatus() {
  const statusElement = document.querySelector('input[name="status"]:checked');
  return statusElement ? statusElement.value : "Không xác định";
}

// Lấy lịch hiển thị
function getSchedule() {
  const scheduleElement = document.getElementById("schedule");
  return scheduleElement ? scheduleElement.value : "Không xác định";
}

// Lấy tên tác giả
function getAuthor() {
  const authorElement = document.getElementById("author");
  return authorElement ? authorElement.value : "Không xác định";
}

// Lấy danh mục
function getCategory() {
  const categoryElement = document.getElementById("category");
  return categoryElement ? categoryElement.value : "Không xác định";
}

// Lấy tóm tắt ảnh
function getImageCaption() {
  const imageCaptionElement = document.getElementById("image_caption");
  return imageCaptionElement ? imageCaptionElement.value : "Không xác định";
}

function disableFormatButtons(disabled) {
  document.querySelectorAll(".button-container button").forEach((button) => {
    button.disabled = disabled;
  });
}

function hasSelection() {
  return window.getSelection().toString().trim().length > 0;
}


function updateButtonState() {
  disableFormatButtons(!hasSelection());
}

// Toggle style helper function
function toggleStyle(styleProperty, activeValue, inactiveValue) {
  if (hasSelection()) {
    contentArea.style[styleProperty] =
      contentArea.style[styleProperty] === activeValue
        ? inactiveValue
        : activeValue;
  }
}

// Áp dụng danh sách không có số
function applyUnorderedList() {
  if (hasSelection()) {
    wrapSelectionWithList("ul");
  }
}


function applyOrderedList() {
  if (hasSelection()) {
    wrapSelectionWithList("ol");
  }
}

function wrapSelectionWithList(listType) {
  const selectedText = window.getSelection().toString().trim();
  if (selectedText) {
    const listItems = selectedText
      .split("\n")
      .map((line) => `<li>${line.trim()}</li>`)
      .join("");
    const listHTML = `<${listType}>${listItems}</${listType}>`;
    document.execCommand("insertHTML", false, listHTML);
  }
}

// Khởi tạo sự kiện cho các nút định dạng
function initFormatButtons() {
  document.querySelectorAll(".button-container button").forEach((button) => {
    button.addEventListener("click", function () {
      const format = this.querySelector("span").innerText;

      switch (format) {
        case "format_bold":
          toggleStyle("fontWeight", "bold", "normal");
          break;
        case "format_italic":
          toggleStyle("fontStyle", "italic", "normal");
          break;
        case "format_underlined":
          toggleStyle("textDecoration", "underline", "none");
          break;
        case "format_list_bulleted":
          applyUnorderedList();
          break;
        case "format_list_numbered":
          applyOrderedList();
          break;
        case "format_align_left":
          contentArea.style.textAlign = "left";
          break;
        case "format_align_center":
          contentArea.style.textAlign = "center";
          break;
        case "format_align_right":
          contentArea.style.textAlign = "right";
          break;
        case "insert_link":
          insertLink();
          break;
        case "image":
          insertImage();
          break;
        default:
          break;
      }
    });
  });
}

document.addEventListener("selectionchange", updateButtonState);

function hasSelection() {
  const selection = window.getSelection();
  return selection && selection.toString().length > 0;
}


function showPicker(button, picker) {
  if (hasSelection()) {
    picker.style.display = picker.style.display === "block" ? "none" : "block";
    picker.style.top = `${button.offsetTop + button.offsetHeight}px`;
    picker.style.left = `${button.offsetLeft}px`;
  }
}

function initColorAndLineHeightButtons() {
  initTextColorButton();
  initBgColorButton();
  initLineHeightButton();
}

function initTextColorButton() {
  textColorButton.addEventListener("click", function (event) {
    event.stopPropagation();
    showPicker(textColorButton, textColorPicker);
    bgColorPicker.style.display = "none";
  });

  textColorPicker.addEventListener("click", function (event) {
    if (hasSelection() && event.target.classList.contains("color-option")) {
      changeTextColor(event);
    }
  });
}

function initTextColorButton() {
  textColorButton.addEventListener("click", function (event) {
    event.stopPropagation();
    showPicker(textColorButton, textColorPicker);
    bgColorPicker.style.display = "none";
  });

  textColorPicker.addEventListener("click", function (event) {
    if (hasSelection() && event.target.classList.contains("color-option")) {
      changeTextColor(event);
    }
  });
}

function changeTextColor(event) {
  const selection = window.getSelection();
  const selectedText = selection.toString();
  
  if (selectedText) {
    const range = selection.getRangeAt(0);
    const selectedNode = range.startContainer;

    document.execCommand('styleWithCSS', false, true); 
    document.execCommand('foreColor', false, event.target.dataset.color);

    textColorPicker.style.display = "none";
  }
}

function initBgColorButton() {
  bgColorButton.addEventListener("click", function (event) {
    event.stopPropagation();
    showPicker(bgColorButton, bgColorPicker);
    textColorPicker.style.display = "none";
  });

  bgColorPicker.addEventListener("click", function (event) {
    if (hasSelection() && event.target.classList.contains("color-option")) {
      changeBgColor(event);
    }
  });
}

function changeBgColor(event) {
  const selection = window.getSelection();
  const selectedText = selection.toString();
  
  const span = document.createElement("span");
  span.style.backgroundColor = event.target.dataset.color;
  span.textContent = selectedText;

  const range = selection.getRangeAt(0);
  range.deleteContents();
  range.insertNode(span);

  bgColorPicker.style.display = "none";
}

function initLineHeightButton() {
  lineHeightButton.addEventListener("click", function (event) {
    event.stopPropagation();
    showPicker(lineHeightButton, lineHeightOptions);
  });

  lineHeightOptions.addEventListener("click", function (event) {
    if (hasSelection() && event.target.classList.contains("line-option")) {
      changeLineHeight(event);
    }
  });
}

function changeLineHeight(event) {
  const selection = window.getSelection();
  const range = selection.getRangeAt(0);
  const selectedText = selection.toString();

  if (selectedText) {
    const wrapper = document.createElement("span");
    wrapper.style.display = "inline-block"; 
    wrapper.style.lineHeight = event.target.dataset.lineHeight;

    range.deleteContents();
    range.insertNode(wrapper);

    wrapper.appendChild(range.extractContents());

    lineHeightOptions.style.display = "none";
  }
}

function initFontControls() {
  fontNameSelect.addEventListener("change", function () {
    if (hasSelection()) {
      contentArea.style.fontFamily = fontNameSelect.value;
    }
  });

  fontSizeSelect.addEventListener("change", function () {
    if (hasSelection()) {
      contentArea.style.fontSize = `${fontSizeSelect.value}px`;
    }
  });
}

document.addEventListener("click", function (event) {
  if (
    !textColorPicker.contains(event.target) &&
    event.target !== textColorButton
  ) {
    textColorPicker.style.display = "none";
  }
  if (!bgColorPicker.contains(event.target) && event.target !== bgColorButton) {
    bgColorPicker.style.display = "none";
  }
  if (
    !lineHeightOptions.contains(event.target) &&
    event.target !== lineHeightButton
  ) {
    lineHeightOptions.style.display = "none";
  }
});

const bulletDropdownButton = document.getElementById("bullet-dropdown-button");
const bulletDropdownMenu = document.getElementById("bullet-dropdown-menu");
const bulletOptions = document.querySelectorAll(".bullet-option");


let currentBullet = null;

bulletDropdownButton.addEventListener("click", () => {
  bulletDropdownMenu.classList.toggle("show");
});


window.addEventListener("click", (e) => {
  if (
    !bulletDropdownButton.contains(e.target) &&
    !bulletDropdownMenu.contains(e.target)
  ) {
    bulletDropdownMenu.classList.remove("show");
  }
});

bulletOptions.forEach((option) => {
  option.addEventListener("click", () => {
    currentBullet = option.getAttribute("data-bullet"); 
    bulletDropdownMenu.classList.remove("show");
    addBulletToContent(currentBullet); 
    contentArea.focus();
  });
});


function addBulletToContent(bulletStyle) {
  if (!bulletStyle || bulletStyle === "none") {
    currentBullet = null; 
    return;
  }
  const cursorPosition = contentArea.selectionStart; 
  const textBefore = contentArea.value.substring(0, cursorPosition);
  const textAfter = contentArea.value.substring(cursorPosition);
  const bullet = getBulletSymbol(bulletStyle); 
  const newLine = `${bullet} `; 
  contentArea.value = `${textBefore}\n${newLine}${textAfter}`.trim(); 
  const newCursorPosition = cursorPosition + newLine.length;
  contentArea.setSelectionRange(newCursorPosition, newCursorPosition); 
}


contentArea.addEventListener("keydown", (e) => {
  if (e.key === "Enter" && currentBullet) {
    e.preventDefault(); 
    const cursorPosition = contentArea.selectionStart;
    const textBefore = contentArea.value.substring(0, cursorPosition);
    const textAfter = contentArea.value.substring(cursorPosition);
    const bullet = getBulletSymbol(currentBullet);
    const newLine = `\n${bullet} `; 
    contentArea.value = `${textBefore}${newLine}${textAfter}`;
    const newCursorPosition = cursorPosition + newLine.length; 
    contentArea.setSelectionRange(newCursorPosition, newCursorPosition); 
  }
});

function getBulletSymbol(style) {
  if (style === "circle") return "●";
  if (style === "hollow-circle") return "○";
  if (style === "square") return "■";
  if (style === "cross") return "✚";
  if (style === "diamond") return "◆";
  if (style === "arrow") return "➤";
  if (style === "check") return "✔";
  return ""; 
}


const numberedDropdownButton = document.getElementById(
  "numbered-dropdown-button"
);
const numberedDropdownMenu = document.getElementById("numbered-dropdown-menu");
const numberingOptions = document.querySelectorAll(".numbering-option");

let currentNumbering = null; 
let currentIndex = 1; 

numberedDropdownButton.addEventListener("click", () => {
  numberedDropdownMenu.classList.toggle("show");
});


window.addEventListener("click", (e) => {
  if (
    !numberedDropdownButton.contains(e.target) &&
    !numberedDropdownMenu.contains(e.target)
  ) {
    numberedDropdownMenu.classList.remove("show");
  }
});


numberingOptions.forEach((option) => {
  option.addEventListener("click", () => {
    currentNumbering = option.getAttribute("data-numbering");
    if (currentNumbering === "none") {
      removeNumbering();
    } else {
      currentIndex = getNextIndex(); 
      addNewNumberingLine(currentNumbering); 
    }
    numberedDropdownMenu.classList.remove("show");
    contentArea.focus();
  });
});

// Xóa toàn bộ số thứ tự
function removeNumbering() {
  const textLines = contentArea.value.split("\n");
  const newContent = textLines
    .map((line) => line.replace(/^\d+(\)|\.)\s*/, ""))
    .join("\n");
  contentArea.value = newContent;
  currentNumbering = null;
  currentIndex = 1; 
}

// Thêm kiểu numbering mới ở dòng mới
function addNewNumberingLine(numberingStyle) {
  const cursorPosition = contentArea.selectionStart; 
  const textBefore = contentArea.value.substring(0, cursorPosition); 
  const textAfter = contentArea.value.substring(cursorPosition); 
  const prefix = getPrefix(currentIndex, numberingStyle); 
  const newLine = `${prefix} `; 
  currentIndex++; 
  contentArea.value = `${textBefore}\n${newLine}${textAfter}`.trim(); 
  const newCursorPosition = cursorPosition + newLine.length + 1; 
  contentArea.setSelectionRange(newCursorPosition, newCursorPosition); 
}

// Tự động thêm số khi nhấn Enter
contentArea.addEventListener("keydown", (e) => {
  if (e.key === "Enter" && currentNumbering && currentNumbering !== "none") {
    e.preventDefault();
    const prefix = getPrefix(currentIndex, currentNumbering);
    currentIndex++; 
    const cursorPosition = contentArea.selectionStart;
    const textBefore = contentArea.value.substring(0, cursorPosition);
    const textAfter = contentArea.value.substring(cursorPosition);
    contentArea.value = `${textBefore}\n${prefix} ${textAfter}`;
    contentArea.setSelectionRange(
      cursorPosition + prefix.length + 2,
      cursorPosition + prefix.length + 2
    );
  }
});

// Lấy số tiếp theo dựa trên nội dung hiện tại
function getNextIndex() {
  const textLines = contentArea.value.split("\n");
  let maxIndex = 0;
  textLines.forEach((line) => {
    const match = line.match(/^(\d+)(\)|\.)\s/); 
    if (match) {
      const number = parseInt(match[1]);
      if (number > maxIndex) {
        maxIndex = number; 
      }
    }
  });
  return maxIndex + 1; 
}

// Hàm lấy tiền tố dựa trên kiểu numbering
function getPrefix(index, style) {
  if (style === "1.") return `${index}.`;
  if (style === "I.") return toRoman(index) + ".";
  if (style === "A.") return String.fromCharCode(64 + index) + ".";
  if (style === "a)") return String.fromCharCode(96 + index) + ")";
  if (style === "a.") return String.fromCharCode(96 + index) + ".";
  if (style === "i.") return toRoman(index).toLowerCase() + ".";
  return "";
}

// Hàm chuyển số thành số La Mã
function toRoman(num) {
  const romanNumerals = [
    "M",
    "CM",
    "D",
    "CD",
    "C",
    "XC",
    "L",
    "XL",
    "X",
    "IX",
    "V",
    "IV",
    "I",
  ];
  const values = [1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1];
  let result = "";
  for (let i = 0; i < values.length; i++) {
    while (num >= values[i]) {
      result += romanNumerals[i];
      num -= values[i];
    }
  }
  return result;
}


// image-------------------------------------------
// Xử lý sự kiện upload ảnh
var fileInput;

function handleImageUpload() {
  const imageUploadInput = document.getElementById("image_path");
  const uploadImageDiv = document.querySelector(".image_path");

  if (!imageUploadInput || !uploadImageDiv) {
    console.error("Không tìm thấy phần tử input hoặc div");
    return;
  }

  imageUploadInput.addEventListener("change", function () {
    console.log("Đã chọn file: ", imageUploadInput.files.length);
    if (imageUploadInput.files.length > 0) {
      const file = imageUploadInput.files[0];
      fileInput = file;
      const reader = new FileReader(); 

      reader.onload = function (e) {
        const imgElement = document.createElement("img");
        imgElement.src = e.target.result; 
        imgElement.alt = file.name; 
        imgElement.style.maxWidth = "100%"; 

        uploadImageDiv.innerHTML = "";
        uploadImageDiv.appendChild(imgElement); 
      };

      reader.readAsDataURL(file);
    } else {
      alert("Không có ảnh nào được chọn.");
    }
  });
}

// Gọi hàm khi trang được tải xong
document.addEventListener("DOMContentLoaded", function () {
  handleImageUpload();
});

document.querySelectorAll('.cancel-btn').forEach((button) => {
  button.addEventListener('click', (event) => {
    event.preventDefault(); 
    cancelForm(event.target); 
  });
});



// nút hủy form
function cancelForm(button) {
  console.log("Hàm cancelForm được gọi");

  // Lấy form cha gần nhất chứa nút Cancel
  const form = button.closest('.editor-container');

  if (!form) {
    alert("Không tìm thấy form liên kết với nút này!");
    return;
  }

  // Kiểm tra dữ liệu chưa lưu trong các trường input, textarea, và select
  const inputs = form.querySelectorAll("input, textarea, select");
  if (!inputs.length) {
    alert("Không tìm thấy dữ liệu trong form!");
    return;
  }

  let hasUnsavedData = false;

  inputs.forEach((input) => {
    if (input.type === "radio" || input.type === "checkbox") {
      if (input.checked !== input.defaultChecked) {
        hasUnsavedData = true;
      }
    } else if (input.value.trim() !== "") {
      hasUnsavedData = true;
    }
  });

  // Hiển thị xác nhận nếu có dữ liệu chưa được lưu
  if (hasUnsavedData) {
    const confirmCancel = confirm(
      "Bạn có muốn hủy lại trang không? Dữ liệu chưa được lưu sẽ bị mất."
    );
    if (!confirmCancel) {
      alert("Dữ liệu vẫn được giữ lại!");
      return;
    }
  }

  // Reset tất cả dữ liệu trong form
  inputs.forEach((input) => {
    if (input.type === "radio" || input.type === "checkbox") {
      input.checked = input.defaultChecked; 
    } else {
      input.value = ""; 
    }
  });

  alert("Dữ liệu đã bị xóa!");
}



// Xử lý sự kiện nhấn nút lưu
function saveForm() {
  console.log("Hàm saveForm được gọi");
  // Truy xuất dữ liệu từ các trường nhập liệu
  var id = document.getElementById("id").value;
  var old_image = document.getElementById("old_image").value;
  var title = document.getElementById("title").value;
  var content = document.getElementById("content").value;
  var imageCaption = document.getElementById("image_caption").value;
  var author = document.getElementById("author").value;
  var category = document.getElementById("category").value;
  var status = document.querySelector('input[name="status"]:checked')
    ? document.querySelector('input[name="status"]:checked').value
    : null; // Trạng thái
 

  var imageFile = fileInput;

  if (!title || !content) {
    alert("Vui lòng nhập đầy đủ tiêu đề và nội dung bài viết!");
    return; 
  }

  if (!status) {
    alert("Vui lòng chọn trạng thái!");
    return;
  }

  // Tạo đối tượng FormData để gửi dữ liệu lên server
  var formData = new FormData();
  formData.append("title", title);
  formData.append("content", content);
  formData.append("image_caption", imageCaption);
  formData.append("author", author);
  formData.append("category", category);
  formData.append("status", status);

  if (imageFile) {
    formData.append("image", imageFile); 
  }

  if (id) {
    formData.append("id", id);
    if (!imageFile) {
      formData.append("old_image", old_image);
    }
    fetch("../BE/db_blogAdmin_detail.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        console.log("Phản hồi từ server:", data);
        if (data.status === "success") {
          alert("Bài viết đã được lưu thành công!");
          window.location.href = "../admin/BlogAdmin.php";
        } else {
          alert("Có lỗi xảy ra khi lưu bài viết: " + (data.message || ""));
        }
      })
      .catch((error) => {
        console.error("Lỗi Fetch:", error);
        alert("Không thể lưu bài viết. Vui lòng thử lại sau.");
      });
  }

  // Gửi dữ liệu lên server
  fetch("../BE/db_blogAdmin_detail.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("Phản hồi từ server:", data);
      if (data.status === "success") {
        alert("Bài viết đã được lưu thành công!");
        window.location.href = "../admin/BlogAdmin.php";
      } else {
        alert("Có lỗi xảy ra khi lưu bài viết: " + (data.message || ""));
      }
    })
    .catch((error) => {
      console.error("Lỗi Fetch:", error);
      alert("Không thể lưu bài viết. Vui lòng thử lại sau.");
    });
}

// Khởi tạo các sự kiện
function init() {
  handleImageUpload();
  initFormatButtons();
  initColorAndLineHeightButtons();
  initFontControls();
  disableFormatButtons(true); // Vô hiệu hóa các nút định dạng khi trang tải
}

// Gọi hàm khởi tạo khi tài liệu đã sẵn sàng
document.addEventListener("DOMContentLoaded", init);
