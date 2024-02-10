function updateElementPositions() {
    var elems = document.getElementsByClassName('orbit');
    
    // Check if the screen width is greater than 900px
    if (window.innerWidth > 900) {
        var increase = Math.PI * 2 / elems.length;
        var centerX = window.innerWidth / 2; // Calculate the center X coordinate
        var centerY = window.innerHeight / 2; // Calculate the center Y coordinate
        var offsetX = 50; // X center offset in pixels
        var offsetY = 40; // Y center offset in pixels
        var radiusX = 500; // Adjust the X radius as needed
        var radiusY = 300; // Adjust the Y radius as needed
        var angle = -60, elem;

        // Check if the screen width is greater than 1200px
        if (window.innerWidth < 1200) {
            radiusX = 320; // Adjust the X radius for larger screens
        }

        for (var i = 0; i < elems.length; i++) {
            elem = elems[i];
            var x = centerX - elem.offsetWidth / 2 + radiusX * Math.cos(angle) - elem.offsetWidth / 2 + offsetX;
            var y = centerY - elem.offsetHeight / 2 + radiusY * Math.sin(angle) - elem.offsetHeight / 2 + offsetY;
            elem.style.position = 'absolute';
            elem.style.left = x + 'px';
            elem.style.top = y + 'px';
            angle += increase;
            var centerElement = document.getElementById("vlastnosti-nadpis");
            centerElement.classList.add("center");
        }
    } else {
        for (var i = 0; i < elems.length; i++) {
            elem = elems[i];
            elem.style.position = 'inherit';
            elem.style.left = 'auto';
            elem.style.top = 'auto';
            var centerElement = document.getElementById("vlastnosti-nadpis");
            centerElement.classList.remove("center");
        }
    }
}

window.addEventListener('load', function () {
    updateElementPositions();
});

// Call the function on window resize
window.addEventListener('resize', updateElementPositions);