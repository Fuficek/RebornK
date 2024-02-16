// Function to update element positions
function updateElementPositions() {
    // Get elements with class 'orbit' once
    var elems = document.getElementsByClassName('orbit');

    // Calculate common values once outside the loop
    var centerX = window.innerWidth / 2; // Calculate the center X coordinate
    var centerY = window.innerHeight / 2; // Calculate the center Y coordinate
    var angle = -60;
    var increase = Math.PI * 2 / elems.length;

    // Loop through elements only once
    for (var i = 0, len = elems.length; i < len; i++) {
        var elem = elems[i];

        // Calculate positioning based on screen width
        var x, y;
        if (window.innerWidth > 900) {
            var offsetX = 50; // X center offset in pixels
            var offsetY = 40; // Y center offset in pixels
            var radiusX = 500; // Adjust the X radius as needed
            var radiusY = 300; // Adjust the Y radius as needed

            // Adjust the X radius for larger screens
            if (window.innerWidth < 1200) {
                radiusX = 320;
            }

            // Calculate position using trigonometric functions
            x = centerX - elem.offsetWidth / 2 + radiusX * Math.cos(angle) - elem.offsetWidth / 2 + offsetX;
            y = centerY - elem.offsetHeight / 2 + radiusY * Math.sin(angle) - elem.offsetHeight / 2 + offsetY;

            // Add 'center' class to specified element
            document.getElementById("vlastnosti-nadpis").classList.add("center");
        } else {
            // Reset position and remove 'center' class
            elem.style.position = 'inherit'; // Reset position
            elem.style.left = 'auto'; // Reset left
            elem.style.top = 'auto'; // Reset top
            document.getElementById("vlastnosti-nadpis").classList.remove("center");
        }

        // Apply styles to the element
        if (window.innerWidth > 900) {
            elem.style.position = 'absolute'; // Apply position:absolute only for wider screens
            elem.style.left = x + 'px';
            elem.style.top = y + 'px';
        }

        angle += increase;
    }
}

// Call the function on window load and resize events with debounce
var timeout;
window.addEventListener('load', function () {
    clearTimeout(timeout);
    timeout = setTimeout(updateElementPositions, 200); // Delay execution after window load
});

window.addEventListener('resize', function () {
    clearTimeout(timeout);
    timeout = setTimeout(updateElementPositions, 200); // Debounce resize event
});
