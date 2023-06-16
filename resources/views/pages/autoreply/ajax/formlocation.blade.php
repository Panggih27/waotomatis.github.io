 <style>
     #map {
         height: 350px;
     }
 </style>
 <div class="mb-3">
     <div id="map"></div>
 </div>
 <div class="row">
     <div class="mb-3 col-6">
         <label for="latitude">Latitude</label>
         <input type="text" name="latitude" class="form-control" id="latitude"
             value="{{ isset($autoreply) && $autoreply->reply_type == 'location' ? json_decode($autoreply->reply)->lat : old('latitude') }}"
             placeholder="Latitude" readonly>
     </div>
     <div class="mb-3 col-6">
         <label for="longitude">Longitude</label>
         <input type="text" name="longitude" class="form-control" id="longitude"
             value="{{ isset($autoreply) && $autoreply->reply_type == 'location' ? json_decode($autoreply->reply)->long : old('longitude') }}"
             placeholder="Longitude" readonly>
     </div>
 </div>

 <script>
     $(document).ready(function() {
         @if (isset($autoreply) && $autoreply->reply_type == 'location')
             var defLat = '{{ json_decode($autoreply->reply)->lat }}';
             var defLong = '{{ json_decode($autoreply->reply)->long }}';
         @else
             var defLat = -6.175392;
             var defLong = 106.827153;
         @endif
         function setToInput(lat, long) {
             $('#latitude').val(lat)
             $('#longitude').val(long)
         }
         setToInput(defLat, defLong);
         var map = L.map('map').setView([defLat, defLong], 14);
         var marker = L.marker([defLat, defLong], {
             draggable: true
         }).addTo(map);
         L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
             maxZoom: 19,
             attribution: '© OpenStreetMap'
         }).addTo(map);
         marker.on('moveend', function(e) {
             setToInput(e.target._latlng.lat, e.target._latlng.lng);
         });
         map.on('click', function(e) {
             marker.setLatLng(e.latlng)
             setToInput(e.latlng.lat, e.latlng.lng);
         });
         var control = L.Control.geocoder({
             placeholder: 'Search here...',
             defaultMarkGeocode: false,
         }).on('markgeocode', function(e) {
             marker.setLatLng(e.geocode.center)
             map.setView(e.geocode.center, map.getZoom());
             setToInput(e.geocode.center.lat, e.geocode.center.lng);
         }).addTo(map);
     })
 </script>
