/**
 * Kafánek Brain - AI Design Studio JavaScript
 * Golden Ratio Magic (φ = 1.618)
 */

(function($) {
    'use strict';
    
    class KafanekDesignStudio {
        constructor() {
            this.PHI = kafanekDesign.phi;
            this.GOLDEN_ANGLE = kafanekDesign.golden_angle;
            this.selectedCategory = null;
            this.selectedStyle = null;
            this.init();
        }
        
        init() {
            this.bindEvents();
        }
        
        bindEvents() {
            $('.category-btn').on('click', (e) => this.selectCategory(e));
            $('#generate-palette-btn').on('click', () => this.generateGoldenPalette());
            $('#generate-design-btn').on('click', () => this.generateDesign());
            $(document).on('click', '.tool-btn', (e) => this.handleToolAction(e));
            $(document).on('click', '.variation-item', (e) => this.selectVariation(e));
        }
        
        selectCategory(e) {
            const $btn = $(e.currentTarget);
            const category = $btn.data('category');
            
            $('.category-btn').removeClass('active');
            $btn.addClass('active');
            
            this.selectedCategory = category;
            this.loadStyles(category);
            $('.style-selection').slideDown(300);
        }
        
        loadStyles(category) {
            const styles = kafanekDesign.styles[category] || [];
            const $stylesGrid = $('#styles-grid');
            $stylesGrid.empty();
            
            styles.forEach(style => {
                $('<button>')
                    .addClass('style-btn')
                    .text(style.replace(/_/g, ' '))
                    .data('style', style)
                    .on('click', (e) => this.selectStyle(e))
                    .appendTo($stylesGrid);
            });
        }
        
        selectStyle(e) {
            $('.style-btn').removeClass('active');
            $(e.currentTarget).addClass('active');
            this.selectedStyle = $(e.currentTarget).data('style');
        }
        
        generateGoldenPalette() {
            const baseHue = Math.floor(Math.random() * 360);
            const palette = [];
            
            for (let i = 0; i < 5; i++) {
                const hue = (baseHue + i * this.GOLDEN_ANGLE) % 360;
                const hex = this.hslToHex(hue, 70, 60 - i * 5);
                palette.push(hex);
            }
            
            this.displayPalette(palette);
        }
        
        displayPalette(palette) {
            const $container = $('#generated-palette');
            $container.empty();
            
            palette.forEach(hex => {
                $('<div>')
                    .addClass('color-swatch')
                    .css('background', hex)
                    .attr('title', hex)
                    .append($('<span>').addClass('hex-value').text(hex))
                    .on('click', () => this.copyToClipboard(hex))
                    .appendTo($container);
            });
        }
        
        async generateDesign() {
            if (!this.selectedCategory || !this.selectedStyle) {
                alert('Vyber kategorii a styl!');
                return;
            }
            
            const description = $('#design-description').val().trim();
            if (!description) {
                alert('Napiš popis designu!');
                return;
            }
            
            this.showLoading();
            
            try {
                const response = await $.ajax({
                    url: kafanekDesign.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'kafanek_generate_design',
                        nonce: kafanekDesign.nonce,
                        category: this.selectedCategory,
                        style: this.selectedStyle,
                        description: description,
                        colors: this.getSelectedColors(),
                        use_golden_ratio: $('#use-golden-ratio').is(':checked'),
                        generate_variations: $('#generate-variations').is(':checked'),
                        quality: $('#image-quality').val()
                    }
                });
                
                if (response.success) {
                    this.displayResults(response.data);
                    if ($('#use-golden-ratio').is(':checked')) {
                        setTimeout(() => this.drawGoldenGrid(), 500);
                    }
                }
            } catch (error) {
                alert('Chyba při generování!');
            } finally {
                this.hideLoading();
            }
        }
        
        getSelectedColors() {
            return $('#color1, #color2, #color3').map(function() {
                return $(this).val();
            }).get();
        }
        
        displayResults(data) {
            $('.design-results-panel').slideDown(400);
            $('#main-design-image').attr('src', data.image_url);
            
            if (data.variations) this.displayVariations(data.variations);
            if (data.colors) this.displayExtractedColors(data.colors);
            
            $('html, body').animate({
                scrollTop: $('.design-results-panel').offset().top - 100
            }, 600);
        }
        
        displayVariations(variations) {
            const $grid = $('#variations-grid');
            $grid.empty();
            variations.forEach(v => {
                $('<div>')
                    .addClass('variation-item')
                    .append($('<img>').attr('src', v.url))
                    .appendTo($grid);
            });
        }
        
        displayExtractedColors(colors) {
            const $swatches = $('#color-swatches');
            $swatches.empty();
            colors.forEach(color => {
                const hex = color.hex || color;
                $('<div>')
                    .addClass('color-swatch')
                    .css('background', hex)
                    .append($('<div>').addClass('color-info').text(hex))
                    .on('click', () => this.copyToClipboard(hex))
                    .appendTo($swatches);
            });
        }
        
        drawGoldenGrid() {
            const canvas = document.getElementById('golden-grid-canvas');
            const img = document.getElementById('main-design-image');
            if (!canvas || !img) return;
            
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            const ctx = canvas.getContext('2d');
            
            ctx.strokeStyle = 'rgba(105, 112, 119, 0.7)';
            ctx.lineWidth = 3;
            
            const goldenX = canvas.width / this.PHI;
            const goldenY = canvas.height / this.PHI;
            
            ctx.beginPath();
            ctx.moveTo(goldenX, 0);
            ctx.lineTo(goldenX, canvas.height);
            ctx.moveTo(0, goldenY);
            ctx.lineTo(canvas.width, goldenY);
            ctx.stroke();
            
            ctx.fillStyle = 'rgba(105, 112, 119, 0.9)';
            ctx.beginPath();
            ctx.arc(goldenX, goldenY, 10, 0, 2 * Math.PI);
            ctx.fill();
            
            canvas.classList.add('active');
        }
        
        handleToolAction(e) {
            const action = $(e.currentTarget).data('action');
            
            switch(action) {
                case 'toggle-grid':
                    $('#golden-grid-canvas').toggleClass('active');
                    break;
                case 'download':
                    window.open($('#main-design-image').attr('src'), '_blank');
                    break;
            }
        }
        
        showLoading() {
            $('#generate-design-btn').prop('disabled', true);
            $('.loading-indicator').fadeIn(200);
        }
        
        hideLoading() {
            $('#generate-design-btn').prop('disabled', false);
            $('.loading-indicator').fadeOut(200);
        }
        
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                this.showNotification(`${text} zkopírováno!`);
            });
        }
        
        showNotification(message) {
            const $notif = $('<div>')
                .css({
                    position: 'fixed',
                    top: '20px',
                    right: '20px',
                    background: '#10b981',
                    color: 'white',
                    padding: '15px 25px',
                    borderRadius: '8px',
                    zIndex: 99999,
                    boxShadow: '0 4px 20px rgba(0,0,0,0.2)'
                })
                .text(message)
                .appendTo('body');
            
            setTimeout(() => $notif.fadeOut(300, () => $notif.remove()), 3000);
        }
        
        hslToHex(h, s, l) {
            h /= 360;
            s /= 100;
            l /= 100;
            
            let r, g, b;
            if (s === 0) {
                r = g = b = l;
            } else {
                const hue2rgb = (p, q, t) => {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1/6) return p + (q - p) * 6 * t;
                    if (t < 1/2) return q;
                    if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                    return p;
                };
                
                const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                const p = 2 * l - q;
                r = hue2rgb(p, q, h + 1/3);
                g = hue2rgb(p, q, h);
                b = hue2rgb(p, q, h - 1/3);
            }
            
            return '#' + [r, g, b].map(x => {
                const hex = Math.round(x * 255).toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            }).join('');
        }
        
        selectVariation(e) {
            const url = $(e.currentTarget).find('img').attr('src');
            $('#main-design-image').attr('src', url);
            $('html, body').animate({
                scrollTop: $('.main-design-container').offset().top - 100
            }, 400);
        }
    }
    
    // Initialize when DOM ready
    $(document).ready(() => {
        new KafanekDesignStudio();
        console.log('🎨 Kafánek Design Studio ready!');
    });
    
})(jQuery);
