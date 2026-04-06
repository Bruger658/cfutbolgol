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
            theme: {
                extend: {
                    colors: {
                        'brand-blue': '#0d7ff2',
                        'brand-pale': '#f0f9ff',
                    }
                }
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            // Gallery & Carousel Logic
            const modal = document.getElementById('lightbox-modal');
            const modalImg = document.getElementById('lightbox-img');
            const closeBtn = document.getElementById('close-lightbox');
            const prevLightbox = document.getElementById('prev-lightbox');
            const nextLightbox = document.getElementById('next-lightbox');

            const track = document.querySelector('.carousel-track');
            const slides = Array.from(track.children);
            const nextButton = document.querySelector('.carousel-button-right');
            const prevButton = document.querySelector('.carousel-button-left');
            const dotsNav = document.querySelector('.carousel-nav');
            const dots = Array.from(dotsNav.children);

            let currentSlideIndex = 0;
            let autoPlayTimer;

            const updateCarousel = (targetIndex) => {
                track.style.transform = `translateX(-${targetIndex * 100}%)`;
                dots[currentSlideIndex].classList.remove('bg-brand-blue');
                dots[currentSlideIndex].classList.add('bg-slate-300');
                dots[targetIndex].classList.add('bg-brand-blue');
                dots[targetIndex].classList.remove('bg-slate-300');
                currentSlideIndex = targetIndex;
            };

            const moveToNextSlide = () => {
                const targetIndex = (currentSlideIndex + 1) % slides.length;
                updateCarousel(targetIndex);
            };

            const moveToPrevSlide = () => {
                const targetIndex = (currentSlideIndex - 1 + slides.length) % slides.length;
                updateCarousel(targetIndex);
            };

            nextButton.addEventListener('click', () => {
                moveToNextSlide();
                resetAutoPlay();
            });

            prevButton.addEventListener('click', () => {
                moveToPrevSlide();
                resetAutoPlay();
            });

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
                startAutoPlay();
            };

            startAutoPlay();

            // Lightbox Logic
            const openModal = (index) => {
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
                modal.classList.add('opacity-0');
                modal.classList.remove('opacity-100');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modalImg.src = '';
                }, 300);
                document.body.style.overflow = '';
            };

            const navigateLightbox = (direction) => {
                currentSlideIndex = (currentSlideIndex + direction + slides.length) % slides.length;
                const img = slides[currentSlideIndex].querySelector('img');
                modalImg.src = img.src;
            };

            slides.forEach((slide, index) => {
                slide.querySelector('img').addEventListener('click', () => openModal(index));
            });

            closeBtn.addEventListener('click', closeModal);
            prevLightbox.addEventListener('click', (e) => { e.stopPropagation(); navigateLightbox(-1); });
            nextLightbox.addEventListener('click', (e) => { e.stopPropagation(); navigateLightbox(1); });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

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
                        fullText: `<p class="mb-4">Este es el artículo completo sobre <strong>${card.querySelector('.news-title').textContent}</strong>. En Centro Fútbol Gol estamos comprometidos con el desarrollo integral de nuestros deportistas.</p><p class="mb-4">Nuestra nueva iniciativa busca fortalecer los pilares fundamentales de la formación deportiva, integrando procesos de vanguardia que permiten a cada niño y joven alcanzar su máximo potencial. Contamos con un equipo interdisciplinario que supervisa cada etapa del crecimiento futbolístico.</p><p>A través de estas actualizaciones, reafirmamos nuestro liderazgo como la academia referente en la región, brindando oportunidades únicas de proyección nacional e internacional para nuestros talentos más destacados.</p>`
                    };
                    openNewsModal(newsData);
                });
            });

            newsCloseBtn.addEventListener('click', closeNewsModal);
            newsModal.addEventListener('click', (e) => {
                if (e.target === newsModal) closeNewsModal();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (!modal.classList.contains('hidden')) closeModal();
                    if (!newsModal.classList.contains('hidden')) closeNewsModal();
                }
                if (!modal.classList.contains('hidden')) {
                    if (e.key === 'ArrowLeft') navigateLightbox(-1);
                    if (e.key === 'ArrowRight') navigateLightbox(1);
                }
            });
        });
    </script>
    <style>
        body {
            font-family: 'Lexend', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .carousel-track {
            transition: transform 0.5s ease-in-out;
        }
    </style>
</head>


<body class="bg-white text-slate-900 antialiased">
    <!-- Lightbox Modal -->
    <div class="fixed inset-0 z-[100] bg-black/95 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
        id="lightbox-modal">
        <button class="absolute top-6 right-6 text-white hover:text-brand-blue transition-colors z-50"
            id="close-lightbox">
            <span class="material-symbols-outlined text-4xl">close</span>
        </button>
        <button class="absolute left-6 top-1/2 -translate-y-1/2 text-white hover:text-brand-blue transition-colors"
            id="prev-lightbox">
            <span class="material-symbols-outlined text-5xl">chevron_left</span>
        </button>
        <button class="absolute right-6 top-1/2 -translate-y-1/2 text-white hover:text-brand-blue transition-colors"
            id="next-lightbox">
            <span class="material-symbols-outlined text-5xl">chevron_right</span>
        </button>
        <img alt="" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl" id="lightbox-img" src="" />
    </div>
    <!-- News Modal -->
    <div class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4"
        id="news-modal">
        <div
            class="modal-container bg-white w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-[2rem] shadow-2xl relative transform scale-95 transition-transform duration-300">
            <button
                class="absolute top-4 right-4 z-10 bg-white/80 hover:bg-white rounded-full p-2 text-slate-900 shadow-md transition-all"
                id="close-news">
                <span class="material-symbols-outlined">close</span>
            </button>
            <img alt="" class="w-full h-64 object-cover" id="news-modal-img" src="" />
            <div class="p-8 md:p-12">
                <span class="text-xs font-bold text-brand-blue tracking-widest uppercase mb-2 block"
                    id="news-modal-tag"></span>
                <h3 class="text-3xl md:text-4xl font-black text-slate-900 mb-6 leading-tight" id="news-modal-title">
                </h3>
                <div class="text-slate-600 text-lg leading-relaxed space-y-4" id="news-modal-content"></div>
                <div class="mt-10 pt-8 border-t border-slate-100 flex justify-between items-center">
                    <button
                        class="px-8 py-3 bg-brand-blue text-white font-bold rounded-xl shadow-lg hover:shadow-brand-blue/20 transition-all">Contactar
                        por esta noticia</button>
                    <div class="flex gap-4">
                        <span
                            class="material-symbols-outlined text-slate-400 cursor-pointer hover:text-brand-blue">share</span>
                        <span
                            class="material-symbols-outlined text-slate-400 cursor-pointer hover:text-brand-blue">bookmark</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm">
        <div class="flex justify-between items-center px-6 md:px-12 py-4 max-w-screen-2xl mx-auto">
            <div class="flex items-center gap-3">
                <img alt="Centro Fútbol Gol Logo" class="w-auto object-contain h-12"
                    src="images/cfg.png"
                    style="background: transparent; border: none;" />
                <span class="text-xl font-black text-[#0d7ff2] uppercase tracking-tighter hidden sm:block">Centro Fútbol
                    Gol</span>
            </div>
            <div class="hidden lg:flex items-center gap-8">
                <a class="text-slate-600 hover:text-brand-blue transition-colors font-medium text-sm tracking-tight"
                    href="#historia">Historia</a>
                <a class="text-slate-600 hover:text-brand-blue transition-colors font-medium text-sm tracking-tight"
                    href="#equipos">Equipos</a>
                <a class="text-slate-600 hover:text-brand-blue transition-colors font-medium text-sm tracking-tight"
                    href="#calendario">Calendario</a>
                <a class="text-slate-600 hover:text-brand-blue transition-colors font-medium text-sm tracking-tight"
                    href="#noticias">Noticias</a>
                <a class="text-slate-600 hover:text-brand-blue transition-colors font-medium text-sm tracking-tight"
                    href="#inscripcion">Inscripción</a>
                <a class="text-slate-600 hover:text-brand-blue transition-colors font-medium text-sm tracking-tight"
                    href="#sedes">Sedes</a>
            </div>
            <button
                class="bg-[#0d7ff2] text-white px-6 py-2.5 rounded-lg font-bold text-sm hover:bg-blue-600 transition-all active:scale-95">
                CFG
            </button>
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
                        href="#"><span class="material-symbols-outlined text-sm"
                            style="font-variation-settings: 'FILL' 1;">social_leaderboard</span></a>
                    <a class="w-10 h-10 bg-white shadow-sm rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all"
                        href="#"><span class="material-symbols-outlined text-sm"
                            style="font-variation-settings: 'FILL' 1;">play_circle</span></a>
                    <a class="w-10 h-10 bg-white shadow-sm rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all"
                        href="#"><span class="material-symbols-outlined text-sm"
                            style="font-variation-settings: 'FILL' 1;">share</span></a>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-12">
                <div>
                    <h5 class="text-on-surface font-black text-sm uppercase tracking-widest mb-6">Navegación</h5>
                    <ul class="space-y-4">
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="#">Inicio</a></li>
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="#">Historia</a></li>
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="#">Equipos</a></li>
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="#">Calendario</a></li>
                    </ul>
                </div>

                <!-- <div>
                    <h5 class="text-on-surface font-black text-sm uppercase tracking-widest mb-6">Ayuda</h5>
                    <ul class="space-y-4">
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="#">Privacidad</a></li>
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="#">Términos</a></li>
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="#">Contacto</a></li>
                        <li><a class="text-slate-500 hover:text-blue-700 underline-offset-4 hover:underline text-sm transition-all"
                                href="#">Soporte</a></li>
                    </ul>
                </div> -->

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
            <div class="flex gap-6">
                <span class="text-blue-600 font-semibold text-xs">Fútbol Base</span>
                <span class="text-blue-600 font-semibold text-xs">Alto Rendimiento</span>
                <span class="text-blue-600 font-semibold text-xs">Valores</span>
            </div>
        </div>
    </footer>
</body>

</html>