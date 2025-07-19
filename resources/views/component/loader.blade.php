<div id="preloader"></div>
<style>
    :root {
        --background-color: #fff;
        /* fallback jika variabel tidak ada */
        --accent-color: #0EA2BC;
        /* fallback jika variabel tidak ada */
    }

    #preloader {
        position: fixed;
        inset: 0;
        z-index: 9999;
        overflow: hidden;
        background-color: var(--background-color, #fff);
        transition: all 0.6s ease-out;
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #preloader:before,
    #preloader:after {
        content: "";
        position: absolute;
        border: 4px solid var(--accent-color, #0EA2BC);
        border-radius: 50%;
        animation: animate-preloader 2s cubic-bezier(0, 0.2, 0.8, 1) infinite;
        pointer-events: none;
    }

    #preloader:after {
        animation-delay: -0.5s;
    }

    @keyframes animate-preloader {
        0% {
            width: 10px;
            height: 10px;
            top: calc(50% - 5px);
            left: calc(50% - 5px);
            opacity: 1;
        }

        100% {
            width: 72px;
            height: 72px;
            top: calc(50% - 36px);
            left: calc(50% - 36px);
            opacity: 0;
        }
    }
</style>
<script>
    // Pastikan loader tampil saat halaman mulai dimuat, lalu hilang setelah selesai
    document.addEventListener("DOMContentLoaded", function () {
        // Loader akan tetap tampil sampai window load
        window.addEventListener('load', function () {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 600); // sesuai dengan transition
            }
        });
    });
</script>