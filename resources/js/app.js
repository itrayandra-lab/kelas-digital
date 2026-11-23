import "./libs/trix";
import "./bootstrap";

// Alpine.js setup
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

// Splide.js setup
import Splide from "@splidejs/splide";

// Make Alpine available globally
window.Alpine = Alpine;

// Register plugins
Alpine.plugin(collapse);

// Start Alpine
Alpine.start();

// Initialize Splide sliders on page load
document.addEventListener("DOMContentLoaded", () => {
    // Hero slider
    const heroSlider = document.querySelector("#hero-slider");
    if (heroSlider) {
        new Splide(heroSlider, {
            type: "loop",
            perPage: 1,
            autoplay: true,
            interval: 7000, // long enough to read a slide's paragraph
            speed: 700,
            pauseOnHover: true,
            pauseOnFocus: true,
            arrows: true,
            pagination: true,
            keyboard: "focused",
            lazyLoad: "nearby",
            gap: 0,
            i18n: { prev: "Slide sebelumnya", next: "Slide berikutnya", slideX: "Ke slide %s", pageX: "Ke slide %s", carousel: "slider", slide: "slide", slideLabel: "%s dari %s" },
        }).mount();
    }
});

function uploadTrixAttachment(editor, attachment) {
    if (!attachment.file) {
        return;
    }

    const endpoint = editor.dataset.uploadEndpoint;

    if (!endpoint || !window.axios) {
        return;
    }

    const formData = new FormData();
    formData.append("attachment", attachment.file);

    window.axios
        .post(endpoint, formData, {
            onUploadProgress(progressEvent) {
                if (progressEvent.total) {
                    const progress = Math.round(
                        (progressEvent.loaded / progressEvent.total) * 100
                    );
                    attachment.setUploadProgress(progress);
                }
            },
        })
        .then(({ data }) => {
            if (data?.image_url) {
                attachment.setAttributes({
                    url: data.image_url,
                });
            } else {
                attachment.remove();
            }
        })
        .catch(() => {
            attachment.remove();
        });
}

document.addEventListener("trix-attachment-add", (event) => {
    uploadTrixAttachment(event.target, event.attachment);
});
