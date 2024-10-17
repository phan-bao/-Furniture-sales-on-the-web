const prevBtn = document.querySelector('.prev-btn');
const nextBtn = document.querySelector('.next-btn');
const sliderItems = document.querySelector('.slider-items');
let currentIndex = 0;
const slideCount = sliderItems.children.length;
const slideWidth = sliderItems.querySelector('.slider-item').offsetWidth;

if (prevBtn && nextBtn && sliderItems) {
    nextBtn.addEventListener('click', () => {
        if (currentIndex < slideCount - 1) {
            currentIndex++;
        } else {
            currentIndex = 0;  // Quay lại slide đầu tiên
        }
        sliderItems.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
    });

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
        } else {
            currentIndex = slideCount - 1;  // Quay lại slide cuối cùng
        }
        sliderItems.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
    });
}
