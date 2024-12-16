document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const searchForm = document.getElementById('searchForm');
    const graveInfoBox = document.getElementById('graveInfoBox');

    searchForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const searchTerm = searchInput.value.trim();
        
        if (!searchTerm) return;

        try {
            const response = await fetch(`search.php?term=${encodeURIComponent(searchTerm)}`);
            const data = await response.json();
            
            if (data.length > 0) {
                const grave = data[0];
                const birthDate = grave.birth_date ? new Date(grave.birth_date).toLocaleDateString() : 'Not available';
                const deathDate = grave.death_date ? new Date(grave.death_date).toLocaleDateString() : 'Not available';
                
                graveInfoBox.innerHTML = `
                    <div class="grave-info">
                        <h3>${grave.name}</h3>
                        <div class="grave-details">
                            <p><strong>Birth Date:</strong> ${birthDate}</p>
                            <p><strong>Death Date:</strong> ${deathDate}</p>
                        </div>
                    </div>
                `;
                graveInfoBox.style.display = 'block';

                // Automatically show route without button
                showRoute(grave.longitude, grave.latitude, grave.name);
            } else {
                graveInfoBox.innerHTML = `
                    <div class="grave-info">
                        <p class="no-results">No records found for this grave.</p>
                    </div>
                `;
                graveInfoBox.style.display = 'block';
            }
        } catch (error) {
            console.error('Search error:', error);
            graveInfoBox.innerHTML = `
                <div class="grave-info">
                    <p class="error">Error searching for grave.</p>
                </div>
            `;
            graveInfoBox.style.display = 'block';
        }
    });
});

// Route display function
window.showRoute = function(lng, lat, name) {
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

            try {
                const query = await fetch(
                    `https://api.mapbox.com/directions/v5/mapbox/walking/${userLocation[0]},${userLocation[1]};${graveLocation[0]},${graveLocation[1]}?geometries=geojson&access_token=${mapboxgl.accessToken}`
                );
                const json = await query.json();
                const data = json.routes[0];

                // Remove existing route
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