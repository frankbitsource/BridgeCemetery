// Map initialization and controls
document.addEventListener('DOMContentLoaded', function() {
    mapboxgl.accessToken = mapboxToken; // This would be passed from PHP

    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [120.57508914689474, 16.411633262870357], // Baguio Cemetery
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

    // Error handling
    map.on('error', (e) => {
        console.error('Map error:', e);
    });

    // Make map available globally
    window.cemeterMap = map;
});
