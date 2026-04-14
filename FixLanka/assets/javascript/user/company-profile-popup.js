const COMPANY_POPUP_API_BASE = '/2nd-Year-Group-Project/FixLanka';

(function initCompanyPopup() {
    const modal = document.getElementById('companyModal');
    if (!modal) return;

    let activeCompanyId = null;

    const logoEl = document.getElementById('companyModalLogo');
    const titleEl = document.getElementById('companyModalTitle');
    const typeEl = document.getElementById('companyModalType');
    const starsEl = document.getElementById('companyModalStars');
    const ratingTextEl = document.getElementById('companyModalRatingText');
    const contactEl = document.getElementById('companyModalContact');
    const emailEl = document.getElementById('companyModalEmail');
    const websiteEl = document.getElementById('companyModalWebsite');
    const districtsEl = document.getElementById('companyModalDistricts');
    const addressEl = document.getElementById('companyModalAddress');
    const descriptionEl = document.getElementById('companyModalDescription');
    const servicesSectionEl = document.getElementById('companyModalServicesSection');
    const servicesEl = document.getElementById('companyModalServices');

    function fallbackInitials(name) {
        if (!name) return 'CO';
        const parts = String(name).trim().split(' ').filter(Boolean);
        if (parts.length >= 2) return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        return String(name).substring(0, 2).toUpperCase();
    }

    function fallbackStars(rating) {
        const value = Number(rating) || 0;
        const fullStars = Math.floor(value);
        const hasHalfStar = value % 1 !== 0;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

        let html = '';
        for (let i = 0; i < fullStars; i++) html += '<i class="fas fa-star star"></i>';
        if (hasHalfStar) html += '<i class="fas fa-star-half-alt star"></i>';
        for (let i = 0; i < emptyStars; i++) html += '<i class="far fa-star star empty"></i>';
        return html;
    }

    function safeEscapeHtml(text) {
        if (typeof window.escapeHtml === 'function') {
            return window.escapeHtml(text);
        }
        const div = document.createElement('div');
        div.textContent = String(text ?? '');
        return div.innerHTML;
    }

    function setText(element, value) {
        if (element) element.textContent = value;
    }

    function setModalData(companyId, company) {
        const name = company.name || 'Company';
        const businessType = company.business_type || 'Service Company';
        const rating = Number(company.ratings ?? 0);
        const districts = company.districts || 'N/A';
        const description = company.description || 'No description available.';
        const contact = company.contact_no || 'N/A';
        const email = company.email || 'N/A';
        const website = company.website || 'N/A';
        const address = company.address || 'N/A';

        const services = String(businessType)
            .split(',')
            .map(item => item.trim())
            .filter(Boolean);

        const initials = typeof window.generateAvatarInitials === 'function'
            ? window.generateAvatarInitials(name)
            : fallbackInitials(name);

        logoEl.innerHTML = safeEscapeHtml(initials);
        setText(titleEl, name);
        setText(typeEl, businessType);

        const starsHtml = typeof window.generateStars === 'function'
            ? window.generateStars(Number.isFinite(rating) ? rating : 0)
            : fallbackStars(Number.isFinite(rating) ? rating : 0);

        if (starsEl) starsEl.innerHTML = starsHtml;
        setText(ratingTextEl, `${(Number.isFinite(rating) ? rating : 0).toFixed(1)} / 5.0`);

        setText(contactEl, contact);
        setText(emailEl, email);
        setText(websiteEl, website);
        setText(districtsEl, districts);
        setText(addressEl, address);
        setText(descriptionEl, description);

        if (servicesSectionEl && servicesEl) {
            if (services.length) {
                servicesSectionEl.style.display = '';
                servicesEl.innerHTML = services
                    .map(service => `<span class="modal-service-tag"><i class="fas fa-check-circle"></i> ${safeEscapeHtml(service)}</span>`)
                    .join('');
            } else {
                servicesSectionEl.style.display = 'none';
                servicesEl.innerHTML = '';
            }
        }

        activeCompanyId = companyId;
    }

    window.openCompanyProfile = async function openCompanyProfile(companyId) {
        const numericId = Number(companyId);
        if (!Number.isFinite(numericId) || numericId <= 0) {
            console.error('Invalid company ID:', companyId);
            return;
        }

        try {
            const apiUrl = `${COMPANY_POPUP_API_BASE}/api/companies.php?action=getDetails&id=${encodeURIComponent(numericId)}`;
            const response = await fetch(apiUrl, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const result = await response.json();
            if (!result || !result.success || !result.data) {
                throw new Error('Invalid response');
            }

            setModalData(numericId, result.data);
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        } catch (error) {
            console.error('Failed to load company profile:', error);
            alert('Failed to load company details. Please try again.');
        }
    };

    window.closeCompanyModal = function closeCompanyModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    };

    window.requestCompanyQuoteFromModal = function requestCompanyQuoteFromModal() {
        if (!activeCompanyId) return;
        if (typeof window.requestCompanyQuote === 'function') {
            window.requestCompanyQuote(activeCompanyId);
        }
    };

    window.sendCompanyRepairRequest = function sendCompanyRepairRequest() {
        if (!activeCompanyId) return;
        if (typeof window.sendRepairRequest === 'function') {
            window.sendRepairRequest('company', activeCompanyId);
        }
    };

    modal.addEventListener('click', function onModalClick(event) {
        if (event.target === modal) {
            window.closeCompanyModal();
        }
    });
})();
