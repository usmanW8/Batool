// =========================================
// Batool's Aptitude - Main JavaScript
// =========================================

// Initialize Icons
lucide.createIcons();

// Image Rescue Script: Automatically replaces broken images
document.addEventListener('DOMContentLoaded', () => {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.onerror = function () {
            console.warn('Image failed to load:', this.src);
            this.src = 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=1000&auto=format&fit=crop'; // Elegant fallback
            this.alt = 'Image unavailable';
        };
    });
});

// Navigation Logic
function navigateTo(pageId) {
    document.querySelectorAll('.page-section').forEach(section => {
        section.classList.remove('active');
    });

    const target = document.getElementById(pageId);
    if (target) {
        target.classList.add('active');
        window.scrollTo(0, 0);
    }

    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });

    if (pageId === 'home') {
        document.querySelector('button[onclick="navigateTo(\'home\')"]')?.classList.add('active');
    }

    document.getElementById('mobile-menu').classList.add('hidden');

    // Update navbar underline indicator
    updateNavIndicator(pageId);
}

// Helper function to update navbar indicator
function updateNavIndicator(pageId) {
    const navIndicator = document.getElementById('nav-indicator');
    if (!navIndicator) return;

    // Map business, digital, culture to professional
    let targetNav = pageId;
    if (['business', 'digital', 'culture'].includes(pageId)) {
        targetNav = 'professional';
    }

    const activeNav = document.querySelector(`[data-nav="${targetNav}"]`);
    if (activeNav) {
        const rect = activeNav.getBoundingClientRect();
        const navRect = document.getElementById('desktop-nav')?.getBoundingClientRect();

        if (navRect) {
            navIndicator.style.width = rect.width + 'px';
            navIndicator.style.left = (rect.left - navRect.left) + 'px';
            navIndicator.style.opacity = '1';
        }
    }
}

// Dark Mode Logic
const themeToggleBtn = document.getElementById('theme-toggle');
const themeToggleMobile = document.getElementById('theme-toggle-mobile');

function toggleTheme() {
    document.documentElement.classList.toggle('dark');
    setTimeout(() => { lucide.createIcons(); }, 100);
}

themeToggleBtn.addEventListener('click', toggleTheme);
themeToggleMobile.addEventListener('click', toggleTheme);

// Mobile Menu Toggle
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}

// Lightbox Logic
function openLightbox(src, title) {
    const lightbox = document.getElementById('lightbox');
    const img = document.getElementById('lightbox-img');
    const caption = document.getElementById('lightbox-caption');
    img.src = src;
    img.alt = title;
    caption.innerText = title;
    lightbox.classList.add('show');
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.remove('show');
}

// Typewriter Effect
function typeWriter(element, text, speed = 80) {
    let i = 0;
    element.textContent = '';
    element.classList.remove('typing-complete');

    function type() {
        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            setTimeout(type, speed);
        } else {
            // Remove cursor after typing is complete
            setTimeout(() => {
                element.classList.add('typing-complete');
            }, 500);
        }
    }
    type();
}

// Set Home as active initially
document.addEventListener('DOMContentLoaded', () => {
    navigateTo('home');

    // Start typewriter effect after a short delay
    setTimeout(() => {
        const typewriterElement = document.getElementById('typewriter-text');
        if (typewriterElement) {
            // Get text from data attribute set by PHP
            const text = typewriterElement.getAttribute('data-text') || '"Blending handmade art, digital creativity, and cultural expression."';
            typeWriter(typewriterElement, text, 50);
        }
    }, 1000);

    // =========================================
    // Navbar Animated Underline
    // =========================================
    const navIndicator = document.getElementById('nav-indicator');
    const navItems = document.querySelectorAll('.nav-item-wrapper[data-nav]');

    if (navIndicator && navItems.length > 0) {
        // Function to position the indicator
        function positionIndicator(wrapper) {
            if (!wrapper) return;

            const navContainer = document.getElementById('desktop-nav');
            if (!navContainer) return;

            // Get the button inside the wrapper for accurate width
            const button = wrapper.querySelector('button');
            if (!button) return;

            // Use offsetLeft of wrapper and offsetWidth of button
            const leftPosition = wrapper.offsetLeft;
            const width = button.offsetWidth;

            navIndicator.style.width = width + 'px';
            navIndicator.style.left = leftPosition + 'px';
            navIndicator.style.opacity = '1';
        }

        // Position on active item initially (with slight delay to ensure layout is ready)
        setTimeout(() => {
            const activeNav = document.querySelector('.nav-item-wrapper[data-nav="home"]');
            if (activeNav) positionIndicator(activeNav);
        }, 200);

        // Add hover listeners
        navItems.forEach(wrapper => {
            wrapper.addEventListener('mouseenter', () => {
                positionIndicator(wrapper);
            });
        });

        // Reset to active item on mouse leave
        const navContainer = document.getElementById('desktop-nav');
        if (navContainer) {
            navContainer.addEventListener('mouseleave', () => {
                const activePage = document.querySelector('.page-section.active')?.id;
                let targetNav = activePage;

                // Map business, digital, culture to professional
                if (['business', 'digital', 'culture'].includes(activePage)) {
                    targetNav = 'professional';
                }

                const activeNav = document.querySelector(`.nav-item-wrapper[data-nav="${targetNav}"]`);
                if (activeNav) {
                    positionIndicator(activeNav);
                } else {
                    // If no match, use home
                    const homeNav = document.querySelector('.nav-item-wrapper[data-nav="home"]');
                    if (homeNav) positionIndicator(homeNav);
                }
            });
        }
    }
});


// =========================================
// AI Image Generator Functions
// =========================================

let currentImageUrl = '';
let currentPrompt = '';

// Handle form submission
function handleImageGeneration(event) {
    event.preventDefault();
    const input = document.getElementById('ai-prompt-input');
    const prompt = input.value.trim();

    if (!prompt) {
        alert('Please enter a description for your artwork');
        return;
    }

    currentPrompt = prompt;
    generateImage(prompt);
}

// Generate image using Pollinations AI API
async function generateImage(prompt) {
    // Open modal and show loading state
    openImageModal();
    showLoading();

    try {
        // Encode the prompt for URL
        const encodedPrompt = encodeURIComponent(prompt);

        // Construct API URL - New Pollinations AI endpoint with flux model
        const apiUrl = `https://gen.pollinations.ai/image/${encodedPrompt}?model=flux`;

        // Fetch the image with authentication
        const response = await fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer sk_jrxpLXzEeufybC8NRhc2GleviO5GQGR9'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to generate image');
        }

        // Convert response to blob
        const blob = await response.blob();

        // Create object URL from blob
        const imageUrl = URL.createObjectURL(blob);

        // Store the URL for download
        currentImageUrl = imageUrl;

        // Display the image
        displayImage(imageUrl);
        lucide.createIcons();

    } catch (error) {
        console.error('Image generation error:', error);
        showError('Failed to generate image. Please try again with a different prompt.');
        lucide.createIcons();
    }
}

// Download image in specified format
async function downloadImage(format) {
    if (!currentImageUrl) {
        alert('No image to download');
        return;
    }

    try {
        // Fetch the image
        const response = await fetch(currentImageUrl);
        const blob = await response.blob();

        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `ai-artwork-${Date.now()}.${format}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Download failed:', error);
        alert('Download failed. Please try right-clicking the image and selecting "Save image as..."');
    }
}

// Generate more alternative images with same prompt
function generateMoreImages() {
    if (!currentPrompt) {
        alert('No prompt available');
        return;
    }

    // Re-generate with the same prompt
    hideImage();
    hideError();
    generateImage(currentPrompt);
}

// Open full image in new tab
function openFullImage() {
    if (!currentImageUrl) {
        alert('No image available');
        return;
    }

    // Open image in new tab
    window.open(currentImageUrl, '_blank');
}


// Open image modal
function openImageModal() {
    const modal = document.getElementById('ai-image-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';

    // Update prompt display
    document.getElementById('ai-prompt-display').textContent = `"${currentPrompt}"`;
}

// Close image modal
function closeImageModal(event) {
    // If event is passed and target is not the backdrop, don't close
    if (event && event.target.id !== 'ai-image-modal') {
        return;
    }

    const modal = document.getElementById('ai-image-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';

    // Reset states
    hideLoading();
    hideImage();
    hideError();

    // Clear input
    document.getElementById('ai-prompt-input').value = '';
}

// Show loading state
function showLoading() {
    document.getElementById('ai-loading').classList.remove('hidden');
    document.getElementById('ai-image-container').classList.add('hidden');
    document.getElementById('ai-error').classList.add('hidden');
}

// Hide loading state
function hideLoading() {
    document.getElementById('ai-loading').classList.add('hidden');
}

// Display generated image
function displayImage(imageUrl) {
    hideLoading();
    document.getElementById('ai-generated-image').src = imageUrl;
    document.getElementById('ai-image-container').classList.remove('hidden');
    document.getElementById('ai-error').classList.add('hidden');
}

// Hide image
function hideImage() {
    document.getElementById('ai-image-container').classList.add('hidden');
}

// Show error state
function showError(message) {
    hideLoading();
    document.getElementById('ai-error-message').textContent = message;
    document.getElementById('ai-error').classList.remove('hidden');
    document.getElementById('ai-image-container').classList.add('hidden');
}

// Hide error state
function hideError() {
    document.getElementById('ai-error').classList.add('hidden');
}

// Close modal on Escape key
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});
