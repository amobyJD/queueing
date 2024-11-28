<!DOCTYPE html>
<html>
<head>
    <title>Text Moving Effect</title>
    <style>
        /* Define CSS styles */
        #movingText {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            font-size: 24px;
            color: blue;
        }
    </style>
</head>
<body>

<?php
echo "<div id='movingText'>Text Moving Effect<br> sample<br>test</div>";
?>

<script>
    // JavaScript for the moving text effect
    var textElement = document.getElementById('movingText');
    var posY = 0; // Initial position from top (in pixels)
    var speed = 1; // Change the speed of movement by modifying this value

    function moveText() {
        posY += speed;
        textElement.style.top = posY + 'px';

        // Reset position when it reaches the bottom
        if (posY >= window.innerHeight - textElement.clientHeight) {
            posY = 0;
        }

        // Request animation frame for smooth movement
        window.requestAnimationFrame(moveText);
    }

    // Start the animation
    moveText();
</script>

</body>
</html>
