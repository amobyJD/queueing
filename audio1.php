<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text-to-Speech Example</title>
</head>
<body>

<button id="speakButton">Speak</button>

<script>
document.getElementById('speakButton').addEventListener('click', function() {
    var textToSpeak = '1001, Herbert and JD!';

    // Using SpeechSynthesis API for audio announcement
    var synth = window.speechSynthesis;
    var utterance = new SpeechSynthesisUtterance(textToSpeak);
    synth.speak(utterance);
});
</script>

</body>
</html>
