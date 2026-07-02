// DOM Elements
const hamburger = document.querySelector('.hamburger');
const mainNav = document.querySelector('.main-nav');
const navLinks = document.querySelectorAll('.nav-link');
const audioToggle = document.getElementById('audioToggle');
const voiceAudio = document.getElementById('voiceAudio');
const langSelector = document.getElementById('langSelector');
const trackBtn = document.querySelector('.btn-track');
const trackingInput = document.getElementById('trackingCode');

// Translations
const translations = {
  en: {
    home: 'Home',
    track: 'Track Shipment',
    ship: 'Send Shipment',
    about: 'About Us',
    contact: 'Contact',
    trackTitle: 'Track Your Shipment',
    trackDesc: 'Enter your tracking code to get real-time updates',
    services: 'Our Services',
    servicesDesc: 'Comprehensive shipping solutions tailored to your needs',
  },
  es: {
    home: 'Inicio',
    track: 'Rastrear Envío',
    ship: 'Enviar Envío',
    about: 'Sobre Nosotros',
    contact: 'Contacto',
    trackTitle: 'Rastrear Tu Envío',
    trackDesc: 'Ingresa tu código de seguimiento para obtener actualizaciones en tiempo real',
    services: 'Nuestros Servicios',
    servicesDesc: 'Soluciones de envío integral adaptadas a tus necesidades',
  },
  fr: {
    home: 'Accueil',
    track: 'Suivre Expédition',
    ship: 'Envoyer Expédition',
    about: 'À Propos',
    contact: 'Contact',
    trackTitle: 'Suivre Votre Expédition',
    trackDesc: 'Entrez votre code de suivi pour les mises à jour en temps réel',
    services: 'Nos Services',
    servicesDesc: 'Solutions d\'expédition complètes adaptées à vos besoins',
  },
  de: {
    home: 'Startseite',
    track: 'Paket Verfolgung',
    ship: 'Paket Versenden',
    about: 'Über Uns',
    contact: 'Kontakt',
    trackTitle: 'Ihr Paket Verfolgen',
    trackDesc: 'Geben Sie Ihren Verfolgungscode ein, um Echtzeit-Updates zu erhalten',
    services: 'Unsere Dienstleistungen',
    servicesDesc: 'Umfassende Versandlösungen, die auf Ihre Anforderungen zugeschnitten sind',
  },
};

// Hamburger Menu Toggle
if (hamburger) {
  hamburger.addEventListener('click', () => {
    mainNav.classList.toggle('active');
  });
}

// Close menu when nav link clicked
navLinks.forEach(link => {
  link.addEventListener('click', () => {
    mainNav.classList.remove('active');
    updateActiveNav(link);
  });
});

// Update Active Navigation
function updateActiveNav(link) {
  navLinks.forEach(l => l.classList.remove('active'));
  link.classList.add('active');
}

// Audio Toggle
if (audioToggle) {
  audioToggle.addEventListener('click', () => {
    if (voiceAudio.paused) {
      voiceAudio.play().catch(e => {
        console.log('Audio playback error:', e);
        speakMessage('InterGlobe.cloud provides the most reliable and transparent shipping services globally. With 99.7% on-time delivery and 24/7 support, your cargo is in trusted hands.');
      });
      audioToggle.style.transform = 'scale(1.1) rotate(10deg)';
    } else {
      voiceAudio.pause();
      audioToggle.style.transform = 'scale(1) rotate(0deg)';
    }
  });
}

// Text-to-Speech Fallback
function speakMessage(message) {
  if ('speechSynthesis' in window) {
    const utterance = new SpeechSynthesisUtterance(message);
    utterance.rate = 0.95;
    utterance.pitch = 1;
    speechSynthesis.speak(utterance);
  }
}

// Language Selector
if (langSelector) {
  langSelector.addEventListener('change', (e) => {
    const lang = e.target.value;
    updateLanguage(lang);
  });
}

function updateLanguage(lang) {
  const t = translations[lang] || translations.en;
  
  const navItems = document.querySelectorAll('.nav-link');
  if (navItems[0]) navItems[0].textContent = t.home;
  if (navItems[1]) navItems[1].textContent = t.track;
  if (navItems[2]) navItems[2].textContent = t.ship;
  if (navItems[3]) navItems[3].textContent = t.about;
  if (navItems[4]) navItems[4].textContent = t.contact;
  
  const trackingH2 = document.querySelector('.tracking-header h2');
  if (trackingH2) trackingH2.textContent = t.trackTitle;
  
  const trackingP = document.querySelector('.tracking-header p');
  if (trackingP) trackingP.textContent = t.trackDesc;
}

// Tracking Functionality
if (trackBtn) {
  trackBtn.addEventListener('click', (e) => {
    e.preventDefault();
    const trackingCode = trackingInput.value.trim();
    
    if (!trackingCode) {
      showNotification('Please enter a tracking number', 'error');
      return;
    }
    
    showNotification(`Tracking shipment ${trackingCode}...`, 'info');
    
    setTimeout(() => {
      showNotification(`Shipment ${trackingCode} is on its way! Expected delivery: 5-7 business days.`, 'success');
    }, 1500);
  });
}

// Notification System
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.style.cssText = `
    position: fixed;
    top: 80px;
    right: 20px;
    background: ${type === 'success' ? '#00AA44' : type === 'error' ? '#FF6B35' : '#0066CC'};
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    z-index: 2000;
    animation: slideIn 0.3s ease-out;
    max-width: 300px;
  `;
  notification.textContent = message;
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease-out';
    setTimeout(() => notification.remove(), 300);
  }, 4000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateX(100px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }
  
  @keyframes slideOut {
    from {
      opacity: 1;
      transform: translateX(0);
    }
    to {
      opacity: 0;
      transform: translateX(100px);
    }
  }
  
  @keyframes scroll-animation {
    0% {
      opacity: 0;
      transform: translateY(30px);
    }
    100% {
      opacity: 1;
      transform: translateY(0);
    }
  }
`;
document.head.appendChild(style);

// Scroll Animation Observer
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.animation = 'scroll-animation 0.6s ease-out forwards';
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

const animateElements = document.querySelectorAll('.service-card, .feature-item, .form-section');
animateElements.forEach(el => {
  el.style.opacity = '0';
  observer.observe(el);
});

// Form Validation
const formInputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea');

formInputs.forEach(input => {
  input.addEventListener('blur', () => {
    validateInput(input);
  });
});

function validateInput(input) {
  if (!input.value.trim()) {
    input.style.borderColor = '#FF6B35';
    return false;
  }
  
  if (input.type === 'email' && !isValidEmail(input.value)) {
    input.style.borderColor = '#FF6B35';
    return false;
  }
  
  input.style.borderColor = '#00AA44';
  return true;
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// Smooth Scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth' });
    }
  });
});

// Page Navigation
window.addEventListener('hashchange', () => {
  const hash = window.location.hash.substring(1);
  showPage(hash);
});

function showPage(pageId) {
  const sections = document.querySelectorAll('section');
  sections.forEach(section => {
    section.style.display = section.id === pageId ? 'block' : 'none';
  });
  
  if (pageId === 'home' || pageId === '') {
    document.querySelectorAll('section').forEach(s => s.style.display = 'block');
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  updateLanguage('en');
  console.log('InterGlobe.cloud Design Preview Loaded Successfully');
});