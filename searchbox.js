function searchItems(event) {
    if (event.key === 'Enter') {
        const input = document.getElementById('search-box').value.trim().toLowerCase();
        const items = document.querySelectorAll('#lost-items li');
        const foundByTitle = [];
        const foundByDescription = [];

        // Reset display for all items
        items.forEach(item => item.style.display = '');

        // Search by title
        items.forEach(item => {
            const title = item.querySelector('.description h3').textContent.toLowerCase();
            if (title.includes(input)) {
                foundByTitle.push(item);
            }
        });

        // Search by description if no result found by title
        if (foundByTitle.length === 0) {
            items.forEach(item => {
                const description = item.querySelector('.description p').textContent.toLowerCase();
                if (description.includes(input)) {
                    foundByDescription.push(item);
                }
            });
        }

        // Hide items not found by title or description
        items.forEach(item => {
            if (!foundByTitle.includes(item) && !foundByDescription.includes(item)) {
                item.style.display = 'none';
            }
        });

        // Display search information
        const numResults = foundByTitle.length + foundByDescription.length;
        const searchInfo = document.getElementById('search-info');
        searchInfo.textContent = numResults === 0 
            ? `0 results found for '${input}'`
            : numResults === 1 
                ? `result found for '${input}'`
                : `${numResults} results found for '${input}'`;
    }
}
