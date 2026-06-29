const filterButtons = document.querySelectorAll('.filter-btn');
const carCards = document.querySelectorAll('.car-card');

filterButtons.forEach(button => {

    button.addEventListener('click', () => {

        // Remove active class from all buttons
        filterButtons.forEach(btn => {
            btn.classList.remove('active');
        });

        // Add active class to clicked button
        button.classList.add('active');

        // Get selected filter
        const filterValue = button.getAttribute('data-filter');

        // Loop through car cards
        carCards.forEach(card => {

            const category = card.getAttribute('data-category');

            if (filterValue === 'all' || category === filterValue) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }

        });

    });

});