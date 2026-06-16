<html class="light" lang="es">

<head>

    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Centro Fútbol Gol - Academia de Alto Rendimiento</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&amp;display=swap" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0d7ff2",
                        "on-primary": "#ffffff",
                        "primary-container": "#d1e4ff",
                        "on-primary-container": "#001d36",
                        "secondary": "#535f70",
                        "on-secondary": "#ffffff",
                        "secondary-container": "#d7e3f7",
                        "on-secondary-container": "#101c2b",
                        "tertiary": "#6b5778",
                        "on-tertiary": "#ffffff",
                        "tertiary-container": "#f2daff",
                        "on-tertiary-container": "#251431",
                        "error": "#ba1a1a",
                        "on-error": "#ffffff",
                        "background": "#fdfcff",
                        "on-background": "#1a1c1e",
                        "surface": "#fdfcff",
                        "on-surface": "#1a1c1e",
                        "surface-variant": "#dfe3eb",
                        "on-surface-variant": "#43474e",
                        "outline": "#73777f",
                        "brand-pale": "#f0f9ff"

                        
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.5rem",
                        "xl": "1rem",
                        "2xl": "1.5rem",
                        "3xl": "2rem",
                        "full": "9999px"
                        
                    },
                    fontFamily: {
                        "headline": ["Lexend"],
                        "body": ["Lexend"],
                        "label": ["Lexend"]
                    }
                },
            },
        }
    </script>


    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .carousel-track {
            transition: transform 0.5s ease-in-out;
        }

        .kinetic-gradient {
            background: linear-gradient(135deg, #0d7ff2 0%, #0056b3 100%);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }



        

        h1,
        h2,
        h3,
        .font-lexend {
            font-family: 'Lexend', sans-serif;
        }



        /* Carousel animation */
        @keyframes carousel-fade {

            0%,
            20% {
                opacity: 1;
            }

            25%,
            95% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .carousel-item {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .carousel-item.active {
            opacity: 1;
        }

        #map-modal.hidden {
            display: none;
        }

        body {
            min-height: max(884px, 100dvh);
        }
    </style>

    <script>

        const preservedScrollKey = 'cart-form-scroll-position';
        const currentPage = `${window.location.pathname}${window.location.search}`;
        let preservedScrollPosition = null;

        try {
            const storedPosition = JSON.parse(sessionStorage.getItem(preservedScrollKey));

            if (storedPosition?.page === currentPage) {
                preservedScrollPosition = storedPosition;
                history.scrollRestoration = 'manual';
            }

            sessionStorage.removeItem(preservedScrollKey);
        } catch {
            // The cart still works when browser storage is unavailable.
        }

        window.openMap = (venueName, address) => {
            const query = encodeURIComponent(address);
            const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${query}`;
            const opened = window.open(googleMapsUrl, '_blank', 'noopener,noreferrer');

            if (!opened) {
                window.location.href = googleMapsUrl;
            }
        };


        document.addEventListener('DOMContentLoaded', () => {

            document.querySelectorAll('form[data-preserve-scroll]').forEach((form) => {
                form.addEventListener('submit', () => {
                    try {
                        sessionStorage.setItem(preservedScrollKey, JSON.stringify({
                            page: currentPage,
                            left: window.scrollX,
                            top: window.scrollY,
                        }));
                    } catch {
                        // Submitting the form must not depend on browser storage.
                    }
                });
            });

            if (preservedScrollPosition) {
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        window.scrollTo({
                            left: preservedScrollPosition.left,
                            top: preservedScrollPosition.top,
                            behavior: 'auto',
                        });
                    });
                });
            }

            document.querySelectorAll('[data-auto-dismiss]').forEach((message) => {
                const duration = Number(message.dataset.autoDismiss) || 5000;

                window.setTimeout(() => {
                    message.style.transition = 'opacity 300ms ease';
                    message.style.opacity = '0';

                    window.setTimeout(() => message.remove(), 300);
                }, duration);
            });
            // Gallery & Carousel Logic
            const modal = document.getElementById('lightbox-modal');
            const modalImg = document.getElementById('lightbox-img');
            const closeBtn = document.getElementById('close-lightbox');
            const prevLightbox = document.getElementById('prev-lightbox');
            const nextLightbox = document.getElementById('next-lightbox');

            const track = document.querySelector('.carousel-track');
            const slides = track ? Array.from(track.children) : [];          
            const nextButton = document.querySelector('.carousel-button-right');
            const prevButton = document.querySelector('.carousel-button-left');
            const dotsNav = document.querySelector('.carousel-nav');
            const dots = dotsNav ? Array.from(dotsNav.children) : [];
            

            let currentSlideIndex = 0;
            let autoPlayTimer;

            const updateCarousel = (targetIndex) => {
                if (!track || slides.length === 0) {
                    return;
                }
                track.style.transform = `translateX(-${targetIndex * 100}%)`;
                if (dots[currentSlideIndex]) {
                    dots[currentSlideIndex].classList.remove('bg-primary');
                    dots[currentSlideIndex].classList.add('bg-slate-300');
                }
                if (dots[targetIndex]) {
                    dots[targetIndex].classList.add('bg-primary');
                    dots[targetIndex].classList.remove('bg-slate-300');
                }
                currentSlideIndex = targetIndex;
            };

            const moveToNextSlide = () => {
                  if (slides.length <= 1) {
                    return;
                }
                const targetIndex = (currentSlideIndex + 1) % slides.length;
                updateCarousel(targetIndex);
            };

            const moveToPrevSlide = () => {
                 if (slides.length <= 1) {
                    return;
                }
                const targetIndex = (currentSlideIndex - 1 + slides.length) % slides.length;
                updateCarousel(targetIndex);
            };

            if (nextButton) {
                nextButton.addEventListener('click', () => {
                    moveToNextSlide();
                    resetAutoPlay();
                });
            }

            if (prevButton) {
                prevButton.addEventListener('click', () => {
                    moveToPrevSlide();
                    resetAutoPlay();
                });
            }




            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    updateCarousel(index);
                    resetAutoPlay();
                });
            });

          
            const startAutoPlay = () => {
                autoPlayTimer = setInterval(moveToNextSlide, 5000);
            };

            const resetAutoPlay = () => {
                clearInterval(autoPlayTimer);
                if (slides.length > 1) {
                    startAutoPlay();
                }
            };



            if (track && slides.length > 1) {
                startAutoPlay();
            }         

            

            // Lightbox Logic
            const openModal = (index) => {
                if (!modal || !modalImg || slides.length === 0) {
                    return;
                }
                currentSlideIndex = index;
                const img = slides[index].querySelector('img');
                modalImg.src = img.src;
                modalImg.alt = img.alt || 'Enlarged view';
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    modal.classList.remove('opacity-0');
                }, 10);
                document.body.style.overflow = 'hidden';
            };

            const closeModal = () => {
                if (!modal || !modalImg) {
                    return;
                }
                modal.classList.add('opacity-0');
                modal.classList.remove('opacity-100');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modalImg.src = '';
                }, 300);
                document.body.style.overflow = '';
            };

            const navigateLightbox = (direction) => {
                if (!modalImg || slides.length === 0) {
                    return;
                }
                currentSlideIndex = (currentSlideIndex + direction + slides.length) % slides.length;
                const img = slides[currentSlideIndex].querySelector('img');
                modalImg.src = img.src;
            };


            slides.forEach((slide, index) => {
                const img = slide.querySelector('img');
                if (img) {
                    img.addEventListener('click', () => openModal(index));
                }
            });


            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (prevLightbox) {
                prevLightbox.addEventListener('click', (e) => {
                    e.stopPropagation();
                    navigateLightbox(-1);
                });
            }
            if (nextLightbox) {
                nextLightbox.addEventListener('click', (e) => {
                    e.stopPropagation();
                    navigateLightbox(1);
                });
            }

            {{-- if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal();
                });
            }            --}}




           

            // News Modal Logic
            const newsModal = document.getElementById('news-modal');
            const newsCloseBtn = document.getElementById('close-news');
            const newsLinks = document.querySelectorAll('.news-read-more');

            const openNewsModal = (newsData) => {
                document.getElementById('news-modal-img').src = newsData.image;
                document.getElementById('news-modal-tag').textContent = newsData.tag;
                document.getElementById('news-modal-title').textContent = newsData.title;
                document.getElementById('news-modal-content').innerHTML = newsData.fullText;

                newsModal.classList.remove('hidden');
                setTimeout(() => {
                    newsModal.classList.add('opacity-100');
                    newsModal.querySelector('.modal-container').classList.remove('scale-95');
                }, 10);
                document.body.style.overflow = 'hidden';
            };

            const closeNewsModal = () => {
                newsModal.classList.remove('opacity-100');
                newsModal.querySelector('.modal-container').classList.add('scale-95');
                setTimeout(() => {
                    newsModal.classList.add('hidden');
                }, 300);
                document.body.style.overflow = '';
            };

            newsLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const card = link.closest('.news-card-root');
                    const newsData = {
                        tag: card.querySelector('.news-tag').textContent,
                        title: card.querySelector('.news-title').textContent,
                        image: card.querySelector('.news-img').src,
                        fullText: (card.dataset.fullText || '').replace(/\n/g, '<br>')
                    };
                    openNewsModal(newsData);
                });
            });

            newsCloseBtn.addEventListener('click', closeNewsModal);
            newsModal.addEventListener('click', (e) => {
                if (e.target === newsModal) closeNewsModal();
            });

            // Methodology Modal Logic
            const methodologyModal = document.getElementById('methodology-modal');
            const btnMetodologia = document.getElementById('btn-metodologia');
            const closeMethodology = document.getElementById('close-methodology');
            const methodologyContainer = methodologyModal.querySelector('.modal-container');

            const openMethodology = () => {
                methodologyModal.classList.remove('hidden');
                setTimeout(() => {
                    methodologyModal.classList.add('opacity-100');
                    methodologyContainer.classList.remove('scale-95');
                }, 10);
                document.body.style.overflow = 'hidden';
            };

            const closeMethodologyModal = () => {
                methodologyModal.classList.remove('opacity-100');
                methodologyContainer.classList.add('scale-95');
                setTimeout(() => {
                    methodologyModal.classList.add('hidden');
                }, 300);
                document.body.style.overflow = '';
            };

            if (btnMetodologia) btnMetodologia.addEventListener('click', openMethodology);
            if (closeMethodology) closeMethodology.addEventListener('click', closeMethodologyModal);

            methodologyModal.addEventListener('click', (e) => {
                if (e.target === methodologyModal) closeMethodologyModal();
            });

            // Teams Modal Logic
            const teamsModal = document.getElementById('teams-modal');
            const closeTeamsBtn = document.getElementById('close-teams');
            const teamsCards = document.querySelectorAll('.team-card');

            const teamCategories = {
                'Edefi': ['Categoría 2012', 'Categoría 2013', 'Categoría 2014', 'Categoría 2015', 'Categoría 2016', 'Categoría 2017'],
                'Bafi': ['Sexta División', 'Séptima División', 'Octava División', 'Novena División', 'Primera División'],
                'Futsala': ['Categoría 2008', 'Categoría 2009', 'Categoría 2010', 'Categoría 2011', 'Senior +35'],
                'Futsal Femenino': ['Sub-14', 'Sub-16', 'Sub-18', 'Primera División', 'Veteranas']
            };

            const openTeamsModal = (teamName) => {
                const modalTitle = document.getElementById('teams-modal-title');
                const grid = document.getElementById('teams-categories-grid');

                modalTitle.textContent = `Categorías ${teamName}`;
                grid.innerHTML = '';

                const categories = teamCategories[teamName] || ['Iniciación', 'Pre-competitivo', 'Competitivo'];

                categories.forEach(cat => {
                    const div = document.createElement('div');
                    div.className = 'bg-brand-pale p-6 rounded-2xl border-l-4 border-primary shadow-sm hover:shadow-md transition-all';
                    div.innerHTML = `<span class="font-black text-on-surface block">${cat}</span><p class="text-sm text-on-surface-variant mt-1">Días y horarios a coordinar según cupos disponibles.</p>`;
                    grid.appendChild(div);
                });

                teamsModal.classList.remove('hidden');
                setTimeout(() => {
                    teamsModal.classList.add('opacity-100');
                    teamsModal.querySelector('.modal-container').classList.remove('scale-95');
                }, 10);
                document.body.style.overflow = 'hidden';
            };

            const closeTeamsModal = () => {
                teamsModal.classList.remove('opacity-100');
                teamsModal.querySelector('.modal-container').classList.add('scale-95');
                setTimeout(() => {
                    teamsModal.classList.add('hidden');
                }, 300);
                document.body.style.overflow = '';
            };

            teamsCards.forEach(card => {
                card.addEventListener('click', (e) => {
                    e.preventDefault();
                    const teamName = card.querySelector('h4').textContent.trim();
                    openTeamsModal(teamName);
                });
            });

            if (closeTeamsBtn) closeTeamsBtn.addEventListener('click', closeTeamsModal);

            teamsModal.addEventListener('click', (e) => {
                if (e.target === teamsModal) closeTeamsModal();
            });




             // Map Modal Logic
            const mapModal = document.getElementById('map-modal');
            const closeMapBtn = document.getElementById('close-map');
            const mapTitle = document.getElementById('map-modal-title');
            const mapAddress = document.getElementById('map-modal-address');
            const mapFrame = document.getElementById('map-frame');

            window.openMap = (venueName, address) => {
                if (!mapModal || !mapFrame || !mapTitle || !mapAddress) return;

                mapTitle.textContent = venueName;
                mapAddress.textContent = address;
                mapFrame.src = `https://www.google.com/maps?q=${encodeURIComponent(address)}&output=embed`;

                mapModal.classList.remove('hidden');
                setTimeout(() => {
                    mapModal.classList.add('opacity-100');
                    mapModal.querySelector('.modal-container').classList.remove('scale-95');
                }, 10);
                document.body.style.overflow = 'hidden';
            };

            const closeMapModal = () => {
                if (!mapModal || !mapFrame) return;

                mapModal.classList.remove('opacity-100');
                mapModal.querySelector('.modal-container').classList.add('scale-95');
                setTimeout(() => {
                    mapModal.classList.add('hidden');
                    mapFrame.src = '';
                }, 300);
                document.body.style.overflow = '';
            };

            if (closeMapBtn) closeMapBtn.addEventListener('click', closeMapModal);
            if (mapModal) {
                mapModal.addEventListener('click', (e) => {
                    if (e.target === mapModal) closeMapModal();
                });
            }




            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (!modal.classList.contains('hidden')) closeModal();
                    if (!newsModal.classList.contains('hidden')) closeNewsModal();
                    if (!methodologyModal.classList.contains('hidden')) closeMethodologyModal();
                    if (!teamsModal.classList.contains('hidden')) closeTeamsModal();
                    if (mapModal && !mapModal.classList.contains('hidden')) closeMapModal();
                }
                if (!modal.classList.contains('hidden')) {
                    if (e.key === 'ArrowLeft') navigateLightbox(-1);
                    if (e.key === 'ArrowRight') navigateLightbox(1);
                }
            });

            
        });
    </script>
</head>


<body class="bg-surface text-on-surface font-body antialiased">
    <!-- Lightbox Modal -->
    <div class="fixed inset-0 z-[100] bg-black/95 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
        id="lightbox-modal">
        <button class="absolute top-6 right-6 text-white hover:text-primary transition-colors z-50" id="close-lightbox">
            <span class="material-symbols-outlined text-4xl">close</span>
        </button>
        <button class="absolute left-6 top-1/2 -translate-y-1/2 text-white hover:text-primary transition-colors"
            id="prev-lightbox">
            <span class="material-symbols-outlined text-5xl">chevron_left</span>
        </button>
        <button class="absolute right-6 top-1/2 -translate-y-1/2 text-white hover:text-primary transition-colors"
            id="next-lightbox">
            <span class="material-symbols-outlined text-5xl">chevron_right</span>
        </button>
        <img alt="" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl" id="lightbox-img" src="" />
    </div>
    <!-- News Modal -->
    <div class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
        id="news-modal">
        <div
            class="modal-container bg-surface w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-3xl shadow-2xl relative transform scale-95 transition-transform duration-300">
            <button
                class="absolute top-4 right-4 z-10 bg-white/80 hover:bg-white rounded-full p-2 text-slate-900 shadow-md transition-all"
                id="close-news">
                <span class="material-symbols-outlined">close</span>
            </button>
            <img alt="" class="w-full h-64 object-cover" id="news-modal-img" src="" />
            <div class="p-8 md:p-12">
                <span class="text-xs font-bold text-primary tracking-widest uppercase mb-2 block"
                    id="news-modal-tag"></span>
                <h3 class="text-3xl md:text-4xl font-black text-on-surface mb-6 leading-tight" id="news-modal-title">
                </h3>
                <div class="text-on-surface-variant text-lg leading-relaxed space-y-4" id="news-modal-content"></div>
                <div
                    class="mt-10 pt-8 border-t border-surface-variant flex flex-wrap justify-between items-center gap-4">
                    <button
                        class="px-8 py-3 bg-primary text-on-primary font-bold rounded-xl shadow-lg hover:shadow-primary/20 transition-all">Contactar
                        por esta noticia</button>
                    <div class="flex gap-4">
                        <span
                            class="material-symbols-outlined text-outline cursor-pointer hover:text-primary">share</span>
                        <span
                            class="material-symbols-outlined text-outline cursor-pointer hover:text-primary">bookmark</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Methodology Modal -->
    <div class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
        id="methodology-modal">
        <div
            class="modal-container bg-surface w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[2.5rem] shadow-2xl relative transform scale-95 transition-transform duration-300 flex flex-col">
            <button
                class="absolute top-6 right-6 z-20 bg-white/80 hover:bg-white rounded-full p-2 text-slate-900 shadow-md transition-all"
                id="close-methodology">
                <span class="material-symbols-outlined">close</span>
            </button>
            <div class="p-8 md:p-12 overflow-y-auto">
                <div class="mb-10 text-center">
                    <h3 class="text-4xl md:text-5xl font-black text-on-surface leading-tight">Nuestra Metodología</h3>
                    <div class="h-1.5 w-24 bg-primary mx-auto rounded-full mt-4"></div>
                </div>
                <div class="max-w-3xl mx-auto space-y-6 text-on-surface-variant text-lg leading-relaxed">
                    <p>
                        En <strong>Centro Fútbol Gol</strong>, nuestra metodología se fundamenta en un <strong>enfoque
                            formativo integral</strong> que trasciende los límites del campo de juego. Entendemos que
                        para formar a las promesas del mañana, debemos trabajar no solo en sus habilidades físicas, sino
                        también en su carácter, disciplina y comprensión intelectual del deporte.
                    </p>
                    <p>
                        Nuestro programa se divide en tres pilares fundamentales:
                    </p>
                    <ul class="space-y-4">
                        <li class="bg-brand-pale p-6 rounded-2xl border-l-4 border-primary">
                            <span class="font-black text-on-surface block mb-1">Desarrollo Integral:</span>
                            Fomentamos valores de respeto, trabajo en equipo y resiliencia, preparando a nuestros
                            jóvenes para los desafíos dentro y fuera de la cancha.
                        </li>
                        <li class="bg-brand-pale p-6 rounded-2xl border-l-4 border-primary">
                            <span class="font-black text-on-surface block mb-1">Técnica Individual:</span>
                            Utilizamos ejercicios de alta repetición y situaciones de juego real para perfeccionar el
                            control, el regate y la precisión, asegurando que cada jugador domine los fundamentos
                            técnicos básicos.
                        </li>
                        <li class="bg-brand-pale p-6 rounded-2xl border-l-4 border-primary">
                            <span class="font-black text-on-surface block mb-1">Táctica Colectiva:</span>
                            A través del análisis de juego y ejercicios posicionales, enseñamos a los deportistas a
                            interpretar el juego, ocupar espacios de manera inteligente y tomar decisiones rápidas bajo
                            presión.
                        </li>
                    </ul>
                    <p>
                        Gracias a la incorporación de tecnología de vanguardia y un cuerpo técnico altamente calificado,
                        garantizamos un proceso de aprendizaje evolutivo adaptado a cada etapa del crecimiento
                        deportivo.
                    </p>
                </div>
                <div class="mt-10 flex justify-center">
                    <button
                        class="px-10 py-4 bg-primary text-on-primary font-bold rounded-xl shadow-lg hover:shadow-primary/20 transition-all hover:scale-105"
                        onclick="document.getElementById('close-methodology').click()">Entendido</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Teams Modal -->
    <div class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
        id="teams-modal">
        <div
            class="modal-container bg-surface w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[2.5rem] shadow-2xl relative transform scale-95 transition-transform duration-300 flex flex-col">
            <button
                class="absolute top-6 right-6 z-20 bg-white/80 hover:bg-white rounded-full p-2 text-slate-900 shadow-md transition-all"
                id="close-teams">
                <span class="material-symbols-outlined">close</span>
            </button>
            <div class="p-8 md:p-12 overflow-y-auto no-scrollbar">
                <div class="mb-10 text-center">
                    <h3 class="text-4xl md:text-5xl font-black text-on-surface leading-tight" id="teams-modal-title">
                    </h3>
                    <div class="h-1.5 w-24 bg-primary mx-auto rounded-full mt-4"></div>
                </div>
                <div class="max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-2 gap-4" id="teams-categories-grid">
                    <!-- Categories will be injected here -->
                </div>
                <div class="mt-12 flex flex-col items-center gap-6">
                    <p class="text-on-surface-variant text-center max-w-xl">Nuestras categorías están diseñadas para
                        brindar una formación específica acorde a la edad y el nivel competitivo del deportista.</p>
                    <button
                        class="px-10 py-4 bg-primary text-on-primary font-bold rounded-xl shadow-lg hover:shadow-primary/20 transition-all hover:scale-105"
                        onclick="document.getElementById('close-teams').click()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Modal -->
    <div class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
        id="map-modal">
        <div
            class="modal-container bg-surface w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[2.5rem] shadow-2xl relative transform scale-95 transition-transform duration-300 flex flex-col">
            <button
                class="absolute top-6 right-6 z-20 bg-white/80 hover:bg-white rounded-full p-2 text-slate-900 shadow-md transition-all"
                id="close-map">
                <span class="material-symbols-outlined">close</span>
            </button>
            <div class="p-8 md:p-10 flex flex-col gap-6 overflow-y-auto no-scrollbar">
                <div class="pr-10">
                    <h3 class="text-3xl md:text-4xl font-black text-on-surface leading-tight" id="map-modal-title">Cómo llegar</h3>
                    <p class="text-on-surface-variant mt-2" id="map-modal-address"></p>
                </div>
                <div class="w-full h-[420px] rounded-2xl overflow-hidden border border-surface-variant/40">
                    <iframe class="w-full h-full" id="map-frame" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        title="Mapa de la sede"></iframe>
                </div>
            </div>
        </div>
    </div>


    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 bg-surface/95 backdrop-blur-md border-b border-surface-variant shadow-sm">
        <div class="flex justify-between items-center px-6 py-4 max-w-screen-2xl mx-auto">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/cfg.jpg') }}" alt="Escudo de Centro Fútbol Gol"
                    class="h-10 w-auto object-contain" />
                <span class="text-xl font-black text-primary uppercase tracking-tighter">Centro Fútbol Gol</span>
            </div>
            <div class="hidden lg:flex items-center gap-8">
                <a class="text-on-surface-variant hover:text-primary transition-colors font-bold text-sm tracking-tight uppercase"
                    href="{{ route('index') }}#historia">Historia</a>
                <a class="text-on-surface-variant hover:text-primary transition-colors font-bold text-sm tracking-tight uppercase"
                    href="{{ route('index') }}#equipos">Equipos</a>
                <a class="text-on-surface-variant hover:text-primary transition-colors font-bold text-sm tracking-tight uppercase"
                    href="{{ route('index') }}#staff">Staff</a>                
                <a class="text-on-surface-variant hover:text-primary transition-colors font-bold text-sm tracking-tight uppercase"
                    href="{{ route('index') }}#noticias">Noticias</a>
                <a class="text-on-surface-variant hover:text-primary transition-colors font-bold text-sm tracking-tight uppercase"
                    href="{{ route('index') }}#inscripcion">Inscripción</a>
                <a class="text-on-surface-variant hover:text-primary transition-colors font-bold text-sm tracking-tight uppercase"
                    href="{{ route('index') }}#sedes">Sedes</a>
                <a class="inline-flex items-center rounded-full bg-primary px-4 py-2 text-on-primary shadow-lg shadow-primary/20 hover:bg-blue-700 transition-colors font-black text-sm tracking-tight uppercase"
                    href="{{ route('fees.public.index') }}">Pagar cuota</a>
            </div>
            <div class="flex items-center gap-4">
                
                    
                <button class="lg:hidden text-on-surface">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>
        </div>
    </nav>


    {{ $slot }}

    
    <!-- Footer -->
    <footer class="bg-slate-50 border-t border-slate-200">
        <div class="flex flex-col md:flex-row justify-between items-start gap-12 px-8 py-16 max-w-7xl mx-auto">
            <div class="max-w-xs">
                <span class="text-lg font-bold text-blue-700 block mb-6">Centro Fútbol Gol</span>
                <p class="text-slate-500 text-sm leading-relaxed mb-8">Elevando el fútbol formativo con excelencia
                    profesional y valores integrales para las nuevas generaciones.</p>
                <div class="flex gap-4">
                    <a class="w-10 h-10 bg-white shadow-sm rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all"
                        href="#" aria-label="Instagram">@svg('fab-instagram', 'w-4 h-4')</a>
                    <a class="w-10 h-10 bg-white shadow-sm rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all"
                        href="#" aria-label="WhatsApp">@svg('fab-whatsapp', 'w-4 h-4')</a>
                    <a class="w-10 h-10 bg-white shadow-sm rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all"
                        href="#" aria-label="Facebook">@svg('fab-facebook-f', 'w-4 h-4')</a>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-12">
                <div>
                    <h5 class="text-on-surface font-black text-sm uppercase tracking-widest mb-6">Navegación</h5>
                    <ul class="space-y-4">
                         <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="{{ route('index') }}">Inicio</a></li>

                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="{{ route('index') }}#historia">Historia</a></li>
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="{{ route('index') }}#equipos">Equipos</a></li>
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                               href="{{ route('index') }}#staff">Staff</a></li>
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="{{ route('index') }}#calendario">Calendario</a></li>
                    </ul>
                </div>
               

                <div class="col-span-2 md:col-span-1">
                    <h5 class="text-on-surface font-black text-sm uppercase tracking-widest mb-6">Contáctanos</h5>
                    <p class="text-slate-500 text-sm mb-2">info@centrofutbolgol.com</p>
                    <p class="text-slate-500 text-sm mb-4">+54 11 4456-7890</p>
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <p class="text-xs font-bold text-blue-700">Abierto para inscripciones</p>
                        <p class="text-[10px] text-blue-600">Ciclo 2026</p>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="border-t border-slate-200 px-8 py-8 flex flex-col md:flex-row justify-between items-center gap-4 max-w-7xl mx-auto">
            <p class="font-lex text-xs md:text-sm text-slate-500">© 2024 Centro Fútbol Gol. Todos los derechos
                reservados.</p>
            {{-- <div class="flex gap-6">
                <span class="text-blue-600 font-semibold text-xs">Fútbol Base</span>
                <span class="text-blue-600 font-semibold text-xs">Alto Rendimiento</span>
                <span class="text-blue-600 font-semibold text-xs">Valores</span>
            </div> --}}
        </div>
    </footer>
</body>

</html>