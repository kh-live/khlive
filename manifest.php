<?PHP
header('Content-Type: application/json');
?>
{
  "short_name": "KH-timing",
  "name": "KH Live Timing page",
  "display":"fullscreen",
  "theme_color": "#000000",
  "background_color": "#000000",
  "orientation": "portrait",
  "icons": [
    {
      "src": "img/time-vsmall.png",
      "type": "image/png",
      "sizes": "144x144"
    }, {
      "src": "img/time-small.png",
      "type": "image/png",
      "sizes": "192x192"
    }, {
      "src": "img/time.png",
      "type": "image/png",
      "sizes": "512x512"
    }
  ],
  "start_url": "/time"
}
