const images = [
        "../Images/anh-khau-do-lon-mogi.jpg", // Main image
        "../Images/phongkhach5.jpg",
        "../Images/phongkhach3.jpg",
         "../Images/ảnh phòng khách.jpg"
        
    ];

    let currentIndex = 0;
    const imageElement = document.querySelector(".center img");

    function cycleImages() {
        currentIndex = (currentIndex + 1) % images.length; // Loop back to the first image
        imageElement.src = images[currentIndex];
    }

    // Add hover effect
    const centerElement = document.querySelector(".center");
    let hoverInterval;

    centerElement.addEventListener("mouseover", () => {
        hoverInterval = setInterval(cycleImages, 3000); // Cycle images every 3 seconds
    });

    centerElement.addEventListener("mouseout", () => {
        clearInterval(hoverInterval); // Stop cycling when hover ends
        currentIndex = 0; // Reset to the first image
        imageElement.src = images[currentIndex];
    });