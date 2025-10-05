 // Inicializar o mapa quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            try {
                console.log('Inicializando mapa Leaflet...'); // Debug

                // Criar mapa centralizado em São Paulo
                const map = L.map('map').setView([-23.5505, -46.6333], 11); // Lat/Lng de SP, zoom para ver a cidade

                // Tiles claro (CartoDB Positron - tema claro, clean e funcional)
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd',
                    maxZoom: 19
                }).addTo(map);

                let currentMarker; // Para remover marker anterior

                // Função de busca custom (usa Nominatim via fetch - gratuita)
                function performSearch() {
                    const input = document.getElementById('searchInput').value.trim();
                    if (!input) return;

                    // Mostrar loading
                    document.getElementById('searchInput').classList.add('loading');
                    document.getElementById('loadingSpinner').style.display = 'block';

                    // URL Nominatim (foco em SP, Brasil)
                    const query = encodeURIComponent(input + ', São Paulo, SP');
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&limit=1&countrycodes=br&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                const result = data[0];
                                const lat = parseFloat(result.lat);
                                const lng = parseFloat(result.lon);
                                const placeName = result.display_name;

                                // Centralizar mapa com animação
                                map.flyTo([lat, lng], 15);

                                // Remover marker anterior
                                if (currentMarker) {
                                    map.removeLayer(currentMarker);
                                }

                                // Adicionar novo marker com popup
                                currentMarker = L.marker([lat, lng]).addTo(map)
                                    .bindPopup(`<b>${input}</b><br>${placeName}`)
                                    .openPopup();
                            } else {
                                alert('Local não encontrado. Tente outro termo (ex: Estação da Luz).');
                            }
                        })
                        .catch(error => {
                            console.error('Erro na busca:', error);
                            alert('Erro na busca. Verifique a conexão.');
                        })
                        .finally(() => {
                            // Esconder loading
                            document.getElementById('searchInput').classList.remove('loading');
                            document.getElementById('loadingSpinner').style.display = 'none';
                        });
                }

                // Event listeners
                document.getElementById('searchBtn').addEventListener('click', performSearch);
                document.getElementById('searchInput').addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        performSearch();
                    }
                });

                console.log('Mapa Leaflet carregado com sucesso! Teste a busca.'); // Debug

            } catch (error) {
                console.error('Erro ao inicializar mapa:', error);
                document.getElementById('map').style.display = 'none';
                document.getElementById('mapError').style.display = 'block';
            }
        });