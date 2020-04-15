let dropdowns = document.getElementsByClassName('custom-dropdown');

Array.from(dropdowns).forEach((dropdown) => {
    dropdown.addEventListener('click', showDropdownOptions)
});

function showDropdownOptions(event) {

	/* It has to be (this) because it refers to a element even if I will click directly on an icon element in anchor element */
	/* Eevery dropdown should have data-target which contains id of target element */
	let containerId = this.getAttribute('data-target');
	let container = document.getElementById(containerId);

	container.style.display = 'block';
}

/* Close the dropdown menu if the user clicks outside of it */
window.onclick = function(event) {
	let divsToHide = new Array();

	/* Notice that it will work only if dropdown contains as first child arrow down icon element */
	Array.from(dropdowns).forEach((dropdown) => {
		divsToHide.push(
			{
				'div' : document.getElementById(dropdown.getAttribute('data-target')),
				'icon' : dropdown.children[0] 
			}
		);
	});
	
	/* Hide all dropdowns unless one of them was clicked then do not hide that one */
	Array.from(divsToHide).forEach(({div, icon}) => {
		if ((!event.target.matches('.custom-dropdown') || event.target.getAttribute('data-target') !== div.getAttribute('id')) && event.target !== icon) {
			div.style.display = "none";
		}
	})
}