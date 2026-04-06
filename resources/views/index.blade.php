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
    <main class="pt-20">
        <!-- Hero Section -->
        <section class="relative h-[800px] min-h-[600px] flex items-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img class="w-full h-full object-cover" data-alt="Professional football stadium aerial view at sunset"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCoKTJZooUn_XY4iX3aHXUV5GFR8TDuSrAwxeCYVRM01XcYFJftHR_qjIwguyzAVqHLoxGgEHSWbFRWR2mYCF9ryfpx_bYZmX5Hhzyp3zNHjElhhBXxBLsvPLNRtYonq2dJ7aiAb0DfEuQBD4volMkvwbD_JBxwlWiOSPDZeyuy3a1h7rP1ZCKaNSZ5FcfoQ4_Ti8YnSnATHqdQSZ5LkRVdQrCHo5OhR4DamNNKzNj-pzq0b6AD8myD4BctpmJrtUrPLMjzTDEtSqI" />
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/40 to-transparent"></div>
            </div>
            <div class="relative z-10 max-w-7xl mx-auto px-6 md:px-12 w-full">
                <div class="max-w-2xl">
                    <span
                        class="inline-block px-4 py-1 bg-brand-blue/20 text-brand-blue border border-brand-blue/30 rounded-full text-xs font-bold tracking-widest uppercase mb-6">Excelencia
                        Deportiva</span>
                    <h1 class="text-5xl md:text-7xl font-black text-white leading-tight mb-6">Formamos las Promesas del
                        Mañana</h1>
                    <p class="text-xl text-slate-200 mb-10 leading-relaxed">Únete a la academia líder en formación
                        técnica y táctica. Entrenamientos de alto rendimiento para niños y jóvenes en el corazón del
                        fútbol.</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- <button
                            class="px-8 py-4 bg-brand-blue text-white font-bold rounded-xl shadow-xl shadow-brand-blue/20 hover:translate-y-[-2px] transition-all">Empieza
                            tu Camino</button> -->
                        <button
                            class="px-8 py-4 bg-brand-blue text-white font-bold rounded-xl shadow-xl shadow-brand-blue/20 hover:translate-y-[-2px] transition-all">Ver
                            Metodología</button>
                    </div>
                </div>
            </div>
        </section>
        <!-- Our History Section (Fondo Blanco) -->
        <section class="py-24 bg-white" id="historia">
            <div class="max-w-7xl mx-auto px-6 md:px-12">
                <div class="grid md:grid-cols-2 gap-16 items-center">
                    <div class="relative">
                        <div class="aspect-[4/5] rounded-3xl overflow-hidden shadow-2xl">
                            <img class="w-full h-full object-cover"
                                data-alt="Historic black and white photo of youth soccer training"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAKwzWPDqkhNcghKy3dKRNq9dL1Iby4htjcEABg859KzhOLBSyn2TxU_AbJDodc-iFy39tGHQQcqMWQlnI4c5mesrGNv09zaWesqowKsw-D5M0EHwIrgrDM7vrCxn57P6BE7m5QP4iWnRK9zruKtNreXK8iz-VcNXaTtDtIBf77V7hFgmFK1Mq858kLCId2OcTfL_QKfBB8XOH_KNXNv0hatCFanmKso82EKQ3udkU4mVf9Z5vP1niHHWwuG_G87-9IAprwIBDompw" />
                        </div>
                        <div
                            class="absolute -bottom-6 -right-6 bg-brand-blue p-8 rounded-2xl shadow-xl hidden lg:block">
                            <p class="text-4xl font-black text-white">5+</p>
                            <p class="text-white/80 font-medium">Años de Tradición</p>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-brand-blue font-bold tracking-widest uppercase text-sm mb-4">Nuestra Trayectoria
                        </h2>
                        <h3 class="text-4xl font-black text-slate-900 mb-8">Más que un Club, una Familia con Historia
                        </h3>
                        <div class="space-y-6 text-slate-600 text-lg leading-relaxed">
                            <p>Fundado con la visión de profesionalizar el talento local, el Centro Fútbol Gol ha sido
                                el semillero de grandes atletas que hoy brillan en ligas internacionales.</p>
                            <p>Nuestra historia se escribe cada día en el césped, con valores de disciplina, respeto y
                                pasión. Hemos evolucionado desde un pequeño grupo de entrenamiento hasta convertirnos en
                                una institución referente con tecnología deportiva de vanguardia.</p>
                        </div>
                        <div class="mt-10 grid grid-cols-2 gap-6">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-brand-blue"
                                    style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                                <span class="font-bold text-slate-900">5 Títulos</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-brand-blue"
                                    style="font-variation-settings: 'FILL' 1;">groups</span>
                                <span class="font-bold text-slate-900">200+ Alumnos Activos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Categories Section (Azul Tenue) -->
        <section class="py-24 bg-brand-pale" id="equipos">
            <div class="max-w-7xl mx-auto px-6 md:px-12">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-black text-slate-900">Nuestros Equipos</h2>
                    <div class="h-1.5 w-24 bg-brand-blue mx-auto mt-4 rounded-full"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="group cursor-pointer">
                        <div class="relative aspect-square rounded-2xl overflow-hidden mb-4 shadow-md">
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                data-alt="Kids soccer team playing under 8"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCsYKPUZ_B6NP0tp6mcayaDp4vhxKDos5_aocuRfp_52wRpfz0mnzEHEXmQtIy94ZpdGTuUJAPyvXGHBGhaIa-YjKVpbl5fRdrwsFqvBUY-WIKStW1Zq4xlJSkesGzyGLBxkVrS1-QTXF6skPD783g2K269njEGRwVjjLyVo5KExTA5SpltCpIXY6TNFUfj7TvHfSZ9Xilyu74jwRKc9ey0wFN447Nw2N-dnj2zQXk01V3bxBNiMf0sd_Y0gxTAEu26GPQlrd5lzGU" />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-brand-blue/80 to-transparent flex items-end p-6">
                                <h4 class="text-white font-bold text-xl tracking-tight">Edefi</h4>
                            </div>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div class="relative aspect-square rounded-2xl overflow-hidden mb-4 shadow-md">
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                data-alt="Youth football match under 12"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnYD4jGWZAP83qkVrPHYOp7MKhcTV5nhovzPYdIVSqcPuTtDHWUbEXfS1YK58_Ijk8OHVhgsyLG3vX2aokR8ILX9lUWNudtuFS-SttF4vY8_57ykHCFSv-XD-z5AknFuq2tbhHwOU9SFMFnp_d_YRvg8dgUOk74tjQYAR-83PIM4uKMESro_ePYrJM-2bEGF2-xHWTPEtSUXUiqeJWi6CBcEtbXk7wK0j8gHtAhAART3FTQTmZM3TaJr7R_aE5gUXefy2I_esTu8Y" />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-brand-blue/80 to-transparent flex items-end p-6">
                                <h4 class="text-white font-bold text-xl tracking-tight">Bafi</h4>
                            </div>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div class="relative aspect-square rounded-2xl overflow-hidden mb-4 shadow-md">
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                data-alt="Teenage soccer training under 16"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDsmPvZ1HGuWflraI1_jLy94_KgUID09p9ntAUwH_U7E7f2YNBwHvmWNj9fmpbxei4micTee2ystcOWoHNDDNVgNo33janHplUAgQrM0bZ9K37crCFPI-4zEo9Ghv5zIQ9m6WSibKB3CPKoUm7EoI_tJBAOh3_6bchGRM6Wk2qDD6yLjk3fXkE1_cVsFdCPIkzTQhZC10sWbUUEe5YWYfz2-BDlLbIWixuPSBL08xSe5EL9goie3aqb0HfBTvSB3xdY050pprW36jY" />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-brand-blue/80 to-transparent flex items-end p-6">
                                <h4 class="text-white font-bold text-xl tracking-tight">Futsala</h4>
                            </div>
                        </div>
                    </div>
                    <div class="group cursor-pointer">
                        <div class="relative aspect-square rounded-2xl overflow-hidden mb-4 shadow-md">
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                data-alt="Elite football training under 20"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAvSxRjp0ZjUZid9qKuaxkddlBBMkMuIdcRoxO_ug-OUPKA5sx3KiKkJQEnGpX316ueZe36GMB5M5PoTurUZzqbbsENxTvrTT3jLMEzepZXjxZASgqvgkDE7ofpctuypVs1cPeCupeMmNUw2xps8yXbJFkjTSO5zxiVbc7vwHn90lvhL5u6iQsc1bpomYRAxP2rUlN8ti9tFjTQNeBmd8iXWJbj5VoHg6iEaTImMGJokP0A6BsurcnRmJtKyj5pQmhIcyEHR_ImX70" />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-brand-blue/80 to-transparent flex items-end p-6">
                                <h4 class="text-white font-bold text-xl tracking-tight">Futsal Femenino</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Match Calendar Section (Fondo Blanco con Acentos Azules) -->
        <section class="py-24 bg-white" id="calendario">
            <div class="max-w-7xl mx-auto px-6 md:px-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 border-l-4 border-brand-blue pl-4">Próximos
                            Encuentros</h2>
                        <p class="text-slate-600 mt-2">No te pierdas la acción de nuestros equipos</p>
                    </div>
                    
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Match Card 1 -->
                    <div
                        class="bg-brand-pale p-6 rounded-2xl shadow-sm border border-brand-blue/10 hover:border-brand-blue/30 transition-all">
                        <div class="flex justify-between items-center mb-6">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-white bg-brand-blue px-2 py-0.5 rounded">Edefi</span>
                            <span class="text-xs text-slate-500 font-medium">14 Oct</span>
                        </div>
                        <div class="flex flex-col items-center gap-4 mb-6">
                            <div class="flex items-center justify-between w-full">
                                <div class="text-center w-1/3">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full mx-auto mb-2 flex items-center justify-center font-bold text-brand-blue shadow-sm border border-brand-blue/10">
                                        CFG</div>
                                    <p class="text-xs font-bold truncate">CFG</p>
                                </div>
                                <span class="font-black text-brand-blue/30">VS</span>
                                <div class="text-center w-1/3">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full mx-auto mb-2 flex items-center justify-center font-bold text-slate-400 shadow-sm">
                                        LFC</div>
                                    <p class="text-xs font-bold truncate">Almafuerte</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2 border-t border-brand-blue/10 pt-4">
                            <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                                <span class="material-symbols-outlined text-[16px] text-brand-blue">schedule</span>
                                10:30 AM
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                                <span class="material-symbols-outlined text-[16px] text-brand-blue">location_on</span>
                                Sede Almafuerte
                            </div>
                        </div>
                    </div>
                    <!-- Match Card 2 -->
                    <div
                        class="bg-brand-pale p-6 rounded-2xl shadow-sm border border-brand-blue/10 hover:border-brand-blue/30 transition-all">
                        <div class="flex justify-between items-center mb-6">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-white bg-brand-blue px-2 py-0.5 rounded">Bafi</span>
                            <span class="text-xs text-slate-500 font-medium">15 Oct</span>
                        </div>
                        <div class="flex flex-col items-center gap-4 mb-6">
                            <div class="flex items-center justify-between w-full">
                                <div class="text-center w-1/3">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full mx-auto mb-2 flex items-center justify-center font-bold text-brand-blue shadow-sm border border-brand-blue/10">
                                        CFG</div>
                                    <p class="text-xs font-bold truncate">CFG</p>
                                </div>
                                <span class="font-black text-brand-blue/30">VS</span>
                                <div class="text-center w-1/3">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full mx-auto mb-2 flex items-center justify-center font-bold text-slate-400 shadow-sm">
                                        DN</div>
                                    <p class="text-xs font-bold truncate">Almafuerte</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2 border-t border-brand-blue/10 pt-4">
                            <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                                <span class="material-symbols-outlined text-[16px] text-brand-blue">schedule</span>
                                04:00 PM
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                                <span class="material-symbols-outlined text-[16px] text-brand-blue">location_on</span>
                                Sede Almafuerte
                            </div>
                        </div>
                    </div>
                    <!-- Match Card 3 -->
                    <div
                        class="bg-brand-pale p-6 rounded-2xl shadow-sm border border-brand-blue/10 hover:border-brand-blue/30 transition-all">
                        <div class="flex justify-between items-center mb-6">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-white bg-brand-blue px-2 py-0.5 rounded">Futsala</span>
                            <span class="text-xs text-slate-500 font-medium">16 Oct</span>
                        </div>
                        <div class="flex flex-col items-center gap-4 mb-6">
                            <div class="flex items-center justify-between w-full">
                                <div class="text-center w-1/3">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full mx-auto mb-2 flex items-center justify-center font-bold text-brand-blue shadow-sm border border-brand-blue/10">
                                        CFG</div>
                                    <p class="text-xs font-bold truncate">CFG</p>
                                </div>
                                <span class="font-black text-brand-blue/30">VS</span>
                                <div class="text-center w-1/3">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full mx-auto mb-2 flex items-center justify-center font-bold text-slate-400 shadow-sm">
                                        TIT</div>
                                    <p class="text-xs font-bold truncate">Almafuerte</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2 border-t border-brand-blue/10 pt-4">
                            <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                                <span class="material-symbols-outlined text-[16px] text-brand-blue">schedule</span>
                                07:15 PM
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                                <span class="material-symbols-outlined text-[16px] text-brand-blue">location_on</span>
                                Sede Almafuerte
                            </div>
                        </div>
                    </div>
                    <!-- Match Card 4 -->
                    <div
                        class="bg-brand-pale p-6 rounded-2xl shadow-sm border border-brand-blue/10 hover:border-brand-blue/30 transition-all">
                        <div class="flex justify-between items-center mb-6">
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-white bg-brand-blue px-2 py-0.5 rounded">Futsal Femenino</span>
                            <span class="text-xs text-slate-500 font-medium">17 Oct</span>
                        </div>
                        <div class="flex flex-col items-center gap-4 mb-6">
                            <div class="flex items-center justify-between w-full">
                                <div class="text-center w-1/3">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full mx-auto mb-2 flex items-center justify-center font-bold text-brand-blue shadow-sm border border-brand-blue/10">
                                        CFG</div>
                                    <p class="text-xs font-bold truncate">CFG</p>
                                </div>
                                <span class="font-black text-brand-blue/30">VS</span>
                                <div class="text-center w-1/3">
                                    <div
                                        class="w-12 h-12 bg-white rounded-full mx-auto mb-2 flex items-center justify-center font-bold text-slate-400 shadow-sm">
                                        AGL</div>
                                    <p class="text-xs font-bold truncate">Almafuerte</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2 border-t border-brand-blue/10 pt-4">
                            <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                                <span class="material-symbols-outlined text-[16px] text-brand-blue">schedule</span>
                                09:00 AM
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                                <span class="material-symbols-outlined text-[16px] text-brand-blue">location_on</span>
                                Sede Almafuerte
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Training Gallery (Carousel) -->
        <section class="py-24 bg-brand-pale overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 md:px-12 mb-12 text-center">
                <h2 class="text-3xl font-black text-slate-900">Galería</h2>
                <p class="text-slate-600">Nuestra metodología en acción</p>
            </div>
            <div class="relative max-w-4xl mx-auto px-12 group/carousel">
                <div class="overflow-hidden rounded-3xl shadow-2xl bg-white aspect-[16/9]">
                    <div class="carousel-track flex h-full">
                        <div class="carousel-slide min-w-full h-full">
                            <img class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-700"
                                data-alt="Close up of a soccer ball and cleats on grass"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDU9UkKgOSqgdkds1hzpQXrZVfWH1fDhtGwo7_H9sBmG6_2tTZwp5u7qR1szvtcLarnqTE1lwmj9EK6hDzc7OTMQGbJhxoqFO1IWJ60ypK7-rJ0p3gUgp5ReG7LQHo3An0sf4NX15CbC6leJmCFGJO81r62kf1W4rStnovIm6J8JBWdiRCi-laDKcYFJSpbRztZXcvzmGSFEC1-eAqmUqM-ss8-roA-0G3qz-n0ZOso1VMvaagMsOwsQg_VMJTL2LvAtiSOBlszCZg" />
                        </div>
                        <div class="carousel-slide min-w-full h-full">
                            <img class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-700"
                                data-alt="Coach explaining tactics to young players"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuC98y-BkYOf4Q1sUJwfeDialphQzK5rTtzrjbH3go0zdCikmtUcZrEo7HbcilqGfkAPMMqOpMxzPusSE047g_fpc46KxRqPSQw6enlvWPQzaD5tzmXm9zVgNg2MQk87cx4yZcZ_hsYbTNpmo0nMOJ_21Q_DvsVscXH-AGMZKY1zjOXs_VfBZfg4PA8klykrWwFZHRwmxdgLZN1eiBWtK42Qbo7xQQ_HGUOaRab2AVKz5cN6S8gTaX2GwM0gwz_TbLTJBdEYxCEavP4" />
                        </div>
                        <div class="carousel-slide min-w-full h-full">
                            <img class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-700"
                                data-alt="Group of players celebrating a goal"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAIPxWhEdW_VfyNQU5jS7ZFwJ7BZpgZT-cW0quRjfduPwS0iO_jbXEf-M-goSRZsPl0M_Z7-Q3FeJ44b24AVrDG0ORpRiBMo8zkdxlu79UKB1UjZBYU0pqT4y_BNRfRbakdfN-hbp9S45OYpgj0LCQvqoXcK1ekDsOD6JysD7zplW-SWUz5fw9Occd3odoyI6e1gDuM6zJZ0ERH8ktU8v3JSFC2xCui3l4D6lpT08-OFVPXDb902scWi18uiV3-fvvuJevRi9Uji78" />
                        </div>
                        <div class="carousel-slide min-w-full h-full">
                            <img class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-700"
                                data-alt="Intense dribbling drill with cones"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDWEcAxIU4rRwqh7kLJ4mBhpUJcgEyYSb31zfZ-lEJTQH6lICQcrKzmtUMGCgA-O5mcAcuXpmizNZu3bFHur75Uy0jpoN1kD081InZD2ri18mHdKr4Dq-8jy2iaR3plNKUO5Bmi6GFBAPc6aSLgmSSUJ529FL0NpDfq3MiUMVkucpNSY7Q8YOw8KEf6XxMbO6QF0r-CAIEQfhDboYeIbjlT81v0vJFm1if-8e05ZZvf2WewKjxfbJ-x2UysXr_f8g-Bp7c9rWfumrM" />
                        </div>
                    </div>
                </div>
                <!-- Nav Buttons -->
                <button
                    class="carousel-button-left absolute left-0 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-brand-blue hover:text-white text-slate-900 w-12 h-12 rounded-full shadow-lg transition-all flex items-center justify-center border border-brand-blue/10">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <button
                    class="carousel-button-right absolute right-0 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-brand-blue hover:text-white text-slate-900 w-12 h-12 rounded-full shadow-lg transition-all flex items-center justify-center border border-brand-blue/10">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
                <!-- Nav Dots -->
                <div class="carousel-nav flex justify-center gap-3 mt-8">
                    <button aria-label="Slide 1" class="w-2.5 h-2.5 rounded-full bg-brand-blue transition-all"></button>
                    <button aria-label="Slide 2"
                        class="w-2.5 h-2.5 rounded-full bg-slate-300 transition-all hover:bg-brand-blue/50"></button>
                    <button aria-label="Slide 3"
                        class="w-2.5 h-2.5 rounded-full bg-slate-300 transition-all hover:bg-brand-blue/50"></button>
                    <button aria-label="Slide 4"
                        class="w-2.5 h-2.5 rounded-full bg-slate-300 transition-all hover:bg-brand-blue/50"></button>
                </div>
            </div>
        </section>
        <!-- Club News (Fondo Blanco) -->
        <section class="py-24 bg-white" id="noticias">
            <div class="max-w-7xl mx-auto px-6 md:px-12">
                <h2
                    class="text-3xl font-black text-slate-900 mb-12 text-center underline decoration-brand-blue decoration-4 underline-offset-8">
                    Últimas Noticias</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-center">
                    <!-- News Card 1 -->
                    <div
                        class="news-card-root bg-white rounded-3xl overflow-hidden flex flex-col shadow-lg hover:shadow-xl transition-all border border-brand-blue/10 group h-full">
                        <div class="aspect-video overflow-hidden">
                            <img class="news-img w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                data-alt="Corporate meeting in a modern sports facility"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCSRcWY_cuGEWkt31m15GoQrMvYQSJSJehJrXtPMdhh_fiCoPrWoV_DN_fl7trO7Ad2Q-ZC8g38OSype03Q14lSKaqWidt1yHGSuedopFORGJP3TXCkd8mb9h-PfR_PNLKeMRdy7S0nuetVPVi6SA_2bD34iXf_-hWqG5wC8mEpvHqRhXsWIjoyC0RjOgfA4L4_qneINRFqppypxSzc0TWQXKf0C55wxZAMmXBYNp4lfscPmGBwrpCsq8dnyz-aYbJ0rc6MaLBxpLs" />
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <span
                                class="news-tag text-xs font-bold text-brand-blue mb-2 uppercase tracking-wider">Institucional</span>
                            <h4 class="news-title text-xl font-black text-slate-900 mb-4 leading-tight">Nueva Alianza
                                con Scouting Europeo</h4>
                            <p class="text-slate-600 mb-6 line-clamp-3 text-sm">Firmamos un convenio estratégico para
                                proyectar a nuestros mejores talentos a clubes profesionales en Europa con becas
                                completas.</p>
                            <div class="mt-auto">
                                <a class="text-brand-blue font-bold flex items-center gap-2 hover:translate-x-2 transition-transform news-read-more text-sm"
                                    href="#">Leer más <span
                                        class="material-symbols-outlined text-[18px]">open_in_new</span></a>
                            </div>
                        </div>
                    </div>
                    <!-- News Card 2 -->
                    <div
                        class="news-card-root bg-white rounded-3xl overflow-hidden flex flex-col shadow-lg hover:shadow-xl transition-all border border-brand-blue/10 group h-full">
                        <div class="aspect-video overflow-hidden">
                            <img class="news-img w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                data-alt="Modern sports nutrition and performance tracking"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD-HaqivuJMb8v1-rP8Y5aRnJaVGZH4IptXoSIPZEDGAKGLJhd116wO6LLw9VJPol-8CiL_Q1obLF0LMRza_GuYoyvWs7pJdbaX42VZNFSBgbtzGo4nGNxNbG8eIUH6GoKxI7yRXcJIarsvYGhdPjZc5OpOar7u2O1W2mNEUnfL9cUiDwKlVaGe8rT3KtbgU6IpD2dfcxMxMZF15N0lrZz8WuanrQM9zdU4vyrGFCdNLSdBrC2GH7Zh4q7xeoVlhoKqc5WJRgluftk" />
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <span
                                class="news-tag text-xs font-bold text-brand-blue mb-2 uppercase tracking-wider">Entrenamiento</span>
                            <h4 class="news-title text-xl font-black text-slate-900 mb-4 leading-tight">Incorporamos
                                Tecnología de GPS</h4>
                            <p class="text-slate-600 mb-6 line-clamp-3 text-sm">Todas nuestras categorías competitivas
                                ahora cuentan con chalecos de monitoreo GPS para optimizar el rendimiento físico
                                individual.</p>
                            <div class="mt-auto">
                                <a class="text-brand-blue font-bold flex items-center gap-2 hover:translate-x-2 transition-transform news-read-more text-sm"
                                    href="#">Leer más <span
                                        class="material-symbols-outlined text-[18px]">open_in_new</span></a>
                            </div>
                        </div>
                    </div>
                    <!-- News Card 3 -->
                    <div
                        class="news-card-root bg-white rounded-3xl overflow-hidden flex flex-col shadow-lg hover:shadow-xl transition-all border border-brand-blue/10 group h-full">
                        <div class="aspect-video overflow-hidden">
                            <img class="news-img w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                data-alt="Soccer player conditioning and training"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDU9UkKgOSqgdkds1hzpQXrZVfWH1fDhtGwo7_H9sBmG6_2tTZwp5u7qR1szvtcLarnqTE1lwmj9EK6hDzc7OTMQGbJhxoqFO1IWJ60ypK7-rJ0p3gUgp5ReG7LQHo3An0sf4NX15CbC6leJmCFGJO81r62kf1W4rStnovIm6J8JBWdiRCi-laDKcYFJSpbRztZXcvzmGSFEC1-eAqmUqM-ss8-roA-0G3qz-n0ZOso1VMvaagMsOwsQg_VMJTL2LvAtiSOBlszCZg" />
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <span
                                class="news-tag text-xs font-bold text-brand-blue mb-2 uppercase tracking-wider">Competición</span>
                            <h4 class="news-title text-xl font-black text-slate-900 mb-4 leading-tight">Iniciamos Torneo
                                Nacional</h4>
                            <p class="text-slate-600 mb-6 line-clamp-3 text-sm">Nuestra categoría Sub-16 debuta este fin
                                de semana en el torneo más importante del país buscando revalidar el título obtenido.
                            </p>
                            <div class="mt-auto">
                                <a class="text-brand-blue font-bold flex items-center gap-2 hover:translate-x-2 transition-transform news-read-more text-sm"
                                    href="#">Leer más <span
                                        class="material-symbols-outlined text-[18px]">open_in_new</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Enrollment Form Section (Fondo Azul Tenue con Card Blanca) -->
        <section class="py-16 bg-brand-pale" id="inscripcion">
            <div class="max-w-7xl mx-auto px-6 md:px-12">
                <div
                    class="bg-white rounded-[2.5rem] overflow-hidden shadow-xl flex flex-col lg:flex-row min-h-[550px] border border-brand-blue/5">
                    <!-- Inspiring Image Column -->
                    <div class="lg:w-2/5 relative hidden lg:block overflow-hidden">
                        <img alt="Soccer player training on pitch" class="absolute inset-0 w-full h-full object-cover"
                            src="images/cfg.png" />
                        <div class="absolute inset-0 bg-brand-blue/40 mix-blend-multiply"></div>
                        <div class="absolute bottom-10 left-10 right-10 text-white z-10">
                            <p class="text-2xl font-black leading-tight">El primer paso para ser profesional comienza
                                aquí.</p>
                            <div class="h-1.5 w-12 bg-white mt-4 rounded-full"></div>
                        </div>
                    </div>
                    <!-- Form Column -->
                    <div class="lg:w-3/5 p-8 md:p-12 flex flex-col justify-center">
                        <div class="max-w-xl mx-auto w-full">
                            <h2 class="text-3xl font-black text-slate-900 mb-2">Inscripción Online</h2>
                            <p class="text-slate-500 mb-8">Completa el formulario y nuestro equipo técnico te contactará
                                para una prueba de nivel.</p>
                            <form action="#" class="space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label class="text-sm font-bold text-slate-700 ml-1">Nombre del Jugador</label>
                                        <input
                                            class="w-full px-5 py-3 rounded-xl border-slate-200 focus:ring-brand-blue focus:border-brand-blue transition-all"
                                            placeholder="Ej. Juan Pérez" type="text" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-sm font-bold text-slate-700 ml-1">Fecha de Nacimiento</label>
                                        <input
                                            class="w-full px-5 py-3 rounded-xl border-slate-200 focus:ring-brand-blue focus:border-brand-blue transition-all"
                                            type="date" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label class="text-sm font-bold text-slate-700 ml-1">Correo del
                                            Acudiente</label>
                                        <input
                                            class="w-full px-5 py-3 rounded-xl border-slate-200 focus:ring-brand-blue focus:border-brand-blue transition-all"
                                            placeholder="correo@ejemplo.com" type="email" />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-sm font-bold text-slate-700 ml-1">Teléfono de
                                            Contacto</label>
                                        <input
                                            class="w-full px-5 py-3 rounded-xl border-slate-200 focus:ring-brand-blue focus:border-brand-blue transition-all"
                                            placeholder="+57 300 000 0000" type="tel" />
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-sm font-bold text-slate-700 ml-1">Categoría de Interés</label>
                                    <select
                                        class="w-full px-5 py-3 rounded-xl border-slate-200 focus:ring-brand-blue focus:border-brand-blue transition-all">
                                        <option>Selecciona una opción</option>
                                        <option>Edefi</option>
                                        <option>Bafi</option>
                                        <option>Futsala</option>
                                        <option>Futsal Femenino</option>
                                    </select>
                                </div>
                                <button
                                    class="w-full py-4 bg-brand-blue text-white font-black rounded-xl shadow-xl shadow-brand-blue/20 hover:bg-blue-600 transition-all mt-4"
                                    type="submit">
                                    Enviar Solicitud
                                </button>
                                <p class="text-center text-xs text-slate-400 mt-4">
                                    Al enviar este formulario, aceptas nuestras políticas de tratamiento de datos
                                    personales.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>        
    </main>

    <!-- Locations (Sedes) Section -->
    <section class="py-24 bg-white" id="sedes">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-on-surface">Nuestras Sedes</h2>
                <p class="text-on-surface-variant mt-2">Instalaciones de primer nivel cerca de ti</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Sede Card 1 -->
                <div class="bg-surface-container-low rounded-3xl overflow-hidden group border border-outline-variant">
                    <div class="h-48 bg-slate-200 relative">
                        <img class="w-full h-full object-cover" data-alt="Aerial map placeholder of sports complex"
                            data-location="Sede Principal"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCAI7RP2RMA40x6368UojVg8zU4hpjkRet84cuYJ6X2y0zmKwjCcwBC2wfypYP5MJ9PIvmaBmbszwXYauRB4TMOdyrMtzMibxgBWFFEfHQyBhx7I8_JcmQurEdOqjmJbVr9PjjkUjdiJlnZDemt7rVLwig9-qAqNIjHLZRa1Oy6sfM_jVPUUJc1_mEdziWfhcUZjvEVIiQfC8Tn9l-L_NaaSRRAy6ZBw5ORNT4smrxXCPhTIEnZqhibwaLsCvzznPDJxKHQTN_685w" />
                    </div>
                    <div class="p-8">
                        <h4 class="text-xl font-bold mb-2">Sede Stylo(Principal)</h4>
                        <p class="text-on-surface-variant text-sm mb-6">Int Julian 2800. Incluye 2 canchas
                            sintéticas.</p>
                        <button
                            class="w-full py-3 border-2 border-primary text-primary font-bold rounded-xl group-hover:bg-primary group-hover:text-white transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">directions</span> Cómo llegar
                        </button>
                    </div>
                </div>
                <!-- Sede Card 2 -->
                <div class="bg-surface-container-low rounded-3xl overflow-hidden group border border-outline-variant">
                    <div class="h-48 bg-slate-200 relative">
                        <img class="w-full h-full object-cover" data-alt="Satellite map view of soccer fields"
                            data-location="Sede Sur"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuD6V0HoaEUoEsFdt4LNoxQptymiKtWpRw6vCzPlfDstneNltr1k7s1g5EYdHzcs9APuZzAvPTX3S0_937uDZtaSRA4WqV7QZpNy679xlmieei5_YDo9fxm2Dvmq_dtP_B29dB_Cu0BKnfQASXqGYeAjPksCm0aR71cDatLkX8yfdzxdOvv_FSclrkegOaMEta3zkJhQHAi_d4Q7to98QxLCp40kzbZjcZRszTwzgTxUpNygQWiikWmJK9e-x0UPqxoKh_YhUvKURaU" />
                    </div>
                    <div class="p-8">
                        <h4 class="text-xl font-bold mb-2">Sede Almafuerte</h4>
                        <p class="text-on-surface-variant text-sm mb-6">Maximo Paz 800. 2 canchas de cemento.</p>
                        
                        <button
                            class="w-full py-3 border-2 border-primary text-primary font-bold rounded-xl group-hover:bg-primary group-hover:text-white transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">directions</span> Cómo llegar
                        </button>
                    </div>
                </div>
                <!-- Sede Card 3 -->
                <div class="bg-surface-container-low rounded-3xl overflow-hidden group border border-outline-variant">
                    <div class="h-48 bg-slate-200 relative">
                        <img class="w-full h-full object-cover" data-alt="City map layout with markers"
                            data-location="Sede Oriente"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBhdyYFotBpmgzt9V84WDkyXqc4jAp1TuppgH0C6uy2qyl5jpUFUwDCP1SikQHfIwSQnliGACAZ_VAn09tgp0ZeiobBcyYlSh4epuzYi91WFP7FAAGwIoV4R_WS2RatRTh4Flbs7aIrMOn4X4lZWJC9HFTjOa7g4Hqdcm29-etw6TYkApHqFovj25HLbiOvpWii_GjXUK5ZrypOiLcISuJi5GI-WzD0Sw0rhTFAjujGpnT2-ckncceW7Lq3yEYj_oWNlEXJr5YqTpc" />
                    </div>
                    <div class="p-8">
                        <h4 class="text-xl font-bold mb-2">Sede Ituzaingo</h4>
                        <p class="text-on-surface-variant text-sm mb-6">Rivadavia 20000. 2 canchas de pasto sintetic</p>
                        <button
                            class="w-full py-3 border-2 border-primary text-primary font-bold rounded-xl group-hover:bg-primary group-hover:text-white transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">directions</span> Cómo llegar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
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