<?php
session_start();
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Buscar</title>
    <link rel="shortcut icon" href="../img/tremlogo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <!-- Bootstrap Icons para ícones opcionais -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- CSS mínimo para cores de fundo, hovers e filtros (essencial para fidelidade) -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            min-height: 100vh;
            padding-bottom: 70px;
        }
        .card-hover {
            background-color: #1e1e1e !important;
            transition: background-color 0.3s ease;
        }
        .card-hover:hover {
            background-color: #dc3545 !important;
            color: #fff !important;
        }
        .card-hover:hover .text-muted {
            color: #f8f9fa !important;
        }
        .card-hover:hover a {
            color: #fff !important;
            text-decoration: underline;
        }
        .searchbar {
            background-color: #1e1e1e;
            border-radius: 0.5rem;
        }
        .search-icon {
            width: 24px;
            height: 24px;
            filter: brightness(0) invert(1);
            transition: filter 0.3s ease;
            cursor: pointer;
        }
        .search-icon:hover {
            filter: brightness(0) invert(0.7) sepia(1) saturate(5) hue-rotate(-10deg);
        }
        .pfp-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }
        .footer-icon img {
            width: 28px;
            height: 28px;
            filter: brightness(0) invert(1);
            transition: filter 0.3s ease;
        }
        .footer-icon:hover img,
        .footer-icon.active img {
            filter: brightness(0) invert(1) drop-shadow(0 0 15px rgba(255, 193, 7, 0.8)) sepia(1) saturate(5) hue-rotate(-10deg);
        }
        .rodape {
            background-color: #121212;
            border: none;
            box-shadow: none;
            z-index: 1000;
        }
        #map {
            height: 500px;
            background-color: #1e1e1e;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }
        .search-input {
            background-color: #1e1e1e;
            border: 1px solid #333;
            color: #e0e0e0;
            border-radius: 0.5rem;
        }
        .search-input:focus {
            background-color: #1e1e1e;
            border-color: #dc3545;
            color: #e0e0e0;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .search-input::placeholder {
            color: #888;
        }
        @media (max-width: 768px) {
            #map {
                height: 400px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="bg-transparent">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent px-3 py-2">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-oi">
                        <h1 class="text-light fw-bold mb-0 fs-3">Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
                    </div>
                    <div class="pfp">
                        <img src="../assets/img/perfil.png" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
        <!-- Searchbar com input para pesquisa -->
        <div class="searchbar d-flex justify-content-between align-items-center mx-3 mb-3 p-3">
            <div class="flex-grow-1 me-3">
                <input type="text" id="searchInput" class="form-control search-input fs-5" placeholder="Digite o destino (ex: Estação da Luz, São Paulo)" />
            </div>
            <div class="img-bar">
                <img src="../assets/img/lupa.png" alt="Ícone de lupa para busca" class="search-icon" id="searchBtn" />
            </div>
        </div>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">
        <!-- Título da seção de busca -->
        <div class="busca-titulo mb-3">
            <h3 class="text-danger fw-bold fs-4">Encontre estações e rotas</h3>
        </div>
        <!-- Mapa Google Maps -->
        <div id="map"></div>
    </main>

    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" style="max-width: 900px; margin: 0 auto; left: 50%; transform: translateX(-50%); z-index: 1000;" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="d-flex justify-content-around align-items-center">
            <a href="home.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Início">
                <img src="../assets/img/casa.png" alt="Início" />
            </a>
            <a href="buscar.php" class="footer-icon active text-center text-decoration-none p-2" aria-label="Buscar">
                <img src="../assets/img/lupa.png" alt="Buscar" />
            </a>
            <a href="chat.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Chat">
                <img src="../assets/img/chat.png" alt="Chat" />
            </a>
            <a href="perfil.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Perfil">
                <img src="../assets/img/perfil.png" alt="Perfil" />
            </a>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Google Maps JavaScript API (substitua 'SUA_CHAVE_API' pela sua chave real do Google Maps) -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=SUA_CHAVE_API&libraries=places&callback=initMap"></script>
    
    <script>
        let map;
        let geocoder;
        let marker;

        function initMap() {
            // Centralizar no centro de São Paulo como padrão
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: { lat: -23.5505, lng: -46.6333 }, // São Paulo, SP
                styles: [
                    {
                        featureType: 'all',
                        elementType: 'geometry',
                        stylers: [{ color: '#1e1e1e' }]
                    },
                    {
                        featureType: 'all',
                        elementType: 'labels.text.fill',
                        stylers: [{ color: '#e0e0e0' }]
                    },
                    {
                        featureType: 'all',
                        elementType: 'labels.text.stroke',
                        stylers: [{ color: '#121212' }]
                    },
                    {
                        featureType: 'poi',
                        elementType: 'geometry',
                        stylers: [{ color: '#2a2a2a' }]
                    },
                    {
                        featureType: 'road',
                        elementType: 'geometry',
                        stylers: [{ color: '#333333' }]
                    },
                    {
                        featureType: 'water',
                        elementType: 'geometry',
                        stylers: [{ color: '#1e1e1e' }]
                    }
                ]
            });

            geocoder = new google.maps.Geocoder();

            // Event listener para o botão de busca
            document.getElementById('searchBtn').addEventListener('click', performSearch);
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        }

        function performSearch() {
            const input = document.getElementById('searchInput').value;
            if (!input) return;

            geocoder.geocode({ address: input + ', São Paulo, SP' }, function(results, status) {
                if (status === 'OK') {
                    map.setCenter(results[0].geometry.location);
                    map.setZoom(15);

                    // Remove marker anterior
                    if (marker) {
                        marker.setMap(null);
                    }

                    // Adiciona novo marker
                    marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location,
                        title: input
                    });

                    // Info window opcional
                    const infoWindow = new google.maps.InfoWindow({
                        content: `<div style="color: #e0e0e0;"><strong>${input}</strong></div>`
                    });
                    marker.addListener('click', () => {
                        infoWindow.open(map, marker);
                    });
                } else {
                    alert('Geocodificação falhou: ' + status);
                }
            });
        }
    </script>
</body>

</html>
