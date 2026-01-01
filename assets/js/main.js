document.addEventListener('DOMContentLoaded', () => {
    // Add entrance animation for cards
    const cards = document.querySelectorAll('.item-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100); // Staggered delay
    });

    console.log('App loaded successfully');
});
