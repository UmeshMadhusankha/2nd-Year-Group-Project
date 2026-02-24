
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll to sections
    const sectionTitles = document.querySelectorAll('.section-title');
    
    sectionTitles.forEach(title => {
        title.style.cursor = 'pointer';
        
        title.addEventListener('click', function() {
            // Highlight section briefly
            const section = this.closest('.terms-section');
            section.style.backgroundColor = 'var(--bg-tertiary)';
            section.style.transition = 'background-color 0.3s ease';
            
            setTimeout(() => {
                section.style.backgroundColor = 'transparent';
            }, 500);
        });
    });
    
    // Add smooth scroll behavior to footer links
    const footerLinks = document.querySelectorAll('.footer-link');
    footerLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
    
    // Add reading progress indicator
    const progressBar = document.createElement('div');
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), #0066cc);
        z-index: 9999;
        transition: width 0.1s ease;
    `;
    document.body.appendChild(progressBar);
    
    window.addEventListener('scroll', function() {
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight - windowHeight;
        const scrolled = window.scrollY;
        const progress = (scrolled / documentHeight) * 100;
        
        progressBar.style.width = progress + '%';
    });
    
    // Back to top functionality on home button click
    const homeBtn = document.querySelector('.btn-home');
    if (homeBtn) {
        homeBtn.addEventListener('click', function(e) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
