<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cemetery Locator</title>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.0/mapbox-gl-directions.css" type="text/css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <h1>Bridge: Baguio Cemetery</h1>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="admin/login.php">Admin Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h2>Find Your Loved Ones</h2>
        <p>Locate graves in the Baguio Cemetery with ease</p>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="search-container">
            <h3>Search Graves: 
                <br>(First Name/Middle Name/Last Name)</h3>
            <form id="searchForm">
                <input type="text" id="search" placeholder="Enter exact name...">
                <button type="submit">Search</button>
            </form>
            <div id="graveInfoBox"></div>
        </div>
        <div class="map-container">
            <div id="map"></div>
        </div>
    </section>

    

    <script src='https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js'></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-directions/v4.1.0/mapbox-gl-directions.js"></script>
   
       <script>
    mapboxgl.accessToken = '<?php echo MAPBOX_TOKEN; ?>';
    
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [120.57508914689474, 16.411633262870357],
        zoom: 17
    });

    // Add navigation controls
    map.addControl(new mapboxgl.NavigationControl());

    // Add user location control
    const geolocate = new mapboxgl.GeolocateControl({
        positionOptions: {
            enableHighAccuracy: true
        },
        trackUserLocation: true,
        showUserHeading: true
    });
    map.addControl(geolocate);

    // Function to show route to grave
    window.showRoute = function(lat, lng, name) {
        // Get user's location
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const userLocation = [position.coords.longitude, position.coords.latitude];
                const graveLocation = [lng, lat];

                // Clear existing markers
                document.querySelectorAll('.mapboxgl-marker').forEach(marker => marker.remove());

                // Add markers
                new mapboxgl.Marker({ color: '#0000FF' })
                    .setLngLat(userLocation)
                    .setPopup(new mapboxgl.Popup().setHTML('<h3>Your Location</h3>'))
                    .addTo(map);
                
                new mapboxgl.Marker({ color: '#FF0000' })
                    .setLngLat(graveLocation)
                    .setPopup(new mapboxgl.Popup().setHTML(`<h3>${name}</h3>`))
                    .addTo(map);

                // Get route from Mapbox Directions API
                try {
                    const query = await fetch(
                        `https://api.mapbox.com/directions/v5/mapbox/walking/${userLocation[0]},${userLocation[1]};${graveLocation[0]},${graveLocation[1]}?geometries=geojson&access_token=${mapboxgl.accessToken}`
                    );
                    const json = await query.json();
                    const data = json.routes[0];

                    // Remove existing route if any
                    if (map.getLayer('route')) {
                        map.removeLayer('route');
                    }
                    if (map.getSource('route')) {
                        map.removeSource('route');
                    }

                    // Add new route
                    map.addSource('route', {
                        type: 'geojson',
                        data: {
                            type: 'Feature',
                            properties: {},
                            geometry: data.geometry
                        }
                    });

                    map.addLayer({
                        id: 'route',
                        type: 'line',
                        source: 'route',
                        layout: {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        paint: {
                            'line-color': '#0000FF',
                            'line-width': 5,
                            'line-opacity': 0.75
                        }
                    });

                    // Fit map to show route
                    const bounds = new mapboxgl.LngLatBounds()
                        .extend(userLocation)
                        .extend(graveLocation);
                    map.fitBounds(bounds, { padding: 100 });

                } catch (error) {
                    console.error('Error getting route:', error);
                    alert('Error getting route directions');
                }
            },
            (error) => {
                console.error('Geolocation error:', error);
                alert('Please enable location services to see the route.');
            }
        );
    };

    // Error handling
    map.on('error', (e) => {
        console.error('Map error:', e);
    });
</script>
    <script src="assets/js/search.js"></script>
</body>
</html>
