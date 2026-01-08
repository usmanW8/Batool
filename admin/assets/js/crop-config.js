// =========================================
// Image Crop Configuration
// Dimension settings for each image type
// =========================================

const CROP_CONFIGS = {
    'logo': {
        label: 'Logo',
        dimensions: 'Height: 96px (auto width for aspect ratio)',
        description: 'Navbar logo - maintains aspect ratio',
        aspectRatio: NaN, // Free aspect ratio
        minWidth: 200,
        minHeight: 96,
        viewMode: 1
    },
    'hero': {
        label: 'Hero Image',
        dimensions: 'Recommended: 1920×1080px (16:9 ratio)',
        description: 'Full-width hero section background',
        aspectRatio: 16 / 9,
        minWidth: 1920,
        minHeight: 1080,
        viewMode: 1
    },
    'portfolio': {
        label: 'Portfolio Item',
        dimensions: 'Recommended: 800×800px (1:1 square)',
        description: 'Portfolio gallery grid item',
        aspectRatio: 1,
        minWidth: 800,
        minHeight: 800,
        viewMode: 1
    },
    'featured': {
        label: 'Featured Portfolio',
        dimensions: 'Recommended: 1200×800px (3:2 ratio)',
        description: 'Homepage featured portfolio slider',
        aspectRatio: 3 / 2,
        minWidth: 1200,
        minHeight: 800,
        viewMode: 1
    },
    'section': {
        label: 'Section Image',
        dimensions: 'Recommended: 1200×600px (2:1 ratio)',
        description: 'Business/Digital/Culture section images',
        aspectRatio: 2 / 1,
        minWidth: 1200,
        minHeight: 600,
        viewMode: 1
    },
    'product': {
        label: 'Product Image',
        dimensions: 'Recommended: 600×600px (1:1 square)',
        description: 'Shop product card image',
        aspectRatio: 1,
        minWidth: 600,
        minHeight: 600,
        viewMode: 1
    },
    'thumbnail': {
        label: 'Video Thumbnail',
        dimensions: 'Recommended: 1280×720px (16:9 ratio)',
        description: 'YouTube vlog thumbnail',
        aspectRatio: 16 / 9,
        minWidth: 1280,
        minHeight: 720,
        viewMode: 1
    }
};

// Get configuration for a crop type
function getCropConfig(cropType) {
    return CROP_CONFIGS[cropType] || CROP_CONFIGS['section'];
}
