// ===================================================
// AquaLearn - Complete Landing Page JavaScript
// Form Validation, Smooth Scroll, Notifications
// ===================================================

// ============= Navigation Scroll Effect =============
const navbar = document.getElementById('navbar');
const navLinks = document.querySelectorAll('.nav-link');
const mobileToggle = document.getElementById('mobileToggle');
const navMenu = document.getElementById('navMenu');

// Sticky navbar on scroll
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }

    // Update active nav link
    updateActiveNavLink();

    // Show/hide back to top button
    toggleBackToTop();
});

// Mobile menu toggle
mobileToggle.addEventListener('click', () => {
    navMenu.classList.toggle('active');
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (!navMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
        navMenu.classList.remove('active');
    }
});

// ============= Smooth Scroll Navigation =============
navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute('href');
        const targetSection = document.querySelector(targetId);

        if (targetSection) {
            const offsetTop = targetSection.offsetTop - 80;
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });

            // Close mobile menu
            navMenu.classList.remove('active');
        }
    });
});

// Update active nav link based on scroll position
function updateActiveNavLink() {
    const sections = document.querySelectorAll('section[id]');
    const scrollPosition = window.scrollY + 100;

    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;
        const sectionId = section.getAttribute('id');

        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${sectionId}`) {
                    link.classList.add('active');
                }
            });
        }
    });
}

// ============= Back to Top Button =============
const backToTopBtn = document.getElementById('backToTop');

function toggleBackToTop() {
    if (window.scrollY > 300) {
        backToTopBtn.classList.add('show');
    } else {
        backToTopBtn.classList.remove('show');
    }
}

backToTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// ============= Form Validation =============
const registrationForm = document.getElementById('registrationForm');
const formInputs = {
    fullName: document.getElementById('fullName'),
    age: document.getElementById('age'),
    gender: document.getElementById('gender'),
    whatsapp: document.getElementById('whatsapp'),
    address: document.getElementById('address'),
    program: document.getElementById('program'),
    schedule: document.getElementById('schedule')
};

// Validation rules
const validationRules = {
    fullName: {
        required: true,
        minLength: 3,
        pattern: /^[a-zA-Z\s]+$/,
        errorMessages: {
            required: 'Nama lengkap wajib diisi',
            minLength: 'Nama lengkap minimal 3 karakter',
            pattern: 'Nama hanya boleh mengandung huruf dan spasi'
        }
    },
    age: {
        required: true,
        min: 4,
        max: 100,
        errorMessages: {
            required: 'Umur wajib diisi',
            min: 'Umur minimal 4 tahun',
            max: 'Umur maksimal 100 tahun'
        }
    },
    gender: {
        required: true,
        errorMessages: {
            required: 'Jenis kelamin wajib dipilih'
        }
    },
    whatsapp: {
        required: true,
        pattern: /^[0-9]{10,13}$/,
        errorMessages: {
            required: 'Nomor WhatsApp wajib diisi',
            pattern: 'Nomor WhatsApp tidak valid (10-13 digit)'
        }
    },
    address: {
        required: true,
        minLength: 10,
        errorMessages: {
            required: 'Alamat wajib diisi',
            minLength: 'Alamat minimal 10 karakter'
        }
    },
    program: {
        required: true,
        errorMessages: {
            required: 'Program les wajib dipilih'
        }
    },
    schedule: {
        required: true,
        errorMessages: {
            required: 'Jadwal latihan wajib dipilih'
        }
    }
};

// Real-time validation on input blur
Object.keys(formInputs).forEach(fieldName => {
    const input = formInputs[fieldName];

    // Validate on blur
    input.addEventListener('blur', () => {
        validateField(fieldName, input);
    });

    // Clear error on focus
    input.addEventListener('focus', () => {
        clearFieldError(fieldName, input);
    });

    // Real-time validation on input for some fields
    if (fieldName === 'whatsapp') {
        input.addEventListener('input', () => {
            // Remove non-numeric characters
            input.value = input.value.replace(/\D/g, '');
        });
    }

    if (fieldName === 'fullName') {
        input.addEventListener('input', () => {
            // Remove numbers and special characters
            input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
        });
    }
});

// Validate individual field
function validateField(fieldName, input) {
    const rules = validationRules[fieldName];
    const value = input.value.trim();
    const errorSpan = document.getElementById(`${fieldName}Error`);

    // Required validation
    if (rules.required && !value) {
        showFieldError(input, errorSpan, rules.errorMessages.required);
        return false;
    }

    // Min length validation
    if (rules.minLength && value.length < rules.minLength) {
        showFieldError(input, errorSpan, rules.errorMessages.minLength);
        return false;
    }

    // Pattern validation
    if (rules.pattern && !rules.pattern.test(value)) {
        showFieldError(input, errorSpan, rules.errorMessages.pattern);
        return false;
    }

    // Min/Max validation for numbers
    if (rules.min && parseInt(value) < rules.min) {
        showFieldError(input, errorSpan, rules.errorMessages.min);
        return false;
    }

    if (rules.max && parseInt(value) > rules.max) {
        showFieldError(input, errorSpan, rules.errorMessages.max);
        return false;
    }

    // If all validations pass
    clearFieldError(fieldName, input);
    return true;
}

// Show field error
function showFieldError(input, errorSpan, message) {
    input.classList.add('error');
    if (errorSpan) {
        errorSpan.textContent = message;
        errorSpan.style.display = 'block';
    }
}

// Clear field error
function clearFieldError(fieldName, input) {
    const errorSpan = document.getElementById(`${fieldName}Error`);
    input.classList.remove('error');
    if (errorSpan) {
        errorSpan.textContent = '';
        errorSpan.style.display = 'none';
    }
}

// ============= Form Submission =============
registrationForm.addEventListener('submit', (e) => {
    e.preventDefault();

    // Validate all fields
    let isValid = true;
    Object.keys(formInputs).forEach(fieldName => {
        const input = formInputs[fieldName];
        if (!validateField(fieldName, input)) {
            isValid = false;
        }
    });

    if (!isValid) {
        showNotification('Mohon lengkapi semua field dengan benar', 'error');
        // Scroll to first error
        const firstError = document.querySelector('.form-input.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
        return;
    }

    // Show loading state
    const submitBtn = registrationForm.querySelector('.btn-submit');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');

    btnText.style.display = 'none';
    btnLoader.style.display = 'inline';
    submitBtn.disabled = true;

    // Collect form data
    const formData = {
        fullName: formInputs.fullName.value.trim(),
        age: formInputs.age.value,
        gender: formInputs.gender.value,
        whatsapp: formInputs.whatsapp.value,
        address: formInputs.address.value.trim(),
        program: formInputs.program.value,
        schedule: formInputs.schedule.value,
        timestamp: new Date().toLocaleString('id-ID')
    };

    console.log('🎯 Form data collected:', formData);
    console.log('📡 About to call fetch...');

    // Submit to database via API
    fetch('api/submit_registration.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
        .then(response => {
            console.log('📥 Fetch response received:', response);
            return response.json();
        })
        .then(data => {
            console.log('📦 Data parsed:', data);
            // Reset button state
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
            submitBtn.disabled = false;

            if (data.success) {
                // Show success notification with registration ID
                showNotification(
                    `Terima kasih ${formData.fullName}! Pendaftaran Anda telah berhasil disimpan ke database. ` +
                    `ID Pendaftaran: #${data.registration_id}. Kami akan menghubungi Anda melalui WhatsApp dalam 1x24 jam.`,
                    'success'
                );

                // Reset form
                registrationForm.reset();

                // Send WhatsApp notification (optional - commented out)
                // sendWhatsAppNotification(formData);

                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // Show error notification
                showNotification(
                    data.message || 'Gagal menyimpan pendaftaran. Silakan coba lagi.',
                    'error'
                );
            }
        })
        .catch(error => {
            console.error('Error:', error);

            // Reset button state
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
            submitBtn.disabled = false;

            // Show error notification
            showNotification(
                'Terjadi kesalahan koneksi ke server. Pastikan database sudah disetup dan coba lagi.',
                'error'
            );
        });
});

// ============= WhatsApp Notification (Optional) =============
function sendWhatsAppNotification(formData) {
    const whatsappNumber = '6285320808003'; // Ganti dengan nomor admin

    let message = `*PENDAFTARAN BARU - AquaLearn*\n\n`;
    message += `📝 *Data Peserta:*\n`;
    message += `• Nama: ${formData.fullName}\n`;
    message += `• Umur: ${formData.age} tahun\n`;
    message += `• Jenis Kelamin: ${formData.gender}\n`;
    message += `• WhatsApp: ${formData.whatsapp}\n`;
    message += `• Alamat: ${formData.address}\n\n`;
    message += `🏊 *Program:* ${formData.program}\n`;
    message += `📅 *Jadwal:* ${formData.schedule}\n\n`;
    message += `⏰ Waktu Pendaftaran: ${formData.timestamp}`;

    const encodedMessage = encodeURIComponent(message);
    const whatsappURL = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;

    // Optional: Ask user if they want to send WhatsApp message
    // window.open(whatsappURL, '_blank');

    // Store URL for admin use
    console.log('WhatsApp URL:', whatsappURL);
}

// ============= Notification System =============
const notification = document.getElementById('notification');

function showNotification(message, type = 'success') {
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.classList.add('show');

    setTimeout(() => {
        notification.classList.remove('show');
    }, 5000);
}

// ============= Scroll Animations =============
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const scrollObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '0';
            entry.target.style.transform = 'translateY(30px)';
            entry.target.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';

            setTimeout(() => {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, 100);

            scrollObserver.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe elements for scroll animation
const animatedElements = document.querySelectorAll(
    '.program-card, .facility-card, .testimonial-card, .advantage-item, .vm-item'
);

animatedElements.forEach(element => {
    scrollObserver.observe(element);
});

// ============= Program Card Interaction =============
const programCards = document.querySelectorAll('.program-card');

programCards.forEach(card => {
    card.addEventListener('mouseenter', function () {
        this.style.transition = 'all 0.3s ease';
    });
});

// ============= Auto-update schedule options based on program =============
const programSelect = formInputs.program;
const scheduleSelect = formInputs.schedule;

const scheduleOptions = {
    'Kelas Anak-anak': [
        { value: 'Senin, Rabu, Jumat (15:00 - 16:00)', text: 'Senin, Rabu, Jumat (15:00 - 16:00)' },
        { value: 'Selasa, Kamis, Sabtu (15:00 - 16:00)', text: 'Selasa, Kamis, Sabtu (15:00 - 16:00)' }
    ],
    'Kelas Remaja': [
        { value: 'Selasa, Kamis, Sabtu (16:00 - 17:30)', text: 'Selasa, Kamis, Sabtu (16:00 - 17:30)' },
        { value: 'Senin, Rabu, Jumat (16:00 - 17:30)', text: 'Senin, Rabu, Jumat (16:00 - 17:30)' }
    ],
    'Kelas Dewasa': [
        { value: 'Pagi (06:00 - 07:00)', text: 'Pagi (06:00 - 07:00)' },
        { value: 'Sore (18:00 - 19:00)', text: 'Sore (18:00 - 19:00)' }
    ],
    'Kelas Privat': [
        { value: 'Pagi (06:00 - 08:00)', text: 'Pagi (06:00 - 08:00)' },
        { value: 'Siang (15:00 - 17:00)', text: 'Siang (15:00 - 17:00)' },
        { value: 'Sore (17:00 - 19:00)', text: 'Sore (17:00 - 19:00)' }
    ]
};

programSelect.addEventListener('change', () => {
    const selectedProgram = programSelect.value;

    // Clear current options
    scheduleSelect.innerHTML = '<option value="">Pilih jadwal</option>';

    // Add new options based on selected program
    if (scheduleOptions[selectedProgram]) {
        scheduleOptions[selectedProgram].forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.textContent = option.text;
            scheduleSelect.appendChild(optionElement);
        });
    }
});

// ============= Smooth Scrolling for CTA Buttons =============
const ctaButtons = document.querySelectorAll('a[href^="#"]');

ctaButtons.forEach(button => {
    button.addEventListener('click', (e) => {
        const href = button.getAttribute('href');
        if (href.startsWith('#') && href !== '#') {
            e.preventDefault();
            const targetSection = document.querySelector(href);

            if (targetSection) {
                const offsetTop = targetSection.offsetTop - 80;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        }
    });
});

// ============= Prevent Double Form Submission =============
let isSubmitting = false;

registrationForm.addEventListener('submit', (e) => {
    if (isSubmitting) {
        e.preventDefault();
        return false;
    }
});

// ============= Form Auto-save (LocalStorage) - Optional =============
const AUTO_SAVE_KEY = 'aqualear_form_data';

// Save form data to localStorage on input
function autoSaveForm() {
    const formData = {};
    Object.keys(formInputs).forEach(fieldName => {
        formData[fieldName] = formInputs[fieldName].value;
    });
    localStorage.setItem(AUTO_SAVE_KEY, JSON.stringify(formData));
}

// Restore form data from localStorage
function restoreFormData() {
    const savedData = localStorage.getItem(AUTO_SAVE_KEY);
    if (savedData) {
        const formData = JSON.parse(savedData);
        Object.keys(formData).forEach(fieldName => {
            if (formInputs[fieldName]) {
                formInputs[fieldName].value = formData[fieldName];
            }
        });
    }
}

// Auto-save on input change
Object.keys(formInputs).forEach(fieldName => {
    formInputs[fieldName].addEventListener('input', autoSaveForm);
});

// Restore on page load
// Uncomment to enable auto-save feature
// restoreFormData();

// Clear saved data on successful submission
registrationForm.addEventListener('submit', () => {
    setTimeout(() => {
        localStorage.removeItem(AUTO_SAVE_KEY);
    }, 2000);
});
