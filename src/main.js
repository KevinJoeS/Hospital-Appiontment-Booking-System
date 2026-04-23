// Main application logic for Hospital System

const marqueeMessages = [
    "<span>Joe</span> Medical Center - Your Trusted Healthcare Partner!",
    "Welcome to Joe Medical Center!",
    "Book your appointments with ease.",
    "Quality healthcare at your fingertips.",
    "Trusted by thousands of patients."
];

function initMarquee() {
    const marqueeContainer = document.getElementById('marquee-content');
    if (!marqueeContainer) return;

    // Create a continuous loop of messages
    const content = [...marqueeMessages, ...marqueeMessages].map(msg => 
        `<span class="marquee-text">${msg}</span>`
    ).join('');

    marqueeContainer.innerHTML = content;
}

document.addEventListener('DOMContentLoaded', () => {
    initMarquee();
    console.log('Hospital System initialized');
});
