document.addEventListener('DOMContentLoaded', () => {
  const foundForm = document.getElementById('found-form');
  const foundItemsList = document.getElementById('found-items');

  foundForm.addEventListener('submit', (event) => {
    event.preventDefault();

    const foundItemInput = document.getElementById('found-item');
    const foundItemDescription = foundItemInput.value.trim();

    if (foundItemDescription !== '') {
      const listItem = document.createElement('li');
      listItem.textContent = foundItemDescription;
      foundItemsList.appendChild(listItem);
      foundItemInput.value = '';
    }
  });
});
